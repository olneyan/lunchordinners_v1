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
 
class Ced_CsMarketplace_Helper_Report extends Mage_Core_Helper_Abstract
{
	protected $_vendor = null;
	
	public function getTotalOrdersByCountry($vendor) {
		$result = array();
		if ($vendor && $vendor->getId()) {
			foreach($vendor->getAssociatedOrders() as $order) {				
				$countryId = strtolower($order->getShippingCountryCode());
				if(!strlen($countryId)) {
					$mainOrder = Mage::getModel('sales/order')->loadByIncrementId($order->getOrderId());
					if ($mainOrder && $mainOrder->getId()) {
						$countryId = strtolower($mainOrder->getBillingAddress()->getData('country_id'));	
					}
				}
				
				if(strlen($countryId)) {
					if (isset($result[$countryId]['total']))
						$result[$countryId]['total'] += 1;
					else 
						$result[$countryId]['total'] = 1;
						
					if (isset($result[$countryId]['amount']))
						$result[$countryId]['amount'] += (double) $order->getOrderTotal();
					else 
						$result[$countryId]['amount'] = (double) $order->getOrderTotal();
				}
			}
		}
		return $result;
	}
	
	public function getChartData($vendor, $type='order', $range = 'day') {
		$results = array();
		if($vendor && $vendor->getId()) {
			$this->_vendor = $vendor;
			switch ($range) {
				default:
				case 'day':
					for ($i = 0; $i < 24; $i++) {
						$results[$i] = array(
							'hour'  => $i,
							'total' => 0
						);
					}
					$model = $this->_getReportModel($type,$range);
					foreach ($model as $result) {
						$results[$result['hour']] = array(
							'hour'  => $result['hour'],
							'total' => $result['total']
						);
					}
					break;
					
				case 'week':
					$date_start = strtotime('-' . date('w') . ' days');

					for ($i = 0; $i < 7; $i++) {
						$date = date('Y-m-d', $date_start + ($i * 86400));

						$results[date('w', strtotime($date))] = array(
							'day'   => date('D', strtotime($date)),
							'total' => 0
						);
					}
					$model = $this->_getReportModel($type,$range);
					foreach ($model as $result) {
						$results[date('w', strtotime($result['created_at']))] = array(
							'day'   => date('D', strtotime($result['created_at'])),
							'total' => $result['total']
						);
					}
					break;
					
				case 'month':

					for ($i = 1; $i <= date('t'); $i++) {
						$date = date('Y') . '-' . date('m') . '-' . $i;

						$results[date('j', strtotime($date))] = array(
							'day'   => date('d', strtotime($date)),
							'total' => 0
						);
					}
					$model = $this->_getReportModel($type,$range);
					foreach ($model as $result) {
						//print_r($result);die;
						$results[date('j', strtotime($result['created_at']))] = array(
							'day'   => date('d', strtotime($result['created_at'])),
							'total' => $result['total']
						);
					}
					break;
				case 'year':

					for ($i = 1; $i <= 12; $i++) {
						$results[$i] = array(
							'month' => date('M', mktime(0, 0, 0, $i)),
							'total' => 0
						);
					}
					$model = $this->_getReportModel($type,$range);
					foreach ($model as $result) {
						$results[date('n', strtotime($result['created_at']))] = array(
							'month' => date('M', strtotime($result['created_at'])),
							'total' => $result['total']
						);
					}
					break;
			}
		}
		//print_r($results);die;
		return $results;
	}
	
	protected function _getReportModel($model = 'order', $range = 'day') {
		if ($this->_vendor != null && $this->_vendor && $this->_vendor->getId()) {
			$coreResource   = Mage::getSingleton('core/resource');
			$readConnection = $coreResource->getConnection('read');
			$model = $this->_vendor->getAssociatedOrders();
			switch($model) {
				default:
				case 'order' :  switch($range) {
									default:
									case 'day'  : 
												$model->getSelect()
														->reset(Zend_Db_Select::COLUMNS)
														->columns("COUNT(*) AS total, HOUR(created_at) AS hour")
														->where("DATE(created_at) = DATE(NOW())")
														->group("HOUR(created_at)")
														->order("created_at ASC");
												//echo $model->getSelect();die;
												break;
									case 'week' :
												$date_start = strtotime('-' . date('w') . ' days');
												$model->getSelect()
														->reset(Zend_Db_Select::COLUMNS)
														->columns("created_at, COUNT(*) AS total")
														->where("DATE(created_at) >= DATE('" . date('Y-m-d', $date_start) . "')")
														->group("DAYNAME(created_at)");
												//echo $model->getSelect();die;
												break;									
									case 'month': 
												$model->getSelect()
														->reset(Zend_Db_Select::COLUMNS)
														->columns("created_at, COUNT(*) AS total")
														->where("DATE(created_at) >= '" . date('Y') . '-' . date('m') . '-1' . "'")
														->group("DATE(created_at)");
												//echo $model->getSelect();
												break;									
									case 'year' : 
												$model->getSelect()
														->reset(Zend_Db_Select::COLUMNS)
														->columns("created_at, COUNT(*) AS total")
														->where("YEAR(created_at) = YEAR(NOW())")
														->group("MONTH(created_at)");
												//echo $model->getSelect();die;
												break;								
								} 
								break;
				case 'qty'   : $model = $this->_vendor->getAssociatedOrders(); break;
				case 'sale'  : $model = $this->_vendor->getAssociatedOrders(); break;
			}
			//$model = $readConnection->fetchAll($query);
			return $model && count($model)?$model->getData():array();
		}
		return false;
	}
	
	public function getVordersReportModel($vendor,$range = 'day',$from_date,$to_date,$status=Ced_CsMarketplace_Model_Vorders::STATE_PAID) {
		$this->_vendor=$vendor;
		if ($this->_vendor != null && $this->_vendor && $this->_vendor->getId()) {
			$from_date=date("Y-m-d 00:00:00",strtotime($from_date));
			$to_date=date("Y-m-d 59:59:59",strtotime($to_date));
			$coreResource   = Mage::getSingleton('core/resource');
			$readConnection = $coreResource->getConnection('read');
			if($status==Ced_CsMarketplace_Model_Vorders::STATE_OPEN)
				$order_status=Mage_Sales_Model_Order_Invoice::STATE_PAID;
			if($status==Ced_CsMarketplace_Model_Vorders::STATE_PAID)
				$order_status=Mage_Sales_Model_Order_Invoice::STATE_PAID;
			if($status==Ced_CsMarketplace_Model_Vorders::STATE_CANCELED)
				$order_status=Mage_Sales_Model_Order_Invoice::STATE_CANCELED;
			$model = $this->_vendor->getAssociatedOrders();
		 	switch($range) {
					default:$model = $this->_vendor->getAssociatedOrders(); break;
					case 'day'  :
						$model->getSelect()
						->reset(Zend_Db_Select::COLUMNS)
						->columns("DATE(created_at) AS period,COUNT(*) AS order_count,SUM(product_qty) AS product_qty,SUM(`order_total`) as order_total,SUM(`shop_commission_fee`) AS commission_fee,(SUM(`order_total`) - SUM(`shop_commission_fee`)) AS net_earned")
						->where("created_at>='".$from_date."'")
						->where("created_at<='".$to_date."'")
						->group("DATE(created_at)")
						->order("created_at ASC");
						if($status!="*"){
						$model->getSelect()
							->where("payment_state='".$status."'")
							->where("order_payment_state='".$order_status."'");
						}
						/* echo count($model);
						echo $model->getSize(); */
						/* echo $model->getSelect();die; */
						break;
					case 'month':
						$model->getSelect()
						->reset(Zend_Db_Select::COLUMNS)
						->columns("CONCAT(MONTH(created_at),CONCAT('-',YEAR(created_at))) AS period,COUNT(*) AS order_count,SUM(product_qty) AS product_qty, SUM(`order_total`) AS order_total, SUM(`shop_commission_fee`) AS commission_fee,(SUM(`order_total`) - SUM(`shop_commission_fee`)) AS net_earned")
						->where("created_at >='".$from_date."' AND created_at<='".$to_date."'")
						->group("YEAR(created_at), MONTH(created_at)");
						if($status!="*"){
							$model->getSelect()
							->where("payment_state='".$status."'")
							->where("order_payment_state='".$order_status."'");
						}
						//echo $model->getSelect();die;
						break;
					case 'year' :
						$model->getSelect()
						->reset(Zend_Db_Select::COLUMNS)
						->columns("YEAR(created_at) AS period,COUNT(*) AS order_count, SUM(`order_total`) AS order_total,SUM(product_qty) AS product_qty,SUM(`shop_commission_fee`) AS commission_fee,(SUM(`order_total`) - SUM(`shop_commission_fee`)) AS net_earned")
						->where("created_at >='".$from_date."' AND created_at<='".$to_date."'")
						->group("YEAR(created_at)");
						if($status!="*"){
							$model->getSelect()
							->where("payment_state='".$status."'")
							->where("order_payment_state='".$order_status."'");
						}
						//echo $model->getSelect();die;
						break;
				
				}
				
			//$model = $readConnection->fetchAll($query);
			return $model && count($model)?$model:array();
		}
		return false;
	}
	
	public function getVproductsReportModel($vendorId,$from_date = '',$to_date = '' , $group = true) {
		$ordersCollection=Mage::getResourceModel('reports/product_sold_collection');
		$from = $to = '';
		if ($from_date != '' && $to_date != '') {
			$from=date("Y-m-d 00:00:00",strtotime($from_date));
			$to=date("Y-m-d 59:59:59",strtotime($to_date));
		}
		$compositeTypeIds     = Mage::getSingleton('catalog/product_type')->getCompositeTypes();
		$product = Mage::getResourceSingleton('catalog/product');
		$coreResource   = Mage::getSingleton('core/resource');
		$adapter              = $coreResource->getConnection('read');
		$orderTableAliasName  = $adapter->quoteIdentifier('order');
	
		$orderJoinCondition   = array(
			$orderTableAliasName . '.entity_id = order_items.order_id',
			$adapter->quoteInto("{$orderTableAliasName}.state <> ?", Mage_Sales_Model_Order::STATE_CANCELED),
	
		);
	
		$productJoinCondition = array(
			$adapter->quoteInto('(e.type_id NOT IN (?))', $compositeTypeIds),
			'e.entity_id = order_items.product_id',
			$adapter->quoteInto('e.entity_type_id = ?', $product->getTypeId())
		);
	
		if (($from != '' && $to != '') || $group) {
			$fieldName            = $orderTableAliasName . '.created_at';
			$orderJoinCondition[] = $this->_prepareBetweenSql($fieldName, $from, $to);
		}

		$ordersCollection->getSelect()->reset()
			->from(
					array('order_items' =>$coreResource->getTableName('sales/order_item')),
					array(
						'ordered_qty' => 'SUM(order_items.qty_ordered)',
						'order_item_name' => 'order_items.name',
						'order_item_total_sales' => 'SUM(order_items.row_total)',
							'sku'=>'order_items.sku'
			))
			->joinInner(
					array('order' => $coreResource->getTableName('sales/order')),
					implode(' AND ', $orderJoinCondition),
					array()
			)
			->joinLeft(
					array('e' => $product->getEntityTable()),
					implode(' AND ', $productJoinCondition),
					array(
							'entity_id' => 'order_items.product_id',
							'type_id' => 'e.type_id',
			))
			->where('parent_item_id IS NULL')
			->where('vendor_id="'.$vendorId.'"');
			if($group) $ordersCollection->getSelect()->group('order_items.product_id');
			$ordersCollection->getSelect()->having('SUM(order_items.qty_ordered) > ?', 0);
		/* echo $ordersCollection->getSelect();die; */
		return $ordersCollection;
	}

/**
 * Prepare between sql
 *
 * @param  string $fieldName Field name with table suffix ('created_at' or 'main_table.created_at')
 * @param  string $from
 * @param  string $to
 * @return string Formatted sql string
 */
protected function _prepareBetweenSql($fieldName, $from, $to)
{
	$coreResource   = Mage::getSingleton('core/resource');
	$adapter              = $coreResource->getConnection('read');
	return sprintf('(%s >= %s AND %s <= %s)',
			$fieldName,
			$adapter->quote($from),
			$fieldName,
			$adapter->quote($to)
	);
}
}