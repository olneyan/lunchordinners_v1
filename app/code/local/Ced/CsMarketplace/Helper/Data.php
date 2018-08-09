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
 
class Ced_CsMarketplace_Helper_Data extends Mage_Core_Helper_Abstract
{
    
	const VORDER_CREATE   = "VORDER_CREATE";  
	const VORDER_CANCELED   = "VORDER_CANCELED";
	const VORDER_PAYMENT_STATE_CHANGED   = "VORDER_PAYMENT_STATE_CHANGED";
	
	const SALES_ORDER_CREATE   = "SALES_ORDER_CREATE";  
	const SALES_ORDER_CANCELED   = "SALES_ORDER_CANCELED";  
	const SALES_ORDER_ITEM   = "SALES_ORDER_ITEM";  
	const SALES_ORDER_PAYMENT_STATE_CHANGED   = "SALES_ORDER_PAYMENT_STATE_CHANGED";  
	
   	const VPAYMENT_CREATE   = "VPAYMENT_CREATE";  
	const VPAYMENT_TOTAL_AMOUNT   = "VPAYMENT_TOTAL_AMOUNT";  
	
	protected $_allowedFeedType = array();

	public static $ced_vendor = false;

	/**
	 * Initialize allowed feed type
	*/
	public function __construct() {
		$this->_allowedFeedType =  explode(',',Mage::getStoreConfig(Ced_CsMarketplace_Model_Feed::XML_FEED_TYPES));
	}
	
	public function getCustomCSS(){
		return Mage::getStoreConfig('ced_csmarketplace/vendor/theme_css');
	}
	
	/**
     * Check if current url is url for home page
     *
     * @return true
     */
    public function getIsDashboard()
    {
        return $this->getVendorUrl() == Mage::getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true))
				||
				$this->getVendorUrl().'/index' == Mage::getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true))
				||
				$this->getVendorUrl().'/index/' == Mage::getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true))
				||
				$this->getVendorUrl().'index' == Mage::getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true))
				||
				$this->getVendorUrl().'index/' == Mage::getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true))
				;
    }

    public function setLogo($logo_src, $logo_alt)
    {
        $this->setLogoSrc($logo_src);
        $this->setLogoAlt($logo_alt);
        return $this;
    }

    public function getLogoSrc()
    {	
		return Mage::getDesign()->getSkinUrl(Mage::getStoreConfig('ced_csmarketplace/vendor/vendor_logo_src'));
    }

    public function getLogoAlt()
    {	
        return Mage::getStoreConfig('ced_csmarketplace/vendor/vendor_logo_alt');
    }
	
	public function getVendorFooterText()
    {	
        return Mage::getStoreConfig('ced_csmarketplace/vendor/vendor_footer_text');
    }
	
	public function getMarketplaceVersion(){
		return trim((string)Mage::getConfig()->getNode('modules/Ced_CsMarketplace/release_version'));
	}
	
	
	/**
	 * Url encode the parameters
	 * @param string | array
	 * @return string | array | boolean
	 */
	public function prepareParams($data){
		if(!is_array($data) && strlen($data)){
			return urlencode($data);
		}
		if($data && is_array($data) && count($data)>0){
			foreach($data as $key=>$value){
				$data[$key] = urlencode($value);
			}
			return $data;
		}
		return false;
	}
	
	/**
	 * Url decode the parameters
	 * @param string | array
	 * @return string | array | boolean
	 */
	public function extractParams($data){
		if(!is_array($data) && strlen($data)){
			return urldecode($data);
		}
		if($data && is_array($data) && count($data)>0){
			foreach($data as $key=>$value){
				$data[$key] = urldecode($value);
			}
			return $data;
		}
		return false;
	}
	
	/**
	 * Add params into url string
	 *
	 * @param string $url (default '')
	 * @param array $params (default array())
	 * @param boolean $urlencode (default true)
	 * @return string | array
	 */
	public function addParams($url = '', $params = array(), $urlencode = true) {
		if(count($params)>0){
			foreach($params as $key=>$value){
				if(parse_url($url, PHP_URL_QUERY)) {
					if($urlencode)
						$url .= '&'.$key.'='.$this->prepareParams($value);
					else
						$url .= '&'.$key.'='.$value;
				} else {
					if($urlencode)
						$url .= '?'.$key.'='.$this->prepareParams($value);
					else
						$url .= '?'.$key.'='.$value;
				}
			}
		}
		return $url;
	}
	
	/**
	 * Retrieve all the extensions name and version developed by CedCommerce
	 * @param boolean $asString (default false)
	 * @return array|string
	 */
	public function getCedCommerceExtensions($asString = false,$productName = false) {
		if($asString) {
			$cedCommerceModules = '';
		} else {
			$cedCommerceModules = array();
		}
		$allModules = Mage::app()->getConfig()->getNode(Ced_CsMarketplace_Model_Feed::XML_PATH_INSTALLATED_MODULES);
		$allModules = json_decode(json_encode($allModules),true);
		foreach($allModules as $name=>$module) {
			$name = trim($name);
			if(preg_match('/ced_/i',$name) && isset($module['release_version']) && !preg_match('/ced_csvendorpanel/i',$name)
				&& !preg_match('/ced_cstransaction/i',$name) && !preg_match('/ced_csvattribute/i',$name) && !preg_match('/ced_csseosuite/i',$name)) {
				
				if($asString) {
					$cedCommerceModules .= $name.':'.trim($module['release_version']).'~';
				} else {
					if($productName){
						$cedCommerceModules[$name]['release_version'] = trim($module['release_version']);
						$cedCommerceModules[$name]['parent_product_name'] = (isset($module['parent_product_name']) && strlen($module['parent_product_name']) > 0) ? $module['parent_product_name'] : trim($name);
					} else {
						$cedCommerceModules[$name] = trim($module['release_version']);
					}
					
				}
			}
		}
		if($asString) trim($cedCommerceModules,'~');
		return $cedCommerceModules;
	}
	
	/**
	 * Retrieve environment information of magento
	 * And installed extensions provided by CedCommerce
	 *
	 * @return array
	 */
	public function getEnvironmentInformation () {
		$info = array();
		$info['domain_name'] = Mage::getBaseUrl();
		$info['framework'] = 'magento';
		$info['edition'] = 'default';
		if(method_exists('Mage','getEdition')) $info['edition'] = Mage::getEdition();
		$info['version'] = Mage::getVersion();
		$info['php_version'] = phpversion();
		$info['feed_types'] = Mage::getStoreConfig(Ced_CsMarketplace_Model_Feed::XML_FEED_TYPES);
		$info['admin_name'] =  Mage::getStoreConfig('trans_email/ident_general/name');
		$info['country_id'] =  Mage::getStoreConfig('general/store_information/merchant_country');
		if($info['country_id']=='')
		{
			$info['country_id'] =  Mage::getStoreConfig('general/country/default');
		}
		if(strlen($info['admin_name']) == 0) $info['admin_name'] =  Mage::getStoreConfig('trans_email/ident_sales/name');
		$info['admin_email'] =  Mage::getStoreConfig('trans_email/ident_general/email');
		if(strlen($info['admin_email']) == 0) $info['admin_email'] =  Mage::getStoreConfig('trans_email/ident_sales/email');
		$info['installed_extensions_by_cedcommerce'] = $this->getCedCommerceExtensions(true);
		
		return $info;
	}
	
	/**
	 * Retrieve admin interest in current feed type
	 *
	 * @param SimpleXMLElement $item
	 * @return boolean $isAllowed
	 */
	public function isAllowedFeedType(SimpleXMLElement $item) {
		$isAllowed = false;
		if(is_array($this->_allowedFeedType) && count($this->_allowedFeedType) >0) {
			$cedModules = $this->getCedCommerceExtensions();
			switch(trim((string)$item->update_type)) {
				case Ced_CsMarketplace_Model_Source_Updates_Type::TYPE_NEW_RELEASE :
				case Ced_CsMarketplace_Model_Source_Updates_Type::TYPE_INSTALLED_UPDATE :
					if (in_array(Ced_CsMarketplace_Model_Source_Updates_Type::TYPE_INSTALLED_UPDATE,$this->_allowedFeedType) && strlen(trim($item->module)) > 0 && isset($cedModules[trim($item->module)]) && version_compare($cedModules[trim($item->module)],trim($item->release_version), '<')===true) {
						$isAllowed = true;
						break;
					}
				case Ced_CsMarketplace_Model_Source_Updates_Type::TYPE_UPDATE_RELEASE :
					if(in_array(Ced_CsMarketplace_Model_Source_Updates_Type::TYPE_UPDATE_RELEASE,$this->_allowedFeedType) && strlen(trim($item->module)) > 0) {
						$isAllowed = true;
						break;
					}
					if(in_array(Ced_CsMarketplace_Model_Source_Updates_Type::TYPE_NEW_RELEASE,$this->_allowedFeedType)) {
						$isAllowed = true;
					}
					break;
				case Ced_CsMarketplace_Model_Source_Updates_Type::TYPE_PROMO :
					if(in_array(Ced_CsMarketplace_Model_Source_Updates_Type::TYPE_PROMO,$this->_allowedFeedType)) {
						$isAllowed = true;
					}
					break;
				case Ced_CsMarketplace_Model_Source_Updates_Type::TYPE_INFO :
					if(in_array(Ced_CsMarketplace_Model_Source_Updates_Type::TYPE_INFO,$this->_allowedFeedType)) {
						$isAllowed = true;
					}
					break;
			}
		}
		return $isAllowed;
	}
  
	/**
     * Retrieve vendor account page url
     *
     * @return string
     */
    public function getCsMarketplaceUrl()
    {
        return $this->_getUrl('csmarketplace/vshops',array('_secure'=>true,'_nosid'=>true));
    }
    

    /**
     * Retrieve CsMarketplace title
     *
     * @return string
     */
    public function getCsMarketplaceTitle()
    {
    	return Mage::getStoreConfig('ced_vshops/general/vshoppage_top_title',Mage::app()->getStore()->getId());
    }
    
    /**
     * Retrieve I am a Vendor title
     *
     * @return string
     */
    public function getIAmAVendorTitle()
    {
    	return Mage::getStoreConfig('ced_vshops/general/vshoppage_vendor_title',Mage::app()->getStore()->getId());
    }
    
    /**
     * Check customer account sharing is enabled
     *
     * @return boolean
     */
    public function isSharingEnabled()
    {
		$stores = Mage::getStoreConfig('ced_vproducts/general/website');
		if(strlen($stores) > 0){
			$stores = explode(',',$stores);
			if(in_array(0,$stores)){
				return true;
			}
		}
	    /* if(Mage::getStoreConfig(Mage_Customer_Model_Config_Share::XML_PATH_CUSTOMER_ACCOUNT_SHARE)==Mage_Customer_Model_Config_Share::SHARE_GLOBAL)
	    	return true; */
	    return false;
    }
    
    /**
     * get Product limit
     *
     * @return integer
     */
    public function getVendorProductLimit()
    {
    	return Mage::getStoreConfig('ced_vproducts/general/limit');
    }
    
	/**
     * Retrieve vendor account page url
     *
     * @return string
     */
    public function getVendorUrl()
    {
		
		if(Mage::getConfig()->getModuleConfig('Ced_CsSeoSuite')->is('active', 'true') && Mage::helper('csseosuite')->isEnabled() && Mage::getStoreConfig('ced_csmarketplace/general/use_in_vendorpanel') && strlen(Mage::helper('csseosuite')->getCustomUrl()) > 0) {
			$vendorUrl = Mage::helper('csseosuite')->getCustomUrl();
			$defaultUrl = $this->_getUrl('csmarketplace/vendor',array('_secure'=>true,'_nosid'=>true));
			$defaultUrlQuery = parse_url($defaultUrl);
			if(isset($defaultUrlQuery['scheme'])) {
				$vendorUrl = str_replace('https://','',$vendorUrl);
				$vendorUrl = str_replace('http://','',$vendorUrl);
				switch($defaultUrlQuery['scheme']) {
					case 'https': $vendorUrl = 'https://'.$vendorUrl; break;
					case 'http':  
					default: $vendorUrl = 'http://'.$vendorUrl; break;
				}
			}
			return $vendorUrl;
		} else {
			return $this->_getUrl('csmarketplace/vendor',array('_secure'=>true,'_nosid'=>true));
		}
        
    }
	
	/* Retrieve vendor account page url
     *
     * @return string
     */
    public function getVendorLoginUrl()
    {
		return $this->_getUrl('csmarketplace/account/login',array('_secure'=>true,'_nosid'=>true));
        
    }
	
	/**
     * Authenticate vendor
     *
	 * @param int $customerId
     * @return boolean
     */
	public function authenticate($customerId = 0) {
		if ($customerId) {
			if(!Ced_CsMarketplace_Helper_Data::$ced_vendor) {
				$vendor = Mage::getModel('csmarketplace/vendor')->loadByCustomerId($customerId);
				Ced_CsMarketplace_Helper_Data::$ced_vendor = $vendor;
			} else {
				$vendor = Ced_CsMarketplace_Helper_Data::$ced_vendor;
			}
			
			if($vendor && $vendor->getId()) {
				return $this->canShow($vendor);
			}
		}
		return false;
	}
	
	/**
     * Check if a vendor can be shown
     *
     * @param  Ced_CsMarketplace_Model_Vendor|int $vendor
     * @return boolean
     */
    public function canShow($vendor) {
        if (is_numeric($vendor)) {
            $vendor = Mage::getModel('csmarketplace/vendor')->load($vendor);
        }
		
		if (!is_object($vendor)) {
            $vendor = Mage::getModel('csmarketplace/vendor')->loadByAttribute('shop_url',$vendor);
        }

        if (!$vendor || !$vendor->getId()) {
            return false;
        }

        if (!$vendor->getIsActive()) {
            return false;
        }
        if(!Mage::app()->getStore()->isAdmin()){
       		if(!$this->isSharingEnabled()&&($vendor->getWebsiteId()!=Mage::app()->getStore()->getWebsiteId())){
        		return false;
        	}
        }

        return true;
    }
	

    /**
     *Rebuild Website Ids
     *@param $vendor
     * @return array $websiteIds
     */
    public function rebuildWebsites()
    {
    		$collection = Mage::getModel('csmarketplace/vproducts')->getVendorProducts('',0,0,-1)->setOrder('vendor_id','ASC');
    		foreach ($collection as $row){
    			$productIds[]=$row->getProductId();
    		}
    		$previousVendorId=0;
    		$vendorWebsiteIds=array();
    		$removeWebsiteIds = array_keys(Mage::app()->getWebsites());
    		$actionModel = Mage::getSingleton('catalog/product_action');
    		$this->updateWebsites($productIds, $removeWebsiteIds, 'remove');
    		
    		foreach ($collection as $row){
    			if(!$this->canShow($row->getVendorId())){
    				continue;
    			}
    			$productWebsiteIds=explode(',',$row->getWebsiteIds());
				if(Mage::registry('current_order_vendor'))
					Mage::unRegister('current_order_vendor');
				Mage::register('current_order_vendor',$row->getVendorId());
    			if(!$previousVendorId || $previousVendorId!=$row->getVendorId()){
    				$vendorWebsiteIds=Mage::getModel('csmarketplace/vendor')->getWebsiteIds($row->getVendorId());
    			}
    			$previousVendorId=$row->getVendorId();
    			$websiteIds=array_intersect($productWebsiteIds,$vendorWebsiteIds);
    			if($websiteIds)
    				$this->updateWebsites(array($row->getProductId()), $websiteIds, 'add');
    		}
    		
    		$indexCollection = Mage::getModel('index/process')->getCollection();
    		foreach ($indexCollection as $index) {
    			/* @var $index Mage_Index_Model_Process */
    			$index->reindexAll();
    		}
    		Mage::app()->cleanCache();
    		
    		$config = new Mage_Core_Model_Config();
    		$config->saveConfig(Ced_CsMarketplace_Model_Vendor::XML_PATH_VENDOR_WEBSITE_SHARE,0);
    	
    }
    
    
    /**
     *update Websites
     *@param $productIds,$websiteIds,$type
     */
    public function updateWebsites($productIds, $websiteIds, $type)
    {
    	Mage::dispatchEvent('catalog_product_website_update_before', array(
    	'website_ids'   => $websiteIds,
    	'product_ids'   => $productIds,
    	'action'        => $type
    	));
    
    	if ($type == 'add') {
    		Mage::getModel('catalog/product_website')->addProducts($websiteIds, $productIds);
    	} else if ($type == 'remove') {
    		Mage::getModel('catalog/product_website')->removeProducts($websiteIds, $productIds);
    	}
    
    	$actionModel = Mage::getSingleton('catalog/product_action');
    	$actionModel->setData(array(
    			'product_ids' => array_unique($productIds),
    			'website_ids' => $websiteIds,
    			'action_type' => $type
    	));
    	
    
    	// add back compatibility system event
    	Mage::dispatchEvent('catalog_product_website_update', array(
    	'website_ids'   => $websiteIds,
    	'product_ids'   => $productIds,
    	'action'        => $type
    	));
    }
    
    
    
	/**
	 * Get new vendor collection
	 *
	 * @return Ced_CsMarketplace_Model_Resource_Vendor_Collection
	 */
	public function getNewVendors() {
		
		return Mage::getResourceModel('csmarketplace/vendor_collection')->addAttributeToFilter('status',array('eq'=>Ced_CsMarketplace_Model_Vendor::VENDOR_NEW_STATUS));
	}
	
	
	public function getFilterParams() {
		return array (
				'_secure' => true,
				Ced_CsMarketplace_Block_Adminhtml_Vendor_Entity_Grid::VAR_NAME_FILTER => base64_encode('status='.Ced_CsMarketplace_Model_Vendor::VENDOR_NEW_STATUS),
			   );
	}
	
	
	/**
     * Check Vendor Log is enabled
     *
     * @return boolean
     */
    public function isVendorLogEnabled()
    {
			return Mage::getStoreConfig('ced_csmarketplace/vlogs/active',$this->getStore()->getId());
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
	 * Log Process Data
	 */
	 public function logProcessedData($data, $tag=false) {
	 

	 	if(!$this->isVendorLogEnabled())
			return;
			
		$file = Mage::getStoreConfig('ced_vlogs/general/process_file');
				
		$controller = Mage::app()->getRequest()->getControllerName();
		$action = Mage::app()->getRequest()->getActionName();
		$router = Mage::app()->getRequest()->getRouteName();
		$module = Mage::app()->getRequest()->getModuleName();
		
		$out = '';
		//if ($html) 
		@$out .= "<pre>";
		@$out .= "Controller: $controller\n";
		@$out .= "Action: $action\n";
		@$out .= "Router: $router\n";
		@$out .= "Module: $module\n";
		foreach(debug_backtrace() as $key=>$info)
        {
            @$out .= "#" . $key . " Called " . $info['function'] ." in " . $info['file'] . " on line " . $info['line']."\n"; 
			break;        
        }
		if($tag)
			@$out .= "#Tag " . $tag."\n"; 
			
		//if ($html)
		@$out .= "</pre>";
		Mage::log("\n Source: \n" . print_r($out, true), Zend_Log::INFO, $file, true);
		Mage::log("\n Processed Data: \n" . print_r($data, true), Zend_Log::INFO, $file, true);
	 }
	 
	 
	 /**
	 * Log Exception
	 */
	 public function logException(Exception $e) {
	 	if(!$this->isVendorLogEnabled())
			return;
			
		$file = Mage::getStoreConfig('ced_vlogs/general/exception_file');
        Mage::log("\n" . $e->__toString(), Zend_Log::ERR, $file, true);
		
	 }
	 
	 /**
     * Check Vendor Log is enabled
     *
     * @return boolean
     */
    public function isVendorDebugEnabled()
    {
    	$isDebugEnable = (int)Mage::getStoreConfig('ced_csmarketplace/vlogs/debug_active');
        $clientIp = $this->_getRequest()->getClientIp();
        $allow = false;

        if( $isDebugEnable ){
            $allow = true;

            // Code copy-pasted from core/helper, isDevAllowed method 
            // I cannot use that method because the client ip is not always correct (e.g varnish)
            $allowedIps = Mage::getStoreConfig('dev/restrict/allow_ips');
            if ( $isDebugEnable && !empty($allowedIps) && !empty($clientIp)) {
                $allowedIps = preg_split('#\s*,\s*#', $allowedIps, null, PREG_SPLIT_NO_EMPTY);
                if (array_search($clientIp, $allowedIps) === false
                    && array_search(Mage::helper('core/http')->getHttpHost(), $allowedIps) === false) {
                    $allow = false;
                }
            }
        }
        return $allow;
	}
	
	/**
	 * Check Vendor Log is enabled
	 *
	 * @return boolean
	 */
	public function isShopEnabled($vendor)
	{
		$vendor_id_tmp=Mage::helper('csmarketplace')->getTableKey('vendor_id');
		$model = Mage::getModel('csmarketplace/vshop')->loadByField(array($vendor_id_tmp),array($vendor->getId()));
		if($model && $model->getId()){
			if($model->getShopDisable()==Ced_CsMarketplace_Model_Vshop::DISABLED)
				return false;
		}
		return true;
	}
	
	public function dateDiff($time1, $time2, $precision = 6) {
		if (!is_int($time1)) {
		  $time1 = strtotime($time1);
		}
		if (!is_int($time2)) {
		  $time2 = strtotime($time2);
		}
		if ($time1 > $time2) {
		  $ttime = $time1;
		  $time1 = $time2;
		  $time2 = $ttime;
		}
	 
		$intervals = array('year','month','day','hour','minute','second');
		$diffs = array();
	 

		foreach ($intervals as $interval) {

		  $ttime = strtotime('+1 ' . $interval, $time1);

		  $add = 1;
		  $looped = 0;
		  while ($time2 >= $ttime) {
			$add++;
			$ttime = strtotime("+" . $add . " " . $interval, $time1);
			$looped++;
		  }
	 
		  $time1 = strtotime("+" . $looped . " " . $interval, $time1);
		  $diffs[$interval] = $looped;
		}
		
		$count = 0;
		$times = array();

		foreach ($diffs as $interval => $value) {
			
			if ($count >= $precision) {
				break;
			}

			if ($value > 0) {

			if ($value != 1) {
				$interval .= "s";
			}

			$times[] = $value . " " . $interval;
			$count++;
			}
		}

		return implode(", ", $times);
	}
	
	public function getTableKey($key){
		$tablePrefix = (string) Mage::getConfig()->getTablePrefix();
		$exists = (boolean) Mage::getSingleton('core/resource')
		->getConnection('core_write')
		->showTableStatus($tablePrefix.'permission_variable');
		if($exists)
		{
			return $key;
		}
		else
		{
			return "`{$key}`";
		}
	}


	public function eligible($vid)
	{

	
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
        				//$paramProductId = $products;
        				 
        				//$vproduct = Mage::getModel('csmarketplace/vproducts')->getCollection()->addFieldToFilter('product_id',['in'=>$paramProductId])->addFieldToFilter('check_status',['nin'=>3])->getFirstItem();
        				//$vid = $vproduct->getVendorId();
        
        				$cartProduct = Mage::getModel('csmarketplace/vproducts')->getCollection()->addFieldToFilter('product_id',['in'=>$id])->addFieldToFilter('check_status',['nin'=>3])->getFirstItem();
        				$cprovid = $cartProduct->getVendorId();
                      //   echo $cprovid; echo "<br>"; echo $vid; die("ifg");
        				if($vid != $cprovid)
        				{
        				
        					
        					$error = 'Only One Vendors Products Can be added into Cart';
        					$vendor  =  Mage::getModel('csmarketplace/vendor')->load($cprovid);
        					$vendorName = $vendor->getPublicName();
        					$shopUrl = $vendor->getVendorShopUrl();
        					$array =['name'=>$vendorName,'shop'=>$shopUrl];
        					//$array = json_encode($array);
        					//set false if you not want to add product to cart
        					//Mage::app()->getRequest()->setParam('product', false);
        					return $array;
        					 
        
        				}
        				else{
        					$array =['name'=>'success'];
        				//	$array = json_encode($array);
        					return $array;
        				}  
        			}
        			catch(Exception $e)
        			{
        				Mage::getSingleton('checkout/session')->addError($e);
        			}
        		}
        	}
        	else{
        		$array =['name'=>'success'];
        				
        		return $array;
        		
        	}
   
	}
}
