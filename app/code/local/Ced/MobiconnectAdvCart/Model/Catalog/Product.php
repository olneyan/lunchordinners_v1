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
class Ced_MobiconnectAdvCart_Model_Catalog_Product extends Mage_Catalog_Model_Config {
	/**
	 *
	 * @param
	 *        	product collection
	 * @return category products and rating count
	 */
	public function getListProduct($collection, $sort, $customerid = null) {
		$baseCurrency = Mage::app ()->getStore ()->getDefaultCurrencyCode ();
		$currency_symbol = Mage::app ()->getLocale ()->currency ( $baseCurrency )->getSymbol ();
		if ($sort) {
			$collection = $this->getSortedCollection ( $collection, $sort );
		}
		$productList = array ();
		$count = 0;
		foreach ( $collection as $product ) {
			
			$productId = $product->getId ();
			$changeData = new Varien_Object ();
			$changeData->setProductId ( $productId );
			$changeData->setIsCategory ( '1' );
			if ($customerid != 'NULL' && $customerid != '') {
				$changeData->setCustomerId ( $customerid );
			}
			$dats = Mage::dispatchEvent ( 'change_product_data', array (
					'product_info' => $changeData 
			) );
			$wishlist = $changeData->getWishlist ();
			$iswishlist = 'OUT';
			if ($wishlist == '1') {
				$iswishlist = 'IN';
			}
			$avg = $changeData->getAvg ();
			if ($product->getSpecialPrice () != '' && $product->getSpecialPrice () < $product->getPrice ()) {
				$categ_product [] = array (
						'product_id' => $product->getId (),
						'product_name' => $product->getName (),
						'type' => $product->getTypeId (),
						'description' => $product->getShortDescription (),
						'special_price' => Mage::helper('core')->currency(Mage::helper ( 'tax' )->getPrice ( $product, $product->getSpecialPrice ()),true,false),
						'regular_price' => Mage::helper('core')->currency(Mage::helper ( 'tax' )->getPrice ( $product, $product->getPrice ()),true,false),
						'Inwishlist' => $iswishlist,
						'stock_status' => $product->isSaleable (),
						'product_image' => Mage::helper ( 'catalog/image' )->init ( $product, 'small_image' )->__toString () 
				);
			} else {
				$categ_product [] = array (
						'product_id' => $product->getId (),
						'product_name' => $product->getName (),
						'type' => $product->getTypeId (),
						'description' => $product->getShortDescription (),
						'special_price' => 'no_special',
						'regular_price' => Mage::helper('core')->currency(Mage::helper ( 'tax' )->getPrice ( $product, $product->getPrice ()),true,false),
						'Inwishlist' => $iswishlist,
						'stock_status' => $product->isSaleable (),
						'product_image' => Mage::helper ( 'catalog/image' )->init ( $product, 'small_image' )->__toString () 
				);
			}
			
			$discounted_price = Mage::getModel ( 'catalogrule/rule' )->calcProductPriceRule ( $product->setCustomerGroupId ( 'CUSTOMER_GROUP_ID' ), $product->getPrice () );
			
			// if the product isn't discounted then default back to the original price
			if ($discounted_price != false) {
				$categ_product [$count] ['special_price'] = Mage::helper('core')->currency(Mage::helper ( 'tax' )->getPrice ( $product, $discounted_price),true,false);
			}
			if ($tax_display_type == 3) {
				$categ_product [$count] ['show-both-price'] = 'YES';
			} else {
				$categ_product [$count] ['show-both-price'] = 'NO';
			}
			if(isset($avg)){ 
				$categ_product[$count] ['review'] = $avg;
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
	/**
	 *
	 * @param category $id        	
	 * @return category product array
	 */
	public function getCategoryProduct($data) {
		$categoryId = $data ['id'];
		$order = strtolower ( $data ['order'] );
		$dir = strtoupper ( $data ['dir'] );
		$offset = $data ['offset'];
		$limit = $data ['limit'];
		$customer = $data ['customer_id'];
		$curr_page = 1;
		// $filtername = $data ['filtername'];
		// $filterid = $data ['filterid'];
		$filters = $data ['filter'];
		if ($offset != 'null' && $offset != '') {
			
			$curr_page = $offset;
		}
		$offset = ($curr_page - 1) * $limit;
		
		if ($order != null && $dir != null) {
			$sort = array (
					$order,
					$dir 
			);
		}
		if (count ( $filters ) > 0) {
			$category = Mage::getModel ( 'catalog/category' )->load ( $categoryId );
			$productCollection = $this->getFilteredCollection ( $filters, $category );
		} else {
			$category = Mage::getModel ( 'catalog/category' )->load ( $categoryId );
			$storeId = Mage::app ()->getStore ()->getId ();
			$productCollection = $category->getProductCollection ()->addAttributeToSelect ( $this->getProductAttributes () )->setStoreId ( $storeId )->addFinalPrice ();
			
			Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $productCollection );
			Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInSearchFilterToCollection ( $productCollection );
		}
		// $productCollection
		$productCollection->getSelect ()->limit ( $limit, $offset );
		$sortBy = $this->getAttributeUsedForSortByArray ( $category );

		$categoryProducts = $this->getListProduct ( $productCollection, $sort, $customer );
		if (count ( $categoryProducts ) > 0) {
			$data = array (
					'data' => array (
							'products' => $categoryProducts,
							'sort' => $sortBy,
							'status' => 'enabled' 
					) 
			);
			return $data;
		} else {
			return;
		}
	}
	
	/**
	 *
	 * @see Mage_Catalog_Model_Config::getAttributeUsedForSortByArray() $return categories available sort by option
	 */
	public function getAttributeUsedForSortByArray($category) {
		$sortOptions = $category->getAvailableSortByOptions ();
		$defaultSort = $category->getDefaultSortBy ();
		foreach ( $sortOptions as $key => $val ) {
			if ($val == 'Name') {
				$sort [] = array (
						"name:A to Z" => array (
								'asc' 
						),
						"name:Z to A" => array (
								'desc' 
						) 
				);
			}
			if ($val == 'Price') {
				$sort [] = array (
						"price:Low To High" => array (
								'asc' 
						),
						"price:High To Low" => array (
								'desc' 
						) 
				);
			}
			if ($val != 'Price' && $val != 'Name') {
				$sort [] = array (
						$val . ":" . ' asc' => array (
								'asc' 
						),
						$val . ":" . ' desc' => array (
								'desc' 
						) 
				);
			}
		}
		
		return $sort;
	}
	/**
	 *
	 * @param
	 *        	product id $id
	 * @return array of product data
	 */
	public function productView($id, $customerid) {
		
		$baseCurrency  = Mage::app ()->getStore ()->getBaseCurrencyCode ();
		
		$currency_symbol = Mage::app ()->getLocale ()->currency ( $baseCurrency )->getSymbol ();
		$product = Mage::getModel ( 'catalog/product' )->load ( $id );
		$product_id [] = $product->getId ();
		$product_name [] = $product->getName ();
		$product_url [] = Mage::getBaseUrl() . 'catalog/product/view/id/' . $product->getId ();
		$description [] = $product->getDescription ();
		$short_description [] = $product->getShortDescription ();
		$type [] = $product->getTypeId ();
		if ($product->isSaleable ()) {
			$stock [] = 'IN STOCK';
		} else {
			$stock [] = 'OUT OF STOCK';
		}
		if ($product->getSpecialPrice () != '' && $product->getSpecialPrice () < $product->getPrice ()) {
			$price [] = array (
					'special_price' => array (
							Mage::helper ( 'tax' )->getPrice ( $product, $product->getSpecialPrice () ) 
					),
					'regular_price' => array (
							Mage::helper ( 'tax' )->getPrice ( $product, $product->getPrice () ) 
					) 
			);
			$percentagediscount = Mage::helper ( 'tax' )->getPrice ( $product, $product->getPrice () ) - Mage::helper ( 'tax' )->getPrice ( $product, $product->getSpecialPrice () );
			$percentagediscount = ($percentagediscount * 100) / Mage::helper ( 'tax' )->getPrice ( $product, $product->getPrice () );
		} else {
			$price [] = array (
					'special_price' => array (
							null 
					),
					'regular_price' => array (
							Mage::helper ( 'tax' )->getPrice ( $product, $product->getPrice () ) 
					) 
			);
		}
		$discounted_price = Mage::getModel ( 'catalogrule/rule' )->calcProductPriceRule ( $product->setCustomerGroupId ( 'CUSTOMER_GROUP_ID' ), $product->getPrice () );
		
		// if the product isn't discounted then default back to the original price
		if ($discounted_price != false) {
			$price ['0'] ['special_price'] ['0'] = Mage::helper ( 'tax' )->getPrice ( $product, $discounted_price );
		}
		$gallery_images = $product->getMediaGalleryImages ();
		
		$items = array ();
		
		foreach ( $gallery_images as $g_image ) {
			$items [] = $g_image ['url'];
		}
		for($i = 1; $i <= count ( $items ); $i ++) {
			$mediaImage [] = array (
					'image' . $i => array (
							$items [$i - 1] 
					) 
			);
		}
		if (empty ( $mediaImage )) {
			$mediaImage [] = array (
					'image1' => array (
							Mage::helper ( 'catalog/image' )->init ( $product, 'small_image' )->__toString () 
					) 
			);
		}
		
		$data = array (
				'data' => array (
						'product-image' => $mediaImage,
						'product-id' => $product_id,
						'product-name' => $product_name,
						'product-url' => $product_url,
						'description' => $description,
						'short-description' => $short_description,
						'type' => $type,
						'stock' => $stock,
						'price' => $price,
						'currency_symbol' => $currency_symbol,
						'status' => 'enabled' 
				) 
		);
		
		$baseimg = Mage::helper ( 'catalog/image' )->init ( $product, 'image' )->__toString ();
		if ($baseimg != '') {
			$data ['data'] ['main-prod-img'] = $baseimg;
		}
		/* Configurable product data start */
		if ($product->type_id == 'configurable') {
			$configAttribute = Mage::getModel ( 'mobiconnectadvcart/catalog_product_configurable' )->getConfigAttribute ( $product );
			$configAvailability = Mage::getModel ( 'mobiconnectadvcart/catalog_product_configurable' )->getProductAvailability ( $product );
			$data ['data'] ['available'] = 'YES';
			if (count ( $configAvailability ) <= 0) {
				$data ['data'] ['available'] = 'NO';
			}
			$option = Mage::getModel ( 'mobiconnectadvcart/catalog_product_configurable' )->optionPrice ( $product );
			$attributeIds = Mage::getModel ( 'mobiconnectadvcart/catalog_product_configurable' )->getConfigAttributeIds ( $product );
			$data ['data'] ['option_price'] = $option;
			$data ['data'] ['attribute_option_ids'] = $attributeIds;
			$data ['data'] ['config_attribute'] = $configAttribute;
			$data ['data'] ['config_availability'] = $configAvailability;
		}
		/* Configurable product data end */
		/*Bundle Product data start*/
		if ($product->type_id == 'bundle') {
			$bundleditem = Mage::getModel ( 'mobiconnectadvcart/catalog_product_bundle' )->getBundleData ( $id );
			$data ['data'] ['price'] ['0'] ['special_price'] ['0'] = null;
			$regularprice = $data ['data'] ['price'] ['0'] ['regular_price'] ['0'];
			if (isset ( $bundleditem ['regular-percent'] ))
				$regularprice = $regularprice * $bundleditem ['regular-percent'] / 100;
			$data ['data'] ['price'] ['0'] ['regular_price'] ['0'] = $regularprice;
			$data ['data'] ['bundleddata'] = $bundleditem;
		}
		/* Bundle Product Data End */
		/*Grouped Product Data Start*/
		if ($product->type_id == 'grouped') {
			$groupdata = Mage::getModel ( 'mobiconnectadvcart/catalog_product_group' )->getGroupedProduct ( $product );
			$data ['data'] ['grouped_product'] = $groupdata;
		}
		/* Grouped Product Data End */
		/*Downloadable Product Data Start*/
		if ($product->type_id == 'downloadable') {
			$download = Mage::getModel ( 'mobiconnectadvcart/catalog_product_downloadable' )->downloadable ($id);
			$data ['data'] ['download-data'] = $download;
		}
		/* Downloadable Product Data End */
		/*Related Product Code*/
		$relatedProductIds = $product->getRelatedProductIds ();
		if (count ( $relatedProductIds ) > 0) {
			$relatedProductsInfo = Mage::getModel ( 'mobiconnectadvcart/catalog_product_relatedProduct' )->getRelatedProduct ( $relatedProductIds );
			$data ['data'] ['related_product'] = $relatedProductsInfo;
		}
		/* Related Product End */

		/*Percentage Discount And Review Count start*/
		$newCollection = Mage::getModel ( 'review/review' )->getCollection ()->addStoreFilter ( Mage::app ()->getStore ()->getId () )->addStatusFilter ( Mage_Review_Model_Review::STATUS_APPROVED )->addEntityFilter ( 'product', $product->getId () )->setDateOrder ();
		
		$reviewcount = $newCollection->count ();
		if ($product->getSpecialPrice () != '' && $product->getSpecialPrice () < $product->getPrice ()) {
			$data ['data'] ['offer'] = round ( $percentagediscount );
		}
		$data ['data'] ['review_count'] = $reviewcount;
		/* Percentage Discount And Review Count end */
		
		$tax_display_type = Mage::getStoreConfig ( 'tax/display/type', Mage::app ()->getStore () );
		if ($tax_display_type != 1) {
			$data ['data'] ['price-including-tax'] =  Mage::helper ( 'tax' )->getPrice ( $product, $product->getFinalPrice () );
		} else {
			$data ['data'] ['price-including-tax'] = '0';
		}
		if ($tax_display_type == 3) {
			$data ['data'] ['show-both-price'] = 'YES';
		} else {
			$data ['data'] ['show-both-price'] = 'NO';
		}
		$changeData = new Varien_Object ();
		$changeData->setProductId ( $id );
		$changeData->setProductData ( $data );
		if ($customerid != 'NULL' && $customerid != '') {
			$changeData->setCustomerId ( $customerid );
		}
		$dats = Mage::dispatchEvent ( 'change_product_data', array (
				'product_info' => $changeData 
		) );
		$data = $changeData->getProductData ();
		if(Mage::helper('core')->isModuleEnabled('Ced_CsMarketplace')){
			$vproduct = Mage::getModel("csmarketplace/vproducts");
			$vid = (int) $vproduct->getVendorIdByProduct($product);
			if($vid>0){
				$vendor = Mage::getModel("csmarketplace/vendor")->load($vid);
				if($vendor && $vendor->getId()){
					//$vendor->getPublicName();
					$data ['data'] ['vendor_id'] = $vid;
					$data ['data'] ['vendor_name'] = $vendor->getPublicName();
				}
			}
		}
		if($product->getData('has_options'))
		{
			$customOption=array();
			$count=0;
			
			//$helper = Mage::helper('mageworx_customoptions');
			//$configValue = $helper->getJsonInGroupIdData($product->getOptions());
			//$configValue = json_decode($configValue,true);
			//print_r($configValue); die("fkxx");
			foreach($product->getOptions() as $o){
				
				$optionType = $o->getType();
				
				
				
					$customOptionPrice = Mage::helper ( 'tax' )->getPrice ( $o, $o->getPrice () );
					if($o->getData('default_price_type')=='percent'){
					    $productSpecialPrice = Mage::helper ( 'tax' )->getPrice ( $product, $product->getSpecialPrice () );
						$calcPrice = $customOptionPrice * ($productSpecialPrice / 100);
						
						if (! isset ( $productSpecialPrice )) {
							$productPrice = Mage::helper ( 'tax' )->getPrice ( $product, $product->getPrice () );
							
							$calcPrice = $customOptionPrice * ($productPrice / 100);
						}
						$o->setPrice ($calcPrice);
					}
					else{
						$customOptionPrice = Mage::helper ( 'tax' )->getPrice ( $product, $o->getPrice () );
						$o->setPrice ($customOptionPrice);
					}
			        $values = $o->getValues();
			        $customOption[]=$o->getData();
			        $innercount=0;
			        if($values){
				        foreach ($values as $v) {
				        	$customValuePrice = Mage::helper ( 'tax' )->getPrice ( $v, $v->getPrice () );
				        	if($v->getData('default_price_type')=='percent'){
				        		$productSpecialPrice = $product->getSpecialPrice ();
								$calcPrice = $customValuePrice * ($productSpecialPrice / 100);
								if (! isset ( $productSpecialPrice )) {
									$productPrice = Mage::helper ( 'tax' )->getPrice ( $product, $product->getPrice () );
							
									$calcPrice = $customValuePrice * ($productPrice / 100);
								}
								$v->setPrice ($calcPrice);
							}
							else{
								$customValuePrice = Mage::helper ( 'tax' )->getPrice ( $product, $v->getPrice () );
								$v->setPrice ($customValuePrice);
							}
							$customOption[$count]['option'][$innercount]=$v->getData();
					       	++$innercount;
						}
					}
			    ++$count;
			}
			
			//print_r($customOption); die("dfkjv");
			$data ['data'] ['custom_option'] = $customOption;
		}
		
		return $data;
	}
	
	/*
	 * @param product collection,sort by option @return sorted product collection
	 */
	public function getSortedCollection($collection, $sort) {
		if ($sort) {
			$collection->setOrder ( $sort [0], $sort [1] );
		}
		return $collection;
	}
	/*
	 * @param category ,filter option @return filtered product collection
	 */
	public function getFilteredCollection($filters, $category) { 
		$storeId = Mage::app ()->getStore ()->getId ();
		$newids = array ();
		foreach ( $filters as $filtername => $val ) {
			// echo $filtername.'<br>';
			$count='1';
			foreach ( $val as $filterid => $key ) {
				if (count ( $newids ) == '0') {
					$productCollection = $category->getProductCollection ()->addAttributeToSelect ( $this->getProductAttributes () )->setStoreId ( $storeId )->addFinalPrice ();
					Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $productCollection );
					Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInSearchFilterToCollection ( $productCollection );
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
						$productCollection = $category->getProductCollection ()->addAttributeToSelect ( $this->getProductAttributes () )->setStoreId ( $storeId )->addFinalPrice ();
						Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $productCollection );
						Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInSearchFilterToCollection ( $productCollection );
					}
					$price = explode ( "-", $filterid );
					if (count ( $newids ) == '0') {
						if ($price [0] != '') {
							$productCollection->getSelect ()->where ( 'price_index.final_price >=' . $price [0] );
							if (count ( $productCollection ) > 0)
								$ids [] = $productCollection->getAllIds ();
						}
						if ($price [1] != '') {
							$productCollection->getSelect ()->where ( 'price_index.final_price <' . $price [1] );
							if (count ( $productCollection ) > 0)
								$ids [] = $productCollection->getAllIds ();
						}
					} else {
						if ($price [0] != '') {
							$newproductCollection->getSelect ()->where ( 'price_index.final_price >=' . $price [0] );
						}
						if ($price [1] != '') {
							$newproductCollection->getSelect ()->where ( 'price_index.final_price <' . $price [1] );
							/*
							 * if(count($productCollection)>0) $ids[]=$productCollection->getAllIds();
							 */
						}
					}
					// return $productCollection;
				}
				++$count;
			}
			if (count ( $newids ) == '0') {
				$newproductCollection = $category->getProductCollection ()->addIdFilter ( $ids )->addAttributeToSelect ( $this->getProductAttributes () )->setStoreId ( $storeId )->addFinalPrice ();
				Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $newproductCollection );
				Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInSearchFilterToCollection ( $newproductCollection );
				$newids [] = $newproductCollection->getAllIds ();
			}
		}
		// var_dump($ids);die;
		
		return $newproductCollection;
	}
	/*
	 * @query parameter return searched product
	 */
	public function getSearchedProduct($data) {
		$categoryId = $data ['id'];
		$order = $data ['order'];
		$dir = strtoupper ( $data ['dir'] );
		$offset = $data ['offset'];
		$limit = $data ['limit'];
		$curr_page = 1;
		// $filtername = $data ['filtername'];
		// $filterid = $data ['filterid'];
		$filters = $data ['filter'];
		if ($offset != 'null' && $offset != '') {
			
			$curr_page = $offset;
		}
		$offset = ($curr_page - 1) * $limit;
		
		if ($order != null && $dir != null) {
			$sort = array (
					$order,
					$dir 
			);
		}
		
		$_helper = Mage::helper ( 'catalogsearch' );
		$queryParam = str_replace ( '%20', ' ', $data ['query'] );
		
		Mage::app ()->getRequest ()->setParam ( $_helper->getQueryParamName (), $queryParam );
		/**
		 *
		 * @var $query Mage_CatalogSearch_Model_Query
		 */
		$query = $_helper->getQuery ();
		
		$query->setStoreId ( Mage::app ()->getStore ()->getId () );
		
		if ($query->getQueryText ()) {
			if ($_helper->isMinQueryLength ()) {
				$query->setId ( 0 )->setIsActive ( 1 )->setIsProcessed ( 1 );
			} else {
				if ($query->getId ()) {
					$query->setPopularity ( $query->getPopularity () + 1 );
				} else {
					$query->setPopularity ( 1 );
				}
				
				/**
				 * We don't support redirect at this moment
				 *
				 * @todo add redirect support for mobile application
				 */
				if (false && $query->getRedirect ()) {
					$query->save ();
					$this->getResponse ()->setRedirect ( $query->getRedirect () );
					return;
				} else {
					$query->prepare ();
				}
			}
			
			$_helper->checkNotes ();
			
			if (! $_helper->isMinQueryLength ()) {
				$query->save ();
			}
		}
		
		try {
			$helper = Mage::helper ( 'catalogsearch' );
			if (method_exists ( $helper, 'getEngine' )) {
				$engine = Mage::helper ( 'catalogsearch' )->getEngine ();
				if ($engine instanceof Varien_Object) {
					
					$isLayeredNavigationAllowed = $engine->isLeyeredNavigationAllowed ();
				} else {
					
					$isLayeredNavigationAllowed = true;
				}
			} else {
				
				$isLayeredNavigationAllowed = true;
			}
			
			$layer = Mage::getSingleton ( 'catalogsearch/layer' );
			$category = null;
			if ($category_id) {
				$category = Mage::getModel ( 'catalog/category' )->load ( $category_id );
				$layer->setCurrentCategory ( $category );
			}
			$collection = $layer->getProductCollection ();
			if (count ( $filters ) > 0) {
				$productCollection = $this->getSearchedFilteredCollection ( $filters, $collection );
			}
			else{
				$productCollection = $collection->addAttributeToSelect ( '*' )->joinAttribute ( 'name', 'catalog_product/name', 'entity_id', null, 'inner', 0 );
			}
			if(!isset($data['page_new']))
				$productCollection->getSelect ()->limit ( $limit, $offset );
			if ($category_id) {
				$productCollection->addCategoryFilter ( $category );
			}
			
			if ($productCollection) {
				$searchedProduct = $this->getListProduct ( $productCollection, $sort );
				$filterableattributes = $this->getFilterableAttributes ();
				$sortBy = $this->getSearchAttributeUsedForSortByArray ( $category );
				//$getCategoryFilter = $this->getCategoryFilter ( $productCollection );
			}
			if (count ( $searchedProduct ) > 0) {
				$data = array (
						'data' => array (
								'products' => $searchedProduct,
								'sort' => $sortBy,
								'filter' => $filterableattributes ['filter_attributes'],
								'filter_label' => $filterableattributes ['filter_label'],
								'category-filter' => $getCategoryFilter,
								'status' => 'success' 
						) 
				);
				return $data;
			} else {
				return;
			}
		} catch ( Mage_Core_Exception $e ) {
			$data = array (
					'data' => array (
							'message' => $e->getMessage (),
							'status' => 'exception' 
					) 
			);
			return $data;
		} catch ( Exception $e ) {
			$data = array (
					'data' => array (
							'message' => $e->getMessage (),
							'status' => 'exception' 
					) 
			);
			return $data;
		}
	}
	/*
	 * @return filtered collection for searched product
	 */
	public function getSearchedFilteredCollection($filters, $collection) {
		$storeId = Mage::app ()->getStore ()->getId ();
		$newids = array ();

		foreach ( $filters as $filtername => $val ) {
			$count='1';
			foreach ( $val as $filterid => $key ) {
				$layer = Mage::getModel( 'catalogsearch/layer' );
				$collection = $layer->getProductCollection ();
				if (count ( $newids ) == '0') {
					$productCollection = $collection->addAttributeToSelect ( $this->getProductAttributes () )->setStoreId ( $storeId )->addFinalPrice ();
					Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $productCollection );
					Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInSearchFilterToCollection ( $productCollection );
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
						$productCollection = $collection ()->addAttributeToSelect ( $this->getProductAttributes () )->setStoreId ( $storeId )->addFinalPrice ();
						Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $productCollection );
						Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInSearchFilterToCollection ( $productCollection );
					}
					$price = explode ( "-", $filterid );
					if (count ( $newids ) == '0') {
						if ($price [0] != '') {
							$productCollection->getSelect ()->where ( 'price_index.final_price >=' . $price [0] );
							if (count ( $productCollection ) > 0)
								$ids [] = $productCollection->getAllIds ();
						}
						if ($price [1] != '') {
							$productCollection->getSelect ()->where ( 'price_index.final_price <' . $price [1] );
							if (count ( $productCollection ) > 0)
								$ids [] = $productCollection->getAllIds ();
						}
					} else {
						if ($price [0] != '') {
							$newproductCollection->getSelect ()->where ( 'price_index.final_price >=' . $price [0] );
						}
						if ($price [1] != '') {
							$newproductCollection->getSelect ()->where ( 'price_index.final_price <' . $price [1] );
							/*
							 * if(count($productCollection)>0) $ids[]=$productCollection->getAllIds();
							 */
						}
					}
					// return $productCollection;
				}
				$productCollection="";
				++$count;
			}
			if (count ( $newids ) == '0') {
				$merged['0'] = call_user_func_array('array_merge', $ids);
				$layer = Mage::getModel( 'catalogsearch/layer' );
				$newproductCollection = $layer->getProductCollection ()->addIdFilter ( $merged )->addAttributeToSelect ( $this->getProductAttributes () )->setStoreId ( $storeId )->addFinalPrice ();
				$newids [] = $newproductCollection->getAllIds ();
			}
		}
		// var_dump($ids);die;
		
		return $newproductCollection;
	}
	/*
	 * @return filterable attributes available on search page
	 */
	public function getFilterableAttributes() {
		$layer = Mage::getSingleton ( 'catalogsearch/layer' );
		$attributes = $layer->getFilterableAttributes ();
		foreach ( $attributes as $attribute ) {
			if ($attribute->getAttributeCode () == 'price') {
				$filterBlockName = 'catalog/layer_filter_price';
			} elseif ($attribute->getBackendType () == 'decimal') {
				$filterBlockName = 'catalog/layer_filter_decimal';
			} else {
				$filterBlockName = 'catalogsearch/layer_filter_attribute';
			}
			$result = Mage::app ()->getLayout ()->createBlock ( $filterBlockName )->setLayer ( $layer )->setAttributeModel ( $attribute )->init ();
			
			$attCode = $attribute->getAttributeCode ();
			$attlabel = $attribute->getStoreLabel ();
			foreach ( $result->getItems () as $option ) {
				
				$attOptions [] = $option->getValue () . '#' . $option->getLabel ();
			}
			
			if (count ( $attOptions ) > 0)
				$filterableAttributes [] = array (
						$attCode => array_values ( $attOptions ) 
				);
			$filterlabel [] = array (
					'att_code' => $attCode,
					'att_label' => $attlabel 
			);
			$attOptions = array ();
		}
		$filterattributes = array (
				'filter_attributes' => $filterableAttributes,
				'filter_label' => $filterlabel 
		);
		return $filterattributes;
	}
	/*
	 * @return available category filter on search page
	 */
	public function getCategoryFilter($productCollection) {
		foreach ( $productCollection as $key ) {
			$cat = $key->getCategoryIds ();
		}
		foreach ( $cat as $key => $value ) {
			$category = Mage::getModel ( 'catalog/category' )->load ( $value );
			$path = $category->getPath ();
			$ids = explode ( '/', $path );
			if (isset ( $ids [2] )) {
				$topParent = Mage::getModel ( 'catalog/category' )->setStoreId ( Mage::app ()->getStore ()->getId () )->load ( $ids [2] );
			} else {
				$topParent = null; // it means you are in one catalog root.
			}
			
			$categoryfilter [] = array (
					'category' => $topParent->getName () 
			);
		}
		return array_unique ( $categoryfilter, SORT_REGULAR );
	}
	/*
	 * @return attributes available for sort on search page
	 */
	public function getSearchAttributeUsedForSortByArray() {
		foreach ( $this->getAttributesUsedForSortBy () as $attribute ) {
			$options [$attribute->getAttributeCode ()] = $attribute->getAttributeCode ();
		}
		foreach ( $options as $key => $val ) {
			if ($val == 'name') {
				$sort [] = array (
						"name:A to Z" => array (
								'asc' 
						),
						"name:Z to A" => array (
								'desc' 
						) 
				);
			}
			if ($val == 'price') {
				$sort [] = array (
						"price:Low To High" => array (
								'asc' 
						),
						"price:High To Low" => array (
								'desc' 
						) 
				);
			}
		}
		return $sort;
	}
}



