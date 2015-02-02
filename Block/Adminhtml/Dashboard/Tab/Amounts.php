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
 * Adminhtml dashboard order amounts diagram
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Junaidbhura_Jbmarketplace_Block_Adminhtml_Dashboard_Tab_Amounts extends Mage_Adminhtml_Block_Template
{
    /**
     * Initialize object
     *
     * @return void
     */
    public function __construct()
    {
        $this->setHtmlId('amounts');
        parent::__construct();
        $this->setTemplate('jbmarketplace/dashboard/tab/amounts.phtml');
    }

    protected  function _prepareLayout()
    {
        $json_data = json_encode( $this->helper('jbmarketplace')->formatAmountsData( '7d', $this->helper('jbmarketplace')->getDataByRange('7d') ) );
        $this->setData('data', $json_data);

        parent::_prepareLayout();
    }

}

