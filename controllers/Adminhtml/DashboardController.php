<?php

class Junaidbhura_Jbmarketplace_Adminhtml_DashboardController extends Mage_Adminhtml_Controller_action
{
   /**
     *
     */
    public function indexAction()
    {
        // Let's call our initAction method which will set some basic params for each action
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * @return Ebizmarts_AbandonedCart_Adminhtml_AbandonedorderController
     */
    protected function _initAction()
    {
        $this->loadLayout()
        // Make the active menu match the menu config nodes (without 'children' inbetween)
            ->_setActiveMenu('vendor_dashboard')
            ->_title($this->__('Dashboard'))->_title($this->__('Vendor Dashboard'))
            ->_addBreadcrumb($this->__('Dashboard'), $this->__('Dashboard'))
            ->_addBreadcrumb($this->__('Vendor'), $this->__('Vendor'));

        return $this;
    }

    /**
     *
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }


    /**
     *
     */
    public function ajaxBlockAction()
    {
        $output   = '';
        $blockTab = $this->getRequest()->getParam('block');
        $period = $this->getRequest()->getParam('period');

        if ($blockTab == 'tab_orders') {
            //$output = $this->getLayout()->createBlock('ebizmarts_abandonedcart/adminhtml_dashboard_' . $blockTab)->toHtml();
            $output = json_encode( Mage::helper('jbmarketplace')->formatOrdersData( $period, Mage::helper('jbmarketplace')->getDataByRange($period) ) );    
        }
        if($blockTab == 'tab_amounts') {
            $output = json_encode( Mage::helper('jbmarketplace')->formatAmountsData( $period, Mage::helper('jbmarketplace')->getDataByRange($period) ) );    
        }
        $this->getResponse()->setBody($output);
        return;
    }

}
