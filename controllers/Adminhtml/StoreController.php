<?php

class Junaidbhura_Jbmarketplace_Adminhtml_StoreController extends Mage_Adminhtml_Controller_action
{


	public function indexAction() {
        $this->_title($this->__('Marketplace'))->_title($this->__('Store'));

        $this->loadLayout();
        $this->_setActiveMenu('jbmarketplace');
        $this->_addContent($this->getLayout()->createBlock('jbmarketplace/adminhtml_store_edit'));
        $this->renderLayout();
	}


 
	public function saveAction() {
        $data = $this->getRequest()->getPost();

        if ($data['jbmarketplace_vendor_id']) {

            if (isset($data['store_logo']['delete'])) {
                Mage::helper('jbmarketplace')->deleteImageFile($data['store_logo']['value']);
            }
            $store_logo = Mage::helper('jbmarketplace')->uploadStoreImage('store_logo');
            if ($store_logo || (isset($data['store_logo']['delete']) && $data['store_logo']['delete'])) {
                $data['store_logo'] = $store_logo;
            } else {
                unset($data['store_logo']);
            }


            if (isset($data['store_image']['delete'])) {
                Mage::helper('jbmarketplace')->deleteImageFile($data['store_image']['value']);
            }
            $store_image = Mage::helper('jbmarketplace')->uploadStoreImage('store_image');
            if ($store_image || (isset($data['store_image']['delete']) && $data['store_image']['delete'])) {
                $data['store_image'] = $store_image;
            } else {
                unset($data['store_image']);
            }
            

            $id = $data['jbmarketplace_vendor_id'];
			$model = Mage::getModel('jbmarketplace/jbmarketplacevendors');	
			$model->setData($data)
				->setId($id);

			try {

				$model->save();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('jbmarketplace')->__('Store information was successfully updated'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }

        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jbmarketplace')->__('Unable to find vendor to save'));
            $this->_redirect('*/*/');
        }

	}


    
}
