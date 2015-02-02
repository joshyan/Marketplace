<?php 

class Junaidbhura_Jbmarketplace_IndexController extends Mage_Core_Controller_Front_Action {

	public function shippingpolicyAction() {
		$warehouse_id   = $this->getRequest()->getParams('wh');
		$model = Mage::getModel('jbmarketplace/jbmarketplacevendors')->load($warehouse_id, 'warehouse_id');
		$return = $model->getStoreShippingpolicy();

		// $this->loadLayout(false);
		// $this->renderLayout();
		$this->getResponse()->setBody($return);
	}

	public function returnpolicyAction() {
		$warehouse_id   = $this->getRequest()->getParams('wh');
		$model = Mage::getModel('jbmarketplace/jbmarketplacevendors')->load($warehouse_id, 'warehouse_id');
		$return = $model->getStoreReturnpolicy();

		$this->getResponse()->setBody($return);
	}


}


?> 