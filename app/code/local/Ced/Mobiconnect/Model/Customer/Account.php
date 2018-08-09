<?php
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category  Ced
  * @package   Ced_Mobiconnect
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobiconnect_Model_Customer_Account extends Mage_Core_Model_Abstract {
	/**
	 * Retrieve customer session model object
	 *
	 * @return Mage_Customer_Model_Session
	 */
	protected function _getSession() {
		return Mage::getSingleton ( 'customer/session' );
	}
	/**
	 * Customer Registration Method
	 */
	public function customerRegister($customer,$isJustConfirmed = false) {
		$data = array ();
		/* check if customer already exist or not */
		$customerRegistered = $this->getCustomer ( $customer ['email'] );
		if ($customerRegistered->getId ()) {
			$error = Mage::helper ( 'mobiconnect' )->__ ( 'There is already an account with this email address' );
			$data = array (
					'data' => array (
							'customer' => array (
									array (
											'message' => $error,
											'status' => 'error' 
									) 
							) 
					) 
			);
			return $data;
		}
		/* Save Customer */
		$customers = Mage::getModel ( 'customer/customer' )->setFirstname ( $customer ['firstname'] )->setLastname ( $customer ['lastname'] )->setEmail ( $customer ['email'] );
		$customers->setPassword ( $customer ['password'] );
		try {
			$customers->save ();
			$session = $this->_getSession ();
			if ($customers->isConfirmationRequired ()) {
				/**
				 *
				 * @var $app Mage_Core_Model_App
				 */
				$app = Mage::app ();
				/**
				 *
				 * @var $store Mage_Core_Model_Store
				 */
				$store = $app->getStore ();
				$customers->sendNewAccountEmail ( 'confirmation', $session->getBeforeAuthUrl (), $store->getId () );
				$message = Mage::helper ( 'mobiconnect' )->__ ( 'Account confirmation is required. Please, check your email for the confirmation link.' );
				$data = array (
					'data' => array (
							'customer' => array (
									array (
											'customer_id' => $customers->getId (),
											'isConfirmationRequired'=>'YES',
											'status' => 'success' ,
											'message'=> $message
									) 
							) 
					) 
				);
			} else {
				$customers->sendNewAccountEmail ( $isJustConfirmed ? 'confirmed' : 'registered', '', Mage::app ()->getStore ()->getId () );
				$message = Mage::helper ( 'mobiconnect' )->__ ( "Thank you for registering with " . Mage::app ()->getStore ()->getName () . " store" );
				 $session = Mage::getSingleton('customer/session');
                   $customer = Mage::getModel('customer/customer')->load($customers->getId ());
                   $session->setCustomerAsLoggedIn($customer);
				$cus=array('customer_id'=>$customers->getId ());
					$customerquote=Mage::getSingleton('checkout/cart')->getQuote();
					//var_dump($customerquote->getData());die;
					
				if(isset($data['cart_id']))
				{ 
					$cart_id=$data['cart_id'];
                    $store=Mage::app()->getStore();  
                    $guestquote = Mage::getModel('sales/quote')->setStore($store)->load($cart_id);
                 	$customerquote->merge($guestquote);
                 	$customerquote->setCustomer(Mage::getSingleton('customer/session')->getCustomer());
                 	$customerquote->collectTotals()->save();
                //var_dump($customerquote->getData());die;
                }

				$data = array (
					'data' => array (
							'customer' => array (
									array (
											'customer_id' => $customers->getId (),
											'isConfirmationRequired'=>'NO',
											'status' => 'success',
											'message'=> $message
									) 
							) 
					) 
				);
			}
			
			return $data;
		} catch ( Exception $e ) {
			$message = $e->getMessage ();
			$data = array (
					'data' => array (
							'customer' => array (
									array (
											'message' => $message,
											'status' => 'exception' 
									) 
							) 
					) 
			);
			return $data;
		}
	}
	public function getCustomer($email) {
		$customer = Mage::getModel ( 'customer/customer' )->getCollection ()->addAttributeToFilter ( 'email', $email )->addAttributeToSelect ( '*' )->getFirstItem ();
		return $customer;
	}
	public function customerLogin($data) {
		$session = $this->_getSession ();
		if ($data) {
			$login ['username'] = $data ['email'];
			$login ['password'] = $data ['password'];
			if (! empty ( $login ['username'] ) && ! empty ( $login ['password'] )) {
				try {
					$session->login ( $login ['username'], $login ['password'] );
					$customerId = $session->getCustomer ()->getId ();
					$secureHash = new Varien_Object ();
					$secureHash->setCustomId ( $customerId );
					$dats = Mage::dispatchEvent ( 'login_hash_event', array (
							'cid' => $secureHash 
					) );
					$hash = $secureHash->getHash ();
					$cus=array('customer_id'=>$customerId);
					$customerquote=Mage::getSingleton('checkout/cart')->getQuote();
					//var_dump($customerquote->getData());die;
					
						$cart_id=$data['cart_id'];
						if(isset($data['cart_id'])){ 
		                    $store=Mage::app()->getStore();  
	                        $guestquote = Mage::getModel('sales/quote')->setStore($store)->load($cart_id);
	                     
	                     $customerquote->merge($guestquote);
		                 $customerquote->setCustomer(Mage::getSingleton('customer/session')->getCustomer());
		                 $customerquote->collectTotals()->save();
		                }
					$summary=Mage::helper ( 'checkout/cart' )->getSummaryCount ();
					if(!isset($summary))
						$summary=0;
					$data = array (
							'data' => array (
									'customer' => array (
											array (
													'customer_id' => $customerId,
													'hash' => $hash,
													'cart_summary' => $summary,
													'status' => 'success' 
											) 
									) 
							) 
					);
				} catch ( Mage_Core_Exception $e ) {
					switch ($e->getCode ()) {
						case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED :
							// $value = $this->_getHelper('customer')->getEmailConfirmationUrl($login['username']);
							$message = Mage::helper ( 'mobiconnect' )->__ ( 'This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value );
							$data = array (
									'data' => array (
											'customer' => array (
													array (
															'message' => $message,
															'status' => 'exception' 
													) 
											) 
									) 
							);
							return $data;
							break;
						case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD :
							$message = $e->getMessage ();
							$data = array (
									'data' => array (
											'customer' => array (
													array (
															'message' => $message,
															'status' => 'exception' 
													) 
											) 
									) 
							);
							return $data;
							break;
						default :
							$message = $e->getMessage ();
							$data = array (
									'data' => array (
											'customer' => array (
													array (
															'message' => $message,
															'status' => 'exception' 
													) 
											) 
									) 
							);
							return $data;
					}
				}
			} else {
				$error [] = Mage::helper ( 'mobiconnect' )->__ ( 'Login And Password are required' );
				$data = array (
						'data' => array (
								'customer' => array (
										array (
												'message' => $error,
												'status' => 'exception' 
										) 
								) 
						) 
				);
				return $data;
			}
		} else {
			$error [] = Mage::helper ( 'mobiconnect' )->__ ( 'Login And Password are required' );
			$data = array (
					'data' => array (
							'customer' => array (
									array (
											'message' => $error,
											'status' => 'exception' 
									) 
							) 
					) 
			);
			return $data;
		}
		return $data;
	}
	public function saveCustomerAddress($data) {
		// Save data
		if ($data) {
			$customer = $this->_getSession ()->getCustomer ();
			/* @var $address Mage_Customer_Model_Address */
			$address = Mage::getModel ( 'customer/address' );
			$addressId = $data ['address_id'];
			
			if ($addressId) {
				$existsAddress = $this->getAddressById ( $addressId );
				
				if ($existsAddress->getId () && $existsAddress->getCustomerId () == $data ['customer']) {
					$address->setId ( $existsAddress->getId () );
				}
			}
			$errors = array ();
			
			/* @var $addressForm Mage_Customer_Model_Form */
			$addressForm = Mage::getModel ( 'customer/form' );
			$addressForm->setFormCode ( 'customer_address_edit' )->setEntity ( $address );
			$addressErrors = $addressForm->validateData ( $data );
			if ($addressErrors !== true) {
				$errors = $addressErrors;
			}
			
			try {
				$addressForm->compactData ( $data );
				$address->setCustomerId ( $data ['customer'] )/*For Getting Info from session change here*/
				->setIsDefaultBilling ( false )->setIsDefaultShipping ( false );
				
				$addressErrors = $address->validate ();
				if ($addressErrors !== true) {
					$errors = array_merge ( $errors, $addressErrors );
				}
				
				if (count ( $errors ) === 0) {
					$address->save ();
					$addressId = $address->getId ();
					$data = array (
							'data' => array (
									'customer' => array (
											array (
													'message' => 'Customer Address has been updated',
													'status' => 'success',
													'address_id' => $addressId 
											) 
									) 
							) 
					);
					return $data;
				} else {
					foreach ( $errors as $errorMessage ) {
						$message [] = $errorMessage;
					}
					$data = array (
							'data' => array (
									'customer' => array (
											array (
													'message' => $message,
													'status' => 'error' 
											) 
									) 
							) 
					);
					return $data;
				}
			} catch ( Mage_Core_Exception $e ) {
				$data = array (
						'data' => array (
								'customer' => array (
										array (
												'message' => $e->getMessage (),
												'status' => 'error' 
										) 
								) 
						) 
				);
				return $data;
			} catch ( Exception $e ) {
				$data = array (
						'data' => array (
								'customer' => array (
										array (
												'message' => $e->getMessage (),
												'status' => 'error' 
										) 
								) 
						) 
				);
				return $data;
			}
		}
	}
	/**
	 * Retrieve customer address by address id
	 *
	 * @param int $addressId        	
	 * @return Mage_Customer_Model_Address
	 */
	public function getAddressById($addressId) {
		return Mage::getModel ( 'customer/address' )->load ( $addressId );
	}
	public function getCustomerAddress($data) {
		$customer = Mage::getModel ( 'customer/customer' )->load ( $data ['customer'] );
		$collection = Mage::getResourceModel ( 'customer/address_collection' )->addAttributeToSelect ( '*' )->setCustomerFilter ( $customer );
		$collection->setOrder ( 'updated_at', 'DESC' );
		$customerAddress = array();
		// $collection->setOrder('updated_at','DESC');
		// var_dump($collection->getData());die;
		foreach ( $collection as $address ) {
			$customerAddress [] = array (
					'firstname' => $address ['firstname'],
					'lastname' => $address ['lastname'],
					'street' => $address ['street'],
					'city' => $address ['city'],
					'region_id' => $address ['region_id'],
					'region' => $address ['region'],
					'country' => Mage::app ()->getLocale ()->getCountryTranslation ( $address ['country_id'] ),
					'pincode' => $address ['postcode'],
					'phone' => $address ['telephone'],
					'address_id' => $address ['entity_id'] 
			)
			;
		}
		
		if (count ( $customerAddress ) > 0) {
			
			$data = array (
					'data' => array (
							'address' => $customerAddress,
							'status' => 'success',
							'customer_id' => $data ['customer'] 
					)
					 
			)
			;
		} else {
			$data = array (
					'data' => array (
							// 'address' => $customerAddress,
							'status' => 'no_address' 
					// 'customer_id'=>$data['customer'],
										)

					 
			)
			;
		}
		return $data;
	}
	public function deleteCustomerAddress($data) {
		$addressId = $data ['address_id'];
		
		if ($addressId) {
			$address = Mage::getModel ( 'customer/address' )->load ( $addressId );
			
			// Validate address_id <=> customer_id
			if ($address->getCustomerId () != $data ['customer']) {
				$data = array (
						'data' => array (
								'customer' => array (
										array (
												'message' => 'The address does not belong to this customer.',
												'status' => 'success' 
										) 
								) 
						) 
				);
				return $data;
			}
			
			try {
				$address->delete ();
				$data = array (
						'data' => array (
								'customer' => array (
										array (
												'message' => 'The address has been deleted.',
												'status' => 'success' 
										) 
								) 
						) 
				);
				return $data;
			} catch ( Exception $e ) {
				$data = array (
						'data' => array (
								'customer' => array (
										array (
												'message' => $e->getMessage (),
												'status' => 'exception' 
										) 
								) 
						) 
				);
				return $data;
			}
		}
		return $data;
	}
	public function logoutCustomer($data) {
		$this->_getSession ()->logout ()->renewSession ();
		$data = array (
				'data' => array (
						'customer' => array (
								array (
										'message' => 'The customer has been successfully logged out.',
										'status' => 'success' 
								) 
						) 
				) 
		);
		return $data;
	}
	public function forgotPassword($data) {
		$email = $data['email'];
		// echo $email;
		if ($email) {
			if (! Zend_Validate::is ( $email, 'EmailAddress' )) {
				$data = array (
						'data' => array (
								'customer' => array (
										array (
												'message' => 'Invalid Email Address',
												'status' => 'error' 
										) 
								) 
						) 
				);
				return $data;
			}
			$customer = Mage::getModel ( 'customer/customer' )->setWebsiteId ( Mage::app ()->getStore ()->getWebsiteId () )->loadByEmail ( $email );
			
			if ($customer->getId ()) {
				try {
					$newPassword = $customer->generatePassword();
					//Mage::log ( $newPassword, null, 'pass.log' );
					$customer->changePassword ( $newPassword, false );
					//Mage::log('forgot password',null,'password.log');
					$customer->sendPasswordReminderEmail ();
					$data = array (
							'data' => array (
									'customer' => array (
											array (
													'message' => 'The password has been successfully updated',
													'status' => 'success' 
											) 
									) 
							) 
					);
					return $data;
				} catch ( Mage_Core_Exception $e ) {
					
					$data = array (
							'data' => array (
									'customer' => array (
											array (
													'message' => $e->getMessage (),
													'status' => 'error' 
											) 
									) 
							) 
					);
					return $data;
				} catch ( Exception $e ) {
					$data = array (
							'data' => array (
									'customer' => array (
											array (
													'message' => $e->getMessage (),
													'status' => 'error' 
											) 
									) 
							) 
					);
					return $data;
				}
			} else {
				$data = array (
						'data' => array (
								'customer' => array (
										array (
												'message' => 'This email address was not found in our records.',
												'status' => 'error' 
										) 
								) 
						) 
				);
				return $data;
			}
		} else {
			$data = array (
					'data' => array (
							'customer' => array (
									array (
											'message' => 'Customer email not specified.',
											'status' => 'error' 
									) 
							) 
					) 
			);
			return $data;
		}
	}
	public function updateProfile($data) {
		//var_dump($data);die;
		if ($data ['change_password']) {
			$currPass = $data ['old_password'];
			$newPass = $data ['new_password'];
			$confPass = $data ['conform_password'];
			
			if (empty ( $currPass ) || empty ( $newPass ) || empty ( $confPass )) {
				$data = array (
						'data' => array (
								'customer' => array (
										array (
												'message' => 'Password fields cannot be empty.',
												'status' => 'error' 
										) 
								) 
						) 
				);
				return $data;
			}
			
			if ($newPass != $confPass) {
				$data = array (
						'data' => array (
								'customer' => array (
										array (
												'message' => 'Please make sure your passwords match.',
												'status' => 'error' 
										) 
								) 
						) 
				);
				return $data;
			}
			$customer = Mage::getModel ( 'customer/customer' )->load ( $data ['customer'] );
			// $customerpass=Mage::getModel('customer/customer');
			$oldPass = $customer->getPasswordHash ();
			
			if (strpos ( $oldPass, ':' )) {
				list ( , $salt ) = explode ( ':', $oldPass );
			} else {
				$salt = false;
			}
			
			if ($customer->hashPassword ( $currPass, $salt ) == $oldPass) {
				
				$customer->setPassword ( $newPass );
				$customer->save ();
				$data = array (
						'data' => array (
								'customer' => array (
										array (
												'message' => 'Password Updated Successfully',
												'status' => 'success' 
										) 
								) 
						) 
				);
				return $data;
			} else {
				$data = array (
						'data' => array (
								'customer' => array (
										array (
												'message' => 'Invalid current password.',
												'status' => 'error' 
										) 
								) 
						) 
				);
				return $data;
			}
		}
	}
}
	
