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
class Ced_MobiconnectAdvCart_Model_Homecontent extends Ced_Mobiconnect_Model_Homecontent {
	public $_limit;
	public $_width = 250;
	public $_height = 250;
	public function _construct() {
		parent::_construct ();
		$this->_limit = Mage::helper ( 'mobiconnect' )->getProductLimit ();
	}
	/**
	 * Show Banner On Home Page
	 * 
	 * @return
	 *
	 */
	public function showSimpleBanner($data) {
		$banners = $this->getSimpleBanners ($data);
		if(count($banners)>0)
		{
			$data = array (
					'data' => array (
							'banner' => $banners,
							'status' => 'enabled' 
					) 
			);
			return $data;
		}
		else
		{
			$data= array(
					'data'=>array(
							'status'=>'no_product'
					)
			);
			return $data;
		}		
		
	}
	
	/**
	 * Get Banner Collection On Home Page
	 * 
	 * @return banner collection
	 */
	public function getSimpleBanners($data) {
		$store_id = array (
				'0' => '0' 
		);
		$store_id [] = $data['storeId'];
		foreach ( $store_id as $value ) {
			$banners = Mage::getModel ( 'mobiconnectadvcart/layouts' )->getCollection ()->addFieldToFilter ( 'status', 'Enabled' )->addFieldToFilter ( 'layout_type', $data['type'] )->setPageSize ( '1' )->setOrder ( 'priority', 'DESC' )->setOrder ( 'id', 'DESC' )->addFieldToFilter ( 'store', array (
							'finset' => $value 
					) );
			foreach ( $banners as $data ) {
				$bannerImage = $data ['banner_image'];
			}
			
			$bannerImages = explode (',', $bannerImage );
			
			$banner = array ();
			foreach ( $bannerImages as $key => $val ) {
				$model = Mage::getModel ( 'mobiconnect/banner' )->getCollection ()->addFieldToFilter ( 'status', '1' )->addFieldToFilter ( 'id', $val );
				
				foreach ( $model as $key => $val ) {
					// $model=Mage::getModel('mobiconnect/banner')->getCollection()->addFieldToFilter('status','1')->addFieldToFilter('id',$val);
					if ($val ['show_in'] == '1') {
						$banner [] = array (
								'id' => $val ['id'],
								'title' => $val ['title'],
								'banner_image' => Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_WEB, true ) . 'media/Banners/images/' . $val ['image'],
								'link_to' => 'product',
								'product_id' => $val ['product_id'],
								'type' => $type 
						);
					} else if ($val ['show_in'] == '2') {
						$banner [] = array (
								'id' => $val ['id'],
								'title' => $val ['title'],
								'banner_image' => Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_WEB, true ) . 'media/Banners/images/' . $val ['image'],
								'link_to' => 'category',
								'product_id' => $val ['category_id'],
								'type' => $type 
						);
					} else {
						$banner [] = array (
								'id' => $val ['id'],
								'title' => $val ['title'],
								'banner_image' => Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_WEB, true ) . 'media/Banners/images/' . $val ['image'],
								'link_to' => 'website',
								'product_id' => $val ['banner_url'],
								'type' => $type 
						);
					}
				}
			}
		}
		return $banner;
	}


	public function showUltimateBanner($data) {

		$banners = $this->getUltimateBanners($data);
		if(count($banners)>0)
		{
			$data = array (
					'data' => array (
							'homepage_ultimate' => array($banners),
							'status' => 'enabled' 
					) 
			);
			return $data;
		}
		else
		{
			$data= array(
					'data'=>array(
							'status'=>'no_product'
					)
			);
			return $data;
		}		
		
	}
	public function getUltimateBanners($data) { 
		$store_id = array (
				'0' => '0' 
		);
		$store_id [] = $data['storeId'];
		foreach ( $store_id as $value ) {
			$banners = Mage::getModel ( 'mobiconnectadvcart/layouts' )->getCollection ()->addFieldToFilter ( 'status', 'Enabled' )->addFieldToFilter ( 'layout_type', $data['type'] )->setPageSize ( '1' )->setOrder ( 'priority', 'DESC' )->setOrder ( 'id', 'DESC' );
			foreach ( $banners as $data ) {
				$bannerImage = $data ['banner_image'];
			}
			
			$bannerImages = explode (',', $bannerImage );
			$count=1;
			$banner = array ();
			foreach ( $bannerImages as $key => $val ) {
				$model = Mage::getModel ( 'mobiconnect/banner' )->getCollection ()->addFieldToFilter ( 'status', '1' )->addFieldToFilter ( 'id', $val );
				
				foreach ( $model as $key => $val ) {
					// $model=Mage::getModel('mobiconnect/banner')->getCollection()->addFieldToFilter('status','1')->addFieldToFilter('id',$val);
					if ($val ['show_in'] == '1') {
						$banner ['images'.$count] =array( array (
								'id' => $val ['id'],
								'title' => $val ['title'],
								'banner_image' => Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_WEB, true ) . 'media/Banners/images/' . $val ['image'],
								'link_to' => 'product',
								'product_id' => $val ['product_id'],
								'type' => $type 
						));
					} else if ($val ['show_in'] == '2') {
						$banner ['images'.$count] =array( array (
								'id' => $val ['id'],
								'title' => $val ['title'],
								'banner_image' => Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_WEB, true ) . 'media/Banners/images/' . $val ['image'],
								'link_to' => 'category',
								'product_id' => $val ['category_id'],
								'type' => $type 
						));
					} else {
						$banner ['images'.$count] =array( array (
								'id' => $val ['id'],
								'title' => $val ['title'],
								'banner_image' => Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_WEB, true ) . 'media/Banners/images/' . $val ['image'],
								'link_to' => 'website',
								'product_id' => $val ['banner_url'],
								'type' => $type 
						));
					}

				}
				$count++;
					if($count>6)
						break;
			}
		}
		return $banner;
	}

	public function showNewArrival() { 
		if (Mage::helper ( 'mobiconnect' )->showNewArrival ()) {
			$newArrivals = $this->getNewArrival ();
			if(count($newArrivals)>0)
			{
				$data = array (
						'data' => array (
								'new_arrival' => $newArrivals,
								'status' => 'enabled' 
						) 
				);
				return $data;
			}
			else
			{
				$data= array(
						'data'=>array(
								'status'=>'no_product'
						)
				);
				return $data;
			}
		} else {
			$data = array (
					'data' => array (
							'message' => 'success',
							'status' => 'disabled' 
					) 
			);
			return $data;
		}
	}
	/**
	 * Show Most Viewed On Home Page
	 * 
	 * @return
	 *
	 */
	public function showMostView() {
		if (Mage::helper ( 'mobiconnect' )->showMostView ()) {
			$mostViewed = $this->getMostView ();
			if(count($mostViewed)>0)
			{
				$data = array (
						'data' => array (
								'most_view' => $mostViewed,
								'status' => 'enabled' 
						) 
				);
				return $data;
			}
			else
			{
				$data= array(
						'data'=>array(
								'status'=>'no_product'
						)
				);
				return $data;
			}
		} else {
			$data = array (
					'data' => array (
							'message' => 'success',
							'status' => 'disabled' 
					) 
			);
			return $data;
		}
	}
	/**
	 * Show Best Selling On Home Page
	 * 
	 * @return
	 *
	 */
	public function showBestSelling() {
		if (Mage::helper ( 'mobiconnect' )->showBestSeller ()) {
			$bestSelling = $this->getBestSeller ();
			if(count($bestSelling)>0)
			{
				$data = array (
						'data' => array (
								'best_selling' => $bestSelling,
								'status' => 'enabled' 
						) 
				);
				return $data;
			}
			else
			{
				$data= array(
						'data'=>array(
								'status'=>'no_product'
						)
				);
				return $data;
			}
		} else {
			$data = array (
					'data' => array (
							'message' => 'success',
							'status' => 'disabled' 
					) 
			);
			return $data;
		}
	}
	/**
	 * Get Banner Collection On Home Page
	 * 
	 * @return banner collection
	 */
	/**
	 * Get Most View Collection
	 * 
	 * @return most view collection
	 */
	public function getMostView() {
		$baseCurrency = Mage::app ()->getStore ()->getDefaultCurrencyCode ();
		$currency_symbol = Mage::app ()->getLocale ()->currency ( $baseCurrency )->getSymbol ();
		$products = Mage::getResourceModel ( 'reports/product_collection' )->addAttributeToSelect ( '*' )->addViewsCount ()->setPageSize ( $this->_limit );	
		Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $products );
		Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInCatalogFilterToCollection ( $products );
		$mostViewed = array ();
		foreach ( $products as $_product ) {
			$_prod=Mage::getModel('catalog/product')->load($_product->getId()) ;
				$mostViewed [] = array (
					'product_id' => $_prod->getId (),
					'product_name' => $_prod->getName (),
					'product_price' => Mage::helper('core')->currency(Mage::helper ( 'tax' )->getPrice ( $_prod, $_prod->getFinalPrice ()),true,false),
					'product_image' => Mage::helper ( 'catalog/image' )->init ( $_prod, 'small_image' )->__toString () 
				);	
			
		}
		
		return $mostViewed;
	}
	/**
	 * Getting New Arrival Collection
	 * 
	 * @return New Arrival Collection
	 */
	public function getNewArrival() {
		
		$baseCurrency = Mage::app ()->getStore ()->getDefaultCurrencyCode ();
		$currency_symbol = Mage::app ()->getLocale ()->currency ( $baseCurrency )->getSymbol ();
		$todayDate = Mage::app ()->getLocale ()->date ()->toString ( Varien_Date::DATETIME_INTERNAL_FORMAT );
		
		$collection = Mage::getResourceModel ( 'catalog/product_collection' );
		$collection->setVisibility ( Mage::getSingleton ( 'catalog/product_visibility' )->getVisibleInCatalogIds () );
		
		$collection->addStoreFilter ()->addAttributeToSelect ( '*' )->addAttributeToSelect ( array (
				'name',
				'price',
				'small_image' 
		) )->addAttributeToFilter ( 'news_from_date', array (
				'date' => true,
				'to' => $todayDate 
		) )->addAttributeToFilter ( 'news_to_date', array (
				'or' => array (
						0 => array (
								'date' => true,
								'from' => $todayDate 
						),
						1 => array (
								'is' => new Zend_Db_Expr ( 'null' ) 
						) 
				) 
		), 'left' )->addAttributeToSort ( 'news_from_date', 'desc' )->setPageSize ( $this->_limit );
		;
		$newlyArrived = array ();
		foreach ( $collection as $_product ) {
			 //var_dump($_product->getData());die;
			$newlyArrived [] = array (
					'product_id' => $_product->getId (),
					'product_name' => $_product->getName (),
					'product_price' => Mage::helper('core')->currency(Mage::helper ( 'tax' )->getPrice ( $_product, $_product->getFinalPrice ()),true,false),
					'product_image' => Mage::helper ( 'catalog/image' )->init ( $_product, 'small_image' )->__toString () 
			)
			;
		}
		
		return $newlyArrived;
	}
	/**
	 * Getting Best Selling Product Colllection
	 * 
	 * @return Best Selling Product Colllection
	 */
	public function getBestSeller() {
		
		$baseCurrency = Mage::app ()->getStore ()->getDefaultCurrencyCode ();
		$currency_symbol = Mage::app ()->getLocale ()->currency ( $baseCurrency )->getSymbol ();
		$storeId = Mage::app ()->getStore ()->getId ();
		$products = Mage::getResourceModel ( 'reports/product_collection' )->addOrderedQty ()->setOrder ( 'ordered_qty', 'desc' )->setPageSize ( $this->_limit );
		; // most best sellers on top
		Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $products );
		Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInCatalogFilterToCollection ( $products );
		// $products=$products->getData();
		// var_dump($products->getData());die;
		$bestSelling = array ();
		foreach ( $products as $_product ) {
			// var_dump($_product->getData());die;
			/*$mostViewed [] = array (
					'product_id' => $_product->getId (),
					'product_name' => $_product->getName (),
					'product_price' => $currency_symbol . $_product->getFinalPrice (),
					'product_image' => Mage::helper ( 'catalog/image' )->init ( $_product, 'small_image' )->constrainOnly ( TRUE )->keepAspectRatio ( TRUE )->keepFrame ( FALSE )->resize ( $this->_width, $this->_height )->__toString () 
			)
			;*/
			$productIds[]=$_product->getId ();
		}

		$_products=Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect ( '*' )->addidFilter($productIds);

		foreach ( $_products as $_prod ) {
			// var_dump($_product->getData());die;

			$bestSelling [] = array (
					'product_id' => $_prod->getId (),
					'product_name' => $_prod->getName (),
					'product_price' => Mage::helper('core')->currency(Mage::helper ( 'tax' )->getPrice ( $_prod, $_prod->getFinalPrice ()),true,false),
					'product_image' => Mage::helper ( 'catalog/image' )->init ( $_prod, 'small_image' )->__toString ()
			)
			;
		}
		
		return $bestSelling;
	}
	public function showFeaturedProduct() {
		$baseCurrency = Mage::app ()->getStore ()->getDefaultCurrencyCode ();
		$currency_symbol = Mage::app ()->getLocale ()->currency ( $baseCurrency )->getSymbol ();
		$entity = 'catalog_product';
		$code = 'featured';
		$attr = Mage::getResourceModel('catalog/eav_attribute')
   		 ->loadByCode($entity,$code);
		if ($attr->getId()) {
			$attr = Mage::getModel('catalog/resource_eav_attribute')->load($attr->getId());
    		if ($attr->usesSource()) {
				$attributeOptions = $attr ->getSource()->getAllOptions();
			    foreach ($attributeOptions as $option) {
        			if($option['label']=='Yes'){
           				$curattributeid=$option['value'];
        			}
      			}

    		}
		}
		$collection = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToSelect ( '*' )->setPageSize ( $this->_limit );
		
		$collection->addFieldToFilter ( array (
				array (
						'attribute' => 'featured',
						'eq' => $curattributeid 
				) 
		) );
		foreach ( $collection as $_product ) {
			$featuredProduct [] = array (
					'product_id' => $_product->getId (),
					'product_name' => $_product->getName (),
					'product_price' =>  Mage::helper('core')->currency(Mage::helper ( 'tax' )->getPrice ( $_product, $_product->getFinalPrice ()),true,false),
					'product_image' => Mage::helper ( 'catalog/image' )->init ( $_product, 'small_image' )->__toString () 
			)
			;
		}
		
			if(count($featuredProduct)>0)
			{ 
				$data = array (
						'data' => array (
								'featured_products' => $featuredProduct,
								'status' => 'enabled' 
						) 
				);
				return $data;
			}
			else
			{ 
				$data= array(
						'data'=>array(
								'status'=>'no_product'
						)
				);
				return $data;
			}
	}
	public function getStoreAddress(){
		$country_code=Mage::getStoreConfig('general/store_information/merchant_country');
		$country_name=Mage::app()->getLocale()->getCountryTranslation($country_code);
		$address=array(
			'store_address'=>nl2br(Mage::getStoreConfig('general/store_information/address')).' '.$country_name,
			'store_contact'=>Mage::getStoreConfig('general/store_information/phone'),
			'store_name'=>Mage::getStoreConfig('general/store_information/name'),
			'hours_operating'=>Mage::getStoreConfig('general/store_information/hours')
			);
		if($address['store_name']=='' || strlen($address['store_address'])<2){
			$data = array (
						'data' => array (
								'address' => $address,
								'status' => 'false' 
					) 
			);
			return $data;
		}
			$data = array (
						'data' => array (
								'address' => $address,
								'status' => 'success' 
					) 
			);
			return $data;
   	}
	
}
?>
