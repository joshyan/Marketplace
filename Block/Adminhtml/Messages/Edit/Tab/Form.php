<?php

    class Junaidbhura_Jbmarketplace_Block_Adminhtml_Messages_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
    {
        protected function _prepareForm()
        {
            $form = new Varien_Data_Form();
            $this->setForm($form);

            // vendor information
            $message_data = Mage::registry('message_data')->getData();
            $title = $message_data['title'];
            $content = $message_data['content'];

            // fieldset for vendor basic information
            $fieldset = $form->addFieldset('message_info', array('legend'=>Mage::helper('jbmarketplace')->__('Basic Information')));
            
            $fieldset->addField('title', 'text', array(
              'label'     => Mage::helper('jbmarketplace')->__('Message Title'),
              'text'     => $title,
              'name'     => 'title'
            ));

            $fieldset->addField('content', 'textarea', array(
              'label'     => Mage::helper('jbmarketplace')->__('Message Content'),
              'text'     => $content,
              'name'     => 'content'
            ));


            if ( Mage::registry('message_data') ) {
                $form->setValues(Mage::registry('message_data')->getData());
            }

            return parent::_prepareForm();
        }
}