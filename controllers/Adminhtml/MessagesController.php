<?php

class Junaidbhura_Jbmarketplace_Adminhtml_MessagesController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('jbmarketplace')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Messages'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

    public function newAction() {
        $this->_forward('edit');
    }
 
    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $store = $this->getRequest()->getParam('store');
        $model = Mage::getModel('jbmarketplace/jbmarketplacemessages')->setStoreId($store)->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data))
                $model->setData($data);

            Mage::register('message_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('jbmarketplace');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('jbmarketplace/adminhtml_messages_edit'))
                    ->_addLeft($this->getLayout()->createBlock('jbmarketplace/adminhtml_messages_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jbmarketplace')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
 
	public function saveAction() {
        $data = $this->getRequest()->getPost();

        if ($data) {
            $now = Mage::getModel('core/date')->timestamp( time() );
            $data['create_dtime'] = date( 'Y-m-d H:i:s', $now );
			$model = Mage::getModel('jbmarketplace/jbmarketplacemessages');	
			$model->setData($data);
			if($id = $this->getRequest()->getParam('id')) {
                $model->setId($id);
            }
			try {

				$model->save();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('jbmarketplace')->__('Message information was successfully updated'));
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
            $this->_redirect('*/*/');
        }

	}


    
}
