<?php 

/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License (AFL 3.0)
  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/afl-3.0.php
  *
  * @category    Ced
  * @package     Ced_CsVAttribute
  * @author   	 CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

class Ced_CsVAttribute_Model_Observer
{
	
	public function vendorEditAttributesLoadAfter($observer) {
		$vendorAttributes = $observer->getvendorattributes();
		if(!Mage::getStoreConfig('ced_csvattribute/general/activation',Mage::app()->getStore()->getId())) {
			$vendorAttributes=$vendorAttributes->addFieldToFilter('is_user_defined',array('eq'=>0));
		}
		return $vendorAttributes;
	}
	
	public function vendorGroupWiseAttributesLoadAfter($observer) {
		$vendorAttributes = $observer->getvendorattributes();
		if(!Mage::getStoreConfig('ced_csvattribute/general/activation',Mage::app()->getStore()->getId())) {
			$vendorAttributes=$vendorAttributes->addFieldToFilter('is_user_defined',array('eq'=>0));
		}
		return $vendorAttributes;
	}

}
