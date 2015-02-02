<?php

    class Junaidbhura_Jbmarketplace_Block_Adminhtml_Vendors_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
    {
        protected function _prepareForm()
        {
            $form = new Varien_Data_Form();
            $this->setForm($form);

            // vendor information
            $vendor_data = Mage::registry('vendor_data')->getData();
            $vendor_id = $vendor_data['vendor_id'];
            $user_name = $vendor_data['user_name'];
            $first_name = $vendor_data['first_name'];
            $last_name = $vendor_data['last_name'];
            $email = $vendor_data['email'];
            $commission = $vendor_data['commission'];

            // fieldset for vendor basic information
            $fieldset = $form->addFieldset('vendor_info', array('legend'=>Mage::helper('jbmarketplace')->__('Basic Information')));
            
            $fieldset->addField('vendor_id', 'note', array(
              'label'     => Mage::helper('jbmarketplace')->__('User ID'),
              'text'     => $vendor_id,
            ));

            $fieldset->addField('user_id', 'hidden', array(
              'name'     => 'user_id',
              'label'     => Mage::helper('jbmarketplace')->__('User ID'),
              'value'     => $vendor_id,
            ));

            $fieldset->addField('user_name', 'note', array(
              'label'     => Mage::helper('jbmarketplace')->__('User Name'),
              'text'     => $user_name,
              'name'     => 'user_name'
            ));

            $fieldset->addField('first_name', 'note', array(
              'label'     => Mage::helper('jbmarketplace')->__('First Name'),
              'text'     => $first_name,
              'name'     => 'first_name'
            ));

            $fieldset->addField('last_name', 'note', array(
              'label'     => Mage::helper('jbmarketplace')->__('Last Name'),
              'text'     => $last_name,
              'name'     => 'last_name'
            ));

            $fieldset->addField('email', 'note', array(
              'label'     => Mage::helper('jbmarketplace')->__('Email'),
              'text'     => $email,
              'name'     => 'email'
            ));

            // fieldset for vendor basic information
            $fieldset_store = $form->addFieldset('vendor_store_info', array('legend'=>Mage::helper('jbmarketplace')->__('Store Information')));
            
            $fieldset_store->addField('store_name', 'text', array(
              'label'     => Mage::helper('jbmarketplace')->__('Store Name'),
              'text'     => $vendor_data['store_name'],
              'name'     => 'store_name'
            ));

            $fieldset_store->addField('store_url', 'text', array(
              'label'     => Mage::helper('jbmarketplace')->__('Store Url'),
              'text'     => $vendor_data['store_url'],
              'name'     => 'store_url'
            ));

            $fieldset_store->addField('store_description', 'textarea', array(
              'label'     => Mage::helper('jbmarketplace')->__('Store Description'),
              'text'     => $vendor_data['store_description'],
              'name'     => 'store_description'
            ));

            // Only admin can set commission
            if( Mage::helper('jbmarketplace')->isAdmin() ) {

                $fieldset_store->addField('commission', 'text', array(
                  'label'     => Mage::helper('jbmarketplace')->__('Commission'),
                  'text'     => $commission,
                  'name'     => 'commission'
                ));
          
            } else {

                $fieldset_store->addField('commission', 'note', array(
                  'label'     => Mage::helper('jbmarketplace')->__('Commission'),
                  'text'     => $commission
                ));

            }

            $fieldset_shipping = $form->addFieldset('shipping_fieldset', array('legend'=>Mage::helper('jbmarketplace')->__('Shipping & Return Policy')));

            $fieldset_shipping->addField('store_shippingpolicy', 'editor', array(
                    'name'  => 'store_shippingpolicy',
                    'label' => Mage::helper('jbmarketplace')->__('Shipping Policy'),
                    'title' => Mage::helper('jbmarketplace')->__('Shipping Policy'),
                    'style' => 'width:1000px; height:500px;',
                    'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
                    'required' => false,
                    'wysiwyg' => true,
                )
            );


            $fieldset_shipping->addField('store_returnpolicy', 'editor', array(
                    'name'  => 'store_returnpolicy',
                    'label' => Mage::helper('jbmarketplace')->__('Return Policy'),
                    'title' => Mage::helper('jbmarketplace')->__('Return Policy'),
                    'style' => 'width:1000px; height:500px;',
                    'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
                    'required' => false,
                    'wysiwyg' => true,
                )
            );


            if ( Mage::registry('vendor_data') ) {
                $form->setValues(Mage::registry('vendor_data')->getData());
            }

            return parent::_prepareForm();
        }


        protected function _prepareLayout() {
            parent::_prepareLayout();
            if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
                $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
            }
        }


}