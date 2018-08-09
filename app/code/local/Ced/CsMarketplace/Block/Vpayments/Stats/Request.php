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
 * CsMarketplace Payment Stats View block
 *
 * @category   Ced
 * @package    Ced_CsMarketplace
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Block_Vpayments_Stats_Request extends Ced_CsMarketplace_Block_Vendor_Abstract
{

	/**
	 * Get set collection of Products report
	 *
	 */
	public function __construct(){
		parent::__construct();
		$this->setPendingAmount(0.00);
		$this->setPendingTransfers(0);
		$this->setPaidAmount(0.00);
		$this->setCanceledAmount(0.00);
		$this->setRefundableAmount(0.00);
		$this->setRefundedAmount(0.00);
		$this->setEarningAmount(0.00);
		
		if($this->getVendor() && $this->getVendor()->getId()) {
			$productsCollection = array();
			$paymentHelper = Mage::helper('csmarketplace/payment');
			$collection=$paymentHelper->_getTransactionsStats($this->getVendor());

			if(count($collection)>0) {
				foreach ($collection as $stats){
					switch($stats->getPaymentState()) {
						case Ced_CsMarketplace_Model_Vorders::STATE_OPEN : $this->setPendingAmount($stats->getNetAmount());
																			   $this->setPendingTransfers($stats->getCount()?$stats->getCount():0);
																			   break;
						case Ced_CsMarketplace_Model_Vorders::STATE_PAID : $this->setPaidAmount($stats->getNetAmount());
																			   break;
						case Ced_CsMarketplace_Model_Vorders::STATE_CANCELED : $this->setCanceledAmount($stats->getNetAmount());
																			   break;
						case Ced_CsMarketplace_Model_Vorders::STATE_REFUND : $this->setRefundableAmount($stats->getNetAmount());
																			   break;
						case Ced_CsMarketplace_Model_Vorders::STATE_REFUNDED : $this->setRefundedAmount($stats->getNetAmount());
																			   break;
					}
				}
			}
				$amounts = 0.0000;
				$main_table=Mage::helper('csmarketplace')->getTableKey('main_table');
				$amount_column=Mage::helper('csmarketplace')->getTableKey('amount');
				$amounts = Mage::getModel('csmarketplace/vpayment_requested')
								->getCollection()
								->addFieldToFilter('vendor_id',array('eq'=>$this->getVendorId()))
								->addFieldToFilter('status',array('eq'=>Ced_CsMarketplace_Model_Vpayment_Requested::PAYMENT_STATUS_REQUESTED));
				$amounts->getSelect()->columns("SUM({$main_table}.{$amount_column}) AS amounts");
				if(count($amounts) > 0) {
					$amounts = $amounts->getFirstItem()->getData("amounts");
				}			

			$this->setEarningAmount($this->getVendor()->getAssociatedPayments()->getFirstItem()->getBalance());
			$this->setRequestedAmount($amounts);
		}		
	}
	
	
}
