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
 
class Ced_VendorApi_VordersController extends Ced_VendorApi_Controller_Abstract
{
	/*get all the orders for particulr vendor*/
	public function itemAction()
	{
		$vendorId  = $this->getRequest()->getParam('vendor_id');
		$validate=array(
			'vendor_id'=>$vendorId,
			'hash'=>$this->getRequest()->getParam('hashkey')
			);
		$validateRequest=$this->validate($validate);
		$hashkey   = $this->getRequest()->getParam('hashkey');
		$filter=   $this->getRequest()->getParam('filter');
		$offset=   $this->getRequest()->getParam('page');
		$vendorOrders = Mage::getModel('vendorapi/order_api')->items($vendorId,$offset,$filter);
		if(is_array($vendorOrders)){
		$response=json_encode($vendorOrders);
		$this->getResponse()->setBody($response);
		}
		else{
			$this->getResponse()->setBody($vendorOrders);
		}
		
	}

	/*getting info of orders for particulr vendor*/
	public function infoAction()
	{
		$vendorId  = $this->getRequest()->getParam('vendor_id');
		$vsession = $this->getRequest()->getParam('hashkey');
		$validate=array(
			'vendor_id'=>$vendorId,
			'hash'=>$this->getRequest()->getParam('hashkey')
			);
		$validateRequest=$this->validate($validate);
		$orderIncrementId = $this->getRequest()->getParam('order_id');
		$vendorOrders = Mage::getModel('vendorapi/order_api')->info($vendorId,$vsession,$orderIncrementId);
		$response=json_encode($vendorOrders);
		$this->getResponse()->setBody($response);
	}

	/*getting payment info for particulr vendor*/
	public function paymentAction()
	{
		$vendorId  = $this->getRequest()->getParam('vendor_id');
		$vsession = $this->getRequest()->getParam('hashkey');
		$vendorPayment = Mage::getModel('vendorapi/order_api')->payments($vendorId,$vsession);
		print_r($vendorPayment);
		return $vendorPayment;
	}
	public function orderstatusAction(){
		$statusArray=Mage::getModel('sales/order_invoice')->getStates();
		$orderStatus=array(array(
				'label'=>'Please Select Option',
				'value'=>''
				));
		$paymentarray=Mage::getModel('csmarketplace/vorders')->getStates();
		$paymentstatus=array(array(
				'label'=>'Please Select Option',
				'value'=>''
				));
		foreach ($statusArray as $key => $val) {
			$orderStatus[]=array(
				'label'=>$val,
				'value'=>$key
				);
		}
		foreach ($paymentarray as $key => $val) {
			$paymentstatus[]=array(
				'label'=>$val,
				'value'=>$key
				);
		}
		$data=array(
			'data'=>array(
				'order_payment_status'=>$orderStatus,
				'vendor_payment_status'=>$paymentstatus

				)
			);
		$response=json_encode($data);
		$this->getResponse()->setBody($response);
	}
}

?>