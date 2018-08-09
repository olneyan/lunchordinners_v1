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
 * @package     Ced_CsMarketplace 
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com> 
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
 * Vendor Payment Requested model
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Model_Vpayment_Requested extends Ced_CsMarketplace_Model_Abstract
{
	const PAYMENT_STATUS_REQUESTED 	  = 1;
	const PAYMENT_STATUS_PROCESSED 	  = 2;
	const PAYMENT_STATUS_CANCELED = 3;
	
	public static $_statuses = null;
	
	protected $_eventPrefix      = 'csmarketplace_vpayments_requested';
	protected $_eventObject      = 'vpayment_requested';
	
	/**
     * Initialize resource model
     */
	protected function _construct()
	{
		$this->_init('csmarketplace/vpayment_requested');
	}
	
	/**
     * Retrieve vendor payment status array
     *
     * @return array
     */
    public static function getStatuses()
    {
        if (is_null(self::$_statuses)) {
            self::$_statuses = array(
                self::PAYMENT_STATUS_REQUESTED       => Mage::helper('sales')->__('Requested'),
                self::PAYMENT_STATUS_PROCESSED       => Mage::helper('sales')->__('Processed'),
                self::PAYMENT_STATUS_CANCELED   => Mage::helper('sales')->__('Canceled'),
            );
        }
        return self::$_statuses;
    }
	
}

?>