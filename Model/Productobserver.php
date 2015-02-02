<?php
/**
 * Product Collection Observer Model
 *
 * @category    Model
 * @package     Junaidbhura_Jbmarketplace
 * @author      Junaid Bhura <info@junaidbhura.com>
 */

class Junaidbhura_Jbmarketplace_Model_Productobserver {
	/**
	* Function to limit products based on users
	*/
	public function limitUsers( $observer ) {
		// Get current logged in user
		$current_user = Mage::getSingleton( 'admin/session' )->getUser();
		
		// Limit only for vendors
		if ( $current_user->getRole()->getRoleId() == Mage::getStoreConfig( 'jbmarketplace/jbmarketplace/vendors_role' ) ) {

			// Get collection
			$event = $observer->getEvent();
			$collection = $event->getCollection();
			
			// Get product collection by this user
			$my_products = Mage::getModel( 'jbmarketplace/jbmarketplaceproducts' )
				->getCollection()
				->addFieldToSelect( 'product_id' )
				->addFieldToFilter( 'user_id', $current_user->getUserId() )
				->load();
			
			$my_product_array = array();
			foreach ( $my_products as $product ) {
				$my_product_array[] = $product->getProductId();
			}
			
			if ( count( $my_product_array ) == 0 )
				$my_product_array[] = -1;
			
			// Limit collection based on current user's products
			$collection->addAttributeToFilter( 'entity_id', array(
				'in' => array( $my_product_array )
			) );
			
			return $this;
		
		}
	}
	
	/**
	* Function to assign a product ID to a user
	*/
	public function newProduct( $observer ) {
		//Mage::log('Controller name: '.Mage::app()->getRequest()->getControllerName());
		
		$current_user = Mage::getSingleton( 'admin/session' )->getUser();
		// Assign only for vendor
		if ( Mage::helper('jbmarketplace')->isVendor() ) {
			// Get the new product
			$product = $observer->getEvent()->getProduct();
			
			// Check if the product is aready assigned to this user (not a new product)
			$my_product = Mage::getModel( 'jbmarketplace/jbmarketplaceproducts' )
					->getCollection()
					->addFieldToSelect( 'product_id' )
					->addFieldToFilter( 'product_id', $product->getEntityId() )
					->addFieldToFilter( 'user_id', $current_user->getUserId() )
					->load();
			// Mage::log('Size:'.$my_product->getSize());
			// Product does not exist for user, save it
			if ( $my_product->getSize() == 0 ) {
				// Assign product to user
				$now = Mage::getModel('core/date')->timestamp( time() );
				Mage::getModel( 'jbmarketplace/jbmarketplaceproducts' )
					->setProductId( $product->getEntityId() )
					->setUserId( $current_user->getUserId() )
					->setJbmarketplaceProductsDtime( date( 'Y-m-d H:i:s', $now ))
					->save();
			}

		}
		
	}
	
	public function setWarehouse( $observer ) {
		//Mage::log('Controller name: '.Mage::app()->getRequest()->getControllerName());

		// Assign only for vendor
		if ( Mage::helper('jbmarketplace')->isVendor() ) {
			// Get the new product
			$product = $observer->getEvent()->getProduct();			

			$current_user = Mage::getSingleton( 'admin/session' )->getUser();
			// assign warehouse to the product by current vendor
			$vendor_id = $current_user->getUserId();
			$warehouse_id = Mage::getModel( 'jbmarketplace/jbmarketplacevendors' )->load($vendor_id, 'vendor_id')->getWarehouseId();

			if($warehouse_id && $product->getWarehouse() != $warehouse_id) {
				$product->setWarehouse($warehouse_id);
			}

		}

	}

	/**
	* Function to check if a user has permission to edit a product
	*/
	public function editProduct( $observer ) {
		// Get current user
		$current_user = Mage::getSingleton( 'admin/session' )->getUser();
		
		// Check only for vendors
		if ( $current_user->getRole()->getRoleId() == Mage::getStoreConfig( 'jbmarketplace/jbmarketplace/vendors_role' ) ) {
			// Get the product
			$product = $observer->getEvent()->getProduct();
			
			// Check if the product is assigned to this user
			$my_product = Mage::getModel( 'jbmarketplace/jbmarketplaceproducts' )
					->getCollection()
					->addFieldToSelect( 'product_id' )
					->addFieldToFilter( 'product_id', $product->getEntityId() )
					->addFieldToFilter( 'user_id', $current_user->getUserId() )
					->load();
			
			// Check if product not found, if so redirect back
			if ( $my_product->getSize() == 0 )
				Mage::app()->getResponse()->setRedirect( Mage::helper( 'adminhtml' )->getUrl(  '*/catalog_product/index' ) );
		}
	}
	
	/**
	* Function to remove a product's ID from a user
	*/
	public function deleteProduct( $observer ) {
		// Get the product
		$product = $observer->getEvent()->getProduct();
		
		// Get current user
		$current_user = Mage::getSingleton( 'admin/session' )->getUser();
		
		// Check if the product is assigned to this user
		$my_product = Mage::getModel( 'jbmarketplace/jbmarketplaceproducts' )
				->getCollection()
				->addFieldToSelect( '*' )
				->addFieldToFilter( 'product_id', $product->getEntityId() )
				->addFieldToFilter( 'user_id', $current_user->getUserId() )
				->load();
		
		// Check if product found, if so, delete its entry
		if ( $my_product->getSize() > 0 ) {
			foreach ( $my_product as $_product ) {
				Mage::getModel( 'jbmarketplace/jbmarketplaceproducts' )
					->setJbmarketplaceProductId( $_product->getJbmarketplaceProductId() )
					->delete();
			}
		}
	}
}
?>