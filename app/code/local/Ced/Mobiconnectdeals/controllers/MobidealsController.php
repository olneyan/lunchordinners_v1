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
  * @package   Ced_Mobiconnectdeals
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobiconnectdeals_MobidealsController extends Ced_Mobiconnect_Controller_Action
{
    /**
    * GetDeals group
    * @param Post Data Params() 
    **/
	function getdealgroupAction(){
        $data=$this->getRequest()->getParams();
        
        $enable = Mage::getStoreConfig('mobiconnectdeals/mobideals/activation');
        if($enable){
            $valid=Mage::getModel('mobiconnect/checkoutmobi')->validate($data);
            if($valid)
            {
                $response=Mage::getModel('mobiconnectdeals/group')->getdealgroupdata($data);
                $this->getResponse()->setBody($response);
            }
            else{
                $this->getResponse()->setBody(json_encode(array('error'=>'true','message'=>'Deals not Found.')));
            }
        }else{
            $this->getResponse()->setBody(json_encode(array('error'=>'true','message'=>'Deals not Found.')));
        }
        
	}
	/**
    * GET Deal
    * @param Post Data Params() 
    **/
	function getdealAction(){
        $data=$this->getRequest()->getParams();
        $enable = Mage::getStoreConfig('mobiconnectdeals/mobideals/activation');
        if($enable){
            $valid=Mage::getModel('mobiconnect/checkoutmobi')->validate($data);
            if($valid)
            {
                $response=Mage::getModel('mobiconnectdeals/deals')->getdealsdata($data);
                $this->getResponse()->setBody($response);
            }
            else{
                $this->getResponse()->setBody(json_encode(array('error'=>'true','message'=>'Deals not Found.')));
            }
        }else{
            $this->getResponse()->setBody(json_encode(array('error'=>'true','message'=>'Deals not Found.')));
        }
        
	}
}