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
 * Vendor Payment model
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Model_Vpayment extends Mage_Core_Model_Abstract
{
	const TRANSACTION_TYPE_CREDIT = 0;
	const TRANSACTION_TYPE_DEBIT = 1;
	
	const PAYMENT_STATUS_OPEN 	  = 1;
	const PAYMENT_STATUS_PAID 	  = 2;
	const PAYMENT_STATUS_CANCELED = 3;
	const PAYMENT_STATUS_REFUND   = 4;
	const PAYMENT_STATUS_REFUNDED = 5;
	
	protected static $_states;
	protected static $_statuses;
	
	protected $_eventPrefix      = 'csmarketplace_vpayments';
	protected $_eventObject      = 'vpayment';
	
	/**
     * Initialize resource model
     */
	protected function _construct()
	{
		$this->_init('csmarketplace/vpayment');
	}
	
	/**
     * Retrieve vendor payment type array
     *
     * @return array
     */
    public static function getStates()
    {
        if (is_null(self::$_states)) {
            self::$_states = array(
                self::TRANSACTION_TYPE_CREDIT => Mage::helper('sales')->__('Credit'),
                self::TRANSACTION_TYPE_DEBIT  => Mage::helper('sales')->__('Debit'),
            );
        }
        return self::$_states;
    }
	
	/**
     * Retrieve vendor payment open status
     *
     * @return int $openStatus
     */
    public function getOpenStatus()
    {
		if (!Mage::getStoreConfig('ced_vpayments/general/payment_approval')) 
			return $this->getConfirmStatus();
		
		$openStatus = self::PAYMENT_STATUS_OPEN;
        if ($this->getData('transaction_type')) {
			switch ($this->getData('transaction_type')) {
				case self::TRANSACTION_TYPE_DEBIT : $openStatus = self::PAYMENT_STATUS_REFUND;
													break;
				case self::TRANSACTION_TYPE_CREDIT : 
				default : $openStatus = self::PAYMENT_STATUS_OPEN;
						  break;
			}
		}
		return $openStatus;
    }
	
	/**
     * Retrieve vendor payment confirm status
     *
     * @return int $confirmStatus
     */
    public function getConfirmStatus()
    {
		$confirmStatus = self::PAYMENT_STATUS_PAID;
        if ($this->getData('transaction_type')) {
			switch ($this->getData('transaction_type')) {
				case self::TRANSACTION_TYPE_DEBIT : $confirmStatus = self::PAYMENT_STATUS_REFUNDED;
													break;
				case self::TRANSACTION_TYPE_CREDIT : 
				default : $confirmStatus = self::PAYMENT_STATUS_PAID;
						  break;
			}
		}
		return $confirmStatus;
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
                self::PAYMENT_STATUS_OPEN       => Mage::helper('sales')->__('Pending'),
                self::PAYMENT_STATUS_PAID       => Mage::helper('sales')->__('Paid'),
                self::PAYMENT_STATUS_CANCELED   => Mage::helper('sales')->__('Canceled'),
				self::PAYMENT_STATUS_REFUND     => Mage::helper('csmarketplace')->__('Refund'),
				self::PAYMENT_STATUS_REFUNDED   => Mage::helper('csmarketplace')->__('Refunded'),
            );
        }
        return self::$_statuses;
    }
	
	 /**
     * Retrive product current balance by vendor Id
     *
     * @param   string $vendorId
     * @return  float
     */
	public function getCurrentBalance($vendorId){
		$collection = Mage::getModel('csmarketplace/vpayment')->getCollection()
						->addFieldToFilter('vendor_id',$vendorId)
						->setOrder('id', 'desc');
		if(count($collection)>0)
			return array($collection->getFirstItem()->getBalance(),$collection->getFirstItem()->getBaseBalance());
		else
			return array(0,0);	
	}
	
	public function saveOrders($data = array()) {
		if(count($data) > 0 && isset($data['amount_desc'])) {
			$state = $this->getStateByType($data);
			$amount_desc = json_decode($data['amount_desc'],true);
			if(is_array($amount_desc) && count($amount_desc) > 0 && isset($data['vendor_id']) ) {
				foreach($amount_desc as $orderId=>$amount) {
					$model = Mage::getModel('csmarketplace/vorders')->loadByField(array('vendor_id','order_id'),array($data['vendor_id'],trim($orderId)));

					if($model->getVendorId() == $data['vendor_id'] && $model->getOrderId() == trim($orderId)) {

						$requestedModel = Mage::getModel('csmarketplace/vpayment_requested')->loadByField(array('vendor_id','order_id'),array($data['vendor_id'],trim($model->getId())));
						if($requestedModel->getId()) {
							$requestedModel->setData('status',Ced_CsMarketplace_Model_Vpayment_Requested::PAYMENT_STATUS_PROCESSED);
							$requestedModel->save();
						}
						
						$model->setPaymentState($state)->save();
					}
				}
			}
		}
	}
	
	public function getStateByType($data = array()) {
		$type = isset($data['transaction_type'])?$data['transaction_type']:self::TRANSACTION_TYPE_CREDIT;
		switch ($type) {
			case self::TRANSACTION_TYPE_DEBIT : return Ced_CsMarketplace_Model_Vorders::STATE_REFUNDED; break;
			case self::TRANSACTION_TYPE_CREDIT :
			default : return Ced_CsMarketplace_Model_Vorders::STATE_PAID; break;
		}
	}
	
}

?>