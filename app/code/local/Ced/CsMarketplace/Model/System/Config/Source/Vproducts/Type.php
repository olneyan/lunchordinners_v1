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

class Ced_CsMarketplace_Model_System_Config_Source_Vproducts_Type extends Ced_CsMarketplace_Model_System_Config_Source_Abstract
{
	/**
	 * Supported Product Type by Ced_CsMarketplace extension.
	 */	 
    const XML_PATH_CED_CSMARKETPLACE_VPRODUCTS_TYPE = 'global/ced_csmarketplace/vproducts/types';
	
	/**
     * Retrieve Option values array
     *
	 * @param boolean $defaultValues
	 * @param boolean $withEmpty
     * @return array
     */
    public function toOptionArray($defaultValues = false, $withEmpty = false,$storeId=null)
    {
		$options = array();
		if (!$defaultValues) {
			if($storeId == null) 
				$storeId = Mage::app()->getStore()->getId();
			$allowedType = $this->getAllowedType($storeId);
		}
		
		$types = Mage::app()->getConfig()->getNode(self::XML_PATH_CED_CSMARKETPLACE_VPRODUCTS_TYPE);
		$types = array_keys((array)$types);
		foreach(Mage::getModel('catalog/product_type')->getOptionArray() as $value=>$label) {
			if(in_array($value,$types)) {
				if(!$defaultValues && !in_array($value,$allowedType)) continue;
				$options[] = array('value'=>$value,'label'=>$label);
			}
		}
		if ($withEmpty) {
            array_unshift($options, array('label' => '', 'value' => ''));
        }
		return $options;
    }
    
    
	/**
	 * Get Allowed product type
	 * @param int $storeId
	 * @return array
	 */
	public function getAllowedType($storeId = 0) {
		if($storeId) return explode(',',Mage::getStoreConfig('ced_vproducts/general/type',$storeId));
		return explode(',',Mage::getStoreConfig('ced_vproducts/general/type'));
	}

}