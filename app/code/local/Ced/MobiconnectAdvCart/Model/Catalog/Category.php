<?php
class Ced_MobiconnectAdvCart_Model_Catalog_Category extends Mage_Core_Model_Abstract {
	protected $_width = 135;
	protected $_height = 135;
	protected $_limit;
	protected $suggestedlimit;
	public function _construct() {
		parent::_construct ();
		$this->_limit = Mage::helper ( 'mobiconnect' )->getProductLimit ();
		$this->_suggestedlimit= Mage::helper('mobiconnectadvcart')->getSuggestionLimit();
	}
	/**
	 *
	 * @param unknown $id        	
	 * @return return level 2 categories
	 */
	public function getCategories($id) {
		if (! $id) { 
			
			$categories = Mage::getModel ( 'catalog/category' )->getCollection ()->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'level', '2' )->addAttributeToFilter ( 'is_active', '1' );

			if ($categories) { 
				$category = $this->getMainCategories ( $categories );

				$data = array (
						'data' => array (
								'categories' => $category 
						),
						'status' => 'success' 
				);
				if(Mage::getStoreConfig('mobiconnect/banners/category_banner')){
					$banner=$this->getBanner();
					if(count($banner)>0)
					{	
						$data['data']['bannerstatus']='ENABLED';
						$data['data']['banner']=$banner;
						$data['data']['show']=$banner[0]['show_on'];
					}
					else{
						$data['data']['bannerstatus']='DISABLED';
						$data['data']['messsage']="You don't have any banner yet";
					}
				}
				else{
					$data['data']['bannerstatus']='DISABLED';
				}
				return $data;
			}
			else{
				$data = array (
						'data' => array (
								'categories' => 'You dont have any categories' 
						),
						'status' => 'success' 
				);
				$banner=$this->getBanner();
				if(count($banner)>0)
				{
					$data['data']['bannerstatus']='ENABLED';
					$data['data']['banner']=$banner;
					$data['data']['show']=$banner[0]['show_on'];
				}
				else{
					$data['data']['bannerstatus']='DISABLED';
				}
				return $data;
			}
		} else {

			$categories = Mage::getModel ( 'catalog/category' )->load ( $id );
			$subCategories = $categories->getChildrenCategories ();
			
			$category = $this->getSubCategories ( $subCategories, $id );
			// var_dump($categories->getData());die;
			return $category;
		}
	}
	/**
	 *
	 * @param unknown $categories        	
	 * @return cateogry info array
	 */
	public function getMainCategories($categories) {
		foreach ( $categories as $cat ) {
			$appimg=false;
			if($cat->getAppImage()!='NULL' && $cat->getAppImage()!=''){
				$appimg=Mage::getBaseUrl('media').'catalog/category/'.$cat->getAppImage();
			}
			$products = $cat->getProductCollection ()->addAttributeToSelect ( '*' )->setOrder ( 'price', 'ASC' );
			Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $products );
			Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInSearchFilterToCollection ( $products );
			$showName = Mage::getStoreConfig('mobiconnectadvcart/advancecart/category_name');
			if(!$appimg)
				$showName=1;
			$itemcount=count($products);
			if (! $cat->hasChildren ()) {
				$categdata [] = array (
						'category_id' => $cat->getId (),
						'category_name' => ($showName=='1')?$cat->getName ():'',
						'category_image' => $appimg,
						'product_count'=>($showName=='1')?$itemcount.' Items':'',
						'has_child' => 'NO' 
				);
			} else {
				$categdata [] = array (
						'category_id' => $cat->getId (),
						'category_name' => ($showName=='1')?$cat->getName ():'',
						'category_image' => $appimg,
						'product_count'=>($showName=='1')?$itemcount.' Items':'',
						'has_child' => 'YES' 
				);
			}
		}
		return $categdata;
	}
	/**
	 *
	 * @param unknown $categories        	
	 * @param unknown $id        	
	 * @return subcategories info array
	 */
	public function getSubCategories($categories, $id) {
		$baseCurrency = Mage::app ()->getStore ()->getDefaultCurrencyCode ();
		$currency_symbol = Mage::app ()->getLocale ()->currency ( $baseCurrency )->getSymbol ();
		foreach ( $categories as $cat ) {
			//var_dump($cat->getData());
			$categories = Mage::getModel ( 'catalog/category' )->getCollection ()->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'entity_id', $cat->getId () )->addAttributeToFilter ( 'is_active', '1' );
			foreach ($categories as $val) {
				
			$appimg=false;
			if($val->getAppImage()!='NULL' && $val->getAppImage()!=''){
				$appimg=Mage::getBaseUrl('media').'catalog/category/'.$val->getAppImage();
			}
			}
			$products = $cat->getProductCollection ()->addAttributeToSelect ( '*' )->setOrder ( 'price', 'ASC' );
			Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $products );
			Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInSearchFilterToCollection ( $products );
			$showName = Mage::getStoreConfig('mobiconnectadvcart/advancecart/category_name');
			if(!$appimg)
				$showName=1;
			$itemcount=count($products);
			if (! $cat->hasChildren ()) {
				$categdata [] = array (
						'category_id' => $cat->getId (),
						'category_name' => ($showName=='1')?$cat->getName ():'',
						'category_image' => $appimg,
						'product_count'=>($showName=='1')?$itemcount.' Items':'',
						'has_child' => 'NO' 
				);
			} else {
				$categdata [] = array (
						'category_id' => $cat->getId (),
						'category_name' => ($showName=='1')?$cat->getName ():'',
						'category_image' => $appimg,
						'product_count'=>($showName=='1')?$itemcount.' Items':'',
						'has_child' => 'YES' 
				);
			}
		}
		$category = Mage::getModel ( 'catalog/category' )->load ( $id );
		$bannerimg=false;
		if($category->getBannerImage()!='NULL' && $category->getBannerImage()!=''){
			$bannerimg=Mage::getBaseUrl('media').'catalog/category/'.$category->getBannerImage();
		}
		$products = $category->getProductCollection ()->addAttributeToSelect ( '*' )->setOrder ( 'price', 'ASC' )->setPageSize ( $this->_limit );
	
		Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $products );
			Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInSearchFilterToCollection ( $products );
		
		foreach ( $products as $_product ) {
			
			$productData [] = array (
					'product_id' => $_product->getId (),
					'product_name' => $_product->getName (),
					'product_image' => Mage::helper ( 'catalog/image' )->init ( $_product, 'small_image' )->__toString (),
					'product_price' => Mage::helper('core')->currency(Mage::helper ( 'tax' )->getPrice ( $_product, $_product->getFinalPrice()),true,false),
					
			);
		}
	

		$data = array (
				'data' => array (
						'subcategory' => $categdata,
						'product' => $productData,
						'status' => 'success' 
				) 
		);
		if(count($productData)>0){
			$data['data']['havingproduct']=array('IN');
		}
		else{
			$data['data']['category_name'] = $category->getName();
			$data['data']['category_banner_image'] = $bannerimg;
			$data['data']['has_category_image'] = ($bannerimg)?'Yes':'No';
			$data['data']['havingproduct']=array('OUT');
		}
		return $data;
	}
	public function getBanner(){
		$store_id = array (
				'0' => '0' 
		);
		$store_id [] = $storeid;
		foreach ( $store_id as $value ) {
			$banners = Mage::getModel ( 'mobiconnectadvcart/categorybanner' )->getCollection ()->addFieldToFilter ( 'banner_status', '1' )->setPageSize ( '1' )->setOrder ( 'priority', 'DESC' )->setOrder ( 'id', 'DESC' )->addFieldToFilter ( 'store', array (
							'finset' => $value 
					) );
				 
			foreach ( $banners as $data ) {
				$bannerImage = $data ['banner_image'];
				$showon=$data ['show_on'];
			}
				
			$bannerImages = explode(',',$bannerImage);
			$banner = array ();
			foreach ( $bannerImages as $key => $val ) {
				$model = Mage::getModel ( 'mobiconnect/banner' )->getCollection ()->addFieldToFilter ( 'status', '1' )->addFieldToFilter ( 'id', $val );
				// var_dump($model->getData());die;
				foreach ( $model as $key => $val ) {
					// $model=Mage::getModel('mobiconnect/banner')->getCollection()->addFieldToFilter('status','1')->addFieldToFilter('id',$val);
					if ($val ['show_in'] == '1') {
						$banner [] = array (
							'id' => $val ['id'],
							'title' => $val ['title'],
							'banner_image' => Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_WEB, true ) . 'media/Banners/images/' . $val ['image'],
							'link_to' => 'product',
							'type' => '',
							'product_id' => $val ['product_id'],	
							'show_on'=>$showon	
						);
					} else if ($val ['show_in'] == '2') {
						$banner [] = array (
							'id' => $val ['id'],
							'title' => $val ['title'],
							'banner_image' => Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_WEB, true ) . 'media/Banners/images/' . $val ['image'],
							'link_to' => 'category',
							'type' => '',
							'product_id' => $val ['category_id'],	
							'show_on'=>$showon		
						);
					} else {
						$banner [] = array (
							'id' => $val ['id'],
							'title' => $val ['title'],
							'banner_image' => Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_WEB, true ) . 'media/Banners/images/' . $val ['image'],
							'link_to' => 'website',
							'type' => '',
							'product_id' => $val ['banner_url'],
							'show_on'=>$showon	
								
						);
					}
				}
			}
		}
		return $banner;
	}
	public function getSuggestion($key){
		$enable=Mage::getStoreConfig ( "mobiconnectadvcart/advancecart/autocomplete_enable" );
		if($enable){
			$result = array();
			$storeId    = Mage::app()->getStore()->getId();
			$products = Mage::getModel('catalog/product')->getCollection()
				->addAttributeToSelect('*')		
				->setStoreId($storeId)
				->addStoreFilter($storeId)
				->addFieldToFilter("status",1)	
				->addFieldToFilter('name',array('like'=>'%'. $key.'%'))	
				->setPageSize ( $this->_suggestedlimit )
				->setOrder('name','ASC');

			Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($products);
			Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($products);
			$products->load();
			if(count($products)){
	  			foreach($products as $product){
	    			$result[] = array(
	    				'product_id'=>$product->getId(),
	    				'product_name'=>$product->getName(),
	    			);
					}
			}
			if(count($result)>0){
				$data = array (
						'data' => array (
								'suggestion' => $result,
								'enabled'=>true,
								'status' => 'success' 
						) 
				);
			}
			else{
				$data = array (
						'data' => array (
								'suggestion' => 'NoSuggestion',
								'enabled'=>true,
								'status' => 'empty' 
						) 
				);
			}
		}
		else{ 
			$data = array (
						'data' => array (
								'enabled'=>false,
								'status' => 'success' 
						) 
				);
		}
		return $data;

	}
}

?>
