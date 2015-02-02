<?php

class Junaidbhura_Jbmarketplace_Block_Adminhtml_Grid_Renderer_Billingname extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $order_id = $row->getdata('order_id');
        $order = Mage::getModel('sales/order')->load($order_id);
        $billingId = $order->getBillingAddress()->getId();
        $billinginfo = Mage::getModel('sales/order_address')->load($billingId);
        
        $billingFirstName = $billinginfo->getData('firstname');
        $billingLastName = $billinginfo->getData('lastname');

        return $billingFirstName.' '.$billingLastName;

    }
}