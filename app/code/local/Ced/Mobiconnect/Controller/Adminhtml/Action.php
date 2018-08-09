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
abstract class Ced_Mobiconnect_Controller_Adminhtml_Action extends Mage_Adminhtml_Controller_Action {
	protected $_data;
	
	public function preDispatch() {
		parent::preDispatch();
		$modules = Mage::helper('mobiconnect')->getCedCommerceExtensions(false,true);
		$args = '';
		foreach ($modules as $moduleName=>$releaseVersion){
			$moduleProductName = isset($releaseVersion['parent_product_name']) ? $releaseVersion['parent_product_name'] : $moduleName;
			$releaseVersion = isset($releaseVersion['release_version']) ? $releaseVersion['release_version'] : $releaseVersion;
			$m = strtolower($moduleName);
			if(!preg_match('/ced/i',$m)){ 
				return $this; 
			}  
			$h = Mage::getStoreConfig(Ced_Mobiconnect_Block_Extensions::HASH_PATH_PREFIX.$m.'_hash'); 
			for($i=1;$i<=(int)Mage::getStoreConfig(Ced_Mobiconnect_Block_Extensions::HASH_PATH_PREFIX.$m.'_level');$i++){
				$h = base64_decode($h);
			}
			$h = json_decode($h,true); 
			if(is_array($h) && isset($h['domain']) && isset($h['module_name']) && isset($h['license']) && $h['module_name'] == $m && $h['license'] == Mage::getStoreConfig(Ced_Mobiconnect_Block_Extensions::HASH_PATH_PREFIX.$m)){
			}else{ 
				$args .= $moduleProductName.',';
			}	
		}
		if(strlen($args)>0){
			//echo 'dfsfsdf';die;
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mobiconnect')->__('Please Fill License Information'));
		}
		$apikey = Mage::getStoreConfig ( "mobiconnect/general/key" );
		if(strlen($apikey)<1){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mobiconnect')->__('Please Fill Secret Key which you get during App Generation Process'));
		}
	}
}

