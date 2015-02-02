<?php
/**
 * Product Model
 *
 * @category    Model
 * @package     Junaidbhura_Jbmarketplace
 * @author      Junaid Bhura <info@junaidbhura.com>
 */

class Junaidbhura_Jbmarketplace_Model_Vendorlogin extends Mage_Admin_Model_User {
	
	 /**
     * Find admin start page url
     *
     * @return string
     */
    public function getStartupPageUrl()
    {
    	if(Mage::helper('jbmarketplace')->isVendor()) {
    		return 'jbmarketplace/adminhtml_dashboard';
    	}
        
        $startupPage = Mage::getStoreConfig(self::XML_PATH_STARTUP_PAGE);
        $aclResource = 'admin/' . $startupPage;
        if (Mage::getSingleton('admin/session')->isAllowed($aclResource)) {
            $nodePath = 'menu/' . join('/children/', explode('/', $startupPage)) . '/action';
            $url = Mage::getSingleton('admin/config')->getAdminhtmlConfig()->getNode($nodePath);
            if ($url) {
                return $url;
            }
        }
        return $this->findFirstAvailableMenu();
    }

}
?>