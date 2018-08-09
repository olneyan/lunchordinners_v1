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
  * @package   Ced_MobiconnectAdvCart
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
/**
* Override Controller of Mobiconnect for advance product system 
**/
require_once Mage::getModuleDir('controllers', 'Ced_Mobiconnect').DS.'CheckoutController.php';
class Ced_MobiconnectAdvCart_CheckoutController extends Ced_Mobiconnect_CheckoutController {
   	/**
	* Override addtocart of Mobiconnect if advance product system enable
	**/
   	public function addtocartAction(){
   	   	$enable = Mage::getStoreConfig('mobiconnectadvcart/advancecart/activation'); 
       	if($enable){
   			$data=$this->getRequest()->getParams();
   			Mage::log($data,'1','addtocartpostdev.log',TRUE);
            if(isset($data['super_group'])){
               $data['super_group']=json_decode($data['super_group'],true);
            }
   			if(isset($data['super_attribute'])){
   				$data['super_attribute']=json_decode($data['super_attribute'],true);
   			}
   			if(isset($data['bundle_option_qty'])){
   				$data['bundle_option_qty']=json_decode($data['bundle_option_qty'],true);
   			}
   			if(isset($data['bundle_option'])){
   				$data['bundle_option']=json_decode($data['bundle_option'],true);
   			}
        if(isset($data['Custom'])){
          $customOption=json_decode($data['Custom'],true);
          $data['options'] = $customOption['options'];
          unset($data['Custom']);
        }
	        $valid=Mage::getModel('mobiconnect/checkoutmobi')->validate($data);
	        if($valid)
	        {	Mage::log($data,'1','addtocartpostdev1.log',TRUE);
	            $response=Mage::getModel('mobiconnect/checkoutmobi')->addtocart($data);
	            $this->getResponse()->setBody($response);
	        	return ;
	        }
	        else{
	            $this->getResponse()->setBody(json_encode(array('error'=>'true','message'=>'Cart Id not Found.')));
	        	return ;
	        }
   		}else{
   			parent::addtocartAction();
   		}
	}
}


