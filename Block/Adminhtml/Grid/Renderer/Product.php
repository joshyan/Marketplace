<?php

class Junaidbhura_Jbmarketplace_Block_Adminhtml_Grid_Renderer_Product extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $product_id = $row->getdata('product_id');
        if($product_id==""){
            return "";
        }
        else{
            $product = Mage::getModel('catalog/product')->load($product_id);
            return $product->getName();
        }
    }
}