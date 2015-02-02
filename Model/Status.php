<?php

class Junaidbhura_Jbmarketplace_Model_Status extends Varien_Object
{
    const STATUS_SHIPPED	= 1;
    const STATUS_NOTSHIPPED	= 0;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_SHIPPED    => Mage::helper('jbmarketplace')->__('Shipped'),
            self::STATUS_NOTSHIPPED   => Mage::helper('jbmarketplace')->__('Not Shipped')
        );
    }
}