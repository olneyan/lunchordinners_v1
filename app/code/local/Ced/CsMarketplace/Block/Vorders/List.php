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
 * CsMarketplace Orders List block
 *
 * @category   Ced
 * @package    Ced_CsMarketplace
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Block_Vorders_List extends Ced_CsMarketplace_Block_Vendor_Abstract
{
	protected $_filterCollection;
	/**
	 * Get set collection of Orders
	 *
	 */
	public function __construct(){
		parent::__construct();
		$ordersCollection = array();
		if ($vendorId = $this->getVendorId()) {
			$ordersCollection = $this->getVendor()->getAssociatedOrders()->setOrder('id', 'DESC');
			$main_table=Mage::helper('csmarketplace')->getTableKey('main_table');
			$order_total=Mage::helper('csmarketplace')->getTableKey('order_total');
			$shop_commission_fee=Mage::helper('csmarketplace')->getTableKey('shop_commission_fee');
			$ordersCollection->getSelect()->columns(array('net_vendor_earn' => new Zend_Db_Expr("({$main_table}.{$order_total} - {$main_table}.{$shop_commission_fee})")));			
			$filterCollection = $this->filterOrders($ordersCollection);
			//echo $ordersCollection->getSelect();die;
			$this->setVorders($filterCollection);
		}		
	}
	

	public function filterOrders($ordersCollection){
		$params = Mage::getSingleton('core/session')->getData('order_filter');
		$main_table=Mage::helper('csmarketplace')->getTableKey('main_table');
		$order_total=Mage::helper('csmarketplace')->getTableKey('order_total');
		$shop_commission_fee=Mage::helper('csmarketplace')->getTableKey('shop_commission_fee');
		if(is_array($params) && count($params)>0){
			foreach($params as $field=>$value){
				if($field=='__SID')
					continue;
				if(is_array($value)){
					if(isset($value['from']) && urldecode($value['from'])!=""){
						$from = urldecode($value['from']);
						if($field=='created_at'){
							$from=date("Y-m-d 00:00:00",strtotime($from));
						} 
						
						if($field=='net_vendor_earn')
							$ordersCollection->getSelect()->where("({$main_table}.{$order_total}- {$main_table}.{$shop_commission_fee}) >='".$from."'");
						else
							$ordersCollection->addFieldToFilter($field, array('gteq'=>$from));
					}
					if(isset($value['to'])  && urldecode($value['to'])!=""){
						$to = urldecode($value['to']);
						if($field=='created_at'){
							$to=date("Y-m-d 59:59:59",strtotime($to));
						}
						if($field=='net_vendor_earn')
							$ordersCollection->getSelect()->where("({$main_table}.{$order_total}- {$main_table}.{$shop_commission_fee}) <='".$to."'");
						else
							$ordersCollection->addFieldToFilter($field, array('lteq'=>$to));
					}
				}else if(urldecode($value)!=""){
					$ordersCollection->addFieldToFilter($field, array("like"=>'%'.urldecode($value).'%'));
				}
		
			}
		}
		//echo $ordersCollection->getSelect();die;
		return $ordersCollection;
	}
	
	
	/**
	 * prepare list layout
	 *
	 */
	protected function _prepareLayout() {
		parent::_prepareLayout();
			$pager = $this->getLayout()->createBlock('csmarketplace/html_pager', 'custom.pager');
			$pager->setAvailableLimit(array(5=>5,10=>10,20=>20,'all'=>'all'));
			$pager->setCollection($this->getVorders());
			$this->setChild('pager', $pager);
		return $this;
	}
	/**
	 * return the pager
	 *
	 */
	public function getPagerHtml() {
		return $this->getChildHtml('pager');
	}
	
	/**
	 * return Back Url
	 *
	 */
	public function getBackUrl()
	{
		return $this->getUrl('*/*/index',array('_secure'=>true,'_nosid'=>true));
	}
	 /**
     * Return order view link
     *
     * @param string $order
     * @return String
     */
	public function getViewUrl($order)
	{
		return $this->getUrl('*/*/view', array('order_id' =>$order->getId(),'_secure'=>true,'_nosid'=>true));
	}
	
}
