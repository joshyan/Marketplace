<?php
class Junaidbhura_Jbmarketplace_Block_Adminhtml_Dashboard extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        $this->_headerText = $this->__('Vendor Dashboard');
        parent::__construct();
        $this->setTemplate('jbmarketplace/dashboard/index.phtml');
    }

    protected  function _prepareLayout()
    {
        $this->setChild('sales',
            $this->getLayout()->createBlock('jbmarketplace/adminhtml_dashboard_sales')
        );
        $this->setChild('income',
            $this->getLayout()->createBlock('jbmarketplace/adminhtml_dashboard_income')
        );

        $this->setChild('diagrams',
            $this->getLayout()->createBlock('jbmarketplace/adminhtml_dashboard_diagrams')
        );

        $this->setChild('lastOrders',
            $this->getLayout()->createBlock('jbmarketplace/adminhtml_dashboard_grid')
        );

        parent::_prepareLayout();
    }

}