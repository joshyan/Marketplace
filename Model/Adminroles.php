<?php
/**
 * Adminroles Model
 *
 * @category    Model
 * @package     Junaidbhura_Jbmarketplace
 * @author      Junaid Bhura <info@junaidbhura.com>
 */

/**
 * Used in creating options for Admin Roles config value selection
 *
 */
class Junaidbhura_Jbmarketplace_Model_Adminroles {
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		// Load admin roles
		$roles = Mage::getModel( 'admin/roles' )->getCollection();
		$roles_array = array();
		foreach ( $roles as $role ) {
			$roles_array[] = array( 'value' => $role->getRoleId(), 'label' => $role->getRoleName() );
		}
		
		return $roles_array;
	}

	/**
	 * Get options in "key-value" format
	 *
	 * @return array
	 */
	public function toArray()
	{
		// Load admin roles
		$roles = Mage::getModel( 'admin/roles' )->getCollection();
		$roles_array = array();
		foreach ( $roles as $role ) {
			$roles_array[ $role->getRoleId() ] = $role->getRoleName();
		}
		
		return $roles_array;
	}
}
?>