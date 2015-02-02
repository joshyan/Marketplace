<?php

    class Junaidbhura_Jbmarketplace_Block_Adminhtml_Orders_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
    {
        protected function _prepareForm()
        {
            $form = new Varien_Data_Form();
            $this->setForm($form);
            // order information
            $order_data = Mage::registry('order_data')->getData();
            $order_id = $order_data['order_id'];
            $order = Mage::getModel('sales/order')->load($order_id);
            $order_number = $order_data['increment_id'] ? $order_data['increment_id'] : $order->getIncrementId();
            //$order_number = $order->getIncrementId();

            $order_billing_address = $order->getBillingAddress()->getFormated(true);
            $order_shipping_address = $order->getShippingAddress()->getFormated(true);
            $order_shipping_method = $order->getShippingDescription();

            // fieldset for shipping information
            $fieldset_shipping = $form->addFieldset('order_shippinginfo', array('legend'=>Mage::helper('jbmarketplace')->__('Shipping Information')));
            
            $fieldset_shipping->addField('shipping_method', 'note', array(
              'label'     => Mage::helper('jbmarketplace')->__('Shipping Method'),
              'text'     => $order_shipping_method.'<br>'.$order_data['shipping_method'],
            ));

            // $fieldset_shipping->addField('billing_address', 'note', array(
            //   'label'     => Mage::helper('jbmarketplace')->__('Billing Address'),
            //   'text'     => $order_billing_address,
            // ));
            
            $fieldset_shipping->addField('shipping_address', 'note', array(
              'label'     => Mage::helper('jbmarketplace')->__('Shipping Address'),
              'text'     => $order_shipping_address,
            ));

            // //fieldset for order information
            // $fieldset_order = $form->addFieldset('order_orderinfo', array('legend'=>Mage::helper('jbmarketplace')->__('Order Details')));

            // $fieldset_order->addField('order_number', 'note', array(
            //   'label'     => Mage::helper('jbmarketplace')->__('Order #'),
            //   'text'     => $order_number,
            // ));

            // $fieldset_order->addField('gross_sales', 'note', array(
            //   'label'     => Mage::helper('jbmarketplace')->__('Gross Sales'),
            //   'text'     => Mage::helper('core')->currency($order_data['gross_sales']),
            // ));

            // $fieldset_order->addField('shipping_fee', 'note', array(
            //   'label'     => Mage::helper('jbmarketplace')->__('Shipping Fee'),
            //   'text'     => Mage::helper('core')->currency($order_data['shipping_fee']),
            // ));

            // $fieldset_order->addField('tax', 'note', array(
            //   'label'     => Mage::helper('jbmarketplace')->__('Tax'),
            //   'text'     => Mage::helper('core')->currency($order_data['tax']),
            // ));

            // $fieldset_order->addField('sales', 'note', array(
            //   'label'     => Mage::helper('jbmarketplace')->__('Sales'),
            //   'text'     => Mage::helper('core')->currency($order_data['sales']),
            // ));

            // $fieldset_order->addField('income', 'note', array(
            //   'label'     => Mage::helper('jbmarketplace')->__('Income'),
            //   'text'     => Mage::helper('core')->currency($order_data['income']),
            // ));

            // $fieldset_order->addField('order_dtime', 'note', array(
            //   'label'     => Mage::helper('jbmarketplace')->__('Order Date'),
            //   'text'     => Mage::helper('core')->formatDate($order_data['order_dtime'], 'medium', true),
            // ));


            // fieldset for status information
            $fieldset_status = $form->addFieldset('order_status', array('legend'=>Mage::helper('jbmarketplace')->__('Order Product Shipped?')));

            if($order_data['status'] == 1) {
	            $fieldset_status->addField('status', 'note', array(
	              'label'     => Mage::helper('jbmarketplace')->__('Order Product Shipped?'),
	              'text'     => "Shipped",
	            ));
            } else {
	            $fieldset_status->addField('status', 'select', array(
	                    'label'     => Mage::helper('jbmarketplace')->__('Order Product Shipped?'),
	                    'name'      => 'status',
	                    'required'  => true,
	                    'values'    => array(
	                        array(
	                            'value'     => 0,
	                            'label'     => Mage::helper('jbmarketplace')->__('Not Shipped'),
	                        ),

	                        array(
	                            'value'     => 1,
	                            'label'     => Mage::helper('jbmarketplace')->__('Shipped'),
	                        ),
	                    ),
	            ));
        	}

            $fieldset_status->addField('tracking_code', 'text', array(
              'label'     => Mage::helper('jbmarketplace')->__('Tracking Code'),
              'name'      => 'tracking_code',
              'text'      => $order_data['tracking_code'],
            ));

            $fieldset_status->addField('shipping_carrier', 'text', array(
              'label'     => Mage::helper('jbmarketplace')->__('Shipping Carrier'),
              'name'      => 'shipping_carrier',
              'text'      => $order_data['shipping_carrier'],
            ));

            $fieldset_status->addField('message', 'textarea', array(
              'label'     => Mage::helper('jbmarketplace')->__('Message'),
              'name'      => 'message',
              'text'      => $order_data['message'],
            ));


            if ( Mage::registry('order_data') ) {
                $form->setValues(Mage::registry('order_data')->getData());
            }

            return parent::_prepareForm();
        }
        
}