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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Junaidbhura_Jbmarketplace_Block_Storeprofile extends Mage_Core_Block_Template
{
    
    public $options;
    public $code;
    public $store_url;
    public $store_name;
    public $store_image;
    public $store_description;
    public $store_returnpolicy;

    public function _prepareLayout()
    {
        $store_url = $this->store_url = $this->getRequest()->getParam('name');
        $model = Mage::getModel('jbmarketplace/jbmarketplacevendors')->load($store_url, 'store_url');
        $this->store_name = $model->getStoreName();
        $this->store_description = $model->getStoreDescription();
        $this->store_image = $model->getStoreImage();
        $this->store_returnpolicy = $model->getStoreReturnpolicy();

        // add Home breadcrumb
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {

            $breadcrumbs->addCrumb('home', array(
                'label' => $this->__('Home'),
                'title' => $this->__('Go to Home Page'),
                'link'  => Mage::getBaseUrl()
            ))->addCrumb('store_list', array(
                'label' => 'Store List',
                'title' => 'Store List',
                'link'  => '/store-list'
            ))->addCrumb('store', array(
                'label' => 'Store profile: '.$this->store_name,
                'title' => $this->store_name,
                'link'  => ''
            ));
        }

        return parent::_prepareLayout();
    }




}
