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
 * CsMarketplace Payments List block
 *
 * @category   Ced
 * @package    Ced_CsMarketplace
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Block_Vpayments_List_Request extends Ced_CsMarketplace_Block_Vendor_Abstract
{
	/**
	 * Get set collection of Orders
	 *
	 */
	public function __construct(){
		parent::__construct();
		$pendingPayments = array();
		if ($vendorId = $this->getVendorId()) {
			$pendingPayments = $this->getVendor()->getAssociatedOrders()
									->addFieldToFilter('order_payment_state',array('eq'=>Mage_Sales_Model_Order_Invoice::STATE_PAID))
									->addFieldToFilter('payment_state',array('eq'=>Ced_CsMarketplace_Model_Vorders::STATE_OPEN))
									->setOrder('created_at', 'ASC');
			$main_table=Mage::helper('csmarketplace')->getTableKey('main_table');
			$order_total=Mage::helper('csmarketplace')->getTableKey('order_total');
			$shop_commission_fee=Mage::helper('csmarketplace')->getTableKey('shop_commission_fee');
			$pendingPayments->getSelect()->columns(array('net_vendor_earn' => new Zend_Db_Expr("({$main_table}.{$order_total} - {$main_table}.{$shop_commission_fee})")));
			
			$pendingPayments = $this->filterPayment($pendingPayments);
		}
		$this->setPendingVpayments($pendingPayments);
	}
	
	
	public function filterPayment($payment){	
		$params = Mage::getSingleton('core/session')->getData('payment_request_filter');
		if(is_array($params) && count($params)>0){
			foreach($params as $field=>$value){
				if($field=="__SID")
					continue;
				if(is_array($value)){
					if(isset($value['from']) && urldecode($value['from'])!=""){
						$from = urldecode($value['from']);					
						if($field=='created_at'){
							$from=date("Y-m-d 00:00:00",strtotime($from));
						} 
						
						$payment->addFieldToFilter($field, array('gteq'=>$from));
					}
					if(isset($value['to'])  && urldecode($value['to'])!=""){
						$to = urldecode($value['to']);					
						if($field=='created_at'){
							$to=date("Y-m-d 59:59:59",strtotime($to));
						}
						
						$payment->addFieldToFilter($field, array('lteq'=>$to));
					}
				}else if(urldecode($value)!=""){
					if($field == 'payment_method') {
						$payment->addFieldToFilter($field, array("in"=>Mage::helper('csmarketplace/acl')->getDefaultPaymentTypeValue(urldecode($value))));
					} else {
						$payment->addFieldToFilter($field, array("like"=>'%'.urldecode($value).'%'));
					}
				}
			
			}
		}
		return $payment;		
	}
	
	/**
	 * prepare list layout
	 *
	 */
	protected function _prepareLayout() {
		parent::_prepareLayout();
		$pager = $this->getLayout()->createBlock('csmarketplace/html_pager', 'custom.pager');
		$pager->setAvailableLimit(array(5=>5,10=>10,20=>20,'all'=>'all'));
		$pager->setCollection($this->getPendingVpayments());
		$this->setChild('pager', $pager);
		$this->getPendingVpayments()->load();
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
	
}
