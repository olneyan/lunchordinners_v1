<?php
/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category    Ced
  * @package     VendorApi
  * @author      CedCommerce Core Team <connect@cedcommerce.com>
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://cedcommerce.com/license-agreement.txt
  */

class Ced_VendorApi_Model_Vendordashboard extends Mage_Core_Model_Abstract {
	
	public function getInfo($vendorId,$range) {
		$dashboard=array();
		$dashboard['piechart']=$this->getVendorProductsData($vendorId);
		$dashboard['tiles']=$this->getOrdersPlaced($vendorId);
		$dashboard['map']=$this->getMap($vendorId);
		$dashboard['chart']=$this->getChart($vendorId,$range);
		$dashboard['latest_order']=$this->getLatestOrders($vendorId);
		$data = array (
					'data' => array (
											'success' => true ,
											'dashboard'=> $dashboard
									) 
							
					
			);
		return $data;
	}
	/**
	 * Get Associated Orders
	 */
	public function getAssociatedOrders($vendorId) {
		return  Mage::getSingleton('csmarketplace/vendor')->getAssociatedOrders($vendorId);
	}
	 
	/**
	 * Get Associated Payments
	 */
	public function getAssociatedPayments($vendorId) {
		if(isset($vendorId)){
			$vendor=Mage::getModel('csmarketplace/vendor')->load($vendorId);
			$this->_associatedPayments = $vendor->getAssociatedPayments($vendorId);
			return $this->_associatedPayments;
		}
	}
	
	/**
     * Get vendor's pending amount data
     *
     * @return Array
     */
	 public function getPendingAmount($vendorId) {
		// Total Pending Amount
		$pendingAmount = 0;
		
		if ($vendorId) {
			$vendor=Mage::getModel('csmarketplace/vendor')->load($vendorId);
			$ordersCollection = Mage::helper('csmarketplace/payment')->_getTransactionsStats($vendor);
			foreach($ordersCollection as $order) {
				if($order->getData('payment_state') == Ced_CsMarketplace_Model_Vorders::STATE_OPEN) {
					$pendingAmount = $order->getData('net_amount');
					break;
				}
			}

			if ($pendingAmount > 1000000000000) {
				$pendingAmount = round($pendingAmount / 1000000000000, 4);
				$pendingAmount = Mage::app()->getLocale()
                                        ->currency(Mage::app()->getBaseCurrencyCode())
									    ->toCurrency($pendingAmount) . 'T';
			} elseif ($pendingAmount > 1000000000) {
				$pendingAmount = round($pendingAmount / 1000000000, 4);
				$pendingAmount = Mage::app()->getLocale()
                                        ->currency(Mage::app()->getBaseCurrencyCode())
									    ->toCurrency($pendingAmount) . 'B';
			} elseif ($pendingAmount > 1000000) {
				$pendingAmount = round($pendingAmount / 1000000, 4);
				$pendingAmount = Mage::app()->getLocale()
                                        ->currency(Mage::app()->getBaseCurrencyCode())
									    ->toCurrency($pendingAmount) . 'M';
			} elseif ($pendingAmount > 1000) {
				$pendingAmount = round($pendingAmount / 1000, 4);	
				$pendingAmount = Mage::app()->getLocale()
														->currency(Mage::app()->getBaseCurrencyCode())
														->toCurrency($pendingAmount) . 'K';				
			} else {

				$pendingAmount = Mage::app()->getLocale()
                                        ->currency(Mage::app()->getBaseCurrencyCode())
									    ->toCurrency($pendingAmount);
			}

			return $pendingAmount;
		}
		
	}
	
	/**
     * Get vendor's Earned Amount data
     *
     * @return Array
     */
	 public function getEarnedAmount($vendorId) {
		// Total Earned Amount
		$netAmount=0;
		if ($vendorId) {
			$netAmount = $this->getAssociatedPayments($vendorId)->getFirstItem()->getBaseBalance();

			if ($netAmount > 1000000000000) {
				$netAmount = round($netAmount / 1000000000000, 4);
				$netAmount = Mage::app()->getLocale()
                                        ->currency(Mage::app()->getBaseCurrencyCode())
									    ->toCurrency($netAmount) . 'T';
			} elseif ($netAmount > 1000000000) {
				$netAmount = round($netAmount / 1000000000, 4);
				$netAmount = Mage::app()->getLocale()
                                        ->currency(Mage::app()->getBaseCurrencyCode())
									    ->toCurrency($netAmount) . 'B';
			} elseif ($netAmount > 1000000) {
				$netAmount = round($netAmount / 1000000, 4);
				$netAmount = Mage::app()->getLocale()
                                        ->currency(Mage::app()->getBaseCurrencyCode())
									    ->toCurrency($netAmount) . 'M';
			} elseif ($netAmount > 1000) {
				$netAmount = round($netAmount / 1000, 4);	
				$netAmount = Mage::app()->getLocale()
														->currency(Mage::app()->getBaseCurrencyCode())
														->toCurrency($netAmount) . 'K';				
			} else {
				$netAmount = Mage::app()->getLocale()
                                        ->currency(Mage::app()->getBaseCurrencyCode())
									    ->toCurrency($netAmount);
			}
		}
		return $netAmount;
	}
	
	/**
     * Get vendor's Orders Placed data
     *
     * @return Array
     */
	 public function getOrdersPlaced($vendorId) {
		// Total Orders Placed
		$data = array();
		if ($vendorId) {
			$ordersCollection = $this->getAssociatedOrders($vendorId);
			$order_total = count($ordersCollection);

			if ($order_total > 1000000000000) {
				$data['order_placed'] = round($order_total / 1000000000000, 1) . 'T';
			} elseif ($order_total > 1000000000) {
				$data['order_placed'] = round($order_total / 1000000000, 1) . 'B';
			} elseif ($order_total > 1000000) {
				$data['order_placed'] = round($order_total / 1000000, 1) . 'M';
			} elseif ($order_total > 1000) {
				$data['order_placed'] = round($order_total / 1000, 1) . 'K';						
			} else {
				$data['order_placed'] = $order_total;
			}
			$data['sold']=$this->getProductsSold($vendorId);
			$pendingAmount=$this->getPendingAmount($vendorId);

			$data['pending_amount']=$pendingAmount;
			$data['net_earned']=$this->getEarnedAmount($vendorId);
		}
		return $data;
	}
	
	/**
     * Get vendor's Products Sold data
     *
     * @return Array
     */
	 public function getProductsSold($vendorId) {
		$product_sold = 0;
		if ($vendorId) {
			$productsSold = Mage::helper('csmarketplace/report')->getVproductsReportModel($vendorId,'','',false)->getFirstItem()->getData('ordered_qty');
			if ($productsSold > 1000000000000) {
				$product_sold = round($productsSold / 1000000000000, 1) . 'T';
			} elseif ($productsSold > 1000000000) {
				$product_sold = round($productsSold / 1000000000, 1) . 'B';
			} elseif ($productsSold > 1000000) {
				$product_sold = round($productsSold / 1000000, 1) . 'M';
			} elseif ($productsSold > 1000) {
				$product_sold = round($productsSold / 1000, 1) . 'K';						
			} else {
				$product_sold = round($productsSold);
			}
		}
		return $product_sold;
	}
	
	/**
     * Get vendor's Products data
     *
     * @return Array
     */
	 public function getVendorProductsData($vendorId) {
		$data = array();
		if ($vendorId) {
			
			$pendingProducts  	 = count(Mage::getModel('csmarketplace/vproducts')->getVendorProducts(Ced_CsMarketplace_Model_Vproducts::PENDING_STATUS,$vendorId,0,-1));
			$approvedProducts 	 = count(Mage::getModel('csmarketplace/vproducts')->getVendorProducts(Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS,$vendorId,0,-1));
			$disapprovedProducts = count(Mage::getModel('csmarketplace/vproducts')->getVendorProducts(Ced_CsMarketplace_Model_Vproducts::NOT_APPROVED_STATUS,$vendorId,0,-1));

			if ($pendingProducts > 1000000000000) {
				$data['pending'] = round($pendingProducts / 1000000000000, 1) . 'T';
			} elseif ($pendingProducts > 1000000000) {
				$data['pending'] = round($pendingProducts / 1000000000, 1) . 'B';
			} elseif ($pendingProducts > 1000000) {
				$data['pending'] = round($pendingProducts / 1000000, 1) . 'M';
			} elseif ($pendingProducts > 1000) {
				$data['pending'] = round($pendingProducts / 1000, 1) . 'K';						
			} else {
				$data['pending'] = round($pendingProducts);
			}

			
			if ($approvedProducts > 1000000000000) {
				$data['approved'] = round($approvedProducts / 1000000000000, 1) . 'T';
			} elseif ($approvedProducts > 1000000000) {
				$data['approved'] = round($approvedProducts / 1000000000, 1) . 'B';
			} elseif ($approvedProducts > 1000000) {
				$data['approved'] = round($approvedProducts / 1000000, 1) . 'M';
			} elseif ($approvedProducts > 1000) {
				$data['approved'] = round($approvedProducts / 1000, 1) . 'K';						
			} else {
				$data['approved'] = round($approvedProducts);
			}
			if ($disapprovedProducts > 1000000000000) {
				$data['disapproved'] = round($disapprovedProducts / 1000000000000, 1) . 'T';
			} elseif ($disapprovedProducts > 1000000000) {
				$data['disapproved'] = round($disapprovedProducts / 1000000000, 1) . 'B';
			} elseif ($disapprovedProducts > 1000000) {
				$data['disapproved'] = round($disapprovedProducts / 1000000, 1) . 'M';
			} elseif ($disapprovedProducts > 1000) {
				$data['disapproved'] = round($disapprovedProducts / 1000, 1) . 'K';						
			} else {
				$data['disapproved'] = round($disapprovedProducts);
			}
			
		}
		return $data;
	}
	public function getChart($vendorId=0,$range){
	
		if(!$vendorId) return array();
		
		$chart = array();
		$chart = array();

		$chart['xaxis'] = array();
		$chart['data'] = array();
		$charts = array();
	
	
		$reportHelper = Mage::helper('csmarketplace/report');
		$vendor=Mage::getModel('csmarketplace/vendor')->load($vendorId);	
		if($vendor && $vendor->getId()) {
			if(!is_array($range)){
			
			$order = $reportHelper->getChartData($vendor,'order',$range);
		
			foreach ($order as $key => $value) {
				$chart['data'][] = array($key, (int)$value['total']);
			}
			switch ($range) {
				default:
				case 'day':

					for ($i = 0; $i < 24; $i++) {
						$chart['xaxis'][] = array($i, $i);
					}
					break;
				case 'week':
					$date_start = strtotime('-' . date('w') . ' days');

					for ($i = 0; $i < 7; $i++) {
						$date = date('Y-m-d', $date_start + ($i * 86400));

						$chart['xaxis'][] = array(date('w', strtotime($date)), date('D', strtotime($date)));
					}
					break;
				case 'month':

					for ($i = 1; $i <= date('t'); $i++) {
						$date = date('Y') . '-' . date('m') . '-' . $i;

						$chart['xaxis'][] = array(date('j', strtotime($date)), date('d', strtotime($date)));
					}
					break;
				case 'year':

					for ($i = 1; $i <= 12; $i++) {
						$chart['xaxis'][] = array($i, date('M', mktime(0, 0, 0, $i)));
					}
					break;
			}
			return $chart;	
			}else{

			foreach ($range as $key => $crange) {
			$order = $reportHelper->getChartData($vendor,'order',$crange);
		
			foreach ($order as $key => $value) {
				$chart['data'][] = array($key, (int)$value['total']);
			}
			switch ($crange) {
				default:
				case 'day':

					for ($i = 0; $i < 24; $i++) {
						$chart['xaxis'][] = array($i, $i);
					}
					break;
				case 'week':
					$date_start = strtotime('-' . date('w') . ' days');

					for ($i = 0; $i < 7; $i++) {
						$date = date('Y-m-d', $date_start + ($i * 86400));

						$chart['xaxis'][] = array(date('w', strtotime($date)), date('D', strtotime($date)));
					}
					break;
				case 'month':

					for ($i = 1; $i <= date('t'); $i++) {
						$date = date('Y') . '-' . date('m') . '-' . $i;

						$chart['xaxis'][] = array(date('j', strtotime($date)), date('d', strtotime($date)));
					}
					break;
				case 'year':

					for ($i = 1; $i <= 12; $i++) {
						$chart['xaxis'][] = array($i, date('M', mktime(0, 0, 0, $i)));
					}
					break;
				}
				$charts[$crange]=$chart;
				unset($chart);
			}
			 

		}
		return $charts;	
		}
		
	}
	
	public function getMap($vendorId) {
		$map = array();
		if(!$vendorId) return $map;
		
		
		$reportHelper = Mage::helper('csmarketplace/report');
		$vendor=Mage::getModel('csmarketplace/vendor')->load($vendorId);	
		
		if ($vendor && $vendor->getId()) {
			$results = $reportHelper->getTotalOrdersByCountry($vendor);
			
			foreach ($results as $country => $result) {
				$map[] = array(
					'total'  => (string)$result['total'],
					'country_code'=> strtolower($country),
					'amount' => (string)Mage::app()->getLocale()
                                        ->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
									    ->toCurrency($result['amount']),
				);
			}
		}
		return $map;
	}
	public function getLatestOrders($vendorId){
		$ordersCollection = $this->getAssociatedOrders($vendorId)->setOrder('created_at','DESC')->setPageSize(5);
		$main_table=Mage::helper('csmarketplace')->getTableKey('main_table');
		$order_total=Mage::helper('csmarketplace')->getTableKey('order_total');
		$shop_commission_fee=Mage::helper('csmarketplace')->getTableKey('shop_commission_fee');
		$ordersCollection->getSelect()->columns(array('net_vendor_earn' => new Zend_Db_Expr("({$main_table}.{$order_total} - {$main_table}.{$shop_commission_fee})")));
		$orders=$ordersCollection->getData();
		$latestOrders=array();
		foreach ($orders as $key => $order) {
			$latestOrders[$key]['order_id']=$order['order_id'];
			$latestOrders[$key]['purchase_on']=Mage::helper('core')->formatDate( $order['created_at'], 'medium', true);
			$latestOrders[$key]['billing_name']=$order['billing_name'];
			$latestOrders[$key]['net_earned']=Mage::helper('core')->currency($order['net_vendor_earn'],true,false);
			$latestOrders[$key]['order_status']=$order['status'];
			$latestOrders[$key]['grand_total']=Mage::helper('core')->currency($order['grand_total'],true,false);
		}
		return $latestOrders;
	}
	public function getCountry($country_code='') {
		$countryList = Mage::getModel('directory/country')->getResourceCollection()
                                                  ->loadByStore()
                                                  ->toOptionArray(true);
        $regiondatacountrywise=array();
        if($country_code!=''){
        	$collection = Mage::getModel('directory/region')->getCollection()->addFieldToFilter('country_id',trim($country_code))->getData();
        	$regiondatacountrywise['states']=$collection;
        	if(count($collection))
        	$regiondatacountrywise['success']=true;
        	else
        	$regiondatacountrywise['success']=false;	
        	return $regiondatacountrywise;  
        }else{
			$countryList=array_filter($countryList);
			$regiondatacountrywise['country']=$countryList;	
			 return $regiondatacountrywise;     	
        }
                                 
	}	
}
?>
