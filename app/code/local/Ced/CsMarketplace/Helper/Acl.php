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
 
class Ced_CsMarketplace_Helper_Acl extends Mage_Core_Helper_Abstract
{
    protected $_defaultAclValues = null;
    protected $_storeId = 0;
	protected $_order = null;
	protected $_vendorId = null;
	
	const XML_PATH_CED_CSMARKETPLACE_CONFIG = 'global/ced_csmarketplace/vendor/config';
	
	public static $PAYMENT_MODES = array('Offline', 'Online');
	
	
	/**
	 *  Set Store Id
	 *  @param integer
	 *  @return Ced_CsMarketplace_Helper_Acl
	 */
	public function setStoreId($storeId = 0) {
		$this->_storeId = $storeId;
		return $this;
	}

	/**
	 *  Set Order
	 *  @param Ced_CsMarketplace_Model_Vorders
	 *  @return Ced_CsMarketplace_Helper_Acl
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
		if($this->_order == null && $order != null)
			$this->_order = $order;
		return $this->_order;
	}
	
	/**
	 *  Set Order
	 *  @param Ced_CsMarketplace_Model_Vorders
	 *  @return Ced_CsMarketplace_Helper_Acl
	 */
	public function setVendorId($vendorId = 0) {
		$this->_vendorId = $vendorId;
		return $this;
	}
	
	/**
	 *  Get Order
	 *  @return Ced_CsMarketplace_Model_Vorders
	 */
	public function getVendorId($vendorId = null) {
		if($this->_vendorId == null && $vendorId != null)
			$this->_vendorId = $vendorId;
		return $this->_vendorId;
	}
	
	/**
	 * Check the system availability
	 *
	 * @return boolean true|false
	 */
	public function isEnabled() {
		return Mage::getStoreConfig('ced_csmarketplace/general/activation',$this->getStore()->getId());
	}
	
	/**
	 * Get current store
	 * @return Mage_Core_Model_Store
	 */
	 public function getStore() {
	 	if ($this->_storeId) $storeId = (int)$this->_storeId;
	 	else $storeId = (int) Mage::app()->getRequest()->getParam('store', 0);
        if($storeId)
			return Mage::app()->getStore($storeId);
		else 
			return Mage::app()->getStore();
	 }
	
	 /**
	  * Get the default group setting
	  *
	  * @return String 
	  */
	 public function getDefaultGroup() {
		return Mage::getStoreConfig('ced_csmarketplace/vendor/group',$this->getStore()->getId());
	 }
	 
	 /**
	  * Get the default payment type
	  *
	  * @return String 
	  */
	 public function getDefaultPaymentType() {
		return Mage::getStoreConfig('vendor_vpayments/general/online',$this->getStore()->getId());
	 }
	 
	  /**
	  * Get the default payment type
	  *
	  * @return String 
	  */
	 public function getDefaultPaymentTypeLabel($mode = null) {
		if($mode == null || $mode == '') $mode = $this->getDefaultPaymentType();
		return isset(self::$PAYMENT_MODES[$mode])?Mage::helper('csmarketplace')->__(self::$PAYMENT_MODES[$mode]):Mage::helper('csmarketplace')->__('Offline');
	 }
	 
	 /**
	  * Get the default payment type
	  *
	  * @return String 
	  */
	 public function getDefaultPaymentTypeValue($name = null) {
		if($name == null || $name == '') $name = Mage::helper('csmarketplace')->__('Offline');
		$values = array();
		foreach (self::$PAYMENT_MODES as $mid=>$mname) {
			$mname = Mage::helper('csmarketplace')->__($mname);
			if (preg_match('/'.$name.'/i',$mname)) {
				$values[]= $mid;
			}
		}
		return $values;
	 }
	 
	 /**
	  * Get the default commission mode
	  *
	  * @return String 
	  */
	 public function getDefaultCommissionMode() {
		return Mage::getStoreConfig('ced_vpayments/general/commission_mode',$this->getStore()->getId());
	 }
	 
	 /**
	  * Get the default commission fee
	  *
	  * @return String 
	  */
	 public function getDefaultCommissionFee() {
		return Mage::getStoreConfig('ced_vpayments/general/commission_fee',$this->getStore()->getId());
	 }
	  
	/**
	 * Default ACL
	 * Default ACL can be override by Group ACLs
	 * @return array
	 */
    public function getDefultAclValues()
    {
		$storeId = $this->getStore()->getId();
        if($this->_defaultAclValues == null) {
			if ($this->getIsApprovalRequired($storeId)) {
				$this->_defaultAclValues ['status'] = Ced_CsMarketplace_Model_Vendor::VENDOR_NEW_STATUS; 
			} else {
				$this->_defaultAclValues ['status'] = Ced_CsMarketplace_Model_Vendor::VENDOR_APPROVED_STATUS;
			}
			$this->_defaultAclValues['group'] = $this->getDefaultGroup();
											
		}
		return $this->_defaultAclValues;
    }

	/**
	 * Admin approval required or not (Default ACL)
	 * @param int $storeId
	 * @return boolean true|false
	 */
	public function getIsApprovalRequired($storeId = 0) {
		if (!$storeId)
			$storeId = $this->getStore()->getId();
			
		return Mage::getStoreConfig('ced_csmarketplace/general/confirmation',$storeId);
	}
	
	/**
	 * Get the commission Setting based on group
	 *
	 * @param int $vendor_id
	 * @return array
	 */
	 public function getCommissionSettings($vendor_id = 0) {
		$vendor = Mage::getModel('csmarketplace/vendor')->load($vendor_id);
		$groupCode = $this->getDefaultGroup();
		if ($vendor && $vendor->getId()) {
			if(Mage::registry('current_order_vendor')) 
				Mage::unRegister('current_order_vendor');
			Mage::register('current_order_vendor',$vendor);
			$groupCode = $vendor->getGroup();
			if ($groupCode) {
				$groups = Ced_CsMarketplace_Model_System_Config_Source_Group::getGroups();
				if(isset($groups[$groupCode]['model'])) {
					$group = $groups[$groupCode]['model'];
				} else {
					$group = 'csmarketplace/vendor_group_'.strtolower($groupCode);
				}
				
				try {
					$group = Mage::getModel($group);
				} catch (Exception $e) {}
				/* $classFile = str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('_', ' ', str_replace('/', ' ', $group))));
				if(file_exists($classFile)) {
					echo $classFile;die;
				} else {
					echo "else";die;
				} */
				
				if (is_object($group) && $settings = $group->getCommissionSettings($vendor)) {
					return $settings; 
				}
			}
		}
		else {
			   if(Mage::registry('current_order_vendor')) 
				Mage::unRegister('current_order_vendor');
		  }
		return array('type'=>$this->getDefaultCommissionMode(),'rate'=>$this->getDefaultCommissionFee(), 'group' => $groupCode);
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
	 public function calculateCommission($grand_total = 0, $base_grand_total = 0,$base_to_global_rate = 1, $commissionSetting = array()) {
		try {
			$order = $this->getOrder();
			$vendorId = $this->getVendorId();
			/* Set default commission settings */
			if (!isset($commissionSetting['type'])) $commissionSetting['type'] = $this->getDefaultCommissionMode();
			if (!isset($commissionSetting['rate'])) $commissionSetting['rate'] = $this->getDefaultCommissionFee();
			if (!isset($commissionSetting['group'])) $commissionSetting['group'] = $this->getDefaultGroup();
			
			/* print_r($commissionSetting);die; */
			if ($grand_total > 0) {
				if($base_grand_total <= 0) $base_grand_total = $grand_total;
				$rates = Ced_CsMarketplace_Model_System_Config_Source_Rate::getRates();

				if(isset($rates[$commissionSetting['type']]['model'])) {
					$rate = Mage::getModel($rates[$commissionSetting['type']]['model']);
				} else {
					$rate = Mage::getModel('csmarketplace/vendor_rate_'.strtolower($commissionSetting['type']));
				}

				/* echo $rate->calculateCommission($grand_total,$base_grand_total,$base_to_global_rate, $commissionSetting); die('dsffd'); */
				if (is_object($rate) && $commission = $rate->setOrder($order)->setVendorId($vendorId)->calculateCommission($grand_total,$base_grand_total,$base_to_global_rate, $commissionSetting)) {
					return $commission; 
				}
			}
		} catch (Exception $e) {
			/* echo $e->getMessage();die; */
			Mage::log($e->getMessage(),null,'csmarketplace_commission_calculation.log');
		}
		return array('base_fee'=>0.00,'fee'=>0.00,'item_commission' => '');
	 }
	
}
