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
  * @package   Ced_Mobiconnectstore
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobiconnectstore_IndexController extends Ced_Mobiconnect_Controller_Action {
    /**
    *Get available store from app
    **/
    public function getStoreAction(){
       $enable = Mage::getStoreConfig('mobiconnectstore/mobistore/activation'); 
       if($enable){
            $model=Mage::getModel('mobiconnectstore/mobistore')->getStore();
            $this->getResponse()->setBody($model);
       }else{
        $this->getResponse()->setBody(json_encode(array('success'=>false,'message'=>'Store module is not active')));
       }
    }
    /**
    *Set store from app
    **/
    public function setStoreAction(){

      $enable = Mage::getStoreConfig('mobiconnectstore/mobistore/activation'); 
      if($enable){
        $store_id=$this->getRequest()->getParam('store_id'); 
        if($store_id!=''){
          $model=Mage::getModel('mobiconnectstore/mobistore')->setStore($store_id);
          $this->getResponse()->setBody($model);
        }else{
        $this->getResponse()->setBody(json_encode(array('success'=>false,'message'=>'Store Id not Exists.')));
        }
      }
      else{
        $this->getResponse()->setBody(json_encode(array('success'=>false,'message'=>'Store module is not active')));
      }
     
    }	
}


