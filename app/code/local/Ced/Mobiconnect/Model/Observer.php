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
class Ced_Mobiconnect_Model_Observer {
	public function secureHash($observer) {
		$event = $observer->getEvent ();
		$cid = $event->getCid ()->getCustomId ();
		
		$now = new Zend_Date ( Mage::getModel ( 'core/date' )->timestamp (), Zend_Date::TIMESTAMP );
		$now->set ( strtotime ( '+30 days', $now->toString ( Zend_Date::TIMESTAMP ) ), Zend_Date::TIMESTAMP );
		$model = Mage::getModel ( 'mobiconnect/customerhash' )->getCollection ()->addFieldToFilter ( 'customer_id', $cid )->getFirstItem ();
		
		if (!$model->getId ()) {
			try {
				$hash = Mage::helper ( 'core' )->uniqHash ();
				$event->getCid ()->setHash ( $hash );
				$models = Mage::getModel ( 'mobiconnect/customerhash' );
				$models->setData ( 'customer_id', $cid )->setData ( 'secure_hash', $hash )->setData ( 'expiry_date', $now );
				$models->save ();
			} catch ( Exception $e ) {
				Mage::getSingleton ( 'customer/session' )->addError ( $e->getMessage () );
			}
		} 
		else{
				$hash =$model->getData('secure_hash');
				$event->getCid ()->setHash ( $hash );
		}
		/*else {
			try {
				$models = Mage::getModel ( 'mobiconnect/customerhash' );
				$models->setData ( 'customer_id', $cid )->setData ( 'secure_hash', $hash )->setData ( 'expiry_date', $now );
				$models->save ();
			} catch ( Exception $e ) {
				Mage::getSingleton ( 'customer/session' )->addError ( $e->getMessage () );
			}
		}*/
	}
	/**
   * Predispath admin action controller
   *
   * @param Varien_Event_Observer $observer
   */
  public function preDispatch(Varien_Event_Observer $observer)
  {
    if (Mage::getSingleton('admin/session')->isLoggedIn()) {
      $feedModel  = Mage::getModel('mobiconnect/feed');
      /* @var $feedModel Ced_Core_Model_Feed */
      $feedModel->checkUpdate();
  
    }
  }
  
  public function beforeLoadingLayout(Varien_Event_Observer $observer) {
    try {
      
      $action = $observer->getEvent()->getAction();
      $layout = $observer->getEvent()->getLayout();
      $sec=$action->getRequest()->getParam('section');
      
      /* print_r($layout->getUpdate()->getHandles());die('observer'); */
      if($action->getRequest()->getActionName() == 'cedpop') return $this;
      $modules = Mage::helper('mobiconnect')->getCedCommerceExtensions();
       if(preg_match('/ced/i',strtolower($action->getRequest()->getControllerModule()))){

        foreach ($modules as $moduleName=>$releaseVersion)
        {   

          $m = strtolower($moduleName); if(!preg_match('/ced/i',$m)){ return $this; }  $h = Mage::getStoreConfig(Ced_Mobiconnect_Block_Extensions::HASH_PATH_PREFIX.$m.'_hash'); for($i=1;$i<=(int)Mage::getStoreConfig(Ced_Mobiconnect_Block_Extensions::HASH_PATH_PREFIX.$m.'_level');$i++){$h = base64_decode($h);}$h = json_decode($h,true); 
          if(is_array($h) && isset($h['domain']) && isset($h['module_name']) && isset($h['license']) && $h['module_name'] == $m && $h['license'] == Mage::getStoreConfig(Ced_Mobiconnect_Block_Extensions::HASH_PATH_PREFIX.$m)){}else{ $_POST=$_GET=array();$action->getRequest()->setParams(array());$exist = false; foreach($layout->getUpdate()->getHandles() as $handle){ if($handle=='c_e_d_c_o_m_m_e_r_c_e'){ $exist = true; break; } } if(!$exist){ $layout->getUpdate()->addHandle('c_e_d_c_o_m_m_e_r_c_e'); }}  
        }
      }
      return $this;
      
    } catch (Exception $e) {
      return $this;
    }
  }
}
?>