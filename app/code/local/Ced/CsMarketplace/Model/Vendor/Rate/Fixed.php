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
 * Vendor Fixed rate model
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Model_Vendor_Rate_Fixed extends Ced_CsMarketplace_Model_Vendor_Rate_Abstract
{
	/**
	 * Get the commission based on group
	 *
	 * @param float $grand_total
	 * @param float $base_grand_total
	 * @param string $currency
	 * @param array $commissionSetting
	 * @return array
	 */
	public function calculateCommission($grand_total = 0, $base_grand_total = 0, $base_to_global_rate = 1, $commissionSetting = array()) {
		$result = array();
		
		$order = $this->getOrder();
		
		$result['base_fee'] = min($base_grand_total, $commissionSetting['rate']);
        $result['fee'] = min($grand_total, Mage::helper('directory')->currencyConvert($commissionSetting['rate'], $order->getBaseCurrencyCode(), $order->getGlobalCurrencyCode()));
		
		
		$itemCommission = isset($commissionSetting['item_commission']) ? $commissionSetting['item_commission'] : array();
		if(count($itemCommission) > 0) {
			unset($commissionSetting['item_commission']);
			$item_commission = array();
			foreach($itemCommission as $itemId=>$base_price) {
				$price = Mage::helper('directory')->currencyConvert($base_price, $order->getBaseCurrencyCode(), $order->getGlobalCurrencyCode());
				$item_commission[$itemId] = $this->calculateCommission($price, $base_price, $base_to_global_rate, $commissionSetting);
			}
			$result['item_commission'] = json_encode($item_commission);
		}
		
		
		return $result;
	}
}
