<?php

class Junaidbhura_Jbmarketplace_Block_Adminhtml_Grid_Renderer_Sales extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $sales = $row->getdata('sales');
        if($sales==""){
            return "";
        } else{
            return Mage::helper('core')->currency($sales);
        }
    }
}