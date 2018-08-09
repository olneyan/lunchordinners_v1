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
require_once Mage::getModuleDir('controllers', 'Ced_Mobiconnect').DS.'IndexController.php';
class Ced_MobiconnectAdvCart_IndexController extends Ced_Mobiconnect_IndexController {
   	
   	public function getSimpleLayoutAction() {
   		$storeId=$this->getRequest()->getParam('store_id');
   		$data=array('type'=>'simple','storeId'=>$storeId);
		$banner = Mage::getModel ( 'mobiconnectadvcart/homecontent' )->showSimpleBanner ($data);
		$this->printJsonData ( $banner );
	}
	public function getUltimateLayoutAction(){
		$storeId=$this->getRequest()->getParam('store_id');
   		$data=array('type'=>'ultimate','storeId'=>$storeId);
		$banner = Mage::getModel ( 'mobiconnectadvcart/homecontent' )->showUltimateBanner ($data);
		$this->printJsonData ( $banner );

	}
	public function getLiteLayoutAction(){
		$storeId=$this->getRequest()->getParam('store_id');
   		$data=array('type'=>'lite','storeId'=>$storeId);
		$banner = Mage::getModel ( 'mobiconnectadvcart/homecontent' )->showSimpleBanner ($data);
		$this->printJsonData ( $banner );
	}

	public function getCoreLayoutAction(){
		$storeId=$this->getRequest()->getParam('store_id');
   		$data=array('type'=>'core','storeId'=>$storeId);
		$banner = Mage::getModel ( 'mobiconnectadvcart/homecontent' )->showUltimateBanner ($data);
		$this->printJsonData ( $banner );
	}

	public function getmoduleListAction(){
		$modules = Mage::getConfig()->getNode('modules')->children();
		$i=1;
		$activemodules=array();
		foreach ($modules as $moduleName => $moduleSettings) {
			 if($moduleName=='Ced_Mobiconnect' || 
			 	$moduleName=='Ced_Mobiconnectdeals' || 
			 	$moduleName=='Ced_MobiconnectAdvCart' || 
			 	$moduleName=='Ced_MobiconnectCms' || 
			 	$moduleName=='Ced_Mobiconnectstore' || 
			 	$moduleName=='Ced_Mobiconnectreview' || 
			 	$moduleName=='Ced_MobiconnectSocialLogin' || 
			 	$moduleName=='Ced_Mobinotification' || 
			 	$moduleName=='Ced_MobiconnectWishlist' || 
			 	$moduleName=='Ced_Mobiconnectcheckout'
			 	 ){
			 		if($moduleSettings->is('active')){
			 			$enabled=Mage::helper('mobiconnectadvcart')->isEnabled($moduleName);
			 			if($enabled)
			 			$activemodules[$moduleName]=$moduleName;
			 		}
			 }
			 
		}

		if(count($activemodules)>0){
			$data = array (
						'data' => array (
								'modules' => $activemodules, 
						) 
				);
		}
		else{
			$data = array (
						'data' => array (
								'modules' => array('no_modules'=>'no_modules')
						) 
				);
		}
		$this->printJsonData ( $data );
	}

	/*
	 * Home Page New Arrival Product Request Action
	 * @Return New arrived Data As Array
	 */
	public function getHomePageNewArrivalAction() {
	
		$newArrival = Mage::getModel ( 'mobiconnectadvcart/Homecontent' )->showNewArrival ($storeId);
		$this->printJsonData ( $newArrival );
	}
	/*
	 * Home Page Most Viewed Product Request Action @Return Most Viewed Product Data As Array
	 */
	public function getHomePageMostViewAction() {
		
		$mostViewed = Mage::getModel ( 'mobiconnectadvcart/Homecontent' )->showMostView ($storeId);
		$this->printJsonData ( $mostViewed );
	}
	/*
	 * Home Page Best Selling Product Request Action @Return Best Selling Product Data As Array
	 */
	public function getHomePageBestSellingAction() {
		
		$bestSelling = Mage::getModel ( 'mobiconnectadvcart/Homecontent' )->showBestSelling ($storeId);
		$this->printJsonData ( $bestSelling );
	}
	/*
	 * Home Page Featured Product Request Action @Return Featured Product Data As Array
	 */
	public function getHomePageFeaturedProductAction() {
		
		$featured = Mage::getModel ( 'mobiconnectadvcart/Homecontent' )->showFeaturedProduct ($storeId);
		$this->printJsonData ( $featured );
	}
	public function storeAddressAction(){
		$address = Mage::getModel ( 'mobiconnectadvcart/Homecontent' )->getStoreAddress();
		$this->printJsonData ($address);
   }
}
