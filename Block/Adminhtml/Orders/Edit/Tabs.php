<?php

class Junaidbhura_Jbmarketplace_Block_Adminhtml_Orders_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('orders_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('jbmarketplace')->__('Order Information'));
  }

  protected function _beforeToHtml()
  {
      // $this->addTab('form_products_section', array(
      //     'label'     => Mage::helper('jbmarketplace')->__('Basic Information'),
      //     'title'     => Mage::helper('jbmarketplace')->__('Basic Information'),
      //     'content'   => $this->getLayout()->createBlock('jbmarketplace/adminhtml_orders_edit_tab_items')->toHtml(),
      // ));    

      $this->addTab('form_section', array(
          'label'     => Mage::helper('jbmarketplace')->__('Order Information'),
          'title'     => Mage::helper('jbmarketplace')->__('Order Information'),
          'content'   => $this->getLayout()->createBlock('jbmarketplace/adminhtml_orders_edit_tab_items')->toHtml().$this->getLayout()->createBlock('jbmarketplace/adminhtml_orders_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}