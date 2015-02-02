<?php

class Junaidbhura_Jbmarketplace_Block_Adminhtml_Vendors_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('jbmarketplaceGrid');
      $this->setDefaultSort('jbmarketplace_vendor_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function getCurrentVendor() {
    $current_user = Mage::getSingleton( 'admin/session' )->getUser();
    if( $current_user->getRole()->getRoleId() == Mage::getStoreConfig( 'jbmarketplace/jbmarketplace/vendors_role' ) ) {
      return $current_user->getUserId();
    } else {
      return false;
    }  
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('jbmarketplace/jbmarketplacevendors')->getCollection();
      
      $this->setCollection($collection);

      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      if( !$this->getCurrentVendor() ) {

        $this->addColumn('jbmarketplace_vendor_id', array(
            'header'    => Mage::helper('jbmarketplace')->__('ID'),
            'width'     => 5,
            'align'     => 'right',
            'sortable'  => true,
            'index'     => 'jbmarketplace_vendor_id',
        ));

      }

      $this->addColumn('vendor_id', array(
          'header'    => Mage::helper('jbmarketplace')->__('User ID'),
          'width'     => 5,
          'align'     => 'right',
          'sortable'  => true,
          'index'     => 'vendor_id',
      ));

      $this->addColumn('user_name', array(
          'header'    => Mage::helper('jbmarketplace')->__('User Name'),
          'index'     =>'user_name',
      ));

      $this->addColumn('first_name', array(
          'header'    => Mage::helper('jbmarketplace')->__('First Name'),
          'index'     =>'first_name',
      ));

      $this->addColumn('last_name', array(
          'header'    => Mage::helper('jbmarketplace')->__('Last Name'),
          'index'     =>'last_name',
      ));

      $this->addColumn('email', array(
          'header'    => Mage::helper('jbmarketplace')->__('Email'),
          'index'     =>'email',
      ));

      $this->addColumn('commission', array(
          'header'    => Mage::helper('jbmarketplace')->__('Commission'),
          'align'     =>'left',
          'index'     =>'commission',
          'type'      => 'text',
          'width'     => '100px',
      ));

      $this->addColumn('action',
          array(
              'header'    =>  Mage::helper('jbmarketplace')->__('Action'),
              'width'     => '100',
              'type'      => 'action',
              'getter'    => 'getId',
              'actions'   => array(
                  array(
                      'caption'   => Mage::helper('jbmarketplace')->__('Edit'),
                      'url'       => array('base'=> '*/*/edit'),
                      'field'     => 'id'
                  )
              ),
              'filter'    => false,
              'sortable'  => false,
              'index'     => 'stores',
              'is_system' => true,
      ));
		
      if( !$this->getCurrentVendor() ) {
		    $this->addExportType('*/*/exportCsv', Mage::helper('jbmarketplace')->__('CSV'));
		    $this->addExportType('*/*/exportXml', Mage::helper('jbmarketplace')->__('XML'));
	    }

      return parent::_prepareColumns();
  }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}