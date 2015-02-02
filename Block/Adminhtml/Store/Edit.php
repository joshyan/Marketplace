<?php
class Junaidbhura_Jbmarketplace_Block_Adminhtml_Store_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'jbmarketplace';
        $this->_controller = 'adminhtml_store';
        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Save Store'));
        $this->_removeButton('delete');
        $this->_removeButton('back');
    }

    public function getHeaderText()
    {
        return Mage::helper('adminhtml')->__('Manage Store');
    }

}