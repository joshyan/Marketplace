<?php

class Junaidbhura_Jbmarketplace_Block_Adminhtml_Grid_Renderer_Income extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $income = $row->getdata('income');
        if($income==""){
            return "";
        } else{
            return Mage::helper('core')->currency($income);
        }
    }
}