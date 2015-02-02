<?php

class Junaidbhura_Jbmarketplace_Block_Adminhtml_Grid_Renderer_Order extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $order_id = $row->getdata('order_id');
        if($order_id==""){
            return "";
        } else{
            $order = Mage::getModel('sales/order')->load($order_id);
            return $order->getIncrementId();
        }
    }
}