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
class Ced_Mobiconnect_Helper_Data extends Mage_Core_Helper_Abstract {
	public function getWebsites() {
		$websites = Mage::getModel ( 'core/website' )->getCollection ();
		return $websites;
	}
	/*
	 * Check Show Banner Is enabled or Not @return true
	 */
	public function showBanner() {
		$show = Mage::getStoreConfig ( "mobiconnect/banners/banner_homepage" );
		return $show;
	}
	/*
	 * Check Show Most Viewed Is enabled or Not @return true
	 */
	public function showMostView() {
		$show = Mage::getStoreConfig ( "mobiconnect/banners/most_view" );
		return $show;
	}
	/*
	 * Check Show New Arrival Is enabled or Not @return true
	 */
	public function showNewArrival() {
		$show = Mage::getStoreConfig ( "mobiconnect/banners/new_arrival" );
		return $show;
	}
	/*
	 * Check Best Seller Is enabled or Not @return true
	 */
	public function showBestSeller() {
		$show = Mage::getStoreConfig ( "mobiconnect/banners/best_seller" );
		return $show;
	}
	/*
	 * Get nmbr of product to display on most view,new arrivals,best selling @return true
	 */
	public function getProductLimit() {
		$limit = Mage::getStoreConfig ( "mobiconnect/banners/limit_product" );
		return $limit;
	}

	public function changeAddress($data)
	{
		$country= $data['country_name'];
		//$countrycode =Zend_Locale_Data_Translation::$regionTranslation[$country];
		$data['country_id']=$country;
		$states=Mage::getModel('directory/region')->getResourceCollection() ->addCountryFilter($country)->load();
		$state = array();
		//var_dump($states->getData());die;
		foreach ($states as $region){
			$state[]=array(
			'name' =>$region['default_name'],
			'state_id'=>$region['region_id']
			);
		}
		if(is_array($state) && !empty($state)){
			foreach ($state as $val){
				//var_dump($val);die;
				if(in_array($data['region'], $val))
				{
					$data['region_id']=$val['state_id'];
					break;
				}
			}
		}
		return $data;
	}
	/**
	* get tax settings from configuration.  
	* @return $settings_array
	**/
	public function getTaxSetting(){

		$store_id=Mage::app()->getRequest()->getParam('store_id',Mage::app()->getStore()->getId());
		
		// Calculation setting

		$catalog_prices = Mage::getStoreConfig ("tax/calculation/price_includes_tax",$store_id);

		$shipping_prices = Mage::getStoreConfig ("tax/calculation/shipping_includes_tax",$store_id);

		// Price Display Settings

		$display_product_price_in_catalog= Mage::getStoreConfig ("tax/display/type",$store_id);

		$display_shipping_price= Mage::getStoreConfig ("tax/display/shipping",$store_id);

		// Shopping Cart Display Settings

		$cart_display_price= Mage::getStoreConfig ("tax/cart_display/price",$store_id);

		$cart_display_subtotal= Mage::getStoreConfig ("tax/cart_display/subtotal",$store_id);

		$cart_display_shipping_amount= Mage::getStoreConfig ("tax/cart_display/shipping",$store_id);

		$cart_include_tax_in_grandtotal= Mage::getStoreConfig ("tax/cart_display/grandtotal",$store_id);	

		//Orders, Invoices, Credit Memos Display Settings

		$sales_display_price= Mage::getStoreConfig ("tax/sales_display/price",$store_id);

		$sales_display_subtotal= Mage::getStoreConfig ("tax/sales_display/subtotal",$store_id);

		$sales_display_shipping_amount= Mage::getStoreConfig ("tax/sales_display/shipping",$store_id);

		$sales_include_tax_in_grandtotal= Mage::getStoreConfig ("tax/sales_display/grandtotal",$store_id);

		$settings_array=array(
							'calculation'   =>  array('price_includes_tax'=> $catalog_prices,
												 'shipping_includes_tax'=>	$shipping_prices
												),

							'display'		=>  array('type'=>  $display_product_price_in_catalog ,
												 'shipping'=>	$display_shipping_price
												),

							'cart_display'	=>  array('price'=> 		$cart_display_price ,
													  'subtotal'=>		$cart_display_subtotal,
													  'shipping'=> 		$cart_display_shipping_amount, 	
												 	  'grandtotal'=>	$cart_include_tax_in_grandtotal
												    ),

							'sales_display'	=>  array('price'=> 		$sales_display_price,
								  					  'subtotal'=> 		$sales_display_subtotal,
													  'shipping'=>		$sales_display_shipping_amount, 	
												 	  'grandtotal'=>	$sales_include_tax_in_grandtotal
												),
							);	
		
		return $settings_array;
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
     * Remove trilling line breaks
     *
     * @param string $string
     * @return string
     */
    public function trimLineBreaks($string)
    { 
        return preg_replace(array('@\r@', '@\n+@'), array('', PHP_EOL), $string);
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
		$allModules = Mage::app()->getConfig()->getNode(Ced_Mobiconnect_Model_Feed::XML_PATH_INSTALLATED_MODULES);
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
		$info['feed_types'] = Mage::getStoreConfig(Ced_Mobiconnect_Model_Feed::XML_FEED_TYPES);
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
}
?>