<?php

class Junaidbhura_Jbmarketplace_Block_Adminhtml_Grid_Renderer_Shippingname extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $order_id = $row->getdata('order_id');
        $order = Mage::getModel('sales/order')->load($order_id);
        $shippingId = $order->getShippingAddress()->getId();
        $shippinginfo = Mage::getModel('sales/order_address')->load($shippingId);
        
        $shippingFirstName = $shippinginfo->getData('firstname');
        $shippingLastName = $shippinginfo->getData('lastname');

        return $shippingFirstName.' '.$shippingLastName;

    }
}