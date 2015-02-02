<?php

class Junaidbhura_Jbmarketplace_Block_Adminhtml_Orders_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('jbmarketplaceGrid');
      $this->setDefaultSort('real_order_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('jbmarketplace/jbmarketplaceorders')->getCollection();
      $this->setCollection($collection);

      $current_user = Mage::getSingleton( 'admin/session' )->getUser();
      // Get order collection by this user
      if($current_user->getRole()->getRoleId() == Mage::getStoreConfig( 'jbmarketplace/jbmarketplace/vendors_role' )) {
        $user_id = $current_user->getUserId();
        $collection->getSelect()->where('vendor_id = ?', $user_id);        
      }
      //$collection->getSelect()->join( array('eaov'=>Mage::getSingleton('core/resource')->getTableName('eav/attribute_option_value')), 'main_table.brand_name = eaov.option_id and store_id=0', array('brand_name'=>'eaov.value'));
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      // $this->addColumn('jbmarketplace_order_id', array(
      //     'header'    => Mage::helper('jbmarketplace')->__('ID'),
      //     'width'     => '40px',
      //     'type'      => 'number',
      //     'index'     => 'jbmarketplace_order_id',
      // ));
      
        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
        ));

      $this->addColumn('order_dtime', array(
          'header'    => Mage::helper('jbmarketplace')->__('Purchased On'),
          'index'     =>'order_dtime',
          'type'      => 'datetime',
          'width' => '100px',
      ));

      // $this->addColumn('product_id', array(
      //     'header'    => Mage::helper('jbmarketplace')->__('Product Name'),
      //     'align'     =>'left',
      //     'index'     =>'product_id',
      //     'type'      =>'text',
      //     'renderer'  => 'jbmarketplace/adminhtml_grid_renderer_product'
      // ));

      // $this->addColumn('order_qty', array(
      //     'header'    => Mage::helper('jbmarketplace')->__('Ordered Qty'),
      //     'align'     =>'left',
      //     'index'     =>'order_qty',
      //     'type'      => 'number',
      // ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
            'renderer'  => 'jbmarketplace/adminhtml_grid_renderer_billingname'
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
            'renderer'  => 'jbmarketplace/adminhtml_grid_renderer_shippingname'
        ));

      $this->addColumn('sales', array(
          'header'    => Mage::helper('jbmarketplace')->__('Sales'),
          'align'     =>'right',
          'type'      => 'currency',
          'renderer'  => 'jbmarketplace/adminhtml_grid_renderer_sales'
      ));

      $this->addColumn('income', array(
          'header'    => Mage::helper('jbmarketplace')->__('Income'),
          'align'     =>'right',
          'type'      => 'currency',
          'renderer'  => 'jbmarketplace/adminhtml_grid_renderer_income'
      ));

      $this->addColumn('status', array(
          'header'    => Mage::helper('jbmarketplace')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Shipped',
              0 => 'Not Shipped',
          ),
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
		
		  $this->addExportType('*/*/exportCsv', Mage::helper('jbmarketplace')->__('CSV'));
		  $this->addExportType('*/*/exportXml', Mage::helper('jbmarketplace')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('jbmarketplace_order_id');
        $this->getMassactionBlock()->setFormFieldName('order_id');

        $statuses = Mage::getSingleton('jbmarketplace/status')->getOptionArray();

        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('jbmarketplace')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('jbmarketplace')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}