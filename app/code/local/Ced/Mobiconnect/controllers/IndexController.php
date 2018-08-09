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
class Ced_Mobiconnect_IndexController extends Ced_Mobiconnect_Controller_Action {
	
	/*
	 * Home Page banner Request Action
	 * 
	 */
	public function getHomePageBannerAction() { 
		$storeId=$this->getRequest()->getParam('store_id');
		$banner = Mage::getModel ( 'mobiconnect/Homecontent' )->showBanner ($storeId);
		$this->printJsonData ( $banner );
	}
	/*
	 * Home Page New Arrival Product Request Action
	 * @Return New arrived Data As Array
	 */
	public function getHomePageNewArrivalAction() {
		$newArrival = Mage::getModel ( 'mobiconnect/Homecontent' )->showNewArrival ();
		$this->printJsonData ( $newArrival );
	}
	/*
	 * Home Page Most Viewed Product Request Action @Return Most Viewed Product Data As Array
	 */
	public function getHomePageMostViewAction() {
		$mostViewed = Mage::getModel ( 'mobiconnect/Homecontent' )->showMostView ();
		$this->printJsonData ( $mostViewed );
	}
	/*
	 * Home Page Best Selling Product Request Action @Return Best Selling Product Data As Array
	 */
	public function getHomePageBestSellingAction() {
		$bestSelling = Mage::getModel ( 'mobiconnect/Homecontent' )->showBestSelling ();
		$this->printJsonData ( $bestSelling );
	}
	/*
	 * Home Page Featured Product Request Action @Return Featured Product Data As Array
	 */
	public function getHomePageFeaturedProductAction() {
		$featured = Mage::getModel ( 'mobiconnect/Homecontent' )->showFeaturedProduct ();
		$this->printJsonData($featured);
	}
	/*
	 * Home Page Featured Product Request Action @Return Featured Product Data As Array
	 */
	public function getCountryAction() {
		$country_code=$this->getRequest()->getParam('country_code','');
		$getCountry = Mage::getModel ( 'mobiconnect/Homecontent' )->getCountry($country_code);
		$this->printJsonData($getCountry);
	}
	public function testAction(){
		$product= Mage::getModel('catalog/product')->load('908');
		if($product->getData('has_options'))
		{
			$customOption=array();
			$count=0;
			foreach($product->getOptions() as $o){
				echo '<pre>';//print_r($o->getData());
				$optionType = $o->getType();
			    if ($optionType == 'drop_down') {
			        $values = $o->getValues();
			        $customOption[]=$o->getData();
			        $innercount=0;
			        foreach ($values as $v) {
			       $customOption[$count]['option'][$innercount]=$v->getData();
			        //print_r($v->getData());echo '<hr>';
			       ++$innercount;
			        }
			    }else{
			    	$customOption[]=$o->getData();
			        //print_r($o->getData());echo '<hr>';
			    }
			    ++$count;
			}
		}
		if(count($customOption)>0){
			$data=array(
				'data'=>array(
					'custom_option'=>$customOption,
					'success'=>true,

				)

			);
		}
		else{
			$data=array(
				'data'=>array(
					'custom_option'=>'',
					'success'=>false,
					//'message'=>'The product does not have any custom option'

				)

			);
		}
		$response=json_encode($data);
		$this->getResponse()->setBody($response);
	}

}

