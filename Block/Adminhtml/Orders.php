<?php
class Junaidbhura_Jbmarketplace_Block_Adminhtml_Orders extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_orders';
    $this->_blockGroup = 'jbmarketplace';
    $this->_headerText = Mage::helper('jbmarketplace')->__('Manage Vendor Orders');
    parent::__construct();
    $this->_removeButton('add');
  }
}