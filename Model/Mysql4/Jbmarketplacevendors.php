<?php
class Junaidbhura_Jbmarketplace_Model_Mysql4_Jbmarketplacevendors extends Mage_Core_Model_Mysql4_Abstract {
	protected function _construct() {
		$this->_init( 'jbmarketplace/jbmarketplacevendors', 'jbmarketplace_vendor_id' );
	}
}