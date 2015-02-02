<?php

class Junaidbhura_Jbmarketplace_Adminhtml_ImportController extends Mage_Adminhtml_Controller_action
{

    protected function _initProfile($idFieldName = 'id')
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Import and Export'))
             ->_title($this->__('Profiles'));

        //$profileId = (int) $this->getRequest()->getParam($idFieldName);
        $profileId = Mage::getStoreConfig('jbmarketplace/jbmarketplace/profile_id');
        $profile = Mage::getModel('dataflow/profile');

        if ($profileId) {
            $profile->load($profileId);
            if (!$profile->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->__('The profile you are trying to save no longer exists'));
                $this->_redirect('*/*');
                return false;
            }
        }

        Mage::register('current_convert_profile', $profile);

        return $this;
    }


	public function indexAction() {
        $this->_title($this->__('Import'))->_title($this->__('Products'));

        $this->_initProfile();
        $this->loadLayout();
        $this->_setActiveMenu('jbmarketplace');
        $this->_addContent($this->getLayout()->createBlock('jbmarketplace/adminhtml_import_edit'))
            ->_addLeft($this->getLayout()->createBlock('jbmarketplace/adminhtml_import_edit_tabs'));
        $this->renderLayout();
	}

	public function saveAction() {
        $data = $this->getRequest()->getPost();

        if ($data) {

            if (isset($_FILES['file_1']['tmp_name']) || isset($_FILES['file_2']['tmp_name'])
            || isset($_FILES['file_3']['tmp_name'])) {
                for ($index = 0; $index < 3; $index++) {
                    if ($file = $_FILES['file_' . ($index+1)]['tmp_name']) {
                        $uploader = new Mage_Core_Model_File_Uploader('file_' . ($index + 1));
                        $uploader->setAllowedExtensions(array('csv','xml'));
                        $path = Mage::app()->getConfig()->getTempVarDir() . '/import/'.Mage::getSingleton( 'admin/session' )->getUser()->getUsername().'/';
                        $uploader->save($path);
                        if ($uploadFile = $uploader->getUploadedFileName()) {
                            $newFilename = 'import-' . date('YmdHis') . '-' . ($index+1) . '_' . $uploadFile;
                            rename($path . $uploadFile, $path . $newFilename);
                        }
                    }
                    //BOM deleting for UTF files
                    if (isset($newFilename) && $newFilename) {
                        $contents = file_get_contents($path . $newFilename);
                        if (ord($contents[0]) == 0xEF && ord($contents[1]) == 0xBB && ord($contents[2]) == 0xBF) {
                            $contents = substr($contents, 3);
                            file_put_contents($path . $newFilename, $contents);
                        }
                        unset($contents);
                    }
                }
            }

            Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('The profile has been saved.'));

            if ($this->getRequest()->getParam('continue')) {
                $this->_redirect('*/*/edit', array('id' => $profile->getId()));
            } else {
                $this->_redirect('*/*');
            }

        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jbmarketplace')->__('Unable to save profile'));
            $this->_redirect('*/*/');
        }

	}


    public function runAction()
    {
        $this->_initProfile();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function batchRunAction()
    {
        if ($this->getRequest()->isPost()) {
            $batchId = $this->getRequest()->getPost('batch_id', 0);
            $rowIds  = $this->getRequest()->getPost('rows');

            /* @var $batchModel Mage_Dataflow_Model_Batch */
            $batchModel = Mage::getModel('dataflow/batch')->load($batchId);

            if (!$batchModel->getId()) {
                return;
            }
            if (!is_array($rowIds) || count($rowIds) < 1) {
                return;
            }
            if (!$batchModel->getAdapter()) {
                return;
            }

            $batchImportModel = $batchModel->getBatchImportModel();
            $importIds = $batchImportModel->getIdCollection();

            $adapter = Mage::getModel($batchModel->getAdapter());
            $adapter->setBatchParams($batchModel->getParams());

            $errors = array();
            $saved  = 0;
            foreach ($rowIds as $importId) {
                $batchImportModel->load($importId);
                if (!$batchImportModel->getId()) {
                    $errors[] = Mage::helper('dataflow')->__('Skip undefined row.');
                    continue;
                }

                try {
                    $importData = $batchImportModel->getBatchData();
                    $adapter->saveRow($importData);
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                    continue;
                }
                $saved ++;
            }

            if (method_exists($adapter, 'getEventPrefix')) {
                /**
                 * Event for process rules relations after products import
                 */
                Mage::dispatchEvent($adapter->getEventPrefix() . '_finish_before', array(
                    'adapter' => $adapter
                ));

                /**
                 * Clear affected ids for adapter possible reuse
                 */
                $adapter->clearAffectedEntityIds();
            }

            $result = array(
                'savedRows' => $saved,
                'errors'    => $errors
            );
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    public function batchFinishAction()
    {
        $batchId = $this->getRequest()->getParam('id');
        if ($batchId) {
            $batchModel = Mage::getModel('dataflow/batch')->load($batchId);
            /* @var $batchModel Mage_Dataflow_Model_Batch */

            if ($batchModel->getId()) {
                $result = array();
                try {
                    $batchModel->beforeFinish();
                } catch (Mage_Core_Exception $e) {
                    $result['error'] = $e->getMessage();
                } catch (Exception $e) {
                    $result['error'] = Mage::helper('adminhtml')->__('An error occurred while finishing process. Please refresh the cache');
                }
                $batchModel->delete();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }
    }


}
