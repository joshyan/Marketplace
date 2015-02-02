<?php
/**
 * Order Observer Model
 *
 * @category    Model
 * @package     Junaidbhura_Jbmarketplace
 * @author      Junaid Bhura <info@junaidbhura.com>
 */

class Junaidbhura_Jbmarketplace_Model_Orderobserver extends Mage_Payment_Model_Method_Abstract {
	/*
	* Save the order products to junaidbhura_jbmarketplace_orders table after order placed
	*/
	public function orderPlaced( $observer ) {
		// Get order
		$order = $observer->getEvent()->getOrder();
		$order_id = $order->getId();
		$increment_id = $order->getIncrementId();
		$customer_firstname = $order->getCustomerFirstname();
		$customer_lastname = $order->getCustomerLastname();

		// Mage::log('Order Info debug');
		// Mage::log($order->debug());

		$vendor_orders = $sales = $tax = $discount = array();
		$items = $order->getItemsCollection();
		foreach ( $items as $item ) {	
			// Mage::log('Order Items');
			// Mage::log($item->debug());

			// Bypass the configurable's child simple product
			if($item->getQuoteParentItemId()) {
				continue;
			}
			// Get vendor for this item
			$product_id = $item->getProductId();
			$vendor_id = $this->getVendorId($product_id);

			if($vendor_id) {
				//get sales total for the vendor (exclude tax and shipping, used for commission calculation)
				$sales[$vendor_id] += $item->getBaseRowTotal();
				$tax[$vendor_id] += $item->getBaseTaxAmount();
				$discount[$vendor_id] += $item->getBaseDiscountAmount();
				//assign product to the vendor
				$vendor_orders[$vendor_id]['products'][$product_id] = array(
					'product_name'=>$item->getName(),
					'order_qty'=>$item->getQtyOrdered(),
					'original_price'=>$item->getOriginalPrice(),
					'unit_price'=>$item->getBasePrice(),
					'row_total'=>$item->getBaseRowTotal(),
					'tax_percent'=>$item->getTaxPercent(),
					'tax_amount'=>$item->getBaseTaxAmount(),
					'discount_amount'=>$item->getBaseDiscountAmount(),
					'product_options'=>$item->getProductOptions(),
					'sku'=>$item->getSku(),
					'weight'=>$item->getWeight(),
					'warehouse_id'=>$item->getWarehouse(),
				);
			}
		}

		if($vendor_orders) {
			// Get warehouse shipping detail with warehouse_id
			$warehouse_shipping_details = json_decode( $order->getWarehouseShippingDetails() );
			// Mage::log('Warehouse shipping details json');
			// Mage::log($warehouse_shipping_details);

			if($warehouse_shipping_details) {
				foreach($warehouse_shipping_details as $wh) {
					$wh_id = $wh->warehouse;
					$warehouse[$wh_id]['shipping_fee'] = $wh->price;
					$warehouse[$wh_id]['shipping_method'] = $wh->methodTitle;
				}
			}

			$now = Mage::getModel('core/date')->timestamp( time() );
			foreach($vendor_orders as $vendor_id => $vendor_products) {

				$products = json_encode($vendor_products['products']);
				$products_count = count($vendor_products['products']);
				$vendor = Mage::getModel('jbmarketplace/jbmarketplacevendors')->load($vendor_id, 'vendor_id');
				$vendor_commission = $vendor->getCommission();
				$vendor_warehouse_id = $vendor->getWarehouseId();

				//get vendor's shipping fee and shipping method by it's warehouse id
				if($warehouse && $vendor_warehouse_id) {
					$shipping_fee = $warehouse[$vendor_warehouse_id]['shipping_fee'];
					$shipping_method = $warehouse[$vendor_warehouse_id]['shipping_method'];
				}
				$tax_amount = $tax[$vendor_id];
				$gross_sales = $sales[$vendor_id];
				$discount_amount = $discount[$vendor_id];

				$sales_amount = $gross_sales + $shipping_fee + $tax_amount - $discount_amount;
				$tax_charge = 3;
				$income_amount = ($gross_sales + $shipping_fee  - $discount_amount) * (100 - $vendor_commission) / 100 + $tax_amount * (100 - $tax_charge) / 100;

				$data = array('order_id'=>$order_id,
					'increment_id'=>$increment_id,
					'vendor_id'=>$vendor_id,
					'products'=>$products,
					'products_count'=>$products_count,
					'customer_firstname'=>$customer_firstname,
					'customer_lastname'=>$customer_lastname,
					'status'=>'0',
					'gross_sales'=>$gross_sales,
					'sales'=>$sales_amount,
					'income'=>$income_amount,
					'shipping_fee'=>$shipping_fee,
					'shipping_method'=>$shipping_method,
					'tax'=>$tax_amount,
					'discount'=>$discount_amount,
					'order_dtime'=>date( 'Y-m-d H:i:s', $now ));

				$model = Mage::getModel( 'jbmarketplace/jbmarketplaceorders' )->setData($data);
				try {
					$model->save();
				} catch (Exception $e){
					echo $e->getMessage();
				}
			}
		}

	}
	
	/**
	* Function to send an email to vendors when an order is saved
	*/
	public function orderSaved( $observer ) {
		// Check whether to notify vendors
		if ( Mage::getStoreConfig( 'jbmarketplace/jbmarketplace/notify_vendors' ) ) {
			// Get order
			$order = $observer->getEvent()->getOrder();
			//check if its status is matches the settings
			if ( $order->getStatus() == Mage::getStoreConfig( 'jbmarketplace/jbmarketplace/notify_order_status' ) )
				$this->notifyVendors( $order );
		}
	}
	
	/*
	 * Notifies vendors of an order
	 *
	 * @param $object
	 * @return void
	 */
	private function notifyVendors( $order ) {
		$vendors = array();
		
		// Get items in order, and split it based on vendors
		$items = $order->getItemsCollection();
		foreach ( $items as $item ) {
			// Get vendor for this item
			$product_id = $item->getProductId();
			$vendor_id = $this->getVendorId($product_id);
			
			// Check if vendor found
			if ( $vendor_id) {				
				// Add vendor to array
				if ( ! isset( $vendors[ $vendor_id ] ) )
					$vendors[ $vendor_id ]['products'] = array( $product_id => $item->getQtyOrdered() );
				else
					$vendors[ $vendor_id ]['products'][ $product_id ] = $item->getQtyOrdered();
			}
		}
		
		// Check for vendors, and send emails
		if ( count( $vendors ) > 0 ) {
			foreach ( $vendors as $key => $vendor ) {
				$this->emailVendor( array( $key => $vendor ), $order );
			}
		}
	}
	
	private function getVendorId( $product_id ) {
			$vendor = Mage::getModel( 'jbmarketplace/jbmarketplaceproducts' )
				->getCollection()
				->addFieldToSelect( 'user_id' )
				->addFieldToFilter( 'product_id', $product_id )
				->load();	
			
			// Check if vendor found
			if ( $vendor->getSize() > 0 ) {
				$vendor = $vendor->getFirstItem();
				$vendor_id = $vendor->getUserId();	
				return $vendor_id;
			}
			return false;
	}
	/*
	 * Sends email to vendor
	 *
	 * @param $array
	 * @param $object
	 * @return void
	 */
	private function emailVendor( $vendor, $order ) {
		$email_html = '';
		$vendor_id = key( $vendor );
		$products = $vendor[ $vendor_id ]['products'];
		
		// Get vendor information
		$vendor = Mage::getModel( 'admin/user' )->load( $vendor_id );
		
		// Traverse products and build HTML email
		foreach ( $products as $product_id => $qty ) {
			$_product = Mage::getModel( 'catalog/product' )->load( $product_id );
			
			$email_html .= '<tr><td valign="top" style="font-size:12px; padding:7px 9px 9px 9px; border-left:1px solid #EAEAEA; border-bottom:1px solid #EAEAEA;">'. $_product->getName() .' ( '. $_product->getSku() .' )</td>';
			$email_html .= '<td align="center" valign="top" style="font-size:12px; padding:7px 9px 9px 9px; border-left:1px solid #EAEAEA; border-bottom:1px solid #EAEAEA; border-right:1px solid #EAEAEA;">'. number_format( $qty, 2 ) .'</td></tr>';
		}
		
		// Load template and send email
		if ( $email_html != '' ) {
			// Get vendor name and email
			$to_email = $vendor->getEmail();
			$to_name = $vendor->getFirstname() . ' ' . $vendor->getLastname();
			
			// Check if template is set, otherwise use default template
			if ( is_numeric( Mage::getStoreConfig( 'jbmarketplace/jbmarketplace/email_template' ) ) )
				$email_template = Mage::getModel( 'core/email_template' )->load( Mage::getStoreConfig( 'jbmarketplace/jbmarketplace/email_template' ) );
			else
				$email_template = Mage::getModel( 'core/email_template' )->loadDefault( 'junaidbhura_jbmarketplace_email' );
			
			try {
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
			catch( Exception $e ) {}
		}
	}
}
?>