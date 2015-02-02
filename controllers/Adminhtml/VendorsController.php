<?php

class Junaidbhura_Jbmarketplace_Adminhtml_VendorsController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('jbmarketplace')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Vendors Manager'), Mage::helper('adminhtml')->__('Vendors Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('jbmarketplace/jbmarketplacevendors')->load($id);

		if ($model->getId()) {
			// Vendor can only edit his own info
            $current_user_id = Mage::getSingleton( 'admin/session' )->getUser()->getUserId();
            if( $current_user_id != 1 && $model->getVendorId() != $current_user_id ) {
                $this->_forward('denied');
                return;
            }

			Mage::register('vendor_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('jbmarketplace');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Vendor Manager'), Mage::helper('adminhtml')->__('Vendor Manager'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('jbmarketplace/adminhtml_vendors_edit'))
                            ->_addLeft($this->getLayout()->createBlock('jbmarketplace/adminhtml_vendors_edit_tabs'));

			$this->renderLayout();

		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jbmarketplace')->__('Vendor does not exist'));
			$this->_redirect('*/*/');
		}
	}

	public function saveAction() {
        $data = $this->getRequest()->getPost();

        if ($data) {
            $vendor_id = $this->getRequest()->getParam('id');
			$model = Mage::getModel('jbmarketplace/jbmarketplacevendors');	
			$model->setData($data)
				->setId($vendor_id);
			
			try {

				$model->save();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('jbmarketplace')->__('Vendor was successfully updated'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
                //before redirect, save all the products to this vendor.
                // $warehouse_id = $data['warehouse_id'];

                // if($warehouse_id) {
                //     //get all warehouse's product
                //     $warehouse_products = Mage::getModel( 'catalog/product' )
                //         ->getCollection()
                //         ->addFieldToSelect( 'product_id' )
                //         ->addFieldToFilter( 'warehouse', $product->getEntityId() )
                //         ->load();

                //     $warehouse_size = $warehouse_products->getSize();

                //     // Check if the product is aready assigned to this user (not a new product)
                //     $current_products = Mage::getModel( 'jbmarketplace/jbmarketplaceproducts' )
                //             ->getCollection()
                //             ->addFieldToSelect( 'product_id' )
                //             ->addFieldToFilter( 'user_id', $model->getData['vendor_id'] )
                //             ->load();

                //     $current_size = $current_products->getSize();

                //     if($warehouse_size != $current_size) {
                //         // update list
                //     }
                // }

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

  
    public function exportCsvAction()
    {
        $fileName   = 'vendors.csv';
        $content    = $this->getLayout()->createBlock('jbmarketplace/adminhtml_vendors_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'vendors.xml';
        $content    = $this->getLayout()->createBlock('jbmarketplace/adminhtml_vendors_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
    
}
