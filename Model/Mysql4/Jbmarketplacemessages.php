<?php
class Junaidbhura_Jbmarketplace_Model_Mysql4_Jbmarketplacemessages extends Mage_Core_Model_Mysql4_Abstract {
	protected function _construct() {
		$this->_init( 'jbmarketplace/jbmarketplacemessages', 'jbmarketplace_message_id' );
	}
}