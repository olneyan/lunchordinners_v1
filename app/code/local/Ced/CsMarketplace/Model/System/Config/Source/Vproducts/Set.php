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

class Ced_CsMarketplace_Model_System_Config_Source_Vproducts_Set extends Ced_CsMarketplace_Model_System_Config_Source_Abstract
{

    /**
     * Retrieve Option values array
     *
     * @return array
     */
    public function toOptionArray($defaultValues = false,$withEmpty = false)
    {
		$options = array();
		$sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
					->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
					->load()
					->toOptionHash();
		if (!$defaultValues)
			$allowedSet = $this->getAllowedSet(Mage::app()->getStore()->getId());
		foreach($sets as $value=>$label) {
			if(!$defaultValues && !in_array($value,$allowedSet)) continue;
			$options[] = array('value'=>$value,'label'=>$label);
		}
		if ($withEmpty) {
            array_unshift($options, array('label' => '', 'value' => ''));
        }
		return $options;
    }
	
	/**
	 * Get Allowed product attribute set
	 *
	 */
	public function getAllowedSet($storeId = 0) {
		if($storeId) return explode(',',Mage::getStoreConfig('ced_vproducts/general/set',$storeId));
		return explode(',',Mage::getStoreConfig('ced_vproducts/general/set'));
	}

}