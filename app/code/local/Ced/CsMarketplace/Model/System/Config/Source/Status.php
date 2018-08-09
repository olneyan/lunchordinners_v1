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
 
class Ced_CsMarketplace_Model_System_Config_Source_Status extends Ced_CsMarketplace_Model_System_Config_Source_Abstract
{

    /**
     * Retrieve Option values array
     *
     * @return array
     */
    public function toOptionArray($defaultValues = false)
    {
		$options = array();
		if($defaultValues) 
			$options[] = array('value' => Ced_CsMarketplace_Model_Vendor::VENDOR_NEW_STATUS, 'label'=>Mage::helper('csmarketplace')->__('New'));
		$options[] = array('value' => Ced_CsMarketplace_Model_Vendor::VENDOR_APPROVED_STATUS, 'label'=>Mage::helper('csmarketplace')->__('Approved'));
		$options[] = array('value' => Ced_CsMarketplace_Model_Vendor::VENDOR_DISAPPROVED_STATUS, 'label'=>Mage::helper('csmarketplace')->__('Disapproved'));
		
		return $options;
		
    }

}