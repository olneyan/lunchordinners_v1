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
class Ced_CsMarketplace_Model_System_Config_Source_Customers extends Ced_CsMarketplace_Model_System_Config_Source_Abstract
{

    /**
     * Retrieve Option values array
     *
     * @return array
     */
    public function toOptionArray($selected = false)
    {
        $options = array();
		$registeredCustomers = array();
		$customers = Mage::getModel('customer/customer')->getCollection();
		if($selected) {
			$customers->addAttributeToFilter('entity_id',array('eq'=>$selected));
		} else {
			$vendors = Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToSelect('customer_id');
			if(count($vendors)>0) {
				foreach($vendors as $vendor) {
					$registeredCustomers[] = $vendor->getCustomerId();
				}
				if(count($registeredCustomers) > 0) {
					$customers->addAttributeToFilter('entity_id',array('nin'=>$registeredCustomers));
				}
			}
		}
		foreach($customers as $customer) {
			$customer->load($customer->getId());
			$options[] = array('value' => $customer->getId(), 'label'=>$customer->getName()." (".$customer->getEmail().")");
		}
		return $options;
    }

}