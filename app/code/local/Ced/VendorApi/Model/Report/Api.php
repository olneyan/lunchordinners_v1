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

class Ced_VendorApi_Model_Report_Api extends Mage_Api_Model_Resource_Abstract
{	
	
	public function model()
	{
		return Mage::getResourceModel('vendorapi/vapisession');
	}
	
	public function getProductReport($vendorId,$from, $to,$page)
	{
		if($vendorId==null || $to==null || $from==null){
			$response = array (
						'data' => array (
							'message' => 'Vendor Id Is Empty',
							'success' => false, 
						) 
			);
			return $response;
		}
		$limit='10';
		$offset=$page;
		$curr_page = '1';
		if ($offset != 'null' && $offset != '') {
			
			$curr_page = $offset;
		}
		$offset = ($curr_page - 1) * $limit;	
		$reportHelper = Mage::helper('vendorapi');
		$productsCollection=$reportHelper->getVproductsReportModel($vendorId,$from,$to);
		$reportCount = $productsCollection->getSize(); 
		$productsCollection->getSelect ()->limit ($limit,$offset);
		//var_dump($productsCollection);die;
		if(count($productsCollection)>0){
			$productReport=array();
			foreach ($productsCollection as $_report){
				//var_dump($_report);die;
				$productReport[]=array(
					'product_name'=> $_report['order_item_name'],
					'sku'=> $_report['sku'],
					'sales_items'=> round($_report['ordered_qty']),
					'total_sales'=> Mage::app()->getLocale()->currency(Mage::app()->getBaseCurrencyCode())->toCurrency($_report['order_item_total_sales'])
				);
			}
			$response = array (
				'data' => array (
				'product_report'=>$productReport,
				'count'=> $reportCount,
				'success' => true, 
				) 
			);
			return $response;
		}
		else{
			$data = 'NO_ORDER';
			return $data;
		}
	}
	
	public function getOrderReport($vendorId,$period, $from, $to, $payment_state,$page)
	{
		if($vendorId==null || $to==null || $from==null){
			$response = array (
						'data' => array (
							'message' => 'Vendor Id Is Empty',
							'success' => false, 
						) 
			);
			return $response;
		}
		$limit='10';
		$offset=$page;
		$curr_page = '1';
		if ($offset != 'null' && $offset != '') {
			
			$curr_page = $offset;
		}
		$offset = ($curr_page - 1) * $limit;
		$paymentarray=Mage::getModel('csmarketplace/vorders')->getStates();
		$paymentarray['*']='All';
		$paymentarray=array_flip($paymentarray);
		$vendor = Mage::getModel('csmarketplace/vendor')->load($vendorId);
		$reportHelper = Mage::helper('vendorapi');
		$ordersCollection=$reportHelper->getVordersReportModel($vendor,strtolower($period), $from, $to,$paymentarray[$payment_state]);
		$reportCount = count($ordersCollection);
		$ordersCollection->getSelect ()->limit ($limit,$offset);
		
		$order=$ordersCollection->getData();
		if(count($order)>0){
			
			$orderreport=array();
			foreach ($order as $_report){
				$orderreport[]=array(
					'period'=>$_report['period'],
					'orders'=>$_report['order_count'],
					'sales_items'=>$_report['product_qty'],
					'total_sales'=>Mage::app()->getLocale()->currency(Mage::app()->getBaseCurrencyCode())->toCurrency($_report['order_total']),
					'total_commision'=>Mage::app()->getLocale()->currency(Mage::app()->getBaseCurrencyCode())->toCurrency($_report['commission_fee']),
					'net_sales'=>Mage::app()->getLocale()->currency(Mage::app()->getBaseCurrencyCode())->toCurrency($_report['net_earned'])
				);

			}
			$response = array (
				'data' => array (
				'order_report'=>$orderreport,
				'count'=> $reportCount,
				'success' => true, 
				) 
			);
			return $response;
		}
		else{
			$data = 'NO_ORDER';
			return $data;
		}
	}
}
