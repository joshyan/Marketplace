<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml edit admin user account form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Junaidbhura_Jbmarketplace_Block_Adminhtml_Store_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $userId = Mage::getSingleton('admin/session')->getUser()->getId();
        $vendor = Mage::getModel('jbmarketplace/jbmarketplacevendors')
            ->load($userId, 'vendor_id');
        //$user->unsetData('password');
        $data = $vendor->getData();

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('jbmarketplace')->__('Store Information')));

        $fieldset->addField('jbmarketplace_vendor_id', 'hidden', array(
                'name'  => 'jbmarketplace_vendor_id',
                'label' => Mage::helper('jbmarketplace')->__('Jbmarketplace Vendor Id'),
                'title' => Mage::helper('jbmarketplace')->__('Jbmarketplace Vendor Id'),
            )
        );

        $fieldset->addField('store_name', 'text', array(
                'name'  => 'store_name',
                'label' => Mage::helper('jbmarketplace')->__('Store Name'),
                'title' => Mage::helper('jbmarketplace')->__('Store Name'),
                'required' => true,
            )
        );

        $fieldset->addField('store_url', 'text', array(
                'name'  => 'store_url',
                'label' => Mage::helper('jbmarketplace')->__('Store Url'),
                'title' => Mage::helper('jbmarketplace')->__('Store Url'),
                'required' => true,
            )
        );

        $fieldset->addField('store_description', 'textarea', array(
                'name'  => 'store_description',
                'label' => Mage::helper('jbmarketplace')->__('Store Description'),
                'title' => Mage::helper('jbmarketplace')->__('Store Description'),
                'required' => false,
            )
        );

        if (isset($data['store_logo']) && $data['store_logo']) {
            $imageName = Mage::helper('jbmarketplace')->reImageName($data['store_logo']);
            $data['store_logo'] = 'store' . '/' . $imageName;
        }

        $fieldset->addField('store_logo', 'image', array(
            'label' => Mage::helper('jbmarketplace')->__('Store Logo'),
            'required' => false,
            'name' => 'store_logo',
            'note' => '
                This small image will be used throughout the site
                Select an image file file on your computer (2MB max)
                (512px X 512px recommended size)'
        ));

        if (isset($data['store_image']) && $data['store_image']) {
            $imageName = Mage::helper('jbmarketplace')->reImageName($data['store_image']);
            $data['store_image'] = 'store' . '/' . $imageName;
        }
        
        $fieldset->addField('store_image', 'image', array(
            'label' => Mage::helper('jbmarketplace')->__('Store Page Image'),
            'required' => false,
            'name' => 'store_image',
            'note' => '
                This image will be featured on your store page
                Select an image file file on your computer (2MB max)
                (773px X 200px recommended size)'
        ));

        $fieldset_social = $form->addFieldset('social_fieldset', array('legend'=>Mage::helper('jbmarketplace')->__('Social Links')));

        $fieldset_social->addField('store_twitter', 'text', array(
                'name'  => 'store_twitter',
                'label' => Mage::helper('jbmarketplace')->__('Twitter Url'),
                'title' => Mage::helper('jbmarketplace')->__('Twitter Url'),
                'required' => false,
            )
        );

        $fieldset_social->addField('store_facebook', 'text', array(
                'name'  => 'store_facebook',
                'label' => Mage::helper('jbmarketplace')->__('Facebook Url'),
                'title' => Mage::helper('jbmarketplace')->__('Facebook Url'),
                'required' => false,
            )
        );

        $fieldset_social->addField('store_pinterest', 'text', array(
                'name'  => 'store_pinterest',
                'label' => Mage::helper('jbmarketplace')->__('Pinterest Url'),
                'title' => Mage::helper('jbmarketplace')->__('Pinterest Url'),
                'required' => false,
            )
        );

        $fieldset_social->addField('store_googleplus', 'text', array(
                'name'  => 'store_googleplus',
                'label' => Mage::helper('jbmarketplace')->__('Google+ Url'),
                'title' => Mage::helper('jbmarketplace')->__('Google+ Url'),
                'required' => false,
            )
        );

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

        $form->setValues($data);
        $form->setAction($this->getUrl('*/adminhtml_store/save'));
        $form->setMethod('post');
        $form->setEnctype('multipart/form-data');
        $form->setUseContainer(true);
        $form->setId('edit_form');

        $this->setForm($form);

        return parent::_prepareForm();
    }


    protected function _prepareLayout() {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

}
