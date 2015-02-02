<?php
/**
 * Order Observer Model
 *
 * @category    Model
 * @package     Junaidbhura_Jbmarketplace
 * @author      Junaid Bhura <info@junaidbhura.com>
 */

class Junaidbhura_Jbmarketplace_Model_Userobserver {
	/*
	* Save vendor to jbmarketplace vendors table if the user is a vendor
	*/
	public function saveVendor( $observer ) {
		// Get user
		$user = $observer->getEvent()->getObject();
		// Mage::log('User info: ');
		// Mage::log($user->debug());
		$user_debug = $user->debug();

		$original_roles = $user_debug['user_roles'];
		$new_roles = $user_debug['roles'];

		$user_id = $user->getId();

		if($user_id ) {
				$user_name = $user->getUsername();
				$first_name = $user->getFirstname();
				$last_name = $user->getLastname();
				$email = $user->getEmail();

				//if new role is vendor
				if($new_roles[0] == 11) {
					// if save vendor again
					if($original_roles == '11=0') {
						
						$data = array('vendor_id' => $user_id, 'user_name' => $user_name, 'first_name' => $first_name, 'last_name' => $last_name, 'email' => $email);
						$model = Mage::getModel('jbmarketplace/jbmarketplacevendors')->load($user_id, 'vendor_id')->addData($data);
						try {
						    $model->save();
						    Mage::log("Vendor successfully updated. Vendor ID: ".$user_id);
						} catch (Exception $e){
						    echo $e->getMessage();
						}	
										
					} else {
						$collection = Mage::getModel('jbmarketplace/jbmarketplacevendors')->getCollection();
						$collection->addFieldToFilter('vendor_id', $user_id);

						if($collection->getSize() == 0) {  // if user not exist, save this user to jbmarketplace_vendors table
							$data = array('vendor_id' => $user_id, 'user_name' => $user_name, 'first_name' => $first_name, 'last_name' => $last_name, 'email' => $email, 'commission' => '');
							$model = Mage::getModel('jbmarketplace/jbmarketplacevendors')->setData($data);
							try {
								$insertId = $model->save()->getId();
								Mage::log("New vendor successfully inserted. Insert ID: ".$insertId);
							} catch (Exception $e) {
							 	echo $e->getMessage();   
							}
						}
					}
				}

				//if change from vendor to other role
				if($new_roles[0] != 11 && $original_roles == '11=0') {
					//todo. delete the vendor
				}

		}


	}
	
}
?>