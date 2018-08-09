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
 * Vendor rate abstract model
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Model_Vendor_Rate_Abstract extends Mage_Core_Model_Abstract
{
    protected $_order = null;
	protected $_vendorId = null;
	
	/**
	 * Get current store
	 * @return Mage_Core_Model_Store
	 */
	 public function getStore() {
		$storeId = (int) Mage::app()->getRequest()->getParam('store', 0);
        if($storeId)
			return Mage::app()->getStore($storeId);
		else 
			return Mage::app()->getStore();
	 }
	 
	 /**
	 * Get current store
	 * @return Mage_Core_Model_Store
	 */
	 public function getStoreId() {
		return $this->getStore()->getId();
	 }
	 
	 /**
	 *  Set Order
	 *  @param Ced_CsMarketplace_Model_Vorders
	 *  @return Ced_CsMarketplace_Model_Vendor_Rate_Abstract
	 */
	public function setOrder($order) {
		$this->_order = $order;
		return $this;
	}
	
	/**
	 *  Get Order
	 *  @return Ced_CsMarketplace_Model_Vorders
	 */
	public function getOrder($order = null) {
		if($this->_order == null)
			$this->_order = $order;
		return $this->_order;
	}
	
	/**
	 *  Set Order
	 *  @param Ced_CsMarketplace_Model_Vorders
	 *  @return Ced_CsMarketplace_Model_Vendor_Rate_Abstract
	 */
	public function setVendorId($vendorId) {
		$this->_vendorId = $vendorId;
		return $this;
	}
	
	/**
	 *  Get Order
	 *  @return Ced_CsMarketplace_Model_Vorders
	 */
	public function getVendorId($vendorId = 0) {
		if($this->_vendorId == null)
			$this->_vendorId = $vendorId;
		return $this->_vendorId;
	}
	
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
		return false;
	}
}
