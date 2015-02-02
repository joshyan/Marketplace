<?php

class Junaidbhura_Jbmarketplace_Block_Adminhtml_Messages_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('messages_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('jbmarketplace')->__('Message Info'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('jbmarketplace')->__('Message Info'),
          'title'     => Mage::helper('jbmarketplace')->__('Message Info'),
          'content'   => $this->getLayout()->createBlock('jbmarketplace/adminhtml_messages_edit_tab_form')->toHtml(),
      ));
 
      return parent::_beforeToHtml();
  }
}