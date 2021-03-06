<?php

class Junaidbhura_Jbmarketplace_Block_Adminhtml_Orders_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'jbmarketplace';
        $this->_controller = 'adminhtml_orders';
        $this->_mode        = 'edit';
        parent::__construct();
        
        $this->_updateButton('save', 'label', Mage::helper('jbmarketplace')->__('Save Order'));
        $this->_removeButton('delete');
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
             function saveAndContinueEdit(){
                editForm.submit($('edit_form').action + 'back/edit/');
             }";

    }

    public function getHeaderText()
    {
        return Mage::helper('jbmarketplace')->__("Edit Order"); //'%s'", $this->htmlEscape(Mage::registry('manufacturer_data')->getMenufecturerName()));
    }
}