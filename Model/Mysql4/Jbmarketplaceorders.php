<?php
class Junaidbhura_Jbmarketplace_Model_Mysql4_Jbmarketplaceorders extends Mage_Core_Model_Mysql4_Abstract {
	protected function _construct() {
		$this->_init( 'jbmarketplace/jbmarketplaceorders', 'jbmarketplace_order_id' );
	}
}