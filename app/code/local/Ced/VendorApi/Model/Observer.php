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
  * @category    Ced
  * @package     VendorApi
  * @author      CedCommerce Core Team <connect@cedcommerce.com>
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://cedcommerce.com/license-agreement.txt
  */
	class Ced_VendorApi_Model_Observer {
	public function secureHash($observer) {
		$event = $observer->getEvent ();
		$cid = $event->getCid ()->getCustomId ();
		$vid=$event->getCid ()->getVendorId ();
		$now = new Zend_Date ( Mage::getModel ( 'core/date' )->timestamp (), Zend_Date::TIMESTAMP );
		$now->set ( strtotime ( '+30 days', $now->toString ( Zend_Date::TIMESTAMP ) ), Zend_Date::TIMESTAMP );
		$model = Mage::getModel ( 'vendorapi/vendorhash' )->getCollection ()->addFieldToFilter ( 'customer_id', $cid )->addFieldToFilter ( 'vendor_id', $vid )->getFirstItem ();
		
		if (!$model->getId ()) {
			try {
				$hash = Mage::helper ( 'core' )->uniqHash ();
				$event->getCid ()->setHash ( $hash );
				$models = Mage::getModel ( 'vendorapi/vendorhash' );
				$models->setData ( 'customer_id', $cid )->setData('vendor_id',$vid)->setData ( 'secure_hash', $hash )->setData ( 'expiry_date', $now );
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
	public function sendOrderNotification($observer){
		$enable = Mage::getStoreConfig('vendorapi/integrate/enable');
		if($enable){
			$order = $observer->getEvent ()->getOrder();	
			if($order){
				$vorders = Mage::getModel('csmarketplace/vorders')->getCollection()->addFieldToFilter('order_id',array('eq'=>$order->getIncrementId()))->getFirstItem();
				if (count($vorders) > 0){
		            try {
		                $fields = array();
		                $model = Mage::getModel('vendorapi/vendordevices')->getCollection()->addFieldToFilter('vendor_id',$vorders->getVendorId());
		                $orderedproduct=array();
		                $vorder = Mage::getModel('csmarketplace/vorders')->load($order->getId());
	 					$orderItems = $vorders->getItemsCollection();
	        			foreach ($orderItems as $item) {
	        				$orderedproduct[] =  $item->getName();
	        			}
	        			$product = implode(',', $orderedproduct);
		                if(count($model)){
		                    $fields['data']=array(
		                                           'title'=>'#NEW ORDER',
		                                           'message'=>'Order For Your Product '.$product.' Has Been Placed with order id #'.$order->getIncrementId(),
		                                           'created_at'=> $order->getCreatedAt(),
		                                           'link_type'=> 'order',
		                                           'vendor_id'=>$vorders->getVendorId(),
	                                           	   'link_id' =>$order->getIncrementId()
		                                          );
		                }
		                if(count($model)){
		                 	foreach ($model as $key => $value) {
		                 	
			    				$fields['to'] = $value->getDeviceId();
			                    $result=$this->sendPushNotification($fields);  
			                    $result=json_decode($result,true);
			                    if(isset($result['success']) && $result['success']=='1'){
			                         	//Mage::log('message sent successfully',null,'notisentsuccess.log');
			                    
			                    }
		                	}
		                }
		            } catch (Exception $e) {
		               //Mage::log($e->getMessage(),null,'notisentsuccess.log');
		            }
		        }
		    }
		}
	}
	protected function sendPushNotification($fields) {
        $url = 'https://gcm-http.googleapis.com/gcm/send';
        $GOOGLE_API_KEY  = Mage::getStoreConfig('vendorapi/integrate/secretapi');
        $headers = array(
            'Authorization: key=' . $GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
   
        curl_close($ch);
 
        return $result;
    }
}
?>
