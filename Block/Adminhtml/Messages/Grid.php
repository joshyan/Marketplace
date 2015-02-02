<?php

class Junaidbhura_Jbmarketplace_Block_Adminhtml_Messages_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('jbmarketplaceGrid');
      $this->setDefaultSort('jbmarketplace_message_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('jbmarketplace/jbmarketplacemessages')->getCollection();
      
      $this->setCollection($collection);

      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('jbmarketplace_message_id', array(
          'header'    => Mage::helper('jbmarketplace')->__('ID'),
          'width'     => 5,
          'align'     => 'right',
          'sortable'  => true,
          'index'     => 'jbmarketplace_message_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('jbmarketplace')->__('Title'),
          'index'     =>'title',
      ));

      $this->addColumn('create_dtime', array(
          'header'    => Mage::helper('jbmarketplace')->__('Created'),
          'index'     =>'create_dtime',
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
		

      return parent::_prepareColumns();
  }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}