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

class Ced_CsMarketplace_Block_Vendor_Dashboard_Extrainfo extends Ced_CsMarketplace_Block_Vendor_Abstract
{

	public function __construct() {
		parent::__construct();
		if ($this->getVendorId()) {
			$ordersCollection = $this->getVendor()->getAssociatedOrders()->setOrder('created_at','DESC')->setPageSize(5);
			$main_table=Mage::helper('csmarketplace')->getTableKey('main_table');
			$order_total=Mage::helper('csmarketplace')->getTableKey('order_total');
			$shop_commission_fee=Mage::helper('csmarketplace')->getTableKey('shop_commission_fee');
			$ordersCollection->getSelect()->columns(array('net_vendor_earn' => new Zend_Db_Expr("({$main_table}.{$order_total} - {$main_table}.{$shop_commission_fee})")));
			$this->setVorders($ordersCollection);
		}
	}
	
	/**
	 * Return order view link
	 *
	 * @param string $order
	 * @return String
	 */
	public function getViewUrl($order)
	{
		return $this->getUrl('*/vorders/view', array('order_id' =>$order->getId(),'_secure'=>true,'_nosid'=>true));
	}

}
