<?php
class Junaidbhura_Jbmarketplace_Model_Mysql4_Jbmarketplaceproducts extends Mage_Core_Model_Mysql4_Abstract {
	protected function _construct() {
		$this->_init( 'jbmarketplace/jbmarketplaceproducts', 'jbmarketplace_product_id' );
	}
}