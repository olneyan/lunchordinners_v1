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
 class Ced_VendorApi_Controller_Abstract extends Mage_Core_Controller_Front_Action 
 {
	public function preDispatch() 
	{
	   	parent::preDispatch();
	   	$enable = (int) Mage::getStoreConfig("vendorapi/general/enable"); 
	   	if (!$enable) { 
	    	echo 'VendorApi was disabled!'; header("HTTP/1.0 503"); exit();
	    } 

	    /*user agent implementaion start */

	    $checkMobile = false;
	    $isMobile = Zend_Http_UserAgent_Mobile::match(Mage::helper('core/http')->getHttpUserAgent(),$_SERVER);
	    if($isMobile)
	    	$checkMobile = true;
	    
	    $developer = $this->getRequest()->getParam('developer',0);
	    
	     /*  if(!$developer){
	    	if (!$this->isHeaderSet() || !$checkMobile) { 
		    	$data = array(
		    		'header'=>'false'
		    		);
		    	echo json_encode($data);exit();
	    	}
	    }  */ 
	    /*user agent implementaion end */

	    $store_id=$this->getRequest()->getParam('store_id',Mage::app()->getStore()->getId());
	    if(is_numeric($store_id)){
			Mage::app()->setCurrentStore($store_id);
	    }
	}

	public function isHeaderSet() {
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
		$secretKey = Mage::getStoreConfig ( "vendorapi/general/key" );
		if ($token == $secretKey)
			return true;
		else
			return true;
	}

	public function validate($data)
	{
		$vendorId = $data['vendor_id'];	
		$hash = $data['hash'];
		
		if(isset($vendorId) && isset($hash)){
			$model=Mage::getModel('vendorapi/vendorhash')->getCollection()->addFieldToFilter('vendor_id',$vendorId)->getFirstItem();
			$secureHash= $model->getSecureHash();
			if($secureHash==$hash){
				if(Mage::helper('csmarketplace')->authenticate($model->getCustomerId())){
					return true;
				}
				else{
					$data = array ('vendor_approved'=>false);

							echo json_encode($data);
							exit();
				}
				
			}
			else 
			{
				$data = array (
					'data' => array (
											'success' => false ,
											'message'=> 'Invalid User'
									) 
							
					
			);
			
						echo json_encode($data);
						exit();
			}
		}
		else{
			$data = array (
					'data' => array (
											'success' => false ,
											'message'=> 'Vendor id or hash key cantnot be blank'
									) 
							
					
			);
			echo json_encode($data);
			exit();
		}
	}
}

?>
