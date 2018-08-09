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

class Ced_CsMarketplace_Model_System_Config_Source_Vproducts_Website extends Ced_CsMarketplace_Model_System_Config_Source_Abstract
{
	/**
	 * Supported Product Type by Ced_CsMarketplace extension.
	 */	 
    const XML_PATH_CED_CSMARKETPLACE_VPRODUCTS_WEBSITE = 'global/ced_csmarketplace/vproducts/website';
	
	/**
     * Retrieve Option values array
     *
	 * @param boolean $defaultValues
	 * @param boolean $withEmpty
     * @return array
     */
    public function toOptionArray($empty = false, $all = true)
    {
		$websites = Mage::getModel('adminhtml/system_config_source_website')->toOptionArray();
		array_unshift($websites, array('value'=>0, 'label'=> Mage::helper('adminhtml')->__('All Websites')));
		return $websites;
		return Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, $all);
    }
}