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

/**
 * Vendor Product model
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Model_Vproducts extends Ced_CsMarketplace_Model_Abstract {
	
	const NOT_APPROVED_STATUS = 0;
	const APPROVED_STATUS = 1;
	const PENDING_STATUS = 2;	
	const DELETED_STATUS = 3;
	
	const ERROR_IN_PRODUCT_SAVE="error";
	
	const NEW_PRODUCT_MODE='new';
	const EDIT_PRODUCT_MODE='edit';
	const AREA_FRONTEND="frontend";
	
	protected $_eventPrefix = 'ced_csmarketplace_vproduct';
	
	protected $_vproducts=array();
	/**
	 * Initialize vproducts model
	 */
	protected function _construct() {
		$this->_init ( 'csmarketplace/vproducts' );
	}
	
	/**
	 * Check Product Admin Approval required
	 */
	public function isProductApprovalRequired(){
		return Mage::getStoreConfig('ced_vproducts/general/confirmation',Mage::app()->getStore()->getId());
	}
	
	/**
	 * Filter options
	 */
	public function getOptionArray() {
		return array (
				self::APPROVED_STATUS => Mage::helper('csmarketplace')->__('Approved'),
				self::PENDING_STATUS=> Mage::helper('csmarketplace')->__('Pending'),
				self::NOT_APPROVED_STATUS => Mage::helper('csmarketplace')->__('Disapproved') 
		);
	}
	
	/**
	 * Filter options
	 */
	public function getVendorOptionArray() {
		return array (
				self::APPROVED_STATUS.Mage_Catalog_Model_Product_Status::STATUS_ENABLED => Mage::helper('csmarketplace')->__('Approved (Enabled)'),
				self::APPROVED_STATUS.Mage_Catalog_Model_Product_Status::STATUS_DISABLED => Mage::helper('csmarketplace')->__('Approved (Disabled)'),
				self::PENDING_STATUS=> Mage::helper('csmarketplace')->__('Pending'),
				self::NOT_APPROVED_STATUS => Mage::helper('csmarketplace')->__('Disapproved')
		);
	}
	
	/**
	 * Mass action options
	 */
	public function getMassActionArray() {
		return array (
				self::APPROVED_STATUS  => Mage::helper('csmarketplace')->__('Approved'),
				self::NOT_APPROVED_STATUS => Mage::helper('csmarketplace')->__('Disapproved') 
		);
	}
	
		
	/**
	 * Get Vendor Id by Product|Product Id
	 *
	 * @param Mage_Catalog_Model_Product|int $product
	 * @return int $vendorId
	 */
	public function getVendorIdByProduct($product) {
		$vproduct = false;
		if (is_numeric($product)) {
            $vproduct = $this->loadByField('product_id',$product);
        } elseif ($product && $product->getId()) {
			$vproduct = $this->loadByField('product_id',$product->getId());
		}
		if ($vproduct && $vproduct->getId()) {
			return $vproduct->getVendorId();
		}
		return false;
	}

	
	/**
	 * Validate csmarketplace product attribute values.
	 * @return array $errors
	 */
	public function validate(){
		$errors = array();
		$helper=Mage::helper('csmarketplace');
		if (!Zend_Validate::is( trim($this->getName()) , 'NotEmpty')) {
			$errors[] = $helper->__('The Product Name cannot be empty');
		}
		if (!Zend_Validate::is( trim($this->getSku()) , 'NotEmpty')) {
			$errors[] = $helper->__('The Product SKU cannot be empty');
		}
		
		if($this->getType()==Mage_Catalog_Model_Product_Type::TYPE_SIMPLE){
			$weight=	trim($this->getWeight());
			if (!Zend_Validate::is($weight, 'NotEmpty')) {
				$errors[] = $helper->__('The Product Weight cannot be empty');
			}
			else if(!is_numeric($weight)&&!($weight>0))
				$errors[] = $helper->__('The Product Weight must be 0 or Greater');
		}
		
		$qty=trim($this->getQty());
		if (!Zend_Validate::is( $qty, 'NotEmpty')) {
			$errors[] = $helper->__('The Product Stock cannot be empty');
		}
		else if(!is_numeric($qty))
			$errors[] = $helper->__('The Product Stock must be a valid Number');
			
		if (!Zend_Validate::is( trim($this->getTaxClassId()) , 'NotEmpty')) {
			$errors[] = $helper->__('The Product Tax Class cannot be empty');
		}
		
		$price=trim($this->getPrice());
		if (!Zend_Validate::is( $price, 'NotEmpty')) {
			$errors[] = $helper->__('The Product Price cannot be empty');
		}
		else if(!is_numeric($price)&&!($price>0))
			$errors[] = $helper->__('The Product Price must be 0 or Greater');
		
		$special_price=trim($this->getSpecialPrice());
		if($special_price!=''){
			if(!is_numeric($special_price)&&!($special_price>0))
			$errors[] = $helper->__('The Product Special Price must be 0 or Greater');
		}
		
		$shortDescription=strip_tags(trim($this->getShortDescription()));
		$description=strip_tags(trim($this->getDescription()));	
		if (strlen($shortDescription)==0) {
			$errors[] = $helper->__('The Product Short description cannot be empty');
		}
		if (strlen($description)==0) {
			$errors[] = $helper->__('The Product Description cannot be empty');
		}
		if (empty($errors)) {
			return true;
		}
		return $errors;
	}
	
	/**
	 * Save Product
	 * @params $mode
	 * @return int product id
	 */
	public function saveProduct($mode) {
		$product = $this->getProductData();
		$productData = Mage::app()->getRequest()->getPost();
		$productId=$product->getId();
		
		/**
		 * Save Stock data
		 * @params int $productId,array $stockdata
		 */
		$this->saveStockData($productId,$product->getStockData());
		
		/**
		 * Relate Product data
		 * @params int mode,int $productId,array $productData
		 */
		$this->processPostSave($mode,$product,$productData);
		
		/**
		 * Save Product Type Specific data
		 * @params int $productId,array $productData
		 */
		$this->saveTypeData($productId,$productData);
		
		/**
		 * Save Product Images
		 * @params int $productId,array $productData
		 */
		Mage::helper('csmarketplace/vproducts_image')->saveImages ($product,$productData);
		
		
		/**
		 * Send Product Mails
		 * @params array productid,int $status
		 */
		if(!$this->isProductApprovalRequired() && $mode==self::NEW_PRODUCT_MODE){
			Mage::helper('csmarketplace/mail')
			->sendProductNotificationEmail(array($productId),Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS);
		}
		
	}
	
	/**
	 * Save Product Stock data
	 * @params int $productId,array $productData
	 * @return int product id
	 */
	private function saveStockData($productId,$stockData){
		$stockItem = Mage::getModel('cataloginventory/stock_item');
		$stockItem->loadByProduct($productId);
		if(!$stockItem->getId()){$stockItem->setProductId($productId)->setStockId(1);}
		$stockItem->setProductId($productId)->setStockId(1);
		
		$is_in_stock = isset($stockData['is_in_stock'])?$stockData['is_in_stock']:1;
		$stockItem->setData('is_in_stock', $is_in_stock);
		
		$savedStock = $stockItem->save();
		
		$qty = isset($stockData['qty'])?$stockData['qty']:0;
		$stockItem->load($savedStock->getId())->setQty($qty)->save();
		
		$is_in_stock = isset($stockData['is_in_stock'])?$stockData['is_in_stock']:1;
		$stockItem->setData('is_in_stock', $is_in_stock);
		
		$use_config_manage_stock = isset($stockData['use_config_manage_stock'])?$stockData['use_config_manage_stock']:0;
		$stockItem->setData('use_config_manage_stock', $use_config_manage_stock);
		
		$stockItem->setData('manage_stock', 1);
		
		$is_decimal_divided = isset($stockData['is_decimal_divided'])?$stockData['is_decimal_divided']:0;
		$stockItem->setData('is_decimal_divided', $is_decimal_divided);
		
		$savedStock = $stockItem->save();
	}
	
	/**
	 * Save Product Type Specific data
	 * @params int $productId,array $productData
	 * @return int product id
	 */
	private function saveTypeData($productId,$productData){
		$type=isset($productData['type'])?$productData['type']:Mage_Catalog_Model_Product_Type::DEFAULT_TYPE;
		
		switch($type){
			case Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE:$this->saveDownloadableData($productId,isset($productData['downloadable'])?$productData['downloadable']:array());
								break;
			
			default:return;
			
		}
	}
	
	/**
	 * Save Downloadable product data
	 * @params array $data,int id
	 * @return int product id
	 */
	public function saveDownloadableData($productid,$downloadableData)	{
		$samples=array();
		$link_samples=array();
		$links=array();
	
		$linkhelper=Mage::helper('csmarketplace/vproducts_link');
	
		/**
		 * Start uploading data
		 *
		*/
		$samples=$linkhelper->uploadDownloadableFiles("samples",isset($downloadableData['sample'])?$downloadableData['sample']:array());
		$link_samples=$linkhelper->uploadDownloadableFiles("link_samples",isset($downloadableData['link'])?$downloadableData['link']:array());
		$links=$linkhelper->uploadDownloadableFiles("links",isset($downloadableData['link'])?$downloadableData['link']:array());

		/**
		 * Start saving links data
		 *
		*/
		$linkhelper->processLinksData(isset($downloadableData['link'])?$downloadableData['link']:array(),$links,$link_samples,$productid);
		$linkhelper->processSamplesData(isset($downloadableData['sample'])?$downloadableData['sample']:array(),$samples,$productid);
	}
	
	/**
	 * Get Vproduct status
	 * @params $storeId
	 *
	 */
	public function getStatus($storeId){
		$statusModel=Mage::getModel('csmarketplace/vproducts_status')->loadByField(array('product_id','store_id'),array($this->getProductId(),$storeId));
		if($statusModel && $statusModel->getId())
			return $statusModel->getStatus();
		else 
			return 0;
	}
	
	/**
	 * Set Vproduct status
	 * @params $mode,int $productId,array $productData
	 *
	 */
	public function setStatus($status){	
		if($this->getStoreId()){
			$statusAttribute = Mage::getResourceModel('catalog/product_status')->getProductAttribute('status');
			if ($statusAttribute->isScopeWebsite()) {
				$website = Mage::app()->getStore($this->getStoreId())->getWebsite();
				$stores  = $website->getStoreIds();
			} else if ($statusAttribute->isScopeStore()) {
				$stores = array($this->getStoreId());
			} else {
				$stores = array_keys(Mage::app()->getStores());
			}
		}
		else 
			$stores=array(0);//admin store
		foreach ($stores as $store){	
			$statusModel=Mage::getModel('csmarketplace/vproducts_status')->loadByField(array('product_id','store_id'),array($this->getProductId(),$store));
			if($statusModel && $statusModel->getId()){
				if($statusModel->getStatus()!=$status)
				 	$statusModel->setStatus($status)->save();
			}
			else {
				$statusModel=Mage::getModel('csmarketplace/vproducts_status');
				$statusModel->setStatus($status)
				->setStoreId($store)
				->setProductId($this->getProductId())
				->save();
			}
		}
		return $this;
	}
	
	
	/**
	 * Relate Product Data
	 * @params $mode,int $productId,array $productData
	 * 
	 */
	public function processPostSave($mode,$product,$productData){
		$websiteIds='';
		if(isset($productData['product']['website_ids']))
			$websiteIds=implode(",",$productData['product']['website_ids']);
		else if(Mage::registry('ced_csmarketplace_current_website')!='')
			$websiteIds=Mage::registry('ced_csmarketplace_current_website');
		else 
			$websiteIds=implode(",",$product->getWebsiteIds());
		$productId=$product->getId();
		$storeId=$this->getStoreId();
		switch($mode) {
			case self::NEW_PRODUCT_MODE:
									$prodata = isset($productData['product'])?$productData['product']:array();
									Mage::getModel('csmarketplace/vproducts')->setData($prodata)
										->setQty(isset($productData['product']['stock_data']['qty'])?$productData['product']['stock_data']['qty']:0)
										->setIsInStock(isset($productData['product']['stock_data']['is_in_stock'])?$productData['product']['stock_data']['is_in_stock']:1)
										->setPrice($product->getPrice())
										->setSpecialPrice($product->getSpecialPrice())
										->setCheckStatus ($this->isProductApprovalRequired()?self::PENDING_STATUS:self::APPROVED_STATUS )
										->setProductId ($productId)
										->setVendorId(Mage::getSingleton('customer/session')->getVendorId())
										->setType(isset($productData['type'])?$productData['type']:Mage_Catalog_Model_Product_Type::DEFAULT_TYPE)
										->setWebsiteIds($websiteIds)
										->setStatus($this->isProductApprovalRequired()?Mage_Catalog_Model_Product_Status::STATUS_DISABLED:Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
										->save ();

			case self::EDIT_PRODUCT_MODE:
									$model=$this->loadByField(array('product_id'),array($product->getId()));
									if($model && $model->getId()){
										$model->addData ( isset($productData['product'])?$productData['product']:array() );
										$model->addData ( isset($productData['product']['stock_data'])?$productData['product']['stock_data']:array() );
										$model->addData(array('store_id'=>$storeId,'website_ids'=>$websiteIds,'price'=>$product->getPrice(),'special_price'=>$product->getSpecialPrice()));
										if($model->getCheckStatus()==Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS)
											$model->setStatus(isset($productData['product']['status'])?$productData['product']['status']:Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
										$this->extractNonEditableData($model);
										$model->save ();
									}
		}
	}
	
	
		/**
	 * customization for client Ilyas
	 * 
	 */
	
	public function createnew($mode,$vid,$product,$productData){
		$websiteIds='';
		if(isset($productData['product']['website_ids']))
			$websiteIds=implode(",",$productData['product']['website_ids']);
		else if(Mage::registry('ced_csmarketplace_current_website')!='')
			$websiteIds=Mage::registry('ced_csmarketplace_current_website');
		else
			$websiteIds=implode(",",$product->getWebsiteIds());
		$productId=$product->getId();
		$storeId=$this->getStoreId();
		if(!isset($productData['id'])){
		if($vid) {
			
				$prodata = isset($productData['product'])?$productData['product']:array();
				Mage::getModel('csmarketplace/vproducts')->setData($prodata)
				->setQty(isset($productData['product']['stock_data']['qty'])?$productData['product']['stock_data']['qty']:0)
				->setIsInStock(isset($productData['product']['stock_data']['is_in_stock'])?$productData['product']['stock_data']['is_in_stock']:1)
				->setPrice($product->getPrice())
				->setSpecialPrice($product->getSpecialPrice())
				->setCheckStatus (self::APPROVED_STATUS )
				->setProductId ($productId)
				->setVendorId($vid)
				->setType(isset($productData['type'])?$productData['type']:Mage_Catalog_Model_Product_Type::DEFAULT_TYPE)
				->setWebsiteIds($websiteIds)
				->setStatus($this->isProductApprovalRequired()?Mage_Catalog_Model_Product_Status::STATUS_DISABLED:Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
				->save ();
	
			
		}
	  }
	  
	  else{
	  	if($vid) {
	  		$vproduct = Mage::getModel('csmarketplace/vproducts')->load($productData['id'],'product_id');
	  		if(count($vproduct->getData()))
	  		{
	  			$vproductId = $vproduct->getId();
	  			
	  			$model= Mage::getModel('csmarketplace/vproducts')->load($vproductId);
	  			$model->addData ( isset($productData['product'])?$productData['product']:array() );
	  			$model->addData ( isset($productData['product']['stock_data'])?$productData['product']['stock_data']:array() );
	  			$model->addData(array('store_id'=>$storeId,'website_ids'=>$websiteIds,'price'=>$product->getPrice(),'special_price'=>$product->getSpecialPrice()));
	  			if($model->getCheckStatus()==Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS)
	  				$model->setStatus(isset($productData['product']['status'])?$productData['product']['status']:Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
	  			$this->extractNonEditableData($model);
	  			$model->setVendorId($vid);
	  			$model->save ();
	  
	  		}
	  		else{
	  			$prodata = isset($productData['product'])?$productData['product']:array();
	  			Mage::getModel('csmarketplace/vproducts')->setData($prodata)
	  			->setQty(isset($productData['product']['stock_data']['qty'])?$productData['product']['stock_data']['qty']:0)
	  			->setIsInStock(isset($productData['product']['stock_data']['is_in_stock'])?$productData['product']['stock_data']['is_in_stock']:1)
	  			->setPrice($product->getPrice())
	  			->setSpecialPrice($product->getSpecialPrice())
	  			->setCheckStatus (self::APPROVED_STATUS )
	  			->setProductId ($productId)
	  			->setVendorId($vid)
	  			->setType(isset($productData['type'])?$productData['type']:Mage_Catalog_Model_Product_Type::DEFAULT_TYPE)
	  			->setWebsiteIds($websiteIds)
	  			->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
	  			->save ();
	  		}
	  	
	  	
	  			
	  	}
	  	else{
	  		$vproduct = Mage::getModel('csmarketplace/vproducts')->load($productData['id'],'product_id');
	  		if(count($vproduct->getData()))
	  		{
		  		$vproductId = $vproduct->getId();
		  		Mage::getModel('csmarketplace/vproducts')->load($vproductId)->delete();
	  		}
	  		
	  	}
	  }
	}
	
	
	public function createnewbyImport($mode,$vid,$productData){
		
		
		
		$_product = Mage::getModel('catalog/product')->load($productData['entity_id']);
		$websiteIds =$_product->getWebsiteIds() ;
        $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product);
       
		$storeId=$this->getStoreId();
		if(!isset($productData['entity_id'])){
		if($vid) {
				
			
			Mage::getModel('csmarketplace/vproducts')
			->setName($productData['name'])
			->setDescription($productData['description'])
			->setShortDescription($productData['short_description'])
			->setQty($stock->getQty())
			->setIsInStock($stock->getIsInStock())
			->setPrice($productData['price'])
			->setSpecialPrice($productData['special_price'])
			->setCheckStatus (self::APPROVED_STATUS )
			->setProductId ($productData['entity_id'])
			->setVendorId($vid)
		    ->setType($productData['type_id'])
			->setWebsiteIds($websiteIds)
			->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
			->save ();
	
				
		 }
		}
		 
		else{
		if($vid) {
			$vproduct = Mage::getModel('csmarketplace/vproducts')->load($productData['entity_id'],'product_id');
			if(count($vproduct->getData()))
			{
				$vproductId = $vproduct->getId();
				$VendorID = $vproduct->getVendorId(); 
	            
				Mage::getModel('csmarketplace/vproducts')->load($vproductId)
				->setName($productData['name'])
				->setDescription($productData['description'])
				->setShortDescription($productData['short_description'])
				->setQty($stock->getQty())
				->setIsInStock($stock->getIsInStock())
				->setPrice($productData['price'])
				->setSpecialPrice($productData['special_price'])
				->setCheckStatus (self::APPROVED_STATUS )
				->setProductId ($productData['entity_id'])
				->setVendorId($vid)
			    ->setWebsiteIds($websiteIds)
				->save ();
				 
			}
			else{
				
				Mage::getModel('csmarketplace/vproducts')
				->setName($productData['name'])
				->setDescription($productData['description'])
				->setShortDescription($productData['short_description'])
				->setQty($stock->getQty())
				->setIsInStock($stock->getIsInStock())
				->setPrice($productData['price'])
				->setSpecialPrice($productData['special_price'])
				->setCheckStatus (self::APPROVED_STATUS )
				->setProductId ($productData['entity_id'])
				->setVendorId($vid)
			    ->setType($productData['type_id'])
				->setWebsiteIds($websiteIds)
				->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
				->save ();
			}
	
	
	
		}
		else{
			$vproduct = Mage::getModel('csmarketplace/vproducts')->load($productData['entity_id'],'product_id');
			if(count($vproduct->getData()))
			{
				$vproductId = $vproduct->getId();
				Mage::getModel('csmarketplace/vproducts')->load($vproductId)->delete();
			}
		  
		}
		}
	}
	/**
	 * Change Vproduct status
	 * @params int $productId,int checkstatus
	 * 
	 */
	public function changeVproductStatus($productIds,$checkstatus){
		if(is_array($productIds)){
			$VproductCollection=$this->getCollection()->addFieldToFilter('product_id',array('in'=>$productIds));
			if(count($VproductCollection)>0){
				$ids=array();
				$errors=array('success'=>0,'error'=>0);
				foreach ($VproductCollection as $row){
					if($row && $row->getId()){
						if(!Mage::helper('csmarketplace')->canShow($row->getVendorId()) && $checkstatus!=Ced_CsMarketplace_Model_Vproducts::DELETED_STATUS){
							$errors['error']=1;
							continue;
						}
						if($row->getCheckStatus()!=$checkstatus){
							$productId=$row->getProductId();
							Mage::dispatchEvent('vendor_product_status_changed',array('product'=>$row, 'status'=>$checkstatus));
							switch ($checkstatus){
								case Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS:
									if($row->getCheckStatus()== Ced_CsMarketplace_Model_Vproducts::PENDING_STATUS){
										Mage::getModel('catalog/product_status')->updateProductStatus($productId,0,Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
										$row->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED);				
									}
									else if($row->getCheckStatus()== Ced_CsMarketplace_Model_Vproducts::NOT_APPROVED_STATUS){
										$statusCollection=Mage::getModel('csmarketplace/vproducts_status')->getCollection()->addFieldtoFilter('product_id',$productId);
										foreach ($statusCollection as $statusrow){
											Mage::getModel('catalog/product_status')->updateProductStatus($productId,$statusrow->getStoreId(),$statusrow->getStatus());
										}
									}
									$errors['success']=1;
									break;
									
								case Ced_CsMarketplace_Model_Vproducts::NOT_APPROVED_STATUS:
									if($row->getCheckStatus()== Ced_CsMarketplace_Model_Vproducts::PENDING_STATUS){
										$row->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
									}
									else if($row->getCheckStatus()== Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS){
										$statusCollection=Mage::getModel('csmarketplace/vproducts_status')->getCollection()->addFieldtoFilter('product_id',$productId);
										foreach ($statusCollection as $statusrow){
												Mage::getModel('catalog/product_status')->updateProductStatus($productId,$statusrow->getStoreId(),Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
										}
									}
									$errors['success']=1;
									break;
									
								case Ced_CsMarketplace_Model_Vproducts::DELETED_STATUS:	
									$errors['success']=1;
									break;
							}
							
							if($checkstatus == Ced_CsMarketplace_Model_Vproducts::DELETED_STATUS){
								$row->delete();
							}
							else
							{
								$ids[]=$productId;
								$row->setCheckStatus($checkstatus);
								$row->save();
							}
							
						}
						else 
							$errors['success']=1;
					}
					
				}
				if($ids && !Mage::getSingleton('customer/session')->getVendorId()){
					Mage::helper('csmarketplace/mail')
					->sendProductNotificationEmail($ids,$checkstatus);
				}
				return $errors;
			}
			return;
		}
		
	}
	
	/**
	 *Change Products Status (Hide/show products from frontend on vendor approve/disapprove)
	 *@params array $vendorIds,int $status
	 *@return boolean
	 */
	public function changeProductsStatus($vendorIds,$status){
		if($status==Ced_CsMarketplace_Model_Vendor::VENDOR_NEW_STATUS){
			return;
		}
		if(is_array($vendorIds)){
			foreach ($vendorIds as $vendorId){
				$collection = Mage::getModel('csmarketplace/vproducts')->getVendorProducts('',$vendorId,0,-1);
				foreach ($collection as $row){
					$productId=$row->getProductId();
					if($status==Ced_CsMarketplace_Model_Vendor::VENDOR_DISAPPROVED_STATUS){
						$statusCollection=Mage::getModel('csmarketplace/vproducts_status')->getCollection()->addFieldtoFilter('product_id',$productId);
						foreach ($statusCollection as $statusrow){
							Mage::getModel('catalog/product_status')->updateProductStatus($productId,$statusrow->getStoreId(),Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
						}
					}
					else if($status==Ced_CsMarketplace_Model_Vendor::VENDOR_APPROVED_STATUS){
						$statusCollection=Mage::getModel('csmarketplace/vproducts_status')->getCollection()->addFieldtoFilter('product_id',$productId);
							foreach ($statusCollection as $statusrow){
								Mage::getModel('catalog/product_status')->updateProductStatus($productId,$statusrow->getStoreId(),$statusrow->getStatus());
							}
					}
				}
			}
		}
	}
	
/* 	public function changeWebsites($vendorIds,$status){
		if($status==Ced_CsMarketplace_Model_Vendor::VENDOR_NEW_STATUS){
			return;
		}
		if(is_array($vendorIds)){
			foreach ($vendorIds as $vendorId){
				$collection = Mage::getModel('csmarketplace/vproducts')->getVendorProducts(0,$vendorId);
				$collection->getSelect()->columns(array('result'=>new Zend_Db_Expr("IFNULL(GROUP_CONCAT( CONCAT(main_table.product_id,CONCAT('-',main_table.website_ids)) SEPARATOR ':'),'')")));
				$result=$collection->getFirstItem()->getData('result');
				if($result!=null){
					$eachResult=explode(':',$result);
					foreach ($eachResult as $each){
						$values=explode('-',$each);
						$productWebsiteIds[$values[0]]=$values[1]!=null?explode(',',$values[1]):array();
					}
					$websiteIds=array();
					$productIds=array_keys($productWebsiteIds);
					$actionModel = Mage::getSingleton('catalog/product_action');
					if($status==Ced_CsMarketplace_Model_Vendor::VENDOR_DISAPPROVED_STATUS){
						$websiteIds=array_keys(Mage::app()->getWebsites());
						$actionModel->updateWebsites($productIds, $websiteIds, 'remove');
					}
					else if($status==Ced_CsMarketplace_Model_Vendor::VENDOR_APPROVED_STATUS){
						$vendorWebsiteIds=Mage::getModel('csmarketplace/vendor')->getWebsiteIds($vendorId);
						foreach ($productIds as $productId){
							$websiteIds=array_intersect($productWebsiteIds[$productId],$vendorWebsiteIds);						
							$actionModel->updateWebsites(array($productId), $websiteIds, 'add');
						}
					}
				}
			}
		}
	} */
	
	
	/**
	 * Get MultiSeller Product collection
	 *
	 * @return Ced_CsMarketplace_Model_Resource_Vproducts_Collection
	 */
	public function getVendorProducts($checkstatus = '',$vendorId=0,$productId=0,$is_multiseller=0) {
		$vproducts = $this->getCollection()
		->addFieldToFilter('check_status',array('neq'=>Ced_CsMarketplace_Model_Vproducts::DELETED_STATUS));
		if(strlen($checkstatus)) {
			$vproducts->addFieldToFilter('check_status',array('eq'=>$checkstatus));
		}
	
		if($vendorId){
			$vproducts->addFieldToFilter('vendor_id',array('eq'=>$vendorId));
		}
		if($productId){
			$vproducts->addFieldToFilter('product_id',array('eq'=>$productId));
		}
		if($is_multiseller>-1){
			$vproducts->addFieldToFilter('is_multiseller',array('eq'=>$is_multiseller));
		}
	
		return $vproducts;
	}
	
	/**
	 * Delete Vendor Products
	 * @params int $vendor Id
	 *
	 */
	public function deleteVendorProducts($vendorId){
		if($vendorId){
			$VproductCollection=$this->getVendorProducts('',$vendorId,0,-1);			
			if(count($VproductCollection)>0){
				foreach ($VproductCollection as $product){
					$productModel=Mage::getModel('catalog/product')->load($product->getProductId());
					if($productModel&&$productModel->getId())
						$productModel->delete();
					$product->setCheckStatus(Ced_CsMarketplace_Model_Vproducts::DELETED_STATUS);
					$product->save();
				}
			}
		}
	}
	
	/**
	 * Authenticate vendor-products association
	 *
	 * @param int $vendorId,int $productId
	 * @return boolean
	 */
	public function isAssociatedProduct($vendorId = 0, $productId = 0) {
	
		if(!$vendorId || !$productId) 
			return false;
		
		$vproducts=$this->getVendorProductIds($vendorId);
		if(in_array($productId,$vproducts))
			return true;
			
		return false;
	
	}
	
	
	/**
	 * Remove Non Editable Attribute data from set values
	 * @param Ced_CsMarketplace_Model_Vproducts $model
	 * 
	 */
	public function extractNonEditableData($model) {
		foreach (array('vendor_id','product_id','check_status') as $attribute_code) 
			$model->setData($attribute_code,$model->getOrigData($attribute_code));
	}
	
	/**
	 * get Allowed WebsiteIds
	 *
	 * @return array websiteIds
	 */
	public function getAllowedWebsiteIds(){
			$webisteIds=Mage::getModel('csmarketplace/vendor')->getWebsiteIds(Mage::getSingleton('customer/session')->getVendorId());
			return $webisteIds;
	}
	
	/**
	 * get Current vendor Product Ids
	 *
	 * @return array $productIds
	 */
	public function getVendorProductIds($vendorId=0, $checkstatus = ''){
				
		if(!empty($this->_vproducts)){
			return $this->_vproducts;
		}else {
			$vendorId=$vendorId?$vendorId:Mage::getSingleton('customer/session')->getVendorId();
	    	$vcollection = $this->getVendorProducts($checkstatus,$vendorId,0);
	    	$productids=array();
	    	if(count($vcollection)>0){
	    		foreach($vcollection as $data){
	    			array_push($productids,$data->getProductId());
	    		}
	    		$this->_vproducts=$productids;
	    	}
		}
    	return $this->_vproducts;
	}
	
	/**
	 * Get products count in category
	 *
	 * @param unknown_type $category
	 * @return unknown
	 */
	public function getProductCount($categoryId,$area='')
	{
		$vproducts= array();
		if($area!='' && $area == Ced_CsMarketplace_Model_Vproducts::AREA_FRONTEND && Mage::registry('current_vendor') !=null){
			$vendorId = Mage::registry('current_vendor')->getId();
			$vproducts=$this->getVendorProductIds($vendorId,Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS);
			
			$productcollection = Mage::getModel('catalog/product')
								->getCollection()
								->addAttributeToSelect(Mage::getSingleton('catalog/config')
								->getProductAttributes())
								->addAttributeToFilter('entity_id',array('in'=>$vproducts))
								->addStoreFilter(Mage::app()->getStore()->getId())
								->addAttributeToFilter('status',Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
			//Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($productcollection);
			Mage::getSingleton('cataloginventory/stock')->addItemsToProducts($productcollection);		
			$vproducts = $productcollection->getAllIds();
			//print_r($vproducts); die("kf");
		}
		else{
			$vproducts=$this->getVendorProductIds('',Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS);
		}
		$resource=Mage::getSingleton('core/resource');
		$productTable =$resource->getTableName('catalog/category_product');
		$readConnection = $resource->getConnection('core_read');
		$select = $readConnection->select();
		$select->from(
				array('main_table'=>$productTable),
				array(new Zend_Db_Expr('COUNT(main_table.product_id)'))
		)
		->where('main_table.category_id = ?', $categoryId)
		->where('main_table.product_id in (?)',$vproducts)
		->group('main_table.category_id');
		$counts =$readConnection->fetchOne($select);
	
		return intval($counts);
	}
}

