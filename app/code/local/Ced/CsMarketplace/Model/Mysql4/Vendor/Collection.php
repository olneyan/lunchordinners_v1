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


/**
 * Vendor collection
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */ 
 
class Ced_CsMarketplace_Model_Mysql4_Vendor_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('csmarketplace/vendor');
    }
	
	/**
	 * Retrieve Option values array
	 *
	 * @return array
	 */
    public function toOptionArray($vendor_id = 0)
    {
        $options = array();
		$vendors = $this->addAttributeToSelect(array('name','email'));
		if($vendor_id) {
			$vendors->addAttributeToFilter('entity_id',array('eq'=>(int)$vendor_id));
		}
		$options['']=Mage::helper('csmarketplace')->__('-- please select vendor --');
		foreach($vendors as $vendor) {
			$options[$vendor->getId()] = $vendor->getName().' ('.$vendor->getEmail().')';
		}
		return $options;
    }
}
