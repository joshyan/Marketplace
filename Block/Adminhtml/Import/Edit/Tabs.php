<?php

class Junaidbhura_Jbmarketplace_Block_Adminhtml_Import_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('import_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('jbmarketplace')->__('Import Products'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('upload', array(
          'label'     => Mage::helper('jbmarketplace')->__('Upload File'),
          'title'     => Mage::helper('jbmarketplace')->__('Upload File'),
          'content'   => $this->getLayout()->createBlock('jbmarketplace/adminhtml_import_edit_tab_upload')->toHtml(),
      ));
 

       $this->addTab('run', array(
          'label'     => Mage::helper('jbmarketplace')->__('Run Profile'),
          'title'     => Mage::helper('jbmarketplace')->__('Run Profile'),
          'content'   => $this->getLayout()->createBlock('jbmarketplace/adminhtml_import_edit_tab_run')->toHtml(),
       ));

      return parent::_beforeToHtml();
  }
}