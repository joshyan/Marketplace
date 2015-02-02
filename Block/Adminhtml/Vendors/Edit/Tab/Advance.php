<?php

    class Junaidbhura_Jbmarketplace_Block_Adminhtml_Vendors_Edit_Tab_Advance extends Mage_Adminhtml_Block_Widget_Form
    {
        protected function _prepareForm()
        {
            $form = new Varien_Data_Form();
            $this->setForm($form);

            // vendor information
            $vendor_data = Mage::registry('vendor_data')->getData();
            $vendor_id = $vendor_data['vendor_id'];

            $warehouse_options = Mage::getModel('dropcommon/dropship')->getOptionArray();

            $assigned_warehouse_ids = Mage::getModel('jbmarketplace/jbmarketplacevendors')
                    ->getCollection()
                    ->addFieldToSelect( 'warehouse_id' )
                    ->addFieldToFilter( 'vendor_id', array('neq' => $vendor_id) )
                    ->load();
            if($assigned_warehouse_ids->count() > 0) {
                foreach($assigned_warehouse_ids as $value) {
                    $warehouse_id = $value->getWarehouseId();
                    unset($warehouse_options[$warehouse_id]);
                }
            }

            // fieldset for vendor information
            $fieldset = $form->addFieldset('warehouse_info', array('legend'=>Mage::helper('jbmarketplace')->__('Vendor Information')));
            
            $fieldset->addField('warehouse_id', 'select', array(
                'label'     => Mage::helper('jbmarketplace')->__('Warehouse'),
                'name'      => 'warehouse_id',
                'required'  => true,
                'values'    => $warehouse_options
            ));


            if ( Mage::registry('vendor_data') ) {
                $form->setValues(Mage::registry('vendor_data')->getData());
            }

            return parent::_prepareForm();
        }
}