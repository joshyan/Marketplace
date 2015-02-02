<?php
class Junaidbhura_Jbmarketplace_Block_Adminhtml_Vendors extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_vendors';
    $this->_blockGroup = 'jbmarketplace';
    $this->_headerText = Mage::helper('jbmarketplace')->__('Manage Seller');
    parent::__construct();
    $this->_removeButton('add');
  }
}