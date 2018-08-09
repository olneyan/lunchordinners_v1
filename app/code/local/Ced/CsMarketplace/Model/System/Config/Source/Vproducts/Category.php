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

class Ced_CsMarketplace_Model_System_Config_Source_Vproducts_Category extends Ced_CsMarketplace_Model_System_Config_Source_Abstract
{
	/**
	 * Supported Product Type by Ced_CsMarketplace extension.
	 */	 
    const XML_PATH_CED_CSMARKETPLACE_VPRODUCTS_CATEGORY_MODE = 'global/ced_csmarketplace/vproducts/category_mode';
    const XML_PATH_CED_CSMARKETPLACE_VPRODUCTS_CATEGORY = 'global/ced_csmarketplace/vproducts/category';
	
	 /**
     * Retrieve Option values array
     *
     * @return array
     */
    public function toOptionArray()
    {
		$options = array();
		$options[] = array('value' => '0', 'label'=>Mage::helper('csmarketplace')->__('All Allowed Categories'));
		$options[] = array('value' => '1', 'label'=>Mage::helper('csmarketplace')->__('Specific Categories'));
		return $options;
		
    }

}