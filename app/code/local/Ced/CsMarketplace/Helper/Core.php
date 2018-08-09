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
 
class Ced_CsMarketplace_Helper_Core extends Mage_Core_Helper_Abstract
{	
	protected $_allowedFeedType = array();
	
	/**
	 * Initialize allowed feed type
	*/
	public function __construct() {
		$this->_allowedFeedType =  explode(',',Mage::getStoreConfig(Ced_CsMarketplace_Model_Feed::XML_FEED_TYPES));
	}
	
	/**
	 * Retrieve all the extensions name and version developed by CedCommerce
	 * @param boolean $asString (default false)
	 * @return array|string
	 */
	public function getCedCommerceExtensions($asString = false) {
		if($asString) {
			$cedCommerceModules = '';
		} else {
			$cedCommerceModules = array();
		}
		$allModules = Mage::app()->getConfig()->getNode(Ced_CsMarketplace_Model_Feed::XML_PATH_INSTALLATED_MODULES);
		$allModules = json_decode(json_encode($allModules),true);
		foreach($allModules as $name=>$module) {
			$name = trim($name);
			if(preg_match('/ced_/i',$name) && isset($module['release_version'])) {
				if($asString) {
					$cedCommerceModules .= $name.':'.trim($module['release_version']).'~';
				} else {
					$cedCommerceModules[$name] = trim($module['release_version']);
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
		$info['magento_edition'] = 'default';
		if(method_exists('Mage','getEdition')) $info['magento_edition'] = Mage::getEdition();
		$info['magento_version'] = Mage::getVersion();
		$info['php_version'] = phpversion();
		$info['feed_types'] = Mage::getStoreConfig(Ced_CsMarketplace_Model_Feed::XML_FEED_TYPES);
		$info['admin_name'] =  Mage::getStoreConfig('trans_email/ident_general/name');
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
			//echo trim((string)$item->update_type);die;
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
}
