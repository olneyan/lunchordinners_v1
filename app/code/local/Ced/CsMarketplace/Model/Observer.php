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
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Observer model
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
 
Class Ced_CsMarketplace_Model_Observer
{
	
	/**
	 * Predispath admin action controller
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function preDispatch(Varien_Event_Observer $observer)
	{
		if (Mage::getSingleton('admin/session')->isLoggedIn()) {
			$feedModel  = Mage::getModel('csmarketplace/feed');
			/* @var $feedModel Ced_Core_Model_Feed */
			$feedModel->checkUpdate();
		}
	}
	
	public function transactionRender(Varien_Event_Observer $observer){
	/*	$container = $observer->getEvent()->getContainer();
		$html = $container->getHtml();
		$html .='here';
		$container->setHtml($html);
		return $this;
	*/
	}

	public function transactionAmountBlocks(Varien_Event_Observer $observer){
		$container = $observer->getEvent()->getContainer();
		$blocks = $container->getBlocks();
		$blocks[] ='csmarketplace/adminhtml_vpayments_edit_tab_paymentinformation_default';
		$container->setBlocks($blocks);
		return $this;
	}
	
	/**
	 * before layout load
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function beforeLoadLayout(Varien_Event_Observer $observer) {
		try {
			$action = $observer->getEvent()->getAction();
			$layout = $observer->getEvent()->getLayout();
			/* print_r($layout->getUpdate()->getHandles());die('observer'); */
			if($action->getRequest()->getActionName() == 'cedpop') return $this;
			$modules = Mage::helper('csmarketplace')->getCedCommerceExtensions();
			foreach ($modules as $moduleName=>$releaseVersion)
			{
				$m = strtolower($moduleName); if(!preg_match('/ced/i',$m)){ return $this; }  $h = Mage::getStoreConfig(Ced_CsMarketplace_Block_Extensions::HASH_PATH_PREFIX.$m.'_hash'); for($i=1;$i<=(int)Mage::getStoreConfig(Ced_CsMarketplace_Block_Extensions::HASH_PATH_PREFIX.$m.'_level');$i++){$h = base64_decode($h);}$h = json_decode($h,true); 
				if(is_array($h) && isset($h['domain']) && isset($h['module_name']) && isset($h['license']) && $h['module_name'] == $m && $h['license'] == Mage::getStoreConfig(Ced_CsMarketplace_Block_Extensions::HASH_PATH_PREFIX.$m)){}else{ $_POST=$_GET=array();$action->getRequest()->setParams(array());$exist = false; foreach($layout->getUpdate()->getHandles() as $handle){ if($handle=='c_e_d_c_o_m_m_e_r_c_e'){ $exist = true; break; } } if(!$exist){ $layout->getUpdate()->addHandle('c_e_d_c_o_m_m_e_r_c_e'); }}	
				
			}
			return $this;
		} catch (Exception $e) {
			return $this;
		}
	}
	
	/**
	 * Get current store
	 * @return Mage_Core_Model_Store
	 */
	 public function getStore() {
		$storeId = (int) Mage::app()->getRequest()->getParam('store', 0);
        if($storeId)
			return Mage::app()->getStore($storeId);
		else 
			return Mage::app()->getStore();
	 }
	 
	 /**
     * Get customer seesion
     *
     * @return Mage_Customer_Model_Session
     */
	 protected function _getSession() {
		return Mage::getSingleton('customer/session');
	 }
	 
	 
	 /**
	  *Notify Customer Account share Change 
	  *
	  */
	public function coreConfigSaveAfter($observer)
	 {
	 	$groups = $observer->getEvent()->getDataObject()->getGroups();
	 	$customer_share=isset($groups['account_share']['fields']['scope']['value'])?$groups['account_share']['fields']['scope']['value']:Mage::getStoreConfig(Mage_Customer_Model_Config_Share::XML_PATH_CUSTOMER_ACCOUNT_SHARE);
	 	$config = new Mage_Core_Model_Config();
	 	if($customer_share!=''&&$customer_share!=Mage::getStoreConfig(Mage_Customer_Model_Config_Share::XML_PATH_CUSTOMER_ACCOUNT_SHARE))
    		$config->saveConfig(Ced_CsMarketplace_Model_Vendor::XML_PATH_VENDOR_WEBSITE_SHARE,1);
	 }
	 
	 /**
	  *Auto Disable/enable CsMarketplace Link from Frontend when Shop is Disabled/enabled
	  *
	  */
	 public function coreConfigDisableshopAfter($observer){
	 	$groups = $observer->getEvent()->getDataObject()->getGroups();

	 	if($observer->getEvent()->getDataObject()->getPath() == 'ced_csmarketplace/general/shopurl_active'){

		 	if (isset($groups['general']['fields']['shopurl_active']) && $groups['general']['fields']['shopurl_active']['value'] && isset($groups['general']['fields']['publicname_active']) && $groups['general']['fields']['publicname_active']['value']){
		 	}
		 	else {
		 		$config = new Mage_Core_Model_Config();
		 		$config->saveConfig('ced_vshops/general/vshoppage_top_enabled', '0', 'default', 0);
		 	}

		 	if (isset($groups['general']['fields']['shopurl_active']) || isset($groups['general']['fields']['publicname_active'])) {
		 		$collection = Mage::getModel('csmarketplace/vendor_form')->getCollection()->addFieldtoFilter('attribute_code', array('in' => array('shop_url','public_name')));
			 	
			 	foreach($collection as $formData) {
			 		$is_visible = 0;
			 		$use_in_registration = 0;
			 		if($formData->getData('attribute_code')=='shop_url' && $groups['general']['fields']['shopurl_active']['value'])
			 		{
			 			$is_visible = 1;
			 			$use_in_registration = 1;
			 		}
			 		if($formData->getData('attribute_code')=='public_name' && $groups['general']['fields']['publicname_active']['value'])
			 		{
			 			$is_visible = 1;
			 			$use_in_registration = 1;
			 		}
			 		$formData->setData('is_visible',$is_visible);
			 		$formData->setData('use_in_registration',$use_in_registration);
			 		$formData->save();
			 	}

			 	$shop_url_required = 0;
			 	$public_name_required = 0;

			 	$model=Mage::getModel('eav/entity_setup','core_setup');
	            $model->startSetup();
	            if($groups['general']['fields']['shopurl_active']['value']) {
	            	$shop_url_required = 1;
	            }
	            if($groups['general']['fields']['publicname_active']['value']) {
	            	$public_name_required = 1;
	            }
	            $model->updateAttribute('csmarketplace_vendor','shop_url','is_required',$shop_url_required);
	            $model->updateAttribute('csmarketplace_vendor','public_name','is_required',$public_name_required);
	            $model->endSetup();
			}
		}

		// If prefix of memeber id has been changed
				
		if($observer->getEvent()->getDataObject()->getPath() == 'ced_csmarketplace/general/prefixmemberid_active'){
			if (isset($groups['general']['fields']['prefixmemberid_active']) && $groups['general']['fields']['prefixmemberid_active']['value'] !=  Mage::getStoreConfig('ced_csmarketplace/general/prefixmemberid_active')) {
				$vendor_collection = Mage::getModel('csmarketplace/vendor')->getCollection();
				$find = Mage::getStoreConfig('ced_csmarketplace/general/prefixmemberid_active');
				$replace = $groups['general']['fields']['prefixmemberid_active']['value'];
				foreach($vendor_collection as $value){
					$vendor = Mage::getModel('csmarketplace/vendor')->load($value->getId());	
					if($vendor->getId()){
						$member_id = str_replace($find, $replace, $vendor->getMemberId());
						$vendor->setMemberId($member_id);
						$vendor->save();
					}
				}
			}
		}

	 }
	 
	 /**
	  *Vendor registration 
	  *
	  */
	public function VendorRegistration($observer){
		if(Mage::app()->getRequest()->getParam('is_vendor')==1){
			$venderData = Mage::app()->getRequest()->getParam('vendor');
			$customerData = $observer->getCustomer();
			try {
				$vendor = Mage::getModel('csmarketplace/vendor')
						   ->setCustomer($customerData)
						   ->register($venderData);
				
				//Code to Generate Member ID For Every Vendor Registration.
				$prefix = Mage::getStoreConfig('ced_csmarketplace/general/prefixmemberid_active');
				$entityTypeId = Mage::getModel('eav/entity')->setType('csmarketplace_vendor')->getTypeId();
				$member_lastid_model = Mage::getModel('eav/entity_store')->loadByEntityStore($entityTypeId, 0);
				$member_lastid = $member_lastid_model->getIncrementLastId();
				$member_lastid++;
    			$mid = $prefix.$member_lastid;
    			$vendor['member_id'] = $mid;

				if(!$vendor->getErrors()) {
					$vendor->save();
					$member_lastid_model->setIncrementLastId($member_lastid)->save();
					if($vendor->getStatus() == Ced_CsMarketplace_Model_Vendor::VENDOR_NEW_STATUS) {
						$this->_getSession()->addSuccess(Mage::helper('csmarketplace')->__('Your vendor application has been Pending.'));
					} else if ($vendor->getStatus() == Ced_CsMarketplace_Model_Vendor::VENDOR_APPROVED_STATUS) {
						$this->_getSession()->addSuccess(Mage::helper('csmarketplace')->__('Your vendor application has been Approved.'));
					}
				} elseif ($vendor->getErrors()) {
					foreach ($vendor->getErrors() as $error) {
						$this->_getSession()->addError($error);
					}
					$this->_getSession()->setFormData($venderData);
				} else {
					$this->_getSession()->addError(Mage::helper('csmarketplace')->__('Your vendor application has been denied'));
				}
			} catch (Exception $e) {
				Mage::helper('csmarketplace')->logException($e);
			}
		}
	}	
	
	/**
	 *Set Vendor id in item table
	 *
	 */
	public function salesQuoteItemSetVendorId($observer)
		{
			$quoteItem = $observer->getQuoteItem();
			$product = $observer->getProduct();
			$cs_vendor_id = Mage::getModel('csmarketplace/vproducts')->getVendorIdByProduct($product->getId());
			if($cs_vendor_id)
				$quoteItem->setVendorId($cs_vendor_id);
		}
	/**
    * Save Vendor Order Information 
    */
	public function setVendorSalesOrder($observer)
		{
			$order = $observer->getOrder();
			$vorder=Mage::getModel('csmarketplace/vorders')->getCollection()->addFieldToFilter('order_id',$order->getIncrementId())->getFirstItem();
			if($vorder->getId())
			{
				return $this;
			}
			$products = $order->getAllItems();
			$baseToGlobalRate=$order->getBaseToGlobalRate()?$order->getBaseToGlobalRate():1;
			$vendorsBaseOrder = array();
			$vendorQty = array();
			

			Mage::helper('csmarketplace')->logProcessedData($order->getData('increment_id'), Ced_CsMarketplace_Helper_Data::SALES_ORDER_CREATE);
			 foreach ($products as $item) {
			    if($item->getVendorId() > 0) {
					$price = 0;
					$price = $item->getBaseRowTotal()
							+ $item->getBaseTaxAmount()
							+ $item->getBaseHiddenTaxAmount()
							+ $item->getBaseWeeeTaxAppliedRowAmount()
							- $item->getBaseDiscountAmount();
					$vendorsBaseOrder[$item->getVendorId()]['order_total'] = isset($vendorsBaseOrder[$item->getVendorId()]['order_total'])?($vendorsBaseOrder[$item->getVendorId()]['order_total'] + $price) : $price;
					$vendorsBaseOrder[$item->getVendorId()]['item_commission'][$item->getId()] = $price;									;
					$vendorsBaseOrder[$item->getVendorId()]['order_items'][] = $item;
					$vendorQty[$item->getVendorId()] = isset($vendorQty[$item->getVendorId()])?$vendorQty[$item->getVendorId()] + $item->getQtyOrdered() :  $item->getQtyOrdered();
				   
					$logData = $item->getData();
					unset($logData['product']);
					Mage::helper('csmarketplace')->logProcessedData($logData, Ced_CsMarketplace_Helper_Data::SALES_ORDER_ITEM);
				}
			 }
			 
			foreach($vendorsBaseOrder  as $vendorId => $baseOrderTotal){
				
			 try{	
					/* $order->setVendorItemsData($baseOrderTotal['order_items']); */
					$qty = isset($vendorQty[$vendorId])? $vendorQty[$vendorId] : 0;
					$vorder = Mage::getModel('csmarketplace/vorders');
					$vorder->setVendorId($vendorId);
					$vorder->setOrder($order);
					$vorder->setOrderId($order->getIncrementId());
					$vorder->setCurrency($order->getGlobalCurrencyCode());
					$vorder->setOrderTotal(Mage::helper('directory')->currencyConvert($baseOrderTotal['order_total'], $order->getBaseCurrencyCode(), $order->getGlobalCurrencyCode()));
					$vorder->setBaseCurrency($order->getBaseCurrencyCode());
					$vorder->setBaseOrderTotal($baseOrderTotal['order_total']);
					$vorder->setBaseToGlobalRate($baseToGlobalRate);
					$vorder->setProductQty($qty);
					$vorder->setBillingCountryCode($order->getBillingAddress()->getData('country_id'));
					if($order->getShippingAddress())
						$vorder->setShippingCountryCode($order->getShippingAddress()->getData('country_id'));
					$vorder->setItemCommission($baseOrderTotal['item_commission']);
					$vorder->collectCommission();
					
					Mage::dispatchEvent('ced_csmarketplce_vorder_shipping_save_before',array('vorder'=>$vorder));
					
					$vorder->save();
					//Mage::helper('csmarketplace')->logProcessedData($vorder->getData(), Ced_CsMarketplace_Helper_Data::VORDER_CREATE);
				}
				catch(Exception $e){
					Mage::helper('csmarketplace')->logException($e);
				}
				
				
				
			}	
			try {
				if($order){
					$vorders = Mage::getModel('csmarketplace/vorders')->getCollection()->addFieldToFilter('order_id',array('eq'=>$order->getIncrementId()));
					if (count($vorders) > 0)
						Mage::helper('csmarketplace/mail')->sendOrderEmail($order,Ced_CsMarketplace_Model_Vorders::ORDER_NEW_STATUS);
				}
				$orders = $observer->getOrders();
				if($orders && is_array($orders)){
					foreach($orders as $order){
						if($order){
							$vorders = Mage::getModel('csmarketplace/vorders')->getCollection()->addFieldToFilter('order_id',array('eq'=>$order->getIncrementId()));
							if (count($vorders) > 0)
								Mage::helper('csmarketplace/mail')->sendOrderEmail($order,Ced_CsMarketplace_Model_Vorders::ORDER_NEW_STATUS);
						}
					}
				}
			}
			catch(Exception $e) {
				Mage::helper('csmarketplace')->logException($e);
			}
			
		}
	/**
     * Cancel the asscociated vendor order
     *
     * @param Varien_Object $observer
     * @return Ced_CsMarketplace_Model_Observer
     */
	public function orderCancelAfter($observer){
		$order = $observer->getEvent()->getOrder();
		Mage::helper('csmarketplace')->logProcessedData($order->getData('increment_id'), Ced_CsMarketplace_Helper_Data::SALES_ORDER_CANCELED);
		try {
			$vorders = Mage::getModel('csmarketplace/vorders')
							->getCollection()
							->addFieldToFilter('order_id',array('eq'=>$order->getIncrementId()));
			if (count($vorders) > 0) {
				foreach ($vorders as $vorder) {
					if($vorder->canCancel()) {
						$vorder->setOrderPaymentState(Mage_Sales_Model_Order_Invoice::STATE_CANCELED);
						$vorder->setPaymentState(Ced_CsMarketplace_Model_Vorders::STATE_CANCELED);
						$vorder->save();
					} else if ($vorder->canMakeRefund()) {
						$vorder->setPaymentState(Ced_CsMarketplace_Model_Vorders::STATE_REFUND);
						$vorder->save();
					}
					Mage::helper('csmarketplace')->logProcessedData($vorder->getData(), Ced_CsMarketplace_Helper_Data::VORDER_CANCELED);
				}
				Mage::helper('csmarketplace/mail')->sendOrderEmail($order,Ced_CsMarketplace_Model_Vorders::ORDER_CANCEL_STATUS);

			}
			return $this;
		} catch(Exception $e) {
			Mage::helper('csmarketplace')->logException($e);
		}
		
		
	}
	
	/**
     * Refund the asscociated vendor order
     *
     * @param Varien_Object $observer
     * @return Ced_CsMarketplace_Model_Observer
     */
	public function orderCreditmemoRefund($observer){
		$order = $observer->getDataObject();
		try {

		//	if ($order->getState() == Mage_Sales_Model_Order::STATE_CLOSED || ((float)$order->getBaseTotalRefunded() && (float)$order->getBaseTotalRefunded() >= (float)$order->getBaseTotalPaid())) {
			if ($order->getState() == Mage_Sales_Model_Order::STATE_CLOSED) {
				$vorders = Mage::getModel('csmarketplace/vorders')
								->getCollection()
								->addFieldToFilter('order_id',array('eq'=>$order->getIncrementId())); 
				if (count($vorders) > 0) {
					foreach ($vorders as $vorder) {
						if($vorder->canCancel()) {
							$vorder->setOrderPaymentState(Mage_Sales_Model_Order_Invoice::STATE_CANCELED);
							$vorder->setPaymentState(Ced_CsMarketplace_Model_Vorders::STATE_CANCELED);
							$vorder->save();
						} else if($vorder->canMakeRefund()) {
							$vorder->setPaymentState(Ced_CsMarketplace_Model_Vorders::STATE_REFUND);
							$vorder->save();
						}
					}
					
				}
			}
			return $this;
		} catch(Exception $e) {
			Mage::helper('csmarketplace')->logException($e);
		}
	}
		
	/**
	 * Send new order notification email to vendor
	 * @param Varien_Event_Observer $observer
	 */
	/* public function checkoutSubmitAllAfter(Varien_Event_Observer $observer){
		$order = $observer->getOrder();
		try {
			if($order){
				$vorders = Mage::getModel('csmarketplace/vorders')->getCollection()->addFieldToFilter('order_id',array('eq'=>$order->getIncrementId()));
				if (count($vorders) > 0) 
					Mage::helper('csmarketplace/mail')->sendOrderEmail($order,Ced_CsMarketplace_Model_Vorders::ORDER_NEW_STATUS);
			}
			$orders = $observer->getOrders();
			if($orders && is_array($orders)){
				foreach($orders as $order){
					if($order){
						$vorders = Mage::getModel('csmarketplace/vorders')->getCollection()->addFieldToFilter('order_id',array('eq'=>$order->getIncrementId()));
						if (count($vorders) > 0) 
							Mage::helper('csmarketplace/mail')->sendOrderEmail($order,Ced_CsMarketplace_Model_Vorders::ORDER_NEW_STATUS);
					}
				}
			}
		}
		catch(Exception $e) {
			Mage::helper('csmarketplace')->logException($e);
		}
	} */

		
	protected function _vendorForm($attribute) {
		$store = $this->getStore();
		return Mage::getModel('csmarketplace/vendor_form')
							->getCollection()
							->addFieldToFilter('attribute_id',array('eq'=>$attribute->getAttributeId()))
							->addFieldToFilter('attribute_code',array('eq'=>$attribute->getAttributeCode()))
							->addFieldToFilter('store_id',array('eq'=>$store->getId()));
	}
	
	
	/**
	 *Reflect Product data to Vproducts table
	 *
	 */
	public function saveVproductData($observer) {
		
		$productData=Mage::app()->getRequest()->getParams();
		
		$product = $observer->getProduct();
		$store = (int)Mage::app()->getRequest()->getParam('store')?(int)Mage::app()->getRequest()->getParam('store'):0;
		/* if($productData['product']['restaurants'])
		{ */
		 if(isset($productData['product']))	
		 {
		 	Mage::getModel('csmarketplace/vproducts')->setStoreId($store)->createnew('edit',$productData['product']['restaurants'],$product,$productData);
		 }
		else{
			
			$prodData = $observer->getProduct()->getData();
		
			Mage::getModel('csmarketplace/vproducts')->setStoreId($store)->createnewbyImport('edit',$prodData['restaurants'],$prodData);
		}	
			
		/* } */
		
	/* 	if(isset($productData['id'])){
			$product = $observer->getProduct();
			Mage::getModel('csmarketplace/vproducts')->setStoreId($store)->processPostSave(Ced_CsMarketplace_Model_Vproducts::EDIT_PRODUCT_MODE,$product,$productData);
		} */
				
	}
	
	/**
	 * customization for Ilyas
	 * 
	 */
// 	public function CreateVproductData($observer) {
	
// 		$productData=Mage::app()->getRequest()->getParams();
// 		//print_r($productData); die("fk");
// 		$store = (int)Mage::app()->getRequest()->getParam('store')?(int)Mage::app()->getRequest()->getParam('store'):0;
// 		if($productData['product']['restaurants'])
// 		{
// 			$product = $observer->getProduct();
// 			Mage::getModel('csmarketplace/vproducts')->setStoreId($store)->createnew($productData['product']['restaurants'],$product,$productData);
// 		}
	
	
// 	}
	
	/**
	 *Reflect Product status Changes
	 *
	 */
	public function saveStatusChange($observer) {
		$productIds=(array)Mage::app()->getRequest()->getParam('product');
		$status = (int)Mage::app()->getRequest()->getParam('status');
		$store = (int)Mage::app()->getRequest()->getParam('store')?(int)Mage::app()->getRequest()->getParam('store'):0;
		if($status){
 			$collection=Mage::getModel('csmarketplace/vproducts')->getCollection()->addFieldToFilter('product_id',array('in'=>$productIds));
 			if(count($collection)>0){
				foreach ($collection as $row){
						$row->setProductId($row->getProductId());
						$row->setStoreId($store);
						$row->setStatus($status);
				}
 			}
		}
	}
	
	/**
	 *Reflect Product data to Vproducts table
	 *
	 */
	public function saveVproductAttributesData($observer) {	
		$productIds=Mage::helper('adminhtml/catalog_product_edit_action_attribute')->getProductIds();
		if(is_array($productIds)){
			$inventoryData      = Mage::app()->getRequest()->getParam('inventory', array());
			$attributesData     = Mage::app()->getRequest()->getParam('attributes', array());
			$websiteRemoveData  = Mage::app()->getRequest()->getParam('remove_website_ids', array());
			$websiteAddData     = Mage::app()->getRequest()->getParam('add_website_ids', array());
			if($attributesData)
				$productData['product']=$attributesData;
			if($inventoryData)
				$productData['product']['stock_data']=$inventoryData;
			$vproductsModel=Mage::getModel('csmarketplace/vproducts');
			$collection=$vproductsModel->getCollection()->addFieldToFilter('product_id',array('in'=>$productIds));
			if(count($collection)>0){
				foreach ($collection as $row){					
						$oldWebsiteIds=explode(',',$row->getWebsiteIds());
						$websiteIds=implode(',',array_unique(array_filter(array_merge(array_diff($oldWebsiteIds,$websiteRemoveData),$websiteAddData))));
						$row->addData ( $productData['product'] );
						$row->addData ( $productData['product']['stock_data'] );
						if(isset($productData['product']['status'])){
							$row->setproductId($row->getProductId());
							$row->setStoreId(Mage::app()->getRequest()->getParam('store',0));
							$row->setStatus($productData['product']['status']);
						}
						$vproductsModel->extractNonEditableData($row);
						$row->addData(array('website_ids'=>$websiteIds));
						$row->save();
				}
			}
		}
	}
	
	/**
	 *Delete Vproduct data
	 *
	 */
	public function deleteVproductData($observer) {
		$productId=Mage::app()->getRequest()->getParam('id');
		if($productId)
			Mage::getModel('csmarketplace/vproducts')->changeVproductStatus(array($productId),Ced_CsMarketplace_Model_Vproducts::DELETED_STATUS);
	}
	
	/**
	 *Reflect Product data to Vproducts table
	 *
	 */
	public function deleteMassVproductData($observer) {
		$productIds=Mage::app()->getRequest()->getParam('product');
		if(is_array($productIds))
			Mage::getModel('csmarketplace/vproducts')->changeVproductStatus($productIds,Ced_CsMarketplace_Model_Vproducts::DELETED_STATUS);
	}
	
	
	/**
	 *Change Order State on invoice
	 *
	 */
	public function changeOrderPaymentState($observer) {
		$invoice = $observer->getDataObject();
		$order = $invoice->getOrder();
		Mage::helper('csmarketplace')->logProcessedData($order->getData('increment_id'), Ced_CsMarketplace_Helper_Data::SALES_ORDER_PAYMENT_STATE_CHANGED);
		if ($order->getBaseTotalDue() == 0) {
			$vorders = Mage::getModel('csmarketplace/vorders')
							->getCollection()
							->addFieldToFilter('order_id',array('eq'=>$order->getIncrementId()));
			if (count($vorders) > 0) {
				foreach ($vorders as $vorder) {
					try{
						$vorder->setOrderPaymentState(Mage_Sales_Model_Order_Invoice::STATE_PAID);
						$vorder->save();
						Mage::helper('csmarketplace')->logProcessedData($vorder->getData(), Ced_CsMarketplace_Helper_Data::VORDER_PAYMENT_STATE_CHANGED);

						}
						catch(Exception $e){
						Mage::helper('csmarketplace')->logException($e);
						}
				}
			}					 
		}
		return $this;
		//$invocies = $order->getInvoiceCollection(); 
	}
	
	/**
	 *Delete Vendor and assoiciated Product
	 *
	 */
	public function deleteVendor($observer){
		$customerId=Mage::app()->getRequest()->getParam('id');
		if($customerId){
			$vendor= Mage::getModel('csmarketplace/vendor')->loadByCustomerId($customerId);
			if($vendor && $vendor->getId()){
				Mage::getModel('csmarketplace/vproducts')->deleteVendorProducts($vendor->getId());
				Mage::helper('csmarketplace/mail')->sendAccountEmail(Ced_CsMarketplace_Model_Vendor::VENDOR_DELETED_STATUS,'',$vendor);
				$vendor->delete();
				
			}
		}
	}
	
	/**
	 *mass delete Vendor
	 *
	 */
	public function massDeleteVendor($observer){
		$customerids=Mage::app()->getRequest()->getParam('customer');
		foreach ($customerids as $customerId){
			$vendor= Mage::getModel('csmarketplace/vendor')->loadByCustomerId($customerId);
			if($vendor && $vendor->getId()){
				Mage::getModel('csmarketplace/vproducts')->deleteVendorProducts($vendor->getId());
				Mage::helper('csmarketplace/mail')->sendAccountEmail(Ced_CsMarketplace_Model_Vendor::VENDOR_DELETED_STATUS,'',$vendor);
				$vendor->delete();
			}
		}
	
	}
	
	/**
     * setVendorEmail
     *
     */
	public function setVendorEmail($observer){
	
		$customer = $observer->getCustomer();
		$vendor=Mage::getModel('csmarketplace/vendor')->loadByCustomerId($customer->getId());
		if($vendor)
		$vendor->setSettingFromCustomer(true)->setEmail($customer->getEmail())->save();
	}
	/**
	* Add RTL class
	*
	*/
	public function AddClass(Varien_Event_Observer $observer)
        {
		if(Mage::getStoreConfig('ced_csmarketplace/general/rtl_active'))
		{
		         $block = $observer->getBlock();
		    //echo get_class($block)."--->" .get_class( $block->getParentBlock())."<br/>";
		        if (get_class($block) == 'Mage_Page_Block_Html') {

		            $block->addBodyClass('ced_rtl');

		          }
		}
        }
        
        
        /**
         * customization for client
         *
         */
        
        public function CartAdd(Varien_Event_Observer $observer){
        
        	//$product = $observer->getEvent()->getRequest()->getProduct();
        	
        	$cart = Mage::getSingleton('checkout/session')->getQuote();
        	//$cart->getAllItems() to get ALL items, parent as well as child, configurable as well as it's simple associated item
        	if(count($cart->getAllVisibleItems()))
        	{
        		
        		foreach ($cart->getAllVisibleItems() as $item) {
        
        			try{
        				$itemId= $item->getId();
        				$product = $item->getProduct();
        				$id = $product->getId();
        				$name = $product->getName();
        				$sku = $product->getSku();
        				$paramProductId = Mage::app()->getRequest()->getParam('product');
        				 
        				$vproduct = Mage::getModel('csmarketplace/vproducts')->getCollection()->addFieldToFilter('product_id',['in'=>$paramProductId])->addFieldToFilter('check_status',['nin'=>3])->getFirstItem();
        				$vid = $vproduct->getVendorId();
        
        				$cartProduct = Mage::getModel('csmarketplace/vproducts')->getCollection()->addFieldToFilter('product_id',['in'=>$id])->addFieldToFilter('check_status',['nin'=>3])->getFirstItem();
        				$cprovid = $cartProduct->getVendorId();
                      //   echo $cprovid; echo "<br>"; echo $vid; die("ifg");
        				if($vid != $cprovid)
        				{
        				
        					
        					Mage::getSingleton('core/session')->addError(__('Only One Vendors Products Can be added into Cart'));
        					//set false if you not want to add product to cart
        					Mage::app()->getRequest()->setParam('product', false);
        					return $this;
        					 
        
        				}
        			}
        			catch(Exception $e)
        			{
        				Mage::getSingleton('checkout/session')->addError($e);
        			}
        		}
        	}
        		
        	 
        }
        
        public function CustomerLogout(Varien_Event_Observer $observer){
        	foreach( Mage::getSingleton('checkout/session')->getQuote()->getItemsCollection() as $item ){
        		Mage::getSingleton('checkout/cart')->removeItem( $item->getId() )->save();
        		
        	}
        	return $this;
        }
        
        public function loadCustomerQuote(Varien_Event_Observer $observer)
        {
        	
        	$lastQuoteId = Mage::getSingleton('checkout/session')->getQuoteId();
        	if ($lastQuoteId) {
        		$customerQuote = Mage::getModel('sales/quote')
        		->loadByCustomer(Mage::getSingleton('customer/session')->getCustomerId());
        		$customerQuote->setQuoteId($lastQuoteId);
        		$this->_removeAllItems($customerQuote);
        	
        	} else {
        		$quote = Mage::getModel('checkout/session')->getQuote();
        		$this->_removeAllItems($quote);
        	}
        	return $this;
          }
        	
        	protected function _removeAllItems($quote){
        		foreach ($quote->getAllItems() as $item) {
        			$item->isDeleted(true);
        			if ($item->getHasChildren()) {
        				foreach ($item->getChildren() as $child) {
        					$child->isDeleted(true);
        				}
        			}
        		}
        		$quote->collectTotals()->save();
        }
		
}
