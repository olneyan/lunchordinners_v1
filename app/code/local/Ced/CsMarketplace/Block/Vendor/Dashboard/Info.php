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
 * Dashboard CsMarketplace Info
 *
 * @category   Ced
 * @package    Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */

class Ced_CsMarketplace_Block_Vendor_Dashboard_Info extends Ced_CsMarketplace_Block_Vendor_Abstract
{
	protected $_associatedOrders = null;
	protected $_associatedPayments = null;

	public function __construct() {
		if ($this->getVendorId()) {
			$this->_associatedOrders = $this->getVendor()->getAssociatedOrders();
			$this->_associatedPayments = $this->getVendor()->getAssociatedPayments();
		}
		return parent::__construct();
	}
	
	/**
	 * Get Associated Orders
	 */
	public function getAssociatedOrders() {
		$this->_associatedOrders = $this->getVendor()->getAssociatedOrders();
		return $this->_associatedOrders;
	}
	 
	/**
	 * Get Associated Payments
	 */
	public function getAssociatedPayments() {
		$this->_associatedPayments = $this->getVendor()->getAssociatedPayments();
		return $this->_associatedPayments;
	}
	
	/**
     * Get vendor's pending amount data
     *
     * @return Array
     */
	 public function getPendingAmount() {
		// Total Pending Amount
		$pendingAmount = 0;
		$data = array('total'=> $pendingAmount , 'action' => '');
		if ($vendorId = $this->getVendorId()) {
			$ordersCollection = Mage::helper('csmarketplace/payment')->_getTransactionsStats($this->getVendor());
			foreach($ordersCollection as $order) {
				if($order->getData('payment_state') == Ced_CsMarketplace_Model_Vorders::STATE_OPEN) {
					$pendingAmount = $order->getData('net_amount');
					break;
				}
			}

			if ($pendingAmount > 1000000000000) {
				$pendingAmount = round($pendingAmount / 1000000000000, 4);
				$data['total'] = Mage::app()->getLocale()
                                        ->currency(Mage::app()->getBaseCurrencyCode())
									    ->toCurrency($pendingAmount) . 'T';
			} elseif ($pendingAmount > 1000000000) {
				$pendingAmount = round($pendingAmount / 1000000000, 4);
				$data['total'] = Mage::app()->getLocale()
                                        ->currency(Mage::app()->getBaseCurrencyCode())
									    ->toCurrency($pendingAmount) . 'B';
			} elseif ($pendingAmount > 1000000) {
				$pendingAmount = round($pendingAmount / 1000000, 4);
				$data['total'] = Mage::app()->getLocale()
                                        ->currency(Mage::app()->getBaseCurrencyCode())
									    ->toCurrency($pendingAmount) . 'M';
			} elseif ($pendingAmount > 1000) {
				$pendingAmount = round($pendingAmount / 1000, 4);	
				$data['total'] = Mage::app()->getLocale()
														->currency(Mage::app()->getBaseCurrencyCode())
														->toCurrency($pendingAmount) . 'K';				
			} else {
				$data['total'] = Mage::app()->getLocale()
                                        ->currency(Mage::app()->getBaseCurrencyCode())
									    ->toCurrency($pendingAmount);
			}
			
			 
					
			$data['action'] = $this->getUrl('*/vorders/',array('_secure'=>true, 'order_payment_state' => 2, 'payment_state'=>1));
		}
		return $data;
	}
	
	/**
     * Get vendor's Earned Amount data
     *
     * @return Array
     */
	 public function getEarnedAmount() {
		// Total Earned Amount
		$data = array('total'=> 0 , 'action' => '');
		if ($vendorId = $this->getVendorId()) {
			$netAmount = $this->getAssociatedPayments()->getFirstItem()->getBaseBalance();

			if ($netAmount > 1000000000000) {
				$netAmount = round($netAmount / 1000000000000, 4);
				$data['total'] = Mage::app()->getLocale()
                                        ->currency(Mage::app()->getBaseCurrencyCode())
									    ->toCurrency($netAmount) . 'T';
			} elseif ($netAmount > 1000000000) {
				$netAmount = round($netAmount / 1000000000, 4);
				$data['total'] = Mage::app()->getLocale()
                                        ->currency(Mage::app()->getBaseCurrencyCode())
									    ->toCurrency($netAmount) . 'B';
			} elseif ($netAmount > 1000000) {
				$netAmount = round($netAmount / 1000000, 4);
				$data['total'] = Mage::app()->getLocale()
                                        ->currency(Mage::app()->getBaseCurrencyCode())
									    ->toCurrency($netAmount) . 'M';
			} elseif ($netAmount > 1000) {
				$netAmount = round($netAmount / 1000, 4);	
				$data['total'] = Mage::app()->getLocale()
														->currency(Mage::app()->getBaseCurrencyCode())
														->toCurrency($netAmount) . 'K';				
			} else {
				$data['total'] = Mage::app()->getLocale()
                                        ->currency(Mage::app()->getBaseCurrencyCode())
									    ->toCurrency($netAmount);
			}
			
			$data['action'] = $this->getUrl('*/vpayments/',array('_secure'=>true));
		}
		return $data;
	}
	
	/**
     * Get vendor's Orders Placed data
     *
     * @return Array
     */
	 public function getOrdersPlaced() {
		// Total Orders Placed
		$data = array('total'=> 0 , 'action' => '');
		if ($vendorId = $this->getVendorId()) {
			$ordersCollection = $this->getAssociatedOrders();
			$order_total = count($ordersCollection);

			if ($order_total > 1000000000000) {
				$data['total'] = round($order_total / 1000000000000, 1) . 'T';
			} elseif ($order_total > 1000000000) {
				$data['total'] = round($order_total / 1000000000, 1) . 'B';
			} elseif ($order_total > 1000000) {
				$data['total'] = round($order_total / 1000000, 1) . 'M';
			} elseif ($order_total > 1000) {
				$data['total'] = round($order_total / 1000, 1) . 'K';						
			} else {
				$data['total'] = $order_total;
			}
					
			$data['action'] = $this->getUrl('*/vorders/',array('_secure'=>true));
		}
		return $data;
	}
	
	/**
     * Get vendor's Products Sold data
     *
     * @return Array
     */
	 public function getProductsSold() {
		// Total Products Sold
		$data = array('total'=> 0 , 'action' => '');
		if ($vendorId = $this->getVendorId()) {
			$productsSold = Mage::helper('csmarketplace/report')->getVproductsReportModel($this->getVendorId(),'','',false)->getFirstItem()->getData('ordered_qty');
			if ($productsSold > 1000000000000) {
				$data['total'] = round($productsSold / 1000000000000, 1) . 'T';
			} elseif ($productsSold > 1000000000) {
				$data['total'] = round($productsSold / 1000000000, 1) . 'B';
			} elseif ($productsSold > 1000000) {
				$data['total'] = round($productsSold / 1000000, 1) . 'M';
			} elseif ($productsSold > 1000) {
				$data['total'] = round($productsSold / 1000, 1) . 'K';						
			} else {
				$data['total'] = round($productsSold);
			}
					
			$data['action'] = $this->getUrl('*/vreports/vproducts',array('_secure'=>true));
		}
		return $data;
	}
	
	/**
     * Get vendor's Products data
     *
     * @return Array
     */
	 public function getVendorProductsData() {
		// Total Pending Products
		$data = array('total'=> array() , 'action' => '');
		if ($vendorId = $this->getVendorId()) {
			
			$pendingProducts  	 = count(Mage::getModel('csmarketplace/vproducts')->getVendorProducts(Ced_CsMarketplace_Model_Vproducts::PENDING_STATUS,$vendorId,0,-1));
			$approvedProducts 	 = count(Mage::getModel('csmarketplace/vproducts')->getVendorProducts(Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS,$vendorId,0,-1));
			$disapprovedProducts = count(Mage::getModel('csmarketplace/vproducts')->getVendorProducts(Ced_CsMarketplace_Model_Vproducts::NOT_APPROVED_STATUS,$vendorId,0,-1));

			if ($pendingProducts > 1000000000000) {
				$data['total'][] = round($pendingProducts / 1000000000000, 1) . 'T';
			} elseif ($pendingProducts > 1000000000) {
				$data['total'][] = round($pendingProducts / 1000000000, 1) . 'B';
			} elseif ($pendingProducts > 1000000) {
				$data['total'][] = round($pendingProducts / 1000000, 1) . 'M';
			} elseif ($pendingProducts > 1000) {
				$data['total'][] = round($pendingProducts / 1000, 1) . 'K';						
			} else {
				$data['total'][] = round($pendingProducts);
			}
			$data['action'][] = $this->getUrl('*/vproducts/',array('_secure'=>true, 'check_status' => 2));
			
			
			if ($approvedProducts > 1000000000000) {
				$data['total'][] = round($approvedProducts / 1000000000000, 1) . 'T';
			} elseif ($approvedProducts > 1000000000) {
				$data['total'][] = round($approvedProducts / 1000000000, 1) . 'B';
			} elseif ($approvedProducts > 1000000) {
				$data['total'][] = round($approvedProducts / 1000000, 1) . 'M';
			} elseif ($approvedProducts > 1000) {
				$data['total'][] = round($approvedProducts / 1000, 1) . 'K';						
			} else {
				$data['total'][] = round($approvedProducts);
			}
			$data['action'][] = $this->getUrl('*/vproducts/',array('_secure'=>true, 'check_status' => 1));
			
			if ($disapprovedProducts > 1000000000000) {
				$data['total'][] = round($disapprovedProducts / 1000000000000, 1) . 'T';
			} elseif ($disapprovedProducts > 1000000000) {
				$data['total'][] = round($disapprovedProducts / 1000000000, 1) . 'B';
			} elseif ($disapprovedProducts > 1000000) {
				$data['total'][] = round($disapprovedProducts / 1000000, 1) . 'M';
			} elseif ($disapprovedProducts > 1000) {
				$data['total'][] = round($disapprovedProducts / 1000, 1) . 'K';						
			} else {
				$data['total'][] = round($disapprovedProducts);
			}
					
			$data['action'][] = $this->getUrl('*/vproducts/',array('_secure'=>true, 'check_status' => 0));
			
		}
		return $data;
	}
}
