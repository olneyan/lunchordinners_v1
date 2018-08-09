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
class Ced_CsMarketplace_Block_Vpayments_List extends Ced_CsMarketplace_Block_Vendor_Abstract
{
	/**
	 * Get set collection of Orders
	 *
	 */
	public function __construct(){
		parent::__construct();
		$payments = array();
		if ($vendorId = $this->getVendorId()) {
			$payments = $this->getVendor()->getVendorPayments()->setOrder('created_at', 'DESC');
			$payments = $this->filterPayment($payments);
		}
		$this->setVpayments($payments);
	}
	
	
	public function filterPayment($payment){	
		$params = Mage::getSingleton('core/session')->getData('payment_filter');
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
		$pager->setCollection($this->getVpayments());
		$this->setChild('pager', $pager);
		$this->getVpayments()->load();
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
	public function getViewUrl($payment)
	{
		return $this->getUrl('*/*/view', array('payment_id' =>$payment->getId(),'_secure'=>true,'_nosid'=>true));
	}
	
}
