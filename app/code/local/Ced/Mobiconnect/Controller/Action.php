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
abstract class Ced_Mobiconnect_Controller_Action extends Mage_Core_Controller_Front_Action {
	protected $_data;
	
	
	public function preDispatch() 
	{
	   parent::preDispatch();
	   $enable = (int) Mage::getStoreConfig("mobiconnect/general/enable"); 
	  
	   	if (!$enable) { 
	    	echo 'Mobiconnect was disabled!'; header("HTTP/1.0 503"); exit();
	    } 

	    /*user agent implementaion start */

	    /*$checkMobile = false;
	    $isMobile = Zend_Http_UserAgent_Mobile::match(Mage::helper('core/http')->getHttpUserAgent(),$_SERVER);
	    if($isMobile)
	    	$checkMobile = true;
	    
	    $developer = $this->getRequest()->getParam('developer',0);
	    if(!$developer){
	    	if (!$this->isHeaderSet() || !$checkMobile) {
		    	$data = array(
		    		'header'=>'false'
		    		);
		    	$this->printJsonData($data);
	    	}
	    }*/
	    /*user agent implementaion end */

	    /*  if (!$this->isHeaderSet()) { 
	    	$data=array(
	    		'header'=>'false'
	    		);
	    	$this->printJsonData($data);
	    }  */

	    $store_id=$this->getRequest()->getParam('store_id',Mage::app()->getStore()->getId());
	    if(is_numeric($store_id)){
			Mage::app()->setCurrentStore($store_id);
	    }
	}
	public function convertDataToJson($data) {
		return Mage::helper ( 'core' )->jsonEncode ( $data );
	}
	
	public function printJsonData($data) 
	{
		ob_start ();
		$data = $this->convertDataToJson ( $data );
		echo strip_tags ( $data );
		exit ();
		ob_end_flush ();
	}
	public function printHtmlJsonData($data) 
	{
		ob_start ();
		$data = $this->convertDataToJson ( $data );
		echo $data;
		exit ();
		ob_end_flush ();
	}

	public function isHeaderSet() 
	{
		if (!function_exists('getallheaders')) { 
	        foreach($_SERVER as $key=>$value) { 
	            if (substr($key,0,5)=="HTTP_") { 
	                $key=str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5))))); 
	                $head[$key]=$value; 
	            } else { 
	                $head[$key]=$value; 
	        	} 
            } 
        } 

        /*Source Github Darkheir*/
        else { 
			$head = getallheaders ();
		} 
		$token ='';	
		foreach ( $head as $key => $val ) {
			if ($key == "Mobiconnectheader" || $key == "MobiconnectHeader") {
				$token = $val;
			}
		}	
		$secretKey = Mage::getStoreConfig ( "mobiconnect/general/key" );
		if ($token == $secretKey)
			return true;
		else
			return false;
	}
	
	public function matchPass($data) 
	{
		if ($data ['password'] == $data ['conformpass'])
			return true;
		return false;
	}
	public function validate($data)
	{
	
		$customerId=$data['customer'];	
		$hash=$data['hash'];
		if(isset($customerId) && isset($hash)){
		
		$model=Mage::getModel('mobiconnect/customerhash')->getCollection()->addFieldToFilter('customer_id',$customerId)->getFirstItem();
		
		$secureHash= $model->getSecureHash();
		
		if($secureHash==$hash)
			return true;
		else 
		{
			$data = array (
									'data' => array (
											'customer' => array (
													array (
															'message' => 'Invalid User',
															'status' => 'Error' 
													) 
											) 
									) 
							);
					$this->printJsonData ( $data );
					die;
		}
		}
		else{
			$data = array (
					'data' => array (
							'customer' => array (
									array (
											'message' => 'Customer ID OR Hash cannt be blank',
											'status' => 'Error'
									)
							)
					)
			);
			$this->printJsonData ( $data );
			die;
		}
	}
}

?>
