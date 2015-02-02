<?php

class Junaidbhura_Jbmarketplace_Adminhtml_OrdersController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('jbmarketplace')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Vendor Orders Manager'), Mage::helper('adminhtml')->__('Vendor Orders Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('jbmarketplace/jbmarketplaceorders')->load($id);

		if ($model->getId()) {
            // Vendor can only edit his own product
            $current_user_id = Mage::getSingleton( 'admin/session' )->getUser()->getUserId();
            if( $current_user_id != 1 && $model->getVendorId() != $current_user_id ) {
                $this->_forward('denied');
                return;
            }

			Mage::register('order_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('jbmarketplace');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Order Product Manager'), Mage::helper('adminhtml')->__('Order Product Manager'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('jbmarketplace/adminhtml_orders_edit'))
                            ->_addLeft($this->getLayout()->createBlock('jbmarketplace/adminhtml_orders_edit_tabs'));

			$this->renderLayout();

		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jbmarketplace')->__('Order product does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	// public function newAction() {
	// 	$this->_forward('edit');
	// }
 
	public function saveAction() {
        $data = $this->getRequest()->getPost();

        if ($data) {
            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('jbmarketplace/jbmarketplaceorders');
            $order_id = $model->load($id)->getOrderId();		
            $vendor_id = $model->load($id)->getVendorId();	
            //$old_status = $model->load($id)->getStatus();

            if($vendor_id != Mage::getSingleton( 'admin/session' )->getUser()->getUserId()) {
                exit;
            }

			$model->setData($data)
				->setId($id);
			
			try {

				$model->save();

                // check all the products in this order, if all the products are shipped, then send email to notify admin that the order products are shipped.
                if($data['status'] == 1) {
                    $this->sendShippedEmail($order_id, $vendor_id);
                }

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('jbmarketplace')->__('Order status was successfully updated'));
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
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jbmarketplace')->__('Unable to find order to save'));
            $this->_redirect('*/*/');
        }

	}

	private function sendShippedEmail($order_id, $vendor_id) {
        $orders = Mage::getModel( 'jbmarketplace/jbmarketplaceorders' )
                ->getCollection()
                ->addFieldToFilter( 'order_id', $order_id )
                ->addFieldToFilter( 'vendor_id', $vendor_id )
                ->load();

        $email_html = "";
        // Traverse products and build HTML email
        foreach ( $orders as $order ) {
            $increment_id = $order->getIncrementId();
            //$shipping_fee = $order->getShippingFee();
            $tracking_code = $order->getTrackingCode();
            $shipping_carrier = $order->getShippingCarrier();
            $message = $order->getMessage();

            $email_html .= '<tr><td valign="top" style="font-size:12px; padding:7px 9px 9px 9px; border-left:1px solid #EAEAEA; border-bottom:1px solid #EAEAEA;"> #'. $increment_id .' </td>';
            $product_html = "";
            $products = json_decode( $order->getProducts() );
            foreach($products as $product_id => $product_detail) {
                $product_html .= "Name: ".$product_detail->product_name."<br>";
                $product_html .= "SKU: ".$product_detail->sku."<br>";
                $product_html .= "Order Qty: ".$product_detail->order_qty."<br>";
                $product_html .= "Unit Price: ".$product_detail->unit_price."<br>";
                $product_html .= "Gross Sales: ".$product_detail->order_gt."<br>";
                $product_html .= "----------------------<br>";
            }
            $email_html .= '<td valign="top" style="font-size:12px; padding:7px 9px 9px 9px; border-left:1px solid #EAEAEA; border-bottom:1px solid #EAEAEA; border-right:1px solid #EAEAEA;">'. $product_html .'</td>';
            
            $vendor = Mage::getModel( 'jbmarketplace/jbmarketplacevendors' )->load( $vendor_id );
            // Check if we have vendor
            if ( $vendor !== false ) {
                $email_html .= '<td valign="top" style="font-size:12px; padding:7px 9px 9px 9px; border-left:1px solid #EAEAEA; border-bottom:1px solid #EAEAEA; border-right:1px solid #EAEAEA;"> <strong>Vendor:</strong>'. $vendor->getFirstname() . ' ' . $vendor->getLastname() . ' ( ' . $vendor->getEmail() . ' ) <br><strong>User ID:</strong> ' . $vendor->getUserId() .'</td>';
            }
            $email_html .= '<td valign="top" style="font-size:12px; padding:7px 9px 9px 9px; border-left:1px solid #EAEAEA; border-bottom:1px solid #EAEAEA; border-right:1px solid #EAEAEA;"> <strong>Shipping Carrier:</strong>'. $shipping_carrier .'<br><strong>Tracking Code:</strong>'. $tracking_code .'</td>';
            $email_html .= '<td valign="top" style="font-size:12px; padding:7px 9px 9px 9px; border-left:1px solid #EAEAEA; border-bottom:1px solid #EAEAEA; border-right:1px solid #EAEAEA;"> <strong>Message:</strong>'. $message .'</td></tr>';            

        }

        // Load template and send email
        if ( $email_html != '' ) {
            // Get admin name and email
            $to_email = Mage::getStoreConfig('trans_email/ident_general/email');
            $to_name = Mage::getStoreConfig('trans_email/ident_general/name');
            
            $email_template = Mage::getModel( 'core/email_template' )->loadDefault( 'junaidbhura_jbmarketplace_shippedemail' );
            
            try {
                $order = Mage::getModel('sales/order')->load($order_id);
                /*
                Note: We get the processed email template and subject and send it manually
                to counter a problem of the transactional email not sending (for some weird reason!)
                i.e: $email_template->send( $to_email, $to_name, array( 'order' => $order, 'store' => Mage::app()->getStore(), 'items_html' => $email_html ) ); doesn't work!
                */
                $email_content = $email_template->getProcessedTemplate( array( 'order' => $order, 'store' => Mage::app()->getStore(), 'items_html' => $email_html ) );
                $email_subject = $email_template->getProcessedTemplateSubject( array( 'order' => $order, 'store' => Mage::app()->getStore(), 'items_html' => $email_html ) );
                
                $mail = Mage::getModel( 'core/email' );
                $mail->setToName( $to_name );
                $mail->setToEmail( $to_email );
                $mail->setBody( $email_content );
                $mail->setSubject( $email_subject );
                $mail->setType( 'html' );
                if ( Mage::getStoreConfig( 'jbmarketplace/jbmarketplace/email_from_name' ) != '' )
                    $mail->setFromName( Mage::getStoreConfig( 'jbmarketplace/jbmarketplace/email_from_name' ) );
                if ( Mage::getStoreConfig( 'jbmarketplace/jbmarketplace/email_from' ) != '' )
                    $mail->setFromEmail( Mage::getStoreConfig( 'jbmarketplace/jbmarketplace/email_from' ) );

                $mail->send();
            }
            catch( Exception $e ) {
                Mage::log('Order products shipped mail exception');
                Mage::log($e);
            }
        }        

    }

    public function massStatusAction()
    {
        $productIds = $this->getRequest()->getParam('product_id');
        if(!is_array($productIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($productIds as $productId) {
                    $orderproduct = Mage::getSingleton('jbmarketplace/jbmarketplaceorderproducts')
                        ->load($productId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();

                    if($this->getRequest()->getParam('status') == 1) {
                        $this->sendOrderProductEmail($productId);
                    }
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($productIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'vendororders.csv';
        $content    = $this->getLayout()->createBlock('jbmarketplace/adminhtml_orders_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'vendororders.xml';
        $content    = $this->getLayout()->createBlock('jbmarketplace/adminhtml_orders_grid')
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
