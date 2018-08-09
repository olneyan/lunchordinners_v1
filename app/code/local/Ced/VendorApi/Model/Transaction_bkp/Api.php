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

class Ced_VendorApi_Model_Transaction_Api extends Mage_Api_Model_Resource_Abstract
{	
	public function model()
	{
		return Mage::getResourceModel('vendorapi/vapisession');
	}
	
	public function payments($vendorId, $vsession, $filters=null)
	{
		if(!$this->model()->isVendorLoggedIn($vendorId, $vsession)) {
			return false;
		}
		$collection =  Mage::getModel('csmarketplace/vpayment')->getCollection()->addFieldToFilter('vendor_id',array('eq'=>$vendorId));
		$payments = $collection->setOrder('created_at', 'DESC');
		$payments = $this->filterPayment($payments, $filters);
		return $payments;
	}
	
	public function filterPayment($payment, $filters){
		//$params = Mage::getSingleton('core/session')->getData('payment_filter');
		$params = $filters;
		if(is_array($params) && count($params)>0){
			foreach($params as $field=>$value){
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
		return $payment->getData();
	}
}
?>