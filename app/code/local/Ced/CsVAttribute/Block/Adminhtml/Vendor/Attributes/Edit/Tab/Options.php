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
 
class Ced_CsVAttribute_Block_Adminhtml_Vendor_Attributes_Edit_Tab_Options extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Options_Abstract
{
	
	/**
	 * Retrieve frontend labels of attribute for each store
	 *
	 * @return array
	 */
	public function getLabelValues()
	{
		$values = array();
		$values[0] = $this->getAttributeObject()->getFrontend()->getLabel();
		// it can be array and cause bug
		$frontendLabel = $this->getAttributeObject()->getFrontend()->getLabel();
		if (is_array($frontendLabel)) {
			$frontendLabel = array_shift($frontendLabel);
		}
		$storeLabels = $this->getAttributeObject()->getStoreLabels();
		foreach ($this->getStores() as $store) {
			if ($store->getId() != 0) {
				$values[$store->getId()] = isset($storeLabels[$store->getId()]) ? $storeLabels[$store->getId()] : '';
			}
		}
		$val=$values[0];
		if(is_array($val))
			$values[0]=array_shift($val);
		return $values;
	}
}
