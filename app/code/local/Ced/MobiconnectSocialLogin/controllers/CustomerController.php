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
class Ced_MobiconnectSocialLogin_CustomerController extends Ced_Mobiconnect_Controller_Action {
   	
   	public function registerAction() {

		$data = array (
				'firstname' =>$this->getRequest()->getParam('firstname'),
				'lastname' => $this->getRequest()->getParam('lastname'),
				'email' => $this->getRequest()->getParam('email'),
				//'password' => $this->getRequest()->getParam('password'),
		);
	/*	if(!isset($data['firstname']) || $data['firstname']=='' && !isset($data['lastname']) || $data['lastname']=='' && !isset($data['email']) || $data['email']=='')
		{
			echo 'return'; return;
		}*/
		/*$pass = $this->matchPass ( $data );
		if (! $pass) {
			$this->printJsonData ( 'Password Does Not Match' );
			die ();
		}*/
		// $data=$this->getRequest()->getParam($data);
		$customer = Mage::getModel ( 'mobiconnectsociallogin/register' )->customerRegister ( $data );
		$this->printJsonData ( $customer );
	}
}


