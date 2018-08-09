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
 * @package     Ced_AdvFlatRate
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com> 
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ced_AdvFlatRate_Model_Mysql4_Carrier_Advancerate_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	/**
	 * Initialize main table
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('advflatrate/carrier_advancerate');
	}
	
	/**
	 * Set Website Filter
	 *
	 * @return array
	 */
	public function setWebsiteFilter($websiteId)
    {
        return $this->addFieldToFilter('website_id', $websiteId);
    }

    /**
     * Set Condition Filter
     *
     * @return array
     */
    public function setConditionFilter($vendorId)
    {
        return $this->addFieldToFilter('vendor_id', $vendorId);
    }
    
}