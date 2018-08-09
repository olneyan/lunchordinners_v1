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
class Ced_Mobiconnect_Model_Catalog_Product extends Mage_Catalog_Model_Config {
	/**
	 *
	 * @param
	 *        	product collection
	 * @return category products and rating count
	 */
	public function getListProduct($collection, $sort,$customerid=null) {
		$baseCurrency = Mage::app ()->getStore ()->getDefaultCurrencyCode ();
		$currency_symbol = Mage::app ()->getLocale ()->currency ( $baseCurrency )->getSymbol ();
		$avg=0;
		if ($sort) {
			$collection = $this->getSortedCollection ( $collection, $sort );
		}
		$productList = array ();
		$count = 0;
		foreach ( $collection as $product ) {
			
			$productId = $product->getId ();
			$changeData = new Varien_Object ();
					$changeData->setProductId ( $productId );
					$changeData->setIsCategory('1');
					if($customerid!='NULL' && $customerid!=''){
						$changeData->setCustomerId ($customerid);
					}
					$dats = Mage::dispatchEvent ( 'change_product_data', array (
							'product_info' => $changeData 
					) );

			$avg=$changeData->getAvg();
			if($avg=='' && $avg=='')
				$avg=0;
			$wishlist=$changeData->getWishlist();
			$iswishlist='OUT';
			if($wishlist=='1'){
				$iswishlist='IN';
			}
			if ($product->getSpecialPrice () != '' && $product->getSpecialPrice () < $product->getPrice ()) {
				$categ_product [] = array (
						'product_id' => $product->getId (),
						'product_name' => $product->getName (),
						'type' => $product->getTypeId (),
						'description' => $product->getShortDescription (),
						'review'=>$avg,
						'Inwishlist'=>$iswishlist,
						'special_price' => Mage::helper('core')->currency(Mage::helper ( 'tax' )->getPrice ( $product, $product->getSpecialPrice ()),true,false),
						'regular_price' => Mage::helper('core')->currency(Mage::helper ( 'tax' )->getPrice ( $product, $product->getPrice ()),true,false),
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
						'Inwishlist'=>$iswishlist,
						'special_price' => 'no_special',
						'regular_price' => Mage::helper('core')->currency(Mage::helper ( 'tax' )->getPrice ( $product, $product->getPrice ()),true,false),
						'stock_status' => $product->isSaleable (),
						'product_image' => Mage::helper ( 'catalog/image' )->init ( $product, 'small_image' )->__toString () 
				);
			}
			
			$discounted_price = Mage::getModel ( 'catalogrule/rule' )->calcProductPriceRule ( $product->setCustomerGroupId ( 'CUSTOMER_GROUP_ID' ), $product->getPrice () );
			
			// if the product isn't discounted then default back to the original price
			if ($discounted_price != false) {
				$categ_product [$count] ['special_price'] = Mage::helper('core')->currency(Mage::helper ( 'tax' )->getPrice ( $product, $discounted_price),true,false);
			}
			$tax_display_type = Mage::getStoreConfig ( 'tax/display/type', Mage::app ()->getStore () );
		if ($tax_display_type != 1) {
			$data ['data'] ['price-including-tax'] = Mage::helper('core')->currency(Mage::helper ( 'tax' )->getPrice ( $product, $product->getFinalPrice ()),true,false);
		} else {
			$data ['data'] ['price-including-tax'] = '0';
		}
		if ($tax_display_type == 3) {
			$data ['data'] ['show-both-price'] = 'YES';
		} else {
			$data ['data'] ['show-both-price'] = 'NO';
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
		$customer=$data['customer_id'];
		$curr_page = 1;
		$filtername = $data ['filtername'];
		$filterid = $data ['filterid'];
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
		if ($filtername != 'NULL' && $filtername != '') {
			$category = Mage::getModel ( 'catalog/category' )->load ( $categoryId );
			$productCollection = $this->getFilteredCollection ( $filtername, $filterid, $category );
		} else {
			$category = Mage::getModel ( 'catalog/category' )->load ( $categoryId );
			$storeId = Mage::app ()->getStore ()->getId ();
			$productCollection = $category->getProductCollection ()->addAttributeToFilter('type_id','simple')->addAttributeToSelect ( $this->getProductAttributes () )->setStoreId ( $storeId )->addFinalPrice ();
			
			Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $productCollection );
			Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInSearchFilterToCollection ( $productCollection );
		}
		// $productCollection
		$productCollection->getSelect ()->limit ( $limit, $offset );
		$sortBy = $this->getAttributeUsedForSortByArray ( $category );
		$categoryProducts = $this->getListProduct ( $productCollection, $sort,$customer );
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
		$baseCurrency = Mage::app ()->getStore ()->getDefaultCurrencyCode ();
		$currency_symbol = Mage::app ()->getLocale ()->currency ( $baseCurrency )->getSymbol ();
		$product = Mage::getModel ( 'catalog/product' )->load ( $id );
		$product_id [] = $product->getId ();
		$product_name [] = $product->getName ();
		$product_url[]=$product->getProductUrl().'/prodID/'.$product->getId ();
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
				$price ['0']['special_price']['0'] = Mage::helper ( 'tax' )->getPrice ( $product, $discounted_price );
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
							'product-url'=>$product_url,
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
					if($customerid!='NULL' && $customerid!='')
					{
						$changeData->setCustomerId ( $customerid );
					}
					$dats = Mage::dispatchEvent ( 'change_product_data', array (
							'product_info' => $changeData 
					) );
		$data=$changeData->getProductData();
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
	public function getFilteredCollection($filtername, $filterid, $category) {
		if ($filtername != 'price') {
			
			$storeId = Mage::app ()->getStore ()->getId ();
			$productCollection = $category->getProductCollection ()->addAttributeToFilter ( 'type_id', 'simple' )->addAttributeToSelect ( $this->getProductAttributes () )->setStoreId ( $storeId )->addFinalPrice ();
			
			Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $productCollection );
			Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInSearchFilterToCollection ( $productCollection );
			$productCollection->addAttributeToFilter ( $filtername, $filterid );
			return $productCollection;
		} else {
			
			$price = explode ( "-", $filterid );
			$storeId = Mage::app ()->getStore ()->getId ();
			$productCollection = $category->getProductCollection ()->addAttributeToFilter ( 'type_id', 'simple' )->addAttributeToSelect ( '*' )->setStoreId ( $storeId )->addFinalPrice ()->addMinimalPrice ()->addTaxPercents ();
			
			Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $productCollection );
			Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInSearchFilterToCollection ( $productCollection );
			if ($price [0] != '') {
				$productCollection->getSelect ()->where ( 'price_index.final_price >=' . $price [0] );
			}
			if ($price [1] != '') {
				$productCollection->getSelect ()->where ( 'price_index.final_price <' . $price [1] );
			}
			return $productCollection;
		}
	}
	/*
	 * @query parameter return searched product
	 */
	public function getSearchedProduct($data) {

		$categoryId = '';
		$order = $data ['order'];
		$dir = strtoupper ( $data ['dir'] );
		$offset = $data ['offset'];
		$limit = $data ['limit'];
		$curr_page = 1;
		$filtername = $data ['filtername'];
		$filterid = $data ['filterid'];
		$sort=array();
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
			$filterableattributes = $this->getFilterableAttributes ();
			$layer = Mage::getSingleton ( 'catalogsearch/layer' );
			$category = null;
			/*if ($category_id) {
				$category = Mage::getModel ( 'catalog/category' )->load ( $category_id );
				$layer->setCurrentCategory ( $category );
			}*/
			$collection = $layer->getProductCollection ();
			if ($filtername != 'NULL' && $filtername != '') {
				$productCollection = $this->getSearchedFilteredCollection ( $filtername, $filterid, $collection );
			}
			$productCollection = $collection->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'type_id', 'simple' );
			$productCollection->getSelect ()->limit ( $limit, $offset );
			/*if ($category_id) {
				$productCollection->addCategoryFilter ( $category );
			}*/
			
			if ($productCollection) {
				$searchedProduct = $this->getListProduct ( $productCollection, $sort );
				//$filterableattributes = $this->getFilterableAttributes ();
				$sortBy = $this->getSearchAttributeUsedForSortByArray ( $category );
				$getCategoryFilter = $this->getCategoryFilter ( $productCollection );
			}
			if (count ( $searchedProduct ) > 0) {
				$data = array (
						'data' => array (
								'products' => $searchedProduct,
								'sort' => $sortBy,
								'filter' => $filterableattributes['filter_attributes'],
								'filter_label'=>$filterableattributes['filter_label'],
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
	public function getSearchedFilteredCollection($filtername, $filterid, $collection) {
		if ($filtername != 'price') {
			
			$storeId = Mage::app ()->getStore ()->getId ();
			$productCollection = $collection->addAttributeToFilter ( 'type_id', 'simple' )->addAttributeToSelect ( $this->getProductAttributes () )->setStoreId ( $storeId )->addFinalPrice ();
			
			Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $productCollection );
			Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInSearchFilterToCollection ( $productCollection );
			$productCollection->addAttributeToFilter ( $filtername, $filterid );
			return $productCollection;
		} else {
			
			$price = explode ( "-", $filterid );
			$storeId = Mage::app ()->getStore ()->getId ();
			$productCollection = $collection->addAttributeToFilter ( 'type_id', 'simple' )->addAttributeToSelect ( '*' )->setStoreId ( $storeId )->addFinalPrice ()->addMinimalPrice ()->addTaxPercents ();
			Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $productCollection );
			Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInSearchFilterToCollection ( $productCollection );
			$productCollection->getSelect ()->where ( 'price_index.final_price >=' . $price [0] );
			if ($price [1] != '') {
				$productCollection->getSelect ()->where ( 'price_index.final_price <' . $price [1] );
			}
			return $productCollection;
		}
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
			$attlabel=$attribute->getStoreLabel ();
			foreach ( $result->getItems () as $option ) {
				
				$attOptions [] = $option->getValue () . '#' . $option->getLabel ();
			}
			
			if (count ( $attOptions ) > 0)
				$filterableAttributes [] = array (
						$attCode => array_values ( $attOptions ) 
				);
				$filterlabel[]=array(
							'att_code'=>$attCode,
							'att_label'=>$attlabel
							);
			$attOptions = array ();
		}
		$filterattributes=array(
			'filter_attributes'=>$filterableAttributes,
			'filter_label'=>$filterlabel
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
		return $categoryfilter;
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


