<?php
/**
 * Created by Ebizmarts
 * User: gonzalo@ebizmarts.com
 * Date: 1/18/13
 * Time: 4:34 PM
 */

class Junaidbhura_Jbmarketplace_Block_Adminhtml_Dashboard_Sales extends Mage_Adminhtml_Block_Dashboard_Bar
{
    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('jbmarketplace/dashboard/salebar.phtml');


    }

    /**
     * @return Ebizmarts_AbandonedCart_Block_Adminhtml_Dashboard_Sales|Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        if (!Mage::helper('core')->isModuleEnabled('Mage_Reports')) {
            return $this;
        }
        $isFilter = $this->getRequest()->getParam('store') || $this->getRequest()->getParam('website') || $this->getRequest()->getParam('group');

        $current_user = Mage::getSingleton( 'admin/session' )->getUser();
        $sales = Mage::getModel('jbmarketplace/jbmarketplaceorders')->getCollection();
        if($current_user->getRole()->getRoleId() == Mage::getStoreConfig( 'jbmarketplace/jbmarketplace/vendors_role' )) {
	        $user_id = $current_user->getUserId();
	        $sales->getSelect()->where('vendor_id = ?', $user_id)->where('status = ?', 1);  
            $count = $sales->getSize();
	    }

        $salesTotal = Mage::getModel('jbmarketplace/jbmarketplaceorders')->getCollection();
        if($current_user->getRole()->getRoleId() == Mage::getStoreConfig( 'jbmarketplace/jbmarketplace/vendors_role' )) {
            $user_id = $current_user->getUserId();
            $salesTotal->getSelect()->where('vendor_id = ?', $user_id)->where('status = ?', 1);  
        }

        $salesTotal->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns('SUM(sales) AS salestotalsum');
	    
		$result = $salesTotal->getFirstItem()->getData();

        $this->addTotal($this->__('Lifetime Sales'), $result['salestotalsum']);
        $this->addTotal($this->__('Average Sales'), $result['salestotalsum'] / $count);
    }

}