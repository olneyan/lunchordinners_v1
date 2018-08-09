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

class Ced_VendorApi_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $_vendor = null;
	
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
		return $ordersCollection;
	}
	
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
			return $model;
		}
		return false;
	}
}