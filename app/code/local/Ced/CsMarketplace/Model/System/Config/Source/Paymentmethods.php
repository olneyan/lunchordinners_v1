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
 
class Ced_CsMarketplace_Model_System_Config_Source_Paymentmethods extends Ced_CsMarketplace_Model_System_Config_Source_Abstract
{

	const XML_PATH_CED_CSMARKETPLACE_VENDOR_PAYMENT_METHODS = 'global/ced_csmarketplace/vendor/payment_methods';
    /**
     * Retrieve Option values array
     *
     * @return array
     */
    public function toOptionArray()
    {
		$payment_methods = Mage::app()->getConfig()->getNode(self::XML_PATH_CED_CSMARKETPLACE_VENDOR_PAYMENT_METHODS);
        $payment_methods = array_keys((array)$payment_methods);
		$options = array();
		foreach($payment_methods as $payment_method) {
			$payment_method = strtolower(trim($payment_method));
			$options[] = array('value'=>$payment_method,'label'=>Mage::helper('csmarketplace')->__(ucfirst($payment_method)));
		}
		return $options;
    }

}