<?php

class Junaidbhura_Jbmarketplace_Block_Adminhtml_Vendors_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('vendors_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('jbmarketplace')->__('Seller Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('jbmarketplace')->__('Account Info'),
          'title'     => Mage::helper('jbmarketplace')->__('Basic Info'),
          'content'   => $this->getLayout()->createBlock('jbmarketplace/adminhtml_vendors_edit_tab_form')->toHtml(),
      ));
 

       $this->addTab('warehouse', array(
          'label'     => Mage::helper('jbmarketplace')->__('Advance Info'),
          'title'     => Mage::helper('jbmarketplace')->__('Advance Info'),
          'content'   => $this->getLayout()->createBlock('jbmarketplace/adminhtml_vendors_edit_tab_advance')->toHtml(),
       ));

      

      return parent::_beforeToHtml();
  }
}