<?php
/**
 * Created by Ebizmarts
 * User: gonzalo@ebizmarts.com
 * Date: 1/18/13
 * Time: 4:34 PM
 */

class Junaidbhura_Jbmarketplace_Block_Adminhtml_Dashboard_Income extends Mage_Adminhtml_Block_Dashboard_Bar
{
    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('jbmarketplace/dashboard/incomebar.phtml');
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

        $income = Mage::getModel('jbmarketplace/jbmarketplaceorders')->getCollection();
        if($current_user->getRole()->getRoleId() == Mage::getStoreConfig( 'jbmarketplace/jbmarketplace/vendors_role' )) {
            $user_id = $current_user->getUserId();
            $income->getSelect()->where('vendor_id = ?', $user_id)->where('status = ?', 1);
            $count = $income->getSize();
        }

        $incomeTotal = Mage::getModel('jbmarketplace/jbmarketplaceorders')->getCollection();
        if($current_user->getRole()->getRoleId() == Mage::getStoreConfig( 'jbmarketplace/jbmarketplace/vendors_role' )) {
            $user_id = $current_user->getUserId();
            $incomeTotal->getSelect()->where('vendor_id = ?', $user_id)->where('status = ?', 1);
        }
        $incomeTotal->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns('SUM(income) AS incometotalsum');

        $result = $incomeTotal->getFirstItem()->getData();

        $this->addTotal($this->__('Lifetime Income'), $result['incometotalsum']);
        $this->addTotal($this->__('Average Income'), $result['incometotalsum'] / $count);
    }

}