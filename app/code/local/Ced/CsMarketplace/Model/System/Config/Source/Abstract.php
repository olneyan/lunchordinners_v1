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
 
class Ced_CsMarketplace_Model_System_Config_Source_Abstract extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
	 /**
     * Retrieve Full Option values array
     *
     * @param bool $withEmpty       Add empty option to array
     * @param bool $defaultValues
     * @return array
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        $storeId = $this->getAttribute()->getStoreId();
		$this->_options[$storeId] = $this->_optionsDefault[$storeId] = $this->toOptionArray($defaultValues);
        if (!is_array($this->_options)) {
            $this->_options = array();
        }
        if (!is_array($this->_optionsDefault)) {
            $this->_optionsDefault = array();
        }
        if (!isset($this->_options[$storeId])) {
            $collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setPositionOrder('asc')
                ->setAttributeFilter($this->getAttribute()->getId())
                ->setStoreFilter($this->getAttribute()->getStoreId())
                ->load();
            $this->_options[$storeId]        = $collection->toOptionArray($defaultValues);
            $this->_optionsDefault[$storeId] = $collection->toOptionArray($defaultValues);
        }
        $options = ($defaultValues ? $this->_optionsDefault[$storeId] : $this->_options[$storeId]);
        if ($withEmpty) {
            array_unshift($options, array('label' => '', 'value' => ''));
        }
        return $options;
    }
	
	/**
     * Retrieve options for grid filter
     *
     * @param String $value
     * @return String
     */
	public function toFilterOptionArray($defaultValues = false, $withEmpty = false,$storeId=null) {
		if($storeId==null)
			$options = $this->toOptionArray($defaultValues, $withEmpty);
		else 
			$options = $this->toOptionArray($defaultValues, $withEmpty ,$storeId);
		$filterOptions = array();
		if(count($options)) {
			foreach($options as $option) {
				if(isset($option['value']) && isset($option['label'])) {
					$filterOptions[$option['value']] = $option['label'];
				}
			}
		}
		return $filterOptions;
	}
	
	/**
     * Retrieve option label by option value
     *
     * @param String $value
     * @return String
     */
	public function getLabelByValue($value = '') {
		$options = $this->toOptionArray();
		if(count($options)) {
			foreach($options as $option) {
				if(isset($option['value']) && $option['value'] == $value) {
					$value = isset($option['label'])?$option['label']:$value;
					break;
				}
			}
		}
		return $value;
	}
	
	

}