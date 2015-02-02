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


/**
 * Product list toolbar
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Junaidbhura_Jbmarketplace_Block_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
   
    /**
     * Return current URL with rewrites and additional parameters
     *
     * @param array $params Query parameters
     * @return string
     */
    public function getPagerUrl($params=array())
    {
        if(Mage::app()->getFrontController()->getRequest()->getModuleName() == 'marketplace') {
            $urlParams = array();
            $urlParams['_current']  = false;
            $urlParams['_escape']   = true;
            $urlParams['_use_rewrite']   = false;
            $urlParams['_query']    = $params;

            $shopname = $this->getRequest()->getParam('name');
            return $this->getUrl('shops/'.$shopname, $urlParams);
        } else {
            $urlParams = array();
            $urlParams['_current']  = true;
            $urlParams['_escape']   = true;
            $urlParams['_use_rewrite']   = true;
            $urlParams['_query']    = $params;

            return $this->getUrl('*/*/*', $urlParams);
        }


    }

    /**
     * Retrive URL for view mode
     *
     * @param string $mode
     * @return string
     */
    public function getModeUrl($mode)
    {
        return $this->getPagerUrl( array($this->getModeVarName()=>$mode, $this->getPageVarName() => null) );
    }

}
