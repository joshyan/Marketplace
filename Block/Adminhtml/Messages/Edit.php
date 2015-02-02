<?php

class Junaidbhura_Jbmarketplace_Block_Adminhtml_Messages_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'jbmarketplace';
        $this->_controller = 'adminhtml_messages';
        
        if(Mage::helper('jbmarketplace')->isAdmin()) {
            $this->_updateButton('save', 'label', Mage::helper('jbmarketplace')->__('Save Message'));
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

        if(Mage::helper('jbmarketplace')->isVendor()) {
            $this->_removeButton('save');
            $this->_removeButton('delete');
        }

    }

    public function getHeaderText()
    {
        return Mage::helper('jbmarketplace')->__("Edit Message");
    }

}