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
  * @package   Ced_MobiconnectSocialLogin
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_MobiconnectSocialLogin_Model_Register extends Mage_Core_Model_Abstract {
  
	protected function _getSession() {
		return Mage::getSingleton ( 'customer/session' );
	}
    public function getCustomer($email) {
		$customer = Mage::getModel ( 'customer/customer' )->getCollection ()->addAttributeToFilter ( 'email', $email )->addAttributeToSelect ( '*' )->getFirstItem ();
		return $customer;
	}
  	public function customerRegister($postdata){

  		$data = array ();
		/* check if customer already exist or not */
		$customer = $this->getCustomer ($postdata ['email']);
		if ($customer->getId ()) {
			if ($customer->isConfirmationRequired()) {
				$message = Mage::helper ( 'mobiconnect' )->__ ( 'Account confirmation is required. Please, check your email for the confirmation link.' );
					$data = array (
						'data' => array (
								'customer' => array (
										array (
												'customer_id' => $customer->getId (),
												'cart_summary' =>'0',
												'isConfirmationRequired'=>'YES',
												'status' => 'success' ,
												'message'=> $message
										) 
								) 
						) 
					);
				return $data;
			}else{
				Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
				$secureHash = new Varien_Object ();
				$secureHash->setCustomId ( $customer->getId());
				$dats = Mage::dispatchEvent ( 'login_hash_event', array (
							'cid' => $secureHash 
						));
				$hash = $secureHash->getHash ();
				$customerquote=Mage::getSingleton('checkout/cart')->getQuote();
						//var_dump($customerquote->getData());die;
						
							$cart_id=$postdata['cart_id'];
							if(isset($postdata['cart_id'])){ 
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
										'customer_id' => $customer->getId(),
										'hash' => $hash,
										'cart_summary' =>$summary,
										'status' => 'success' 
									) 
								) 
							) 
					);
				return $data;
			}
		}
		else{
			/* Save Customer */
			$customers = Mage::getModel ( 'customer/customer' )->setFirstname ( $postdata ['firstname'] )->setLastname ( $postdata ['lastname'] )->setEmail ( $postdata ['email'] );

			$customers->setPassword ( $customer->generatePassword(10) );
			try {
				$customers->save ();
				$customerId = $customers->getId();
				
				$secureHash = new Varien_Object ();
				$secureHash->setCustomId ( $customerId );
				$dats = Mage::dispatchEvent ( 'login_hash_event', array (
						'cid' => $secureHash 
						) );
				$hash = $secureHash->getHash ();	
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
												'cart_summary' =>'0',
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
					$data = array (
						'data' => array (
								'customer' => array (
										array (
												'customer_id' => $customers->getId (),
												'isConfirmationRequired'=>'NO',
												'cart_summary' =>'0',
												'status' => 'success',
												'hash' => $hash,
												'message'=> $message
										) 
								) 
						) 
					);
				}
					return $data;	
				}	
			 catch ( Exception $e ) {
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
  	}
    
}
?>
