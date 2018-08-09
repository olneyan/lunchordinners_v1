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
class Ced_Mobiconnect_Customer_AccountController extends Ced_Mobiconnect_Controller_Action {
	public function registerAction() {
		$data = array (
				'firstname' =>$this->getRequest()->getParam('firstname'),
				'lastname' => $this->getRequest()->getParam('lastname'),
				'email' => $this->getRequest()->getParam('email'),
				'password' => $this->getRequest()->getParam('password'),
				'cart_id'=>	$this->getRequest()->getParam('cart_id'),
		);
		if(!isset($data['firstname']) || $data['firstname']=='' && !isset($data['lastname']) || $data['lastname']=='' && !isset($data['email']) || $data['email']=='' && !isset($data['password']) || $data['password']=='')
		{
			echo 'return'; return;
		}
		/*$pass = $this->matchPass ( $data );
		if (! $pass) {
			$this->printJsonData ( 'Password Does Not Match' );
			die ();
		}*/
		// $data=$this->getRequest()->getParam($data);
		$customer = Mage::getModel ( 'mobiconnect/customer_account' )->customerRegister ( $data );
		$this->printJsonData ( $customer );
	}
	public function loginAction() {
		$data = array (
				'email' => $this->getRequest()->getParam('email'),
				'password' => $this->getRequest()->getParam('password'),
				'cart_id'=>	$this->getRequest()->getParam('cart_id'),
		);
		
		$customer = Mage::getModel ( 'mobiconnect/customer_account' )->customerLogin ( $data );
		$this->printJsonData ( $customer );
	}
	public function forgotPasswordAction() {
		$data = array (
				 'email'=>$this->getRequest()->getParam('email'),
		);
	
		$customer = Mage::getModel ( 'mobiconnect/customer_account' )->forgotPassword ( $data );
		$this->printJsonData ( $customer );
	}
	public function updateProfileAction() {
		$data = array (
				'customer'=>$this->getRequest()->getParam('customer_id'),
				'email' => $this->getRequest()->getParam('email'),
				'hash'=>$this->getRequest()->getParam('hashkey'),
				'change_password' => $this->getRequest()->getParam('change_password'),
				'old_password' => $this->getRequest()->getParam('old_password'),
				'new_password' => $this->getRequest()->getParam('new_password'),
				'conform_password' => $this->getRequest()->getParam('confirm_password'),

				//'customer'=>'141',
				//'email' => $this->getRequest()->getParam('email'),
				//'hash'=>$this->getRequest()->getParam('hashkey'),
				//'change_password' => '1',
				//'old_password' => 'admin123',
				//'new_password' => '12345678',
				//'conform_password' =>'12345678',
		);
		//var_dump($data);die;
	 	//$validateRequest=$this->validate($data);
		$customer = Mage::getModel ( 'mobiconnect/customer_account' )->updateProfile ( $data );
		$this->printJsonData ( $customer );
	}
	public function saveCustomerAddressAction(){
		$data = array();
		$data['firstname'] =$this->getRequest()->getParam('firstname');
        $data['lastname'] =$this->getRequest()->getParam('lastname');
        $data['street'] =  $this->getRequest()->getParam('street');
        $data['city'] = $this->getRequest()->getParam('city');
        $data['region'] = $this->getRequest()->getParam('state');
        $data['postcode'] =$this->getRequest()->getParam('pincode');
        $data['country_name'] =  $this->getRequest()->getParam('country');
        $data['telephone'] =   $this->getRequest()->getParam('mobile');
        $data['customer']=$this->getRequest()->getParam('customer_id');
        $data['hash']=$this->getRequest()->getParam('hashkey');
        $data['address_id']=$this->getRequest()->getParam('address_id');
if(!isset($data['firstname']) || $data['firstname']=='' && !isset($data['lastname']) || $data['lastname']=='' && !isset($data['street']) || $data['street']=='' && !isset($data['city']) || $data['city']=='' && !isset($data['region']) || $data['region']==''
&& !isset($data['postcode']) || $data['postcode']=='' && !isset($data['country_name']) || $data['country_name']=='' && !isset($data['telephone']) || $data['telephone']=='')
		{
			echo 'return'; return;
		}

        $validateRequest=$this->validate($data);
        
        $data=Mage::helper('mobiconnect')->changeAddress($data);
		$address = Mage::getModel ( 'mobiconnect/customer_account' )->saveCustomerAddress($data);
		$this->printJsonData ( $address );
		
	}
	public function getCustomerAddressAction(){
	
		$data = array(
			'customer'=>$this->getRequest()->getParam('customer_id'),
			'hash'=>$this->getRequest()->getParam('hashkey'),
		);
		$validateRequest=$this->validate($data);
		$address = Mage::getModel ( 'mobiconnect/customer_account' )->getCustomerAddress($data);
		$this->printJsonData ( $address );
	}
	public function deleteCustomerAddressAction(){
		$data = array(
				'customer'=>$this->getRequest()->getParam('customer_id'),
				'hash'=>$this->getRequest()->getParam('hashkey'),
				'address_id'=>$this->getRequest()->getParam('address_id')
		);
		$validateRequest=$this->validate($data);
		$address = Mage::getModel ( 'mobiconnect/customer_account' )->deleteCustomerAddress($data);
		$this->printJsonData ( $address );
	}
	public function logoutAction(){
		
		$data = array(
				'customer'=>$this->getRequest()->getParam('customer_id'),
				'hash'=>$this->getRequest()->getParam('hashkey'),
		);
// 		foreach( Mage::getSingleton('checkout/session')->getQuote()->getItemsCollection() as $item ){
// 			die("die in loop");
// 			Mage::getSingleton('checkout/cart')->removeItem( $item->getId() )->save();
		
// 		}
		$validateRequest=$this->validate($data);
		$address = Mage::getModel ( 'mobiconnect/customer_account' )->logoutCustomer($data);
		$this->printJsonData ( $address );
	}
	public function orderListAction(){ 
		$data = array(
				'customer'=>$this->getRequest()->getParam('customer_id'),
				'hash'=>$this->getRequest()->getParam('hashkey'),
				'offset'=>$this->getRequest()->getParam('page'),
		);

		$validateRequest=$this->validate($data);

		$order = Mage::getModel ( 'mobiconnect/customer_order' )->getCustomerOrder($data);
		$this->printJsonData ( $order );

	}

	public function orderViewAction(){ 
		$data = array(
			  'customer'=>$this->getRequest()->getParam('customer_id'),
				//'customer'=>'141',
				'hash'=>$this->getRequest()->getParam('hashkey'),
				'order_id'=>$this->getRequest()->getParam('order_id'),
				//'order_id'=>'211'
		);
		$validateRequest=$this->validate($data);
		try {
            $orderId = $data['order_id'];
            if (!$orderId) {
                $msg=$this->__('Order id is not specified.');
                $this->printJsonData ( $msg );
            }

            $order = Mage::getModel('sales/order')->load($orderId);

            if ($this->_canViewOrder($order,$data['customer'])) {
                Mage::register('current_order', $order);
            } else {
               $msg=$this->__('Order is not available.');
               $this->printJsonData ( $msg );
            }

            
        } catch (Mage_Core_Exception $e) {
            $msg=$e->getMessage();
        } catch (Exception $e) {
           $msg=$this->__('Unable to render an order.');
           $this->printJsonData ( $msg );
        }

		$orderdetail = Mage::getModel ( 'mobiconnect/customer_order' )->getOrderDetail($data);
		$this->printJsonData ( $orderdetail );

	}
	

        protected function _canViewOrder($order,$customerid)
    {
        $customerId = $customerid;
        $availableStates = Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates();
        if ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerId)
            && in_array($order->getState(), $availableStates, true)
        ) {
            return true;
        }
        return false;
    }
    
    
	
}


