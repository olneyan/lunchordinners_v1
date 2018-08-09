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
  * @package   Ced_Mobiconnectcheckout
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
require_once Mage::getModuleDir('controllers', 'Payu_PayuCheckout').DS.'SharedController.php';
class Ced_Mobiconnectcheckout_SharedController extends Payu_PayuCheckout_SharedController
{
   
    protected $_redirectBlockType = 'payucheckout/shared_redirect';
    protected $_paymentInst = NULL;
	
	
	public function  successAction()
    {
        $response = $this->getRequest()->getPost();
		Mage::getModel('payucheckout/shared')->getResponseOperation($response);
		if(Mage::getSingleton('customer/session')->getIsApp())
        $this->_redirect('mobiconnectcheckout/onepage/index');
        else
        $this->_redirect('checkout/onepage/success');
    }
	
	
	
	 public function failureAction()
    {
       
	   $arrParams = $this->getRequest()->getPost();
	   Mage::getModel('payucheckout/shared')->getResponseOperation($arrParams);
       $this->getCheckout()->clear();
        if(Mage::getSingleton('customer/session')->getIsApp())
        $this->_redirect('mobiconnectcheckout/onepage/index');
        else
	   $this->_redirect('checkout/onepage/failure');
    }


    public function canceledAction()
    {
	    if(Mage::getSingleton('customer/session')->getIsApp())
        $this->_redirect('mobiconnectcheckout/onepage/index');
        else{
        	$arrParams = $this->getRequest()->getParams();
	
       
			Mage::getModel('payucheckout/shared')->getResponseOperation($arrParams);
			
			$this->getCheckout()->clear();
			$this->loadLayout();
	        $this->renderLayout();
        }
	    
    }


   

    
}
    
    
