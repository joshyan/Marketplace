<?php
class Junaidbhura_Jbmarketplace_Block_Adminhtml_Import_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'jbmarketplace';
        $this->_controller = 'adminhtml_import';
        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Save'));
        $this->_removeButton('delete');
        $this->_removeButton('back');

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
        return Mage::helper('adminhtml')->__('Import Products');
    }

    public function getProfileId()
    {
        return Mage::registry('current_convert_profile')->getId();
    }
    
}