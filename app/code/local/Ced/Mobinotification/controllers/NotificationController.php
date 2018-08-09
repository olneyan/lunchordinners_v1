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
  * @package   Ced_Mobinotification
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobinotification_NotificationController extends Mage_Core_Controller_Front_Action
{
    public function saveAction()
    {  
    	if($this->getRequest()->isPost()){
    		$enable= Mage::getStoreConfig('mobinotification/mobinotify/activation');
    		$data=$this->getRequest()->getParams();
    		if(isset($data['Token']) && $data['Token']!='' && $enable){
    			$model = Mage::getModel('mobinotification/mobidevices');
		    	$model->setDeviceId($data['Token']);
          $model->setEmail($data['email']);
          $model->setExtra($data['type']);
		    	$model->save();
		    	$this->getResponse()->setBody(json_encode(array('success'=>true)));
		    	return;
			}else{
				$this->getResponse()->setBody(json_encode(array('success'=>false)));
				return;
			}
    	}
    }
}