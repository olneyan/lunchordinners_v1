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
 
class Ced_VendorApi_VreportController extends Ced_VendorApi_Controller_Abstract
{
	/*get all the report of product for particulr vendor*/
	public function reportAction()
	{
		$vendorId  = $this->getRequest()->getParam('vendor_id');
		$validate=array(
			'vendor_id'=>$vendorId,
			'hash'=>$this->getRequest()->getParam('hashkey')
			);
		$validateRequest=$this->validate($validate);
		$from = $this->getRequest()->getParam('from');
		$to = $this->getRequest()->getParam('to');
		$page = $this->getRequest()->getParam('page');
		$productReport = Mage::getModel('vendorapi/report_api')->getProductReport($vendorId,$from,$to,$page);
		if(is_array($productReport)){
		$response=json_encode($productReport);
		$this->getResponse()->setBody($response);
		}
		else{
			$this->getResponse()->setBody($productReport);
		}
	}

	/*get all the report of order for particulr vendor*/

	public function orderAction()
	{
		$vendorId  =$this->getRequest()->getParam('vendor_id');
		$validate=array(
			'vendor_id'=>$vendorId,
			'hash'=>$this->getRequest()->getParam('hashkey')
			);
		$validateRequest=$this->validate($validate);
		$from = $this->getRequest()->getParam('from');
		$to = $this->getRequest()->getParam('to');
		$period = $this->getRequest()->getParam('period');
		$payment_state =$this->getRequest()->getParam('payment_state');
		$page = $this->getRequest()->getParam('page');
		$orderReport = Mage::getModel('vendorapi/report_api')->getOrderReport($vendorId,$period,$from,$to,$payment_state,$page);
		if(is_array($orderReport)){
		$response=json_encode($orderReport);
		$this->getResponse()->setBody($response);
		}
		else{
			$this->getResponse()->setBody($orderReport);
		}
	}
	public function vpaymentStatusAction(){
		/*$paymentarray=Mage::getModel('csmarketplace/vorders')->getStates();
		$vorderstatus=array();
		foreach ($variable as $key => $value) {
			$vorderstatus[]=array(
				'label'=>$value,
				'value'=>$key
				);
		}
		var_dump($vorderstatus);die;*/
		$helper = Mage::helper ( 'csmarketplace' );
		$vorderstatus=array(
			'0'=>array(
				'label'=>$helper->__('All'),
				'value'=>'*'
			),
			'1'=>array(
				'label'=>$helper->__('Pending'),
				'value'=>Ced_CsMarketplace_Model_Vorders::STATE_OPEN,
			),
			'2'=>array(
				'label'=>$helper->__('Paid'),
				'value'=>Ced_CsMarketplace_Model_Vorders::STATE_PAID,
			),
			'3'=>array(
				'label'=>$helper->__('Cancelled'),
				'value'=>Ced_CsMarketplace_Model_Vorders::STATE_CANCELED,
			),
			);
		$data=array(
			'data'=>array(
				'vordersstatus'=>$vorderstatus
			)
		);
		$response = json_encode($data);
		$this->getResponse()->setBody($response);
	}

}