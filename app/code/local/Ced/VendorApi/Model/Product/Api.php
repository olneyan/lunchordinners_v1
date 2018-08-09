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

class Ced_VendorApi_Model_Product_Api extends Mage_Api_Model_Resource_Abstract
{
	protected $_filtersMap = array(
			'product_id' => 'entity_id',
			'set'        => 'attribute_set_id',
			'type'       => 'type_id'
	);

	protected $_ignoredAttributeTypes = array();

	protected $_ignoredAttributeCodes = array();

	public function getListProduct($collection,$sort,$customerid=null) 
	{
		$baseCurrency = Mage::app ()->getStore ()->getBaseCurrencyCode ();
		$currency_symbol = Mage::app ()->getLocale ()->currency ( $baseCurrency )->getSymbol ();
		$productList = array ();
		//$customerid='138';
		$count = 0;
		if ($sort) { 
			$collection = $this->getSortedCollection ($collection,$sort );
		}
		$categ_product = array();
		foreach ( $collection as $product ) {
			
			$productId = $product->getId ();
			$avg = $this->getProductReview ($productId);
			if ($product->getSpecialPrice () != '' && $product->getSpecialPrice () < $product->getPrice ()) {
				$categ_product [] = array (
						'product_id' => $product->getId (),
						'product_name' => $product->getName (),
						'type' => $product->getTypeId (),
						'description' => $product->getShortDescription (),
						'review'=>$avg,
						//'Inwishlist'=>$iswishlist,
						'special_price' =>  Mage::helper('core')->currency(Mage::helper ( 'tax' )->getPrice ( $product, $product->getSpecialPrice ()),true,false),
						'regular_price' =>  Mage::helper('core')->currency(Mage::helper ( 'tax' )->getPrice ( $product, $product->getPrice ()),true,false),
						'stock_status' => $product->isSaleable (),
						'product_image' => Mage::helper ( 'catalog/image' )->init ( $product, 'small_image' )->__toString () 
				);
			} else {
				$categ_product [] = array (
						'product_id' => $product->getId (),
						'product_name' => $product->getName (),
						'type' => $product->getTypeId (),
						'description' => $product->getShortDescription (),
						'review'=>$avg,
						//'Inwishlist'=>$iswishlist,
						'special_price' => 'no_special',
						'regular_price' =>  Mage::helper('core')->currency(Mage::helper ( 'tax' )->getPrice ( $product, $product->getPrice ()),true,false),
						'stock_status' => $product->isSaleable (),
						'product_image' => Mage::helper ( 'catalog/image' )->init ( $product, 'small_image' )->__toString () 
				);
			}

			/*Wishlist Code Starts Here*/
			if ($customerid != 'NULL' && $customerid != '') {
				$wishlist = Mage::getModel ( 'wishlist/wishlist' )->loadByCustomer ( $customerid ); /* Need to change here for session */
				$wishlistId = $wishlist->getData ( 'wishlist_id' );
				
				// $wishListItemCollection = $wishlist->getItemCollection();
				$item = Mage::getModel ( 'wishlist/item' )->getCollection ()->addFieldToFilter ( 'wishlist_id', $wishlistId )->addFieldToFilter ( 'product_id', $productId );
				
				$item = $item->getData ();
				
				if (count ( $item ) > 0) {
					// $arrProductIds = array();
					
					/*
					 * foreach ($wishListItemCollection as $item) { $arrProductIds[] = $item->getProductId(); }
					 */
				
					$categ_product [$count] ['Inwishlist'] = array (
							'IN' 
					);
					$categ_product [$count] ['item_id'] = array (
							$item ['0'] ['wishlist_item_id'] 
					);
				} else {
					$categ_product [$count] ['Inwishlist'] = array (
							'OUT' 
					);
				}
			} else {
				
				$categ_product [$count] ['Inwishlist'] = array (
						'OUT' 
				);
			}

			/*Wishlist Code Ends Here*/
			$discounted_price = Mage::getModel ( 'catalogrule/rule' )->calcProductPriceRule ( $product->setCustomerGroupId ( 'CUSTOMER_GROUP_ID' ), $product->getPrice () );
			
			// if the product isn't discounted then default back to the original price
			if ($discounted_price != false) {
				$categ_product [$count] ['special_price'] = $currency_symbol . Mage::helper ( 'tax' )->getPrice ( $product, $discounted_price );
			}
			$tax_display_type = Mage::getStoreConfig ( 'tax/display/type', Mage::app ()->getStore () );
			
			if ($tax_display_type != 1) {
				$categ_product [$count] ['price-including-tax'] = $currency_symbol . Mage::helper ( 'tax' )->getPrice ( $product, $product->getFinalPrice () );
			} else { 
				$categ_product [$count] ['price-including-tax'] = '0';
			}
			if ($tax_display_type == 3) {
				$categ_product [$count] ['show-both-price'] = 'YES';
			} else {
				$categ_product [$count]  ['show-both-price'] = 'NO';
			}
			if($product->getTypeId()=='bundle'){ 
				$categ_product[$count] ['price_view'] = $product->getPriceView();
				$min = Mage::getModel ( 'bundle/product_price' )->getTotalPrices ( $product, 'min', 0 );
				$min = Mage::helper ( 'tax' )->getPrice ( $product, $min );
				$max = Mage::getModel ( 'bundle/product_price' )->getTotalPrices ( $product, 'max', 0 );
				$max = Mage::helper ( 'tax' )->getPrice ( $product, $max );
				$categ_product[$count] ['from_price'] = Mage::helper('core')->currency($min,true,false);
				$categ_product[$count] ['to_price'] = Mage::helper('core')->currency($max,true,false);
				
			}
			if($product->getTypeId()=='grouped'){ 
				$minimal = $product->getMinimalPrice();
				$minimal = Mage::helper ( 'tax' )->getPrice ( $product, $minimal );
				$categ_product[$count] ['starting_from'] = Mage::helper('core')->currency($minimal,true,false);
				
			}
				$count ++;
		}
		return $categ_product;
	}
	public function getSortedCollection($collection, $sort) {
		//Mage::log($sort,null,'kkooo.log');
		//Mage::log($collection->getData(),null,'kkooo.log');
		if ($sort) { 
			$collection->addAttributeToSort($sort[0], $sort[1]);
		}
		return $collection;
	}
	protected $_defaultProductAttributeList = array(
			'type_id',
			'category_ids',
			'website_ids',
			'name',
			'description',
			'short_description',
			'sku',
			'weight',
			'status',
			'url_key',
			'url_path',
			'visibility',
			'has_options',
			'gift_message_available',
			'price',
			'special_price',
			'special_from_date',
			'special_to_date',
			'tax_class_id',
			'tier_price',
			'meta_title',
			'meta_keyword',
			'meta_description',
			'custom_design',
			'custom_layout_update',
			'options_container',
			'image_label',
			'small_image_label',
			'thumbnail_label',
			'created_at',
			'updated_at'
	);
	public function getProductReview($productId) {
		$reviews = Mage::getModel ( 'review/review' )->getResourceCollection ()->addStoreFilter ( Mage::app ()->getStore ()->getId () )->addEntityFilter ( 'product', $productId )->addStatusFilter ( Mage_Review_Model_Review::STATUS_APPROVED )->setDateOrder ()->addRateVotes ();
		/**
		 * Getting average of ratings/reviews
		 */
		$avg = 0;
		$ratings = array ();
		if (count ( $reviews ) > 0) {
			foreach ( $reviews->getItems () as $review ) {
				foreach ( $review->getRatingVotes () as $vote ) {
					$ratings [] = ($vote->getPercent ()) / 20;
				}
			}
			
			$avg = array_sum ( $ratings ) / count ( $ratings );
			$avg = ($avg);
		}
		
		return $avg;
	}
	public function model()
	{
		return Mage::getResourceModel('vendorapi/vapisession');
	}
	public function vproduct($data,$filters = null, $store = null){
		
		$vendorId=$data['vendor_id'];
		$curr_page = 1;
		$offset = $data ['page'];
		$limit = 10;
		$filter=$data['filter'];
		if ($offset != 'null' && $offset != '') {
			
			$curr_page = $offset;
		}
		$offset = ($curr_page - 1) * $limit;

		$filters=json_decode($filter,true);
		if(isset($filters['check_status']) && $filters['check_status']=='Select Status'){
			$filters['check_status']='';
		}
		if(isset($filters['type_id']) && $filters['type_id']=='Select Type'){
			$filters['type_id']='';
		}

		$checkstatusarray=Mage::getModel('csmarketplace/vproducts')->getOptionArray();
		$prostatus=array();
		foreach ($checkstatusarray as $key=>$val) {
			$prostatus[$val] = $key;
		}
		$filters['check_status'] =  isset($filters['check_status']) ? $prostatus[$filters['check_status']]:'';
		//Mage::log($filters,null,'vfilter1.log');
		
		if($vendorId==null) {
			$response = array (
						'data' => array (
							'message' => 'Vendor Id Is Empty',
							'success' => false, 
						) 
				);
			return $response;
		}
	
		$collection = Mage::getModel('csmarketplace/vproducts')->getVendorProducts('',$vendorId,0);

		if(count($collection)>0){
			$products=array();
			$statusarray=array();
			foreach($collection as $data){
				array_push($products,$data->getProductId());
				$statusarray[$data->getProductId()]=$data->getCheckStatus();
			}
			$currentStore=Mage::app()->getStore()->getId();
			Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
			$productcollection =Mage::getModel('catalog/product')->getCollection();
			$storeId=0;
			if($store != null){
				$websiteId=Mage::getModel('core/store')->load($store)->getWebsiteId();
				/* Mage::log('store_inp:'.$store.'/website:'.$websiteId, null, 'mylogg.log');
				Mage::log(implode('/',Mage::getModel('csmarketplace/vproducts')->getAllowedWebsiteIds()), null, 'mylogg.log'); */
				if($websiteId){
					if(in_array($websiteId,Mage::getModel('csmarketplace/vproducts')->getAllowedWebsiteIds())){
						$storeId=$store;
					}
				}
			}
			$productcollection->addAttributeToSelect('*')->addAttributeToFilter('entity_id',array('in'=>$products))->addAttributeToSort('entity_id', 'DESC');
			
			if($storeId){
				$productcollection->addStoreFilter($storeId);
				$productcollection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner', $storeId);
				$productcollection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $storeId);
				$productcollection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $storeId);
				$productcollection->joinAttribute('thumbnail', 'catalog_product/thumbnail', 'entity_id', null, 'left', $storeId);
			}
			//Mage::log($storeId, null, 'mylogg.log');
				
			if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
				$productcollection->joinField('qty',
						'cataloginventory/stock_item',
						'qty',
						'product_id=entity_id',
						'{{table}}.stock_id=1',
						'left');
			}
			$productcollection->joinField('check_status','csmarketplace/vproducts', 'check_status','product_id=entity_id',null,'left');
			//$params = Mage::getSingleton('core/session')->getData('product_filter');
			$params = $filters;
			if(isset($params) && is_array($params) && count($params)>0){
				//Mage::log('******Param********',null,'vfilter1.log');
				//Mage::log($params,null,'vfilter1.log');
				foreach($params as $field=>$value){
					//Mage::log($field,null,'vfilter1.log');
					//Mage::log($value,null,'vfilter1.log');
					if($field=='store'||$field=='store_switcher'||$field=="__SID")
						continue;
					if(is_array($value)){
						if(isset($value['from']) && $value['from']!=""){
							$from = $value['from'];
							$productcollection->addAttributeToFilter($field, array('gteq'=>$from));
						}
						if(isset($value['to'])  && $value['to']!=""){
							$to = $value['to'];
							$productcollection->addAttributeToFilter($field, array('lteq'=>$to));
						}
					}else if($value!=null || $value=='0'){
						//Mage::log($field,null,'vfilter1.log');
						//Mage::log($value,null,'vfilter1.log');
						$productcollection->addAttributeToFilter($field, array("like"=>'%'.$value.'%'));
					}
				}
			}
			$baseCurrency = Mage::app ()->getStore ()->getBaseCurrencyCode ();
			$currency_symbol = Mage::app ()->getLocale ()->currency ( $baseCurrency )->getSymbol ();
			//$proregister = Mage::register('product_count',$productcollection);
			$procount = $productcollection->getSize();
			$productcollection->getSelect ()->limit ( $limit, $offset );
			$vendor_product = array();
			$astatus = '';
			foreach ( $productcollection as $product ) {
				$status= $product->getCheckStatus()?(($product->getCheckStatus()==Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS)? "Approved":"Pending") : "Not Approved";
				if($product->getCheckStatus()==Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS)
				{
					$astatus = $product->getStatus()==Mage_Catalog_Model_Product_Status::STATUS_ENABLED?"(Enabled)":"(Disabled)";
				}
				$vendor_product [] = array (
						'product_id' => $product->getId (),
						'product_name' => $product->getName (),
						'type' => $product->getTypeId (),
						'qty'=>$product->getQty(),
						'sku'=>$product->getSku(),
						'status'=>$status,
						'admin_status'=>$astatus,
						'regular_price' => $currency_symbol . Mage::helper ( 'tax' )->getPrice ( $product, $product->getPrice () ),
						'product_image' => Mage::helper ( 'catalog/image' )->init ( $product, 'small_image' )->constrainOnly ( TRUE )->keepAspectRatio ( TRUE )->keepFrame ( FALSE )->resize ( 135, 135 )->__toString () 
				);
			}
			if(count($vendor_product)>0){
		        $response = array (
							'data' => array (
								'products' => $vendor_product,
								'count'=> $procount,
								'success' => true, 
							) 
					);
				return $response;
			}
			else{
				 $response = array (
						'data' => array (
							'message' => 'No product records found',
							'success' => false, 
						) 
				);
			return $response;
			}
		}
		$response = array (
						'data' => array (
							'message' => 'No product records found',
							'success' => false, 
						) 
		);
		return $response;
	}
	public function items($data,$filters = null, $store = null)
	{
	
		$vendorId = $data['vendor_id'];
		$customerId = $data['customer_id'];

		$limit='25';
		$curr_page = 1;

		/*$catfil=$data['catfilter'];
		$filters =$data['multi_filter'];*/

		$order = (isset($data ['order']) && $data ['order']) ? strtolower ( $data ['order'] ): null ;
		$dir = (isset($data ['dir']) && $data ['dir']) ? strtoupper ( $data ['dir'] ):null ;

		$offset = $data ['page'];
		if ($offset != 'null' && $offset != '') {
			
			$curr_page = $offset;
		}
		$offset = ($curr_page - 1) * $limit;

		$sort = array();

		if ($order != null && $dir != null) {
			$sort = array (
					$order,
					$dir 
			);
		}
		
		if($vendorId==null && $customerId == null) {
			$response = array (
						'data' => array (
							'message'=>'Vendor Id or Customer Id Is Empty',
							'status'=>false
						) 
				);
				return $response;
		}	
		$collection = Mage::getModel('csmarketplace/vproducts')->getVendorProducts('',$vendorId,0)->addFieldToFilter('check_status','1');
		if(count($collection)>0){
			
			$products=array();
			$statusarray=array();
			// $data is change to data1 by arun 
			foreach($collection as $data1){
				array_push($products,$data1->getProductId());
				$statusarray[$data1->getProductId()]=$data1->getCheckStatus();
			}
			$currentStore=Mage::app()->getStore()->getId();
			Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
			$productcollection =Mage::getModel('catalog/product')->getCollection();
			Mage::getSingleton('cataloginventory/stock')
			->addInStockFilterToCollection($productcollection);
			$storeId=0;
			if($store != null){
				$websiteId=Mage::getModel('core/store')->load($store)->getWebsiteId();
				
				if($websiteId){
					if(in_array($websiteId,Mage::getModel('csmarketplace/vproducts')->getAllowedWebsiteIds())){
						$storeId=$store;
					}
				}
			}
			$productcollection->addAttributeToSelect('*')->addAttributeToFilter('entity_id',array('in'=>$products));
			
			
				$productcollection->addStoreFilter($storeId);
				$productcollection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner', $storeId);
				$productcollection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $storeId);
				$productcollection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $storeId);
				$productcollection->joinAttribute('thumbnail', 'catalog_product/thumbnail', 'entity_id', null, 'left', $storeId);
			
			
				
			if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
				$productcollection->joinField('qty',
						'cataloginventory/stock_item',
						'qty',
						'product_id=entity_id',
						'{{table}}.stock_id=1',
						'left');
			}
			$productcollection->joinField('check_status','csmarketplace/vproducts', 'check_status','product_id=entity_id',null,'left');
				
			
			if (isset($data['catfilter'])) {
				$catfil = $data['catfilter'];
				$productcollection = $this->getFilteredCollection ( $catfil, $vendorId);
			}
			
			if (isset($data['multi_filter'])) {
				$filters = $data['multi_filter'];
				$productcollection = $this->getMultiFilterCollection ( $filters, $vendorId);
			} 
			$productcollection->getSelect ()->limit ( $limit, $offset );
			$vendorProducts = $this->getListProduct ( $productcollection,$sort,$customerId );
			$sortBy = Mage::getModel('vendorapi/product_sortby')->getAttributeUsedForSortByArray();
			$categoryfilter=Mage::getModel('vendorapi/product_sortby')->getCategoryFilter();
			$helper = Mage::helper('csmarketplace/tool_image');
			$vendor = Mage::getModel('csmarketplace/vendor')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->loadByAttribute('entity_id',$vendorId);
            $storeId = Mage::app()->getStore()->getId();
			$attributes =  Mage::getModel('csmarketplace/vendor_attribute')
							->setStoreId($storeId)
							->getCollection()
							//->addFieldToFilter('use_in_left_profile',array('gt'=>0))
							->setOrder('position_in_left_profile','ASC');
			
			$vendorProfile=array();
			foreach ($attributes as $attribute) {
				$vendorProfile[$attribute->getData('attribute_code')]=
				$vendor->getData($attribute->getData('attribute_code'));
				
			}
			
			if(isset($vendorProfile['country_id']))
			$vendorProfile['country_id'] = Mage::app()->getLocale()->getCountryTranslation($vendorProfile['country_id']);
			$img=($vendor->getData('company_banner')=='')?Mage::getBaseUrl():$helper->init($vendor->getData('company_banner'),'banner')->__toString ();
			if(isset($vendorProfile['profile_picture']))
			$vendorProfile['profile_picture'] = ($vendor->getData('profile_picture')=='')?Mage::getBaseUrl():Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$vendorProfile['profile_picture'];
			if (count ( $vendorProducts ) > 0) {
				$response = array (
						'data' => array (
							'banner_url'=>$img,
							'shop_description'=>$vendor->getData('about'),
							'vendor_profile'=>$vendorProfile,
							'products' => $vendorProducts,
							'sort'=>$sortBy,
							'category-filter'=>$categoryfilter,
							'status' => 'enabled' 
						) 
				);
				
				$changeData = new Varien_Object ();
				$changeData->setVendorId ($vendorId);
				$changeData->setResponse ($response);
				Mage::dispatchEvent ( 'get_vendor_review', array (
							'vendor_info' => $changeData 
					) );
				$response = $changeData->getResponse();
				return $response;
			}
			else{
				return 'NO_PRODUCTS';
			}
		} 
		
		return 'NO_PRODUCTS';
	}
	public function getFilteredCollection ($catfil,$vendorId){
		
			$collection = Mage::getModel('csmarketplace/vproducts')->getVendorProducts(Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS,$vendorId,0);
			$products=array();
			$statusarray=array();
			foreach($collection as $data){
				
				 if($data->getIsMultiseller() && $data->getParentId())
					array_push($products,$data->getParentId());
				else 
				array_push($products,$data->getProductId());
			}
			
			$layer = Mage::getSingleton( "csmarketplace/vshop_layer" );
			$rootCatId = Mage::app()->getWebsite(1)->getDefaultStore()->getRootCategoryId();
			$category = Mage::getModel ( "catalog/category" )->load ($rootCatId);
			$layer->setCurrentCategory ( $category );
			$productcollection = Mage::getModel('catalog/product')
			                    ->getCollection()
								->addAttributeToSelect(Mage::getSingleton('catalog/config')
								->getProductAttributes())
								->addAttributeToFilter('entity_id',array('in'=>$products))
								->addStoreFilter(Mage::app()->getStore()->getId())
								->addAttributeToFilter('status',Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
			//Custom Code Start
			$cat_id = $catfil;
			if(isset($cat_id)) {
				$productcollection->joinField(
						'category_id', 'catalog/category_product', 'category_id',
						'product_id = entity_id', null, 'left'
				)
				->addAttributeToSelect('*')
				->addAttributeToFilter('category_id', array(
						'in'=>explode('#', $cat_id))
				);
			}
			return $productcollection;
	}
	public function getMultiFilterCollection($filter,$vendorId){
			$filters=json_decode($filter,true);
			$storeId = Mage::app ()->getStore ()->getId ();
			$newids = array ();
			$collection = Mage::getModel('csmarketplace/vproducts')->getVendorProducts(Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS,$vendorId,0);

			$products=array();
			$statusarray=array();
			foreach($collection as $data){
				
				 if($data->getIsMultiseller() && $data->getParentId())
					array_push($products,$data->getParentId());
				else 
				array_push($products,$data->getProductId());
			}
			//echo '<pre>';print_r($products);echo '<hr>';
			$layer = Mage::getSingleton( "csmarketplace/vshop_layer" );
			$rootCatId = Mage::app()->getWebsite(1)->getDefaultStore()->getRootCategoryId();
			$category = Mage::getModel ( "catalog/category" )->load ($rootCatId);
			$layer->setCurrentCategory ( $category );
			
								
		foreach ( $filters as $filtername => $val ) {
			
			$count='1';
			foreach ( $val as $filterid => $key ) {
				if (count ( $newids ) == '0') {
					$productCollection = Mage::getModel('catalog/product')
			                    ->getCollection()
								->addAttributeToSelect(Mage::getSingleton('catalog/config')
								->getProductAttributes())
								->addAttributeToFilter('entity_id',array('in'=>$products))
								->addStoreFilter(Mage::app()->getStore()->getId())
								->addAttributeToFilter('status',Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
				}
				if ($filtername != 'price') {
					$connection = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_read' );
					$getMainTable = 'catalog_product_index_eav';
					$attId = $attributeId = Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', $filtername );
					$tableAlias = $filtername . $count;
					
					$conditions = array (
							"{$tableAlias}.entity_id = e.entity_id",
							$connection->quoteInto ( "{$tableAlias}.attribute_id = ?", $attId ),
							$connection->quoteInto ( "{$tableAlias}.store_id = ?", '1' ),
							$connection->quoteInto ( "{$tableAlias}.value = ?", $filterid ) 
					);
					if (count ( $newids ) == '0') {
						 $productCollection->getSelect ()->join ( array (
								$tableAlias => $getMainTable 
						), implode ( ' AND ', $conditions ), array () );
						 
						if (count ( $productCollection ) > 0)
							$ids [] = $productCollection->getAllIds ();
					} else {
						$newproductCollection->getSelect ()->join ( array (
								$tableAlias => $getMainTable 
						), implode ( ' AND ', $conditions ), array () );
					}
				} else {
					if (count ( $newids ) == '0') {
						$productCollection = Mage::getModel('catalog/product')
			                    ->getCollection()
								->addAttributeToSelect('*')
								->addAttributeToFilter('entity_id',array('in'=>$products))
								->addAttributeToFilter('status',Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
					}
					$price = explode ( "-", $filterid );

					$ids = array();
					if (count ( $newids ) == '0') {
						if ($price [0] != '') { 
							$productCollection->addFieldToFilter('price', 
     								array('gteq' => $price[0])
 								);
							if ($productCollection->getSize()>0)
								$ids [] = $productCollection->getAllIds ();
						}
						if ($price [1] != '') { 
							$productCollection->addAttributeToFilter('price', 
     								array('lt' => $price[1])
 								);
							if ($productCollection->getSize()>0)
								$ids [] = $productCollection->getAllIds ();
						}
					} else {
						if ($price [0] != '') { 
							$newproductCollection->addFieldToFilter('price', 
     								array('gteq' => $price[0])
 								);
						}
						if ($price [1] != '') { 
							$newproductCollection->addFieldToFilter('price', 
     								array('lt' => $price[1])
 								);
						}
					}
					//print_r($ids);die;
				}
				++$count;
			}
			if (count ( $newids ) == '0') {
				$newproductCollection = Mage::getModel('catalog/product')
			                    ->getCollection()
								->addAttributeToSelect(Mage::getSingleton('catalog/config')
								
								->getProductAttributes())
								->addAttributeToFilter('entity_id',array('in'=>$products))
								->addAttributeToFilter('entity_id',array('in'=>$ids))
								->addStoreFilter(Mage::app()->getStore()->getId())
								->addAttributeToFilter('status',Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
				$newids [] = $newproductCollection->getAllIds ();
			}
		}
		return $newproductCollection;
	}
	public function info($vendorId, $productId, $store = 0, $attributes = null, $identifierType = null)
	{	

		if($vendorId==null || $productId==null) {
			$response = array (
				'data' => array (
					'message' => 'Vendor Id Is Empty',
					'success' => false, 
				) 
			);
			return $response;
		}
		
		if($vendorId) {

			
			if (!empty($identifierType)) {
				$identifierType = strtolower($identifierType);
			}
			
			$product = $this->_getProduct($vendorId, $productId, $store, $identifierType);
			$medi=array();
			$productGalleryImages =$product->getMediaGalleryImages();
			$productImage = $product->getImage();
			$i=0;
			foreach($productGalleryImages as $_image) {

			$default=($productImage==$_image->getFile())?"true":'';
								  $i++; 
				$medi[]	=	array(
					'image_path'=>Mage::helper('catalog/image')->init($product, 'thumbnail',$_image->getFile())->resize(600, 600)->__toString (),
					'image_name'=>$_image->getFile(),
					'default_image'=>$default
					);
				
			}
			
			$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
			if($product) {
				
				$prodata=$this->allowedProductData();
				$prodata['data']['productdata'] = $product->getData();
				$prodata['data']['productdata']['media_image'] =  $medi;
				$prodata['data']['productdata']['image'] = Mage::helper ( 'catalog/image' )->init ( $product, 'small_image' )->constrainOnly ( TRUE )->keepAspectRatio ( TRUE )->keepFrame ( FALSE )->resize ( 135, 135 )->__toString ();
				$prodata['data']['productdata']['small_image'] = Mage::helper ( 'catalog/image' )->init ( $product, 'small_image' )->constrainOnly ( TRUE )->keepAspectRatio ( TRUE )->keepFrame ( FALSE )->resize ( 135, 135 )->__toString ();
				$prodata['data']['productdata']['thumbnail']	= Mage::helper ( 'catalog/image' )->init ( $product, 'thumbnail' )->constrainOnly ( TRUE )->keepAspectRatio ( TRUE )->keepFrame ( FALSE )->resize ( 135, 135 )->__toString ();
				$prodata['data']['productdata']['stock_item'] = array(
					$stock->getData()
					);
				$prodata['data']['productcategories'] = $product->getCategoryIds();
				$prodata['data']['productwebsites'] = $product->getWebsiteIds();
				return $prodata;
			}
		}
		return false;
	}
	
	protected function _getProduct($vendorId, $productId, $store = null, $identifierType = null)
	{
		$products_ids = $this->getVendorProductIds($vendorId);
		if(in_array($productId, $products_ids))
			return Mage::getModel('catalog/product')->load($productId);
		return false;
	}
	
	public function getVendorProductIds($vendorId=0, $checkstatus = ''){
			$vendorId=$vendorId?$vendorId:Mage::getSingleton('customer/session')->getVendorId();
			$vcollection = Mage::getModel('csmarketplace/vproducts')->getVendorProducts($checkstatus,$vendorId,0);
			$productids=array();
			if(count($vcollection)>0){
				foreach($vcollection as $data){
					array_push($productids,$data->getProductId());
				}
			}
		return $productids;
	}
	
	public function create($vendorId,$type, $set, $sku, $productData, $store = null)
	{
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		if($vendorId==null ||  $type==null || $set==null || $sku==null || $productData==null || empty($productData)) {
			$response = array (
						'data' => array (
							'message' => 'Vendor Id Is Empty',
							'success' => false, 
						) 
		);
		return $response;
		}
		
		if(is_array($productData)) {
			$productData = json_decode(json_encode($productData), true);
		}

		$status = $productData['status'];
		$_websiteIds = isset($productData['website_ids'])?$productData['website_ids']:array();
		
		unset($productData['status'], $productData['websites']);
		
		if (!$type || !$set || !$sku) {
			$response = array (
						'data' => array (
							'message' => 'Sku Or Product Type Is Not Available',
							'success' => false, 
						) 
		);
		return $response;
		}
		
		$this->_checkProductTypeExists($type);
		$this->_checkProductAttributeSet($set);
			
		$mode = Ced_CsMarketplace_Model_Vproducts::NEW_PRODUCT_MODE;
		/** @var $product Mage_Catalog_Model_Product */
		$product = Mage::getModel('catalog/product');
		//$product->setStoreId($this->_getStoreId($store))
		$product->setStoreId(0)->setAttributeSetId($set)
		->setTypeId($type)
		->setSku($sku);
		

		/*	Start	*/
		$product->setStatus (Mage::getModel('csmarketplace/vproducts')->isProductApprovalRequired()?Mage_Catalog_Model_Product_Status::STATUS_DISABLED:Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
		if(Mage::helper('csmarketplace')->isSharingEnabled()){
			$websiteIds = $_websiteIds;
		}
		$product->setWebsiteIds($websiteIds);
		/*	End	*/
	
		if (!isset($productData['stock_data']) || !is_array($productData['stock_data'])) {
			//Set default stock_data if not exist in product data
			$product->setStockData(array('use_config_manage_stock' => 0));
		}
	
		foreach ($product->getMediaAttributes() as $mediaAttribute) {
			$mediaAttrCode = $mediaAttribute->getAttributeCode();
			$product->setData($mediaAttrCode, 'no_selection');
		}
	
		$this->_preparingDataForSave($product, $productData);
		try {

			/**
			 * @todo implement full validation process with errors returning which are ignoring now
			 * @todo see Mage_Catalog_Model_Product::validate()
			 */
			if (is_array($errors = $product->validate())) {
				$strErrors = array();
				foreach($errors as $code => $error) {
					if ($error === true) {
						$error = Mage::helper('catalog')->__('Attribute "%s" is invalid.', $code);
					}
					$strErrors[] = $error;
				}
				$response = array (
						'data' => array (
							'message' => implode("\n", $strErrors),
							'success' => false, 
						) 
		);
				return $response;
			}
			$product->save();
		} catch (Mage_Core_Exception $e) {
			$response = array (
						'data' => array (
							'message' => $e->getMessage(),
							'success' => false, 
						) 
		);
		return $response;
		}
		catch (Exception $e) {
			$response = array (
						'data' => array (
							'message' => $e->getMessage(),
							'success' => false, 
						) 
		);
		return $response;
		}
		
		/**
		 * @todo to save product data in vendor table
		 */

		$this->processPostSave($mode,$product,$productData, $vendorId, $type, 0);
		
		$response = array (
						'data' => array (
							'product_id' => $product->getId(),
							'valerts'=>Mage::getModel('vendorapi/vendor_api')->getVendorAlert($vendorId),
							'success' => true, 
						) 
		);
		return $response;
	}
	
	/**
	 * Update product data
	 *
	 * @param int|string $productId
	 * @param array $productData
	 * @param string|int $store
	 * @return boolean
	 */
	public function update($vendorId, $productId, $productData,$defaultimage,$store = null, $identifierType = null)
	{
		
		if($vendorId==null  || $productId==null || $productData==null) {
			$response = array (
				'data' => array (
					'message' => 'Vendor Id Is Empty',
					'success' => false, 
				) 
			);
			return $response;
		}
		if($productId&&$vendorId){
			$vendorProduct = Mage::getModel('csmarketplace/vproducts')->isAssociatedProduct($vendorId,$productId);
			if(!$vendorProduct){
				$response = array (
					'data' => array (
						'message' => 'Vendor Product Does Not Exist',
						'success' => false, 
					) 
				);
				return $response;
			}
		}
		$product = Mage::getModel('catalog/product');
		$product->load($productId);
	
		$product->setStoreId(0);
		
		$mode = Ced_CsMarketplace_Model_Vproducts::EDIT_PRODUCT_MODE;
		$this->_preparingDataForSave($product, $productData);
		try {
			/**
			 * @todo implement full validation process with errors returning which are ignoring now
			 * @todo see Mage_Catalog_Model_Product::validate()
			 */
			if (is_array($errors = $product->validate())) {
				$strErrors = array();
				foreach($errors as $code => $error) {
					if ($error === true) {
						$error = Mage::helper('catalog')->__('Value for "%s" is invalid.', $code);
					} else {
						$error = Mage::helper('catalog')->__('Value for "%s" is invalid: %s', $code, $error);
					}
					$strErrors[] = $error;
				}
				$response = array (
					'data' => array (
						'message' => implode("\n", $strErrors),
						'success' => false, 
					) 
				);
				return $response;
			}
			
			$product->save();

			if (isset ( $defaultimage) && $defaultimage!='') {
				//case when no new uploads
				if($defaultimage!==''){
					$mediaGallery = $product->getMediaGallery();	
					//if there are images
					if (isset($mediaGallery['images'])){
						//loop through the images
						foreach ($mediaGallery['images'] as $image){
							if (strpos($image['file'],$defaultimage) !== false){
								Mage::getSingleton('catalog/product_action')->updateAttributes(array($productId), array('image'=>$image['file'],'small_image'=>$image['file'],'thumbnail'=>$image['file']), $product->getStoreId());
								break;
							}
						}
					}
				}
			}
		} catch (Mage_Core_Exception $e) {
			
			$response = array (
					'data' => array (
						'message' => $e->getMessage(),
						'success' => false, 
					) 
				);
				return $response;
		}
		catch (Exception $e) {
			$response = array (
					'data' => array (
						'message' => $e->getMessage(),
						'success' => false, 
					) 
				);
				return $response;
		}
		/**
		 * @todo to save product data in vendor table
		 */
		$this->processPostSave($mode,$product,$productData, $vendorId);
		$response = array (
					'data' => array (
						'product_id' => $product->getId(),
						'message' => 'Product Updated Successfully',
						'success' => True, 
					) 
				);
		return $response;
	}
	
	/**
	 *  Set additional data before product saved
	 *
	 *  @param    Mage_Catalog_Model_Product $product
	 *  @param    array $productData
	 *  @return   object
	 */
	protected function _preparingDataForSave($product, $productData)
	{
		if (isset($productData['website_ids']) && is_array($productData['website_ids'])) {
			$product->setWebsiteIds($productData['website_ids']);
		}
		foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
			//Unset data if object attribute has no value in current store
			if (Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID !== (int) $product->getStoreId()
			&& !$product->getExistsStoreValueFlag($attribute->getAttributeCode())
			&& !$attribute->isScopeGlobal()
			) {
				$product->setData($attribute->getAttributeCode(), false);
			}
	
			if ($this->_isAllowedAttribute($attribute)) {
				if (isset($productData[$attribute->getAttributeCode()])) {
					$product->setData(
							$attribute->getAttributeCode(),
							$productData[$attribute->getAttributeCode()]
					);
				} elseif (isset($productData['additional_attributes']['single_data'][$attribute->getAttributeCode()])) {
					$product->setData(
							$attribute->getAttributeCode(),
							$productData['additional_attributes']['single_data'][$attribute->getAttributeCode()]
					);
				} elseif (isset($productData['additional_attributes']['multi_data'][$attribute->getAttributeCode()])) {
					$product->setData(
							$attribute->getAttributeCode(),
							$productData['additional_attributes']['multi_data'][$attribute->getAttributeCode()]
					);
				}
			}
		}
	
		if (isset($productData['categories']) && is_array($productData['categories'])) {
			
			$product->setCategoryIds($productData['categories']);
		}
	
		if (isset($productData['websites']) && is_array($productData['websites'])) {
			foreach ($productData['websites'] as $website) {
				if (is_string($website)) {
					try {
						$website = Mage::app()->getWebsite($website)->getId();
					} catch (Exception $e) { }
				}
			}

			$product->setWebsiteIds($productData['websites']);
		}
	
		if (Mage::app()->isSingleStoreMode()) {
			$product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
		}
	
		if (isset($productData['stock_data']) && is_array($productData['stock_data'])) {
			$product->setStockData($productData['stock_data']);
		}
	
		if (isset($productData['tier_price']) && is_array($productData['tier_price'])) {
			$tierPrices = Mage::getModel('catalog/product_attribute_tierprice_api')
			->prepareTierPrices($product, $productData['tier_price']);
			$product->setData(Mage_Catalog_Model_Product_Attribute_Tierprice_Api::ATTRIBUTE_CODE, $tierPrices);
		}
	}
	
	public function delete($vendorId, $productId, $identifierType = null)
	{
		if($vendorId==null) {
			$response = array (
							'message' => 'Vendor Id Is Empty',
							'success' => false, 
		);
		return $response;
		}

		$vendorProduct = Mage::getModel('csmarketplace/vproducts')->isAssociatedProduct($vendorId,$productId);
		
		 if($productId){
			Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
			try {
				$product = Mage::getModel('catalog/product')->load($productId);
				if($product && $product->getId()) {
					$product->delete();
					Mage::getModel('csmarketplace/vproducts')->changeVproductStatus(array($productId),Ced_CsMarketplace_Model_Vproducts::DELETED_STATUS);
				}
			}
			catch (Mage_Core_Exception $e) {
				$response = array (
							'message' => $e->getMessage(),
							'success' => false, 
			);
			return $response;
			} catch (Exception $e) {
				$response = array (
							'message' => $e->getMessage(),
							'success' => false, 
		);
		return $response;
			}
		}
		$response = array (
							'message' => 'Product Deleted Successfully',
							'success' => true, 
		);
		return $response;
	}
	
	public function _checkProductTypeExists($productType)
	{
		$allowedType = Mage::getModel('csmarketplace/system_config_source_vproducts_type')->getAllowedType(Mage::app()->getStore()->getId());
		if(!(in_array($productType,$allowedType)))
			$this->_fault('product_type_not_exists');
	}
	
	/**
	 * Check if attributeSet is exits and in catalog_product entity group type
	 *
	 * @param  $attributeSetId
	 * @throw Mage_Api_Exception
	 * @return void
	 */
	public function _checkProductAttributeSet($attributeSetId)
	{
		$attributeSet = Mage::getModel('eav/entity_attribute_set')->load($attributeSetId);
		if (is_null($attributeSet->getId())) {
			$this->_fault('product_attribute_set_not_exists');
		}
		if (Mage::getModel('catalog/product')->getResource()->getTypeId() != $attributeSet->getEntityTypeId()) {
			$this->_fault('product_attribute_set_not_valid');
		}
	}
	
	public function _isAllowedAttribute($attribute, $attributes = null)
	{
		if (Mage::getSingleton('api/server')->getApiName() == 'rest') {
			if (!$this->_checkAttributeAcl($attribute)) {
				return false;
			}
		}
		if (is_array($attributes)
		&& !( in_array($attribute->getAttributeCode(), $attributes)
				|| in_array($attribute->getAttributeId(), $attributes))) {
			return false;
		}
		return !in_array($attribute->getFrontendInput(), $this->_ignoredAttributeTypes)
		&& !in_array($attribute->getAttributeCode(), $this->_ignoredAttributeCodes);
	}
	
	public function processPostSave($mode,$product,$productData, $vendorId, $type=null, $storeId=null)
	{
		$websiteIds='';

		if(isset($productData['website_ids'])){
			if(!is_array($productData['website_ids']))
				$websiteIds = implode(",",$productData['website_ids']);
			else
				$websiteIds = $productData['website_ids'];
		}
		else
			$websiteIds = implode(",",$product->getWebsiteIds());
		
		$productId = $product->getId();

		switch($mode) {
			case Ced_CsMarketplace_Model_Vproducts::NEW_PRODUCT_MODE:
			
				$prodata = isset($productData)?$productData:array();

				Mage::getModel('csmarketplace/vproducts')->setData($prodata)
				    ->setQty(isset($productData['stock_data']['qty'])?$productData['stock_data']['qty']:0)
				    ->setIsInStock(isset($productData['stock_data']['is_in_stock'])?$productData['stock_data']['is_in_stock']:1)
				    ->setPrice($product->getPrice())
				    ->setSpecialPrice($product->getSpecialPrice())
				    ->setCheckStatus (Mage::getModel('csmarketplace/vproducts')->isProductApprovalRequired()?Ced_CsMarketplace_Model_Vproducts::PENDING_STATUS:Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS )
				    ->setProductId ($productId)
				    ->setVendorId($vendorId)
				//->setType(isset($productData['type'])?$productData['type']:Mage_Catalog_Model_Product_Type::DEFAULT_TYPE)
				    ->setType($type)
				    ->setWebsiteIds($websiteIds)
				    ->setStatus(Mage::getModel('csmarketplace/vproducts')->isProductApprovalRequired()?Mage_Catalog_Model_Product_Status::STATUS_DISABLED:Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
				    ->save ();
	
			case Ced_CsMarketplace_Model_Vproducts::EDIT_PRODUCT_MODE:

				$model = Mage::getModel('csmarketplace/vproducts')->loadByField(array('product_id'),array($product->getId()));
				if($model && $model->getId()){

					unset($productData['stock_data']);		//as these values are array type 
					unset($productData['website_ids']);
					unset($productData['categories']);

					$model->addData ( isset($productData)?$productData:array() );
					$model->addData ( isset($productData['stock_data'])?$productData['stock_data']:array() );
					$model->addData(array('store_id'=>$storeId,'website_ids'=>$websiteIds,'price'=>$product->getPrice(),'special_price'=>$product->getSpecialPrice()));

					if($model->getCheckStatus()==Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS)
						$model->setStatus(isset($productData['status'])?$productData['status']:Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
					$model->save ();
				}
		}
	}
	public function allowedProductData(){
		$helper = Mage::helper ( 'csmarketplace' );
		$types = Mage::getModel('csmarketplace/system_config_source_vproducts_type')->toOptionArray(false,true);
		$allowed = array('downloadable','grouped','configurable','bundle')	;	
		foreach ($types as $key => $value) {
		
			if(in_array($value['value'], $allowed)){
				unset($types[$key]);
			}
		}	
		$ty=array();	
		$count = 0;
		foreach ($types as $val) {
			$ty[]=$val;
		}		
		$taxes=Mage::getModel('tax/class')->getCollection()
													->addFieldToFilter('class_type',array('eq'=>'PRODUCT'));
		$taxClasses=array();
		/*$taxClasses['-1']=$helper->__('--Please Select--');*/
		$taxClasses['0']=$helper->__('None');
		foreach($taxes as $tax){
			$taxClasses[$tax->getId()]=$tax->getClassName();
		}
		$stock=array(
			'1'=> $helper->__('In Stock'),
			'0'=>$helper->__('Out of Stock')
		);
		$websiteCollection = Mage::getModel('core/website')->getResourceCollection();
		$websitesIds = Mage::getStoreConfig('ced_vproducts/general/website');

		if(strlen($websitesIds) > 0){
		$websitesIds = explode(',',$websitesIds);

		if(in_array(0,$websitesIds)){
			$flag = true;
			$websitesIds = array();
		}
		} else {
			$websitesIds = array();
		}
		
        if (count($websitesIds)>0) { 
            $websiteCollection->addIdFilter($websitesIds);
        }
		$websites=array();
		foreach ($websiteCollection as $_website){
			$websites[$_website->getId()]=$_website->getName();
		}
		$stores=array();
		foreach ($websiteCollection as $_website) {
			foreach ($this->getGroupCollection($_website) as $_group) {
				$stores[$_website->getName()][]=$_group->getName();
			}
		}
		$storeViews=array();
		foreach ($websiteCollection as $_website) {
			foreach ($this->getGroupCollection($_website) as $_group) {
				foreach ($this->getStoreCollection($_group) as $_store){
					$storeViews[$_website->getName()][$_group->getName()][]=$_store->getName();
				}
			}
		}

		$rootCatId = Mage::app()->getWebsite(1)->getDefaultStore()->getRootCategoryId();
   		$catlistHtml = $this->getTreeCategories($rootCatId, false);
		$response = array (
						'data' => array (
							'allowed_pro_type' => $ty,
							'websites'=>$websites,
							'stores'=>$stores,
							'storeViews'=>$storeViews,
							'category'=>$catlistHtml,
							'taxes'=>$taxClasses,
							'stock'=>$stock,
							'success' => true, 
						) 
		);
		return $response;
	}
	public function getGroupCollection($website)
    {
        if (!$website instanceof Mage_Core_Model_Website) {
            $website = Mage::getModel('core/website')->load($website);
        }
        return $website->getGroupCollection();
    }
     public function getStoreCollection($group)
    {
        if (!$group instanceof Mage_Core_Model_Store_Group) {
            $group = Mage::getModel('core/store_group')->load($group);
        }
        $stores = $group->getStoreCollection();
        return $stores;
    }
    function getTreeCategories($parentId, $isChild){
	    $allCats = Mage::getModel('catalog/category')->getCollection()
	                ->addAttributeToSelect('*')
	                ->addAttributeToFilter('is_active','1')
	                ->addAttributeToFilter('include_in_menu','1')
	                ->addAttributeToFilter('parent_id',array('eq' => $parentId))
	                ->addAttributeToSort('position', 'asc');
	               
	   	$cat=array();
	   	$count='0';
	   	$allowed_categories = explode(',',Mage::getStoreConfig('ced_vproducts/general/category',0));
	   	$mode = Mage::getStoreConfig('ced_vproducts/general/category_mode',0);
	    foreach($allCats as $category)
	    {
	    	if($mode=='1' && in_array($category->getId(), $allowed_categories)){
		    	$cat[$count] =array('main_category'=>$category->getId().'#'.$category->getName()." (".Mage::getModel('csmarketplace/vproducts')->getProductCount($category->getId(),Ced_CsMarketplace_Model_Vproducts::AREA_FRONTEND).")");
		    	$subcats = $category->getChildren();
	        	if($subcats != ''){ 
	        		$cat[$count]['sub_categories']=$this->getTreeCategories($category->getId(), true);
	       		} 
	       		++$count; 
       		} 
       		else if($mode=='0'){
       			$cat[$count] =array('main_category'=>$category->getId().'#'.$category->getName()." (".Mage::getModel('csmarketplace/vproducts')->getProductCount($category->getId(),Ced_CsMarketplace_Model_Vproducts::AREA_FRONTEND).")");
		    	$subcats = $category->getChildren();
	        	if($subcats != ''){ 
	        		$cat[$count]['sub_categories']=$this->getTreeCategories($category->getId(), true);
	       		} 
	       		++$count; 
       		}
	    }
	   return $cat;
	}
}
?>