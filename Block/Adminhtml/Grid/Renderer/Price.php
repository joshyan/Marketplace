<?php

class Junaidbhura_Jbmarketplace_Block_Adminhtml_Grid_Renderer_Price extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $order_gt = $row->getdata('order_gt');
        if($order_gt==""){
            return "";
        } else{
            return Mage::helper('core')->currency($order_gt);
        }
    }
}