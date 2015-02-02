<?php
class Junaidbhura_Jbmarketplace_Block_Adminhtml_Messages extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_messages';
    $this->_blockGroup = 'jbmarketplace';
    $this->_headerText = Mage::helper('jbmarketplace')->__('Message Inbox');
    parent::__construct();

    $current_user = Mage::getSingleton( 'admin/session' )->getUser();
    if( $current_user->getRole()->getRoleId() == Mage::getStoreConfig( 'jbmarketplace/jbmarketplace/vendors_role' ) ) {
    	$this->_removeButton('add');
    }
  }
}