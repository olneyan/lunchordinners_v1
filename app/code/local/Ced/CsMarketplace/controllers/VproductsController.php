<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced;
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_CsMarketplace_VproductsController extends Ced_CsMarketplace_Controller_AbstractController {
	
	
	protected $mode='';
	const MAX_QTY_VALUE = 99999999.9999;
	
	public function preDispatch() {
		parent::preDispatch();
		if(Mage::registry('ced_csmarketplace_current_store')) 
			Mage::unRegister('ced_csmarketplace_current_store');
		if(Mage::registry('ced_csmarketplace_current_website'))
			Mage::unRegister('ced_csmarketplace_current_website');
		Mage::register('ced_csmarketplace_current_store',Mage::app()->getStore()->getId());
		Mage::register('ced_csmarketplace_current_website',Mage::app()->getStore()->getWebsiteId());
	}
	
	protected function _redirect($path,$arguments = array()) {
		if(Mage::registry('ced_csmarketplace_current_store')) {
			$currentStoreId = Mage::registry('ced_csmarketplace_current_store');
			Mage::app()->setCurrentStore($currentStoreId);
		}
		parent::_redirect($path,$arguments);
	}
	
	protected function _redirectReferer($defaultUrl=null) {
		if(Mage::registry('ced_csmarketplace_current_store')) {
			$currentStoreId = Mage::registry('ced_csmarketplace_current_store');
			Mage::app()->setCurrentStore($currentStoreId);
		}
		parent::_redirect($defaultUrl);
	}
	
	/**
	 * Default vendor products list page
	 */
	public function indexAction() {
		if(!$this->_getSession()->getVendorId()) return;
		$this->loadLayout ();
		$this->_initLayoutMessages ( 'customer/session' );
		$this->_initLayoutMessages ( 'catalog/session' );		
		$this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Product List' ) );
		$navigationBlock = $this->getLayout()->getBlock('csmarketplace_vendor_navigation');
		if ($navigationBlock) {
			$navigationBlock->setActive('csmarketplace/vproducts/index');
		}
		
		$params = $this->getRequest()->getParams();
		if(!isset($params['p']) && !isset($params['limit']) &&  is_array($params) )
			Mage::getSingleton('core/session')->setData('product_filter',$params);
		
		$this->renderLayout ();
	}
	
	/**
	 * Vendor new product page
	 */
	public function newAction() {
		if(!$this->_getSession()->getVendorId()) 
			return;
		
		if(count(Mage::getModel('csmarketplace/vproducts')->getVendorProductIds($this->_getSession()->getVendorId()))>=Mage::helper('csmarketplace')->getVendorProductLimit()){
			$this->_getSession()->addError('Product Creation limit has Exceeded');
			$this->_redirect('*/*/index/store/'.$this->getRequest()->getParam('store_switcher',0));
			return;
		}
		$allowedType = Mage::getModel('csmarketplace/system_config_source_vproducts_type')->getAllowedType(Mage::app()->getStore()->getId());
		
		$secretkey = time();
		$type = $this->getRequest()->getParam ('type',$secretkey);
		if ($type == $secretkey || (in_array($type,$allowedType))) {
			$update = $this->getLayout ()->getUpdate ();
			$update->addHandle ('default');
			$this->addActionLayoutHandles();
			switch ($type){
				case Mage_Catalog_Model_Product_Type::TYPE_SIMPLE : $update->addHandle ('csmarketplace_vproducts_simple');
								break;
				case Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL : $update->addHandle ('csmarketplace_vproducts_virtual');
								break;
				case Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE : $update->addHandle ('csmarketplace_vproducts_downloadable');
								break;
				default: $update->addHandle ('csmarketplace_vproducts_type');
								break;					
			}
			$this->loadLayoutUpdates ();
			$this->generateLayoutXml ();
			$this->generateLayoutBlocks ();
			$this->_isLayoutLoaded = true;
			$this->_initLayoutMessages ( 'customer/session' );
			$this->_initLayoutMessages ( 'catalog/session' );
			$this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'New' )." ".$this->__ ( 'Product' ) );
			$navigationBlock = $this->getLayout()->getBlock('csmarketplace_vendor_navigation');
			if ($navigationBlock) {
				$navigationBlock->setActive('csmarketplace/vproducts/new');
			}
			$this->renderLayout ();
		} else {
			$this->_redirect('*/*/new');
		}
	}
	
	/**
	 * Vendor edit product page
	 */
	public function editAction(){
		if(!$this->_getSession()->getVendorId()) return;
		$id=$this->getRequest()->getParam('id');
		$vendorId=$this->_getSession()->getVendorId();
		$vendorProduct=0;
		
		if($id&&$vendorId){
			$vendorProduct = Mage::getModel('csmarketplace/vproducts')->isAssociatedProduct($vendorId,$id);
		}
		if(!$vendorProduct){
			$this->_redirect ( 'csmarketplace/vproducts/index');
			return;
		}
		
		if ($type = $this->getRequest ()->getParam ('type')) {
			$update = $this->getLayout ()->getUpdate ();
			$update->addHandle ( 'default' );
			$this->addActionLayoutHandles ();
			switch ($type){
				case Mage_Catalog_Model_Product_Type::TYPE_SIMPLE :$update->addHandle ( 'csmarketplace_vproducts_simple' );
														break;
				case Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL :$update->addHandle ( 'csmarketplace_vproducts_virtual' );
														break;
				case Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE :$update->addHandle ( 'csmarketplace_vproducts_downloadable' );
														break;
				default:$this->_redirect ( 'csmarketplace/vproducts/index');
														break;
			}
			$this->loadLayoutUpdates ();
			$this->generateLayoutXml ();
			$this->generateLayoutBlocks ();
			$this->_isLayoutLoaded = true;
			$this->_initLayoutMessages ( 'customer/session' );
			$this->_initLayoutMessages ( 'catalog/session' );
			$this->getLayout ()->getBlock ( 'head' )->setTitle ($this->__ ( 'Edit' )." ".$this->__ ( 'Product' )  );
			$navigationBlock = $this->getLayout()->getBlock('csmarketplace_vendor_navigation');
			if ($navigationBlock) {
				$navigationBlock->setActive('csmarketplace/vproducts/');
			}
			$this->renderLayout ();
		}
		else {
			$this->_redirect ( 'csmarketplace/vproducts/index');
		}
	}
	
	
	/**
	 * Initialize product from request parameters
	 *
	 * @return Mage_Catalog_Model_Product|const ERROR_IN_PRODUCT_SAVE
	 */
	protected function _initProduct(){
		$productData=$this->getRequest ()->getPost();
		$productId = $this->getRequest ()->getParam ('id');
		
		if ($productId) 
			$this->mode=Ced_CsMarketplace_Model_Vproducts::EDIT_PRODUCT_MODE;
		else 
			$this->mode=Ced_CsMarketplace_Model_Vproducts::NEW_PRODUCT_MODE;
		
		$productData['entity_id']= $productId;
		$errors=array();
		try{
			Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
			$product = Mage::getModel('catalog/product');
			if ($this->mode==Ced_CsMarketplace_Model_Vproducts::EDIT_PRODUCT_MODE) {
				$product->setStoreId($this->getRequest()->getParam('store_switcher',0));
				$vendorId=$this->_getSession()->getVendorId();
				if($productId&&$vendorId){
					$vendorProduct = Mage::getModel('csmarketplace/vproducts')->isAssociatedProduct($vendorId,$productId);
					if(!$vendorProduct){
						return Ced_CsMarketplace_Model_Vproducts::ERROR_IN_PRODUCT_SAVE;
					}
				}
				$product->load($productId);
			}
			else if($this->mode==Ced_CsMarketplace_Model_Vproducts::NEW_PRODUCT_MODE){
				$product->setStoreId(0);
				$allowedType = Mage::getModel('csmarketplace/system_config_source_vproducts_type')->getAllowedType(Mage::app()->getStore()->getId());
				$type = $this->getRequest()->getParam ('type');
				if(!(in_array($type,$allowedType)))
					return Ced_CsMarketplace_Model_Vproducts::ERROR_IN_PRODUCT_SAVE;
			}
			$product->addData(isset($productData['product'])?$productData['product']:'');	
			$product->validate();
		}
		catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
			$errors[]=$e->getMessage();
		} catch (Mage_Core_Exception $e) {
			$errors[]=$e->getMessage();
		} catch (Exception $e) {
			$errors[]=$e->getMessage();
			$product->setTypeId(Mage_Catalog_Model_Product_Type::DEFAULT_TYPE);
			Mage::logException($e);
		}
		
		$vproductModel = Mage::getModel('csmarketplace/vproducts');
		$vproductModel->addData(isset($productData['product'])?$productData['product']:'');
		$vproductModel->addData(isset($productData['product']['stock_data'])?$productData['product']['stock_data']:'');
		$productErrors=$vproductModel->validate();
		
		if (is_array($productErrors)) {
			$errors = array_merge($errors, $productErrors);
		}
		
		if (!empty($errors)) {
			foreach ($errors as $message) {
				$this->_getSession()->addError($message);
			}
			return Ced_CsMarketplace_Model_Vproducts::ERROR_IN_PRODUCT_SAVE;
		}
		return $product;
		
	}
	
	/**
	 * Initialize product saving
	 * @return Mage_Catalog_Model_Product|const ERROR_IN_PRODUCT_SAVE
	 */
	protected function _initProductSave()
	{
		
		$product     = $this->_initProduct();
		if($product== Ced_CsMarketplace_Model_Vproducts::ERROR_IN_PRODUCT_SAVE)
			return Ced_CsMarketplace_Model_Vproducts::ERROR_IN_PRODUCT_SAVE;
		$productData = $this->getRequest()->getPost('product');
		$productId  = (int) $this->getRequest()->getParam('id');
	
		if ($productData) {
			$stock_data = isset($productData['stock_data'])?$productData['stock_data']:'';
			$this->_filterStockData($stock_data);
		}
		
		$product->addData($productData);
		/**
		 * Initialize product categories
		 */
		$categoryIds = $this->getRequest()->getPost('category_ids');
		if (null !== $categoryIds) {
			if (empty($categoryIds)) {
				$categoryIds = '';
			}
			$cats = explode(',',$categoryIds);
			$cats=array_unique($cats);
			$category_array = array ();
			foreach ( $cats as $value ) {
				if (strlen ( $value )) {
					$category_array [] = trim ( $value );
				}
			}
			$product->setCategoryIds ( $category_array );
		}
		
		if ($this->mode==Ced_CsMarketplace_Model_Vproducts::NEW_PRODUCT_MODE) {
			$setId = (int) $this->getRequest()->getParam('set')? (int) $this->getRequest()->getParam('set'):Mage::getModel('catalog/product')->getDefaultAttributeSetId();;
			$product->setAttributeSetId($setId);
		
			if ($typeId = $this->getRequest()->getParam('type')) {
				$product->setTypeId($typeId);
			}
			$product->setStatus (Mage::getModel('csmarketplace/vproducts')->isProductApprovalRequired()?Mage_Catalog_Model_Product_Status::STATUS_DISABLED:Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
			if(Mage::helper('csmarketplace')->isSharingEnabled()){
				$websiteIds = isset($productData['website_ids'])?$productData['website_ids']:array();
			}
			else
				$websiteIds =array(Mage::registry('ced_csmarketplace_current_website'));
			
			$product->setWebsiteIds($websiteIds);
			
		}
		
	
		if (Mage::app()->isSingleStoreMode()) {
			$product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
		}
		return $product;
	}
	
	
	
	/**
	 * Vendor save product action
	 */
	public function saveAction(){
		if(!$this->_getSession()->getVendorId()) 
			return;
		
		if(!$this->getRequest()->getParam('id')){
			if(count(Mage::getModel('csmarketplace/vproducts')->getVendorProductIds($this->_getSession()->getVendorId()))>=Mage::helper('csmarketplace')->getVendorProductLimit()){
				$this->_getSession()->addError($this->__('Product Creation limit has Exceeded'));
				$this->_redirect('*/*/index/store/'.$this->getRequest()->getParam('store_switcher',0));
				return;
			}
		}
		$product= array();
		$data = $this->getRequest ()->getPost();
		if ($data) {
			$product=$this->_initProductSave();
			if($product == Ced_CsMarketplace_Model_Vproducts::ERROR_IN_PRODUCT_SAVE){
				if($this->mode==Ced_CsMarketplace_Model_Vproducts::EDIT_PRODUCT_MODE) {
					 $this->_redirect('*/*/edit/id/'.$this->getRequest()->getParam ('id').'/type/'.$this->getRequest()->getParam ('type').'/store/'.$this->getRequest()->getParam('store_switcher',0));
				}else if($this->mode==Ced_CsMarketplace_Model_Vproducts::NEW_PRODUCT_MODE){
					$this->_getSession()->setFormError(true)->setProductFormData($data);
					 $this->_redirect('*/*/new/type/'.$this->getRequest()->getParam ('type'));
				}else{ 
					$this->_redirect('*/*/index/store/'.$product->getStoreId());
				}
				return;
			}
			try {
				$product=$product->save();
				$this->_getSession()->addSuccess(Mage::helper ( 'csmarketplace' )->__('The product has been saved.'));
				 if($this->mode==Ced_CsMarketplace_Model_Vproducts::NEW_PRODUCT_MODE)
					{
					 Mage::dispatchEvent(
					 'new_vendor_product_creation',array('product' => $product, 'vendor_id'=>$this->_getSession()->getVendorId())
					 );
					}
			} catch (Mage_Core_Exception $e) {
				$this->_getSession()->addError($e->getMessage())
				->setData($data);
				$redirectBack = true;
			} catch (Exception $e) {
				Mage::logException($e);
				$this->_getSession()->addError($e->getMessage());
				$redirectBack = true;
			}
			try {
				Mage::getModel('csmarketplace/vproducts')->setStoreId($product->getStoreId())->setProductData($product)->saveProduct($this->mode);
				$this->_redirect( 'csmarketplace/vproducts/index/store/'.$product->getStoreId());
				return;
			} catch (Mage_Core_Exception $e) {
				$this->_getSession()->addError($e->getMessage())
				->setData($data);
				$redirectBack = true;
			} catch (Exception $e) {
				Mage::logException($e);
				$this->_getSession()->addError($e->getMessage());
				$redirectBack = true;
			}
		}
		$storeId=0;
		if($product)
			$storeId=$product->getStoreId();
		if($this->mode==Ced_CsMarketplace_Model_Vproducts::EDIT_PRODUCT_MODE) {
				$this->_redirect('*/*/edit/id/'.$this->getRequest()->getParam ('id').'/type/'.$this->getRequest()->getParam ('type').'/store/'.$this->getRequest()->getParam('store_switcher',0));
		}else if($this->mode==Ced_CsMarketplace_Model_Vproducts::NEW_PRODUCT_MODE){
				$this->_getSession()->setFormError(true)->setProductFormData($data);
				$this->_redirect('*/*/new/type/'.$this->getRequest()->getParam ('type'));
		}else{ 
				$this->_redirect('*/*/index/store/'.$storeId);
		}
		return;
	}
	
	/**
	 * Filter product stock data
	 *
	 * @param array $stockData
	 * @return null
	 */
	protected function _filterStockData(&$stockData)
	{
		if (is_null($stockData)) {
			return;
		}
		if (!isset($stockData['use_config_manage_stock'])) {
			$stockData['use_config_manage_stock'] = 0;
		}
		if (isset($stockData['qty']) && (float)$stockData['qty'] > self::MAX_QTY_VALUE) {
			$stockData['qty'] = self::MAX_QTY_VALUE;
		}
		if (isset($stockData['min_qty']) && (int)$stockData['min_qty'] < 0) {
			$stockData['min_qty'] = 0;
		}
		if (!isset($stockData['is_decimal_divided']) || $stockData['is_qty_decimal'] == 0) {
			$stockData['is_decimal_divided'] = 0;
		}
	}
	
	/**
	 * Vendor delete product action
	 */
	public function deleteAction(){
		if(!$this->_getSession()->getVendorId()) return;
		$id=$this->getRequest()->getParam('id');
		$vendorId=$this->_getSession()->getVendorId();
		$redirectBack = false;
		$vendorProduct = Mage::getModel('csmarketplace/vproducts')->isAssociatedProduct($vendorId,$id);
		if(!$vendorProduct){
			$redirectBack=true;
		}
		else if($id){
			Mage::register("isSecureArea", 1);
			Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
			try {
				$product = Mage::getModel('catalog/product')->load($id);
				if($product && $product->getId()) {
					$product->delete();
					Mage::getModel('csmarketplace/vproducts')->changeVproductStatus(array($id),Ced_CsMarketplace_Model_Vproducts::DELETED_STATUS);
				}
			}
			catch (Mage_Core_Exception $e) {
				Mage::logException($e);
				$redirectBack = true;
			} catch (Exception $e) {
				Mage::logException($e);
				$redirectBack = true;
			}			
			$this->_getSession ()->addSuccess( Mage::helper('csmarketplace')->__('Your Product Has Been Sucessfully Deleted'));
		}
		else 
			$redirectBack=true;	
		if ($redirectBack) {
			$this->_getSession ()->addError( Mage::helper('csmarketplace')->__('Unable to delete the product'));				
		}
		$this->_redirect('*/*/index');
			
	}
	
	
	
		
	/**
	 * Vendor check product SKU action
	 */
	public function checkSkuAction(){
		if(!$this->_getSession()->getVendorId()) return;
		$sku=$this->getRequest()->getParam('sku');
		$current_id=$this->getRequest()->getParam('id');
		$id = Mage::getModel('catalog/product')->getIdBySku($sku);
		if($current_id && $id==$current_id)
			$result=1;
		else if($id)
			$result=0; 
		else
			$result=1;
		echo json_encode(array("result"=>$result));
	}
	
	/**
	 * Vendor delete product image action
	 */
	public function deleteImageAction(){
		if(!$this->_getSession()->getVendorId()) return;
		$result=0;
		$data= $this->getRequest()->getParams();
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		try{
			$mediaApi = Mage::getModel("catalog/product_attribute_media_api");
			$items = $mediaApi->items($data['productid']);
			if(is_array($items) && count($items)>0 && isset($data['imagename'])){
	    		foreach($items as $item){
	    			if($item['file']==$data['imagename']){
	        			$mediaApi->remove($data['productid'], $item['file']);
	        			$result=1;
	    			}
				}
			}
			$product=Mage::getModel('catalog/product')->setStoreId($data['storeid'])->load($data['productid']);
			if($product && $product->getId() && isset($data['imagename'])){
				if($data['imagename']==$product->getImage()){
					$product->setStoreId($data['storeid'])->setImage(null)->setThumnail(null)->setSmallImage(null)->save();
					$result=1;
				}
			}
		}
	 	catch ( Exception $e ) {
			$result=0;
		}
		echo $result;
	}
	
	/**
	 * Vendor delete product link action
	 */
	public function deleteLinkAction(){
		if(!$this->_getSession()->getVendorId()) return;
		$result=0;
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		$data= $this->getRequest()->getParams();
		try{
			Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
			if(isset($data['linkid'])){
				$link=Mage::getModel('downloadable/link')->load($data['linkid']);
				if($link&&$link->getId()){
					$link->delete();
					$result=1;
				}
			}
		}
		catch ( Exception $e ) {
			$result=0;
		}
		echo $result;
	}
	
	/**
	 * Vendor delete product sample action
	 */
	public function deleteSampleAction(){
		if(!$this->_getSession()->getVendorId()) return;
		$result=0;
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		$data= $this->getRequest()->getParams();
		try{
			Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
			if(isset($data['sampleid'])){
				$sample=Mage::getModel('downloadable/sample')->load($data['sampleid']);
				if($sample&&$sample->getId()){
					$sample->delete();
					$result=1;
				}
			}
		}
		catch ( Exception $e ) {
			$result=0;
		}
		echo $result;
	}

	/**
	 * Vendor product page category list action
	 */
	public function categorytreeAction(){
		if(!$this->_getSession()->getVendorId()) return;
		$data = $this->getRequest()->getParams();
		$category_model = Mage::getModel("catalog/category")->setStoreId($this->getRequest()->getParam('store',0));
		$category = $category_model->load($data["cd"]);
		$children = $category->getChildren();
		$all = explode(",",$children);
		$result_tree = "";
		$ml = $data["ml"]+20;
		$count = 1;
		$total = count($all);
		$plus = 0;
		
		$allowed_categories=array();
		$category_mode=0;
		$category_mode = Mage::getStoreConfig('ced_vproducts/general/category_mode',0);
		if($category_mode)
			$allowed_categories = explode(',',Mage::getStoreConfig('ced_vproducts/general/category',0));
		
		foreach($all as $each){
			$count++;
			$_category = $category_model->load($each);
			
			if($category_mode && !in_array($_category['entity_id'], $allowed_categories))
				continue;
			if($category_mode)
				$childrens=count(array_intersect($category_model->getResource()->getAllChildren($category_model->load($_category['entity_id'])),$allowed_categories))-1;
			else
				$childrens=count($category_model->getResource()->getAllChildren($category_model->load($_category['entity_id'])))-1;
			
			if($childrens > 0){
			    $result[$plus]['counting']=1;
				$result[$plus]['id']= $_category['entity_id'];
				$result[$plus]['name']= $_category->getName(); 
				$result[$plus]['product_count']=Mage::getModel('csmarketplace/vproducts')->getProductCount($_category['entity_id']);
			}
			else{
				$result[$plus]['counting']=0;
				$result[$plus]['id']= $_category['entity_id'];
		    	$result[$plus]['name']= $_category->getName();
		    	$result[$plus]['product_count']=Mage::getModel('csmarketplace/vproducts')->getProductCount($_category['entity_id']);
			}			
			$plus++;
		}
		echo json_encode($result);
	}
	
	/**
	 * Filter Products Action
	 */
	public function filterAction()
	{
		if(!$this->_getSession()->getVendorId()) return;
		$reset_filter = $this->getRequest()->getParam('reset_product_filter');
		$params = $this->getRequest()->getParams();
		
		if($reset_filter==1)
			Mage::getSingleton('core/session')->uns('product_filter');
		elseif(!isset($params['p']) && !isset($params['limit']) &&  is_array($params) )
			Mage::getSingleton('core/session')->setData('product_filter',$params);
	
		$this->loadLayout();
		$this->renderLayout();
	}
}
