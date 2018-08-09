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
 
class Ced_VendorApi_VtransactionController extends Ced_VendorApi_Controller_Abstract
{
	/*getting payment info for particulr vendor*/
	public function paymentAction()
	{
		$vendorId  = $this->getRequest()->getParam('vendor_id');
		$validate=array(
			'vendor_id'=>$vendorId,
			'hash'=>$this->getRequest()->getParam('hashkey')
			);
		$validateRequest=$this->validate($validate);
		$offset = 	$this->getRequest()->getParam('page');
		$filter = 	$this->getRequest()->getParam('filter');
		$vendorPayment = Mage::getModel('vendorapi/transaction_api')->payments($vendorId,$offset,$filter);
		$response=json_encode($vendorPayment);
		$this->getResponse()->setBody($response);
	}
	public function viewpaymentAction(){
		$data=$this->getRequest()->getParams();
		$validate=array(
			'vendor_id'=>$this->getRequest()->getParam('vendor_id'),
			'hash'=>$this->getRequest()->getParam('hashkey')
			);
		$validateRequest=$this->validate($validate);
		$viewPayment =Mage::getModel('vendorapi/transaction_api')->viewpayments($data);
		$response=json_encode($viewPayment);
		$this->getResponse()->setBody($response);
	}
	/*Request Payment Listing*/
	public function paymentrequestAction(){
		$data=$this->getRequest()->getParams();
		$validate=array(
			'vendor_id'=>$this->getRequest()->getParam('vendor_id'),
			'hash'=>$this->getRequest()->getParam('hashkey')
			);
		$data['filter'] = $this->getRequest()->getParam('filter',json_encode(array()));
		$validateRequest=$this->validate($validate);
		$requestPayment =Mage::getModel('vendorapi/transaction_api')->requestPayments($data);
		$response=json_encode($requestPayment);
		$this->getResponse()->setBody($response);
		

	}
	public function requestPaymentAction(){
		$vendorId = $this->getRequest()->getParam('vendor_id');
		$validate=array(
			'vendor_id'=>$vendorId,
			'hash'=>$this->getRequest()->getParam('hashkey')
			);
		$validateRequest=$this->validate($validate);
		$vendor  = Mage::getModel('csmarketplace/vendor')->load($vendorId);
		if(isset($vendorId)){
			if(!Mage::getStoreConfig('ced_csmarketplace/general/request_active',Mage::app()->getStore()->getId())){
				$data=array(
					'data'=>array(
						'message'=>Mage::helper('csmarketplace')->__('Payment Request Is Not Enabled By Vendor'),
						'success'=>false

						)
					);
				$data;
			}
			$orderIds = $this->getRequest()->getParam('payment_id');
			if(strlen($orderIds) > 0) {
				$orderIds = explode(',',$orderIds);
			}

			if (!is_array($orderIds)) {
	            $data=array(
					'data'=>array(
						'message'=>Mage::helper('csmarketplace')->__('Please select amount(s).'),
						'success'=>false

						)
					);
				$data;
	        } else {
	            if (!empty($orderIds)) {
	                try {
						$updated = 0;
	                    foreach ($orderIds as $orderId) {
							$model = Mage::getModel('csmarketplace/vpayment_requested')->loadByField(array('vendor_id','order_id'),array($vendorId,$orderId));
							if($model && $model->getId()){
							} else {
								$amount = 0.000;
								$pendingPayments = $vendor->getAssociatedOrders()
														->addFieldToFilter('order_payment_state',array('eq'=>Mage_Sales_Model_Order_Invoice::STATE_PAID))
														->addFieldToFilter('payment_state',array('eq'=>Ced_CsMarketplace_Model_Vorders::STATE_OPEN))
														->addFieldToFilter('entity_id',array('eq'=> $orderId))
														->addFieldToFilter('vendor_id',array('eq'=> $vendorId))
														->setOrder('created_at', 'ASC');
								$main_table=Mage::helper('csmarketplace')->getTableKey('main_table');
								$order_total=Mage::helper('csmarketplace')->getTableKey('order_total');
								$shop_commission_fee=Mage::helper('csmarketplace')->getTableKey('shop_commission_fee');
								$pendingPayments->getSelect()->columns(array('net_vendor_earn' => new Zend_Db_Expr("({$main_table}.{$order_total} - {$main_table}.{$shop_commission_fee})")));
								
								if(count($pendingPayments) > 0) {
									$amount = $pendingPayments->getFirstItem()->getNetVendorEarn();
								}
								$data = array('vendor_id'=>$vendorId, 'order_id'=>$orderId, 'amount'=>$amount, 'status'=>Ced_CsMarketplace_Model_Vpayment_Requested::PAYMENT_STATUS_REQUESTED,'created_at'=>Mage::getModel('core/date')->date('Y-m-d H:i:s'));
								Mage::getModel('csmarketplace/vpayment_requested')->addData($data)->save();
								$updated++;
							}
	                    }
						if($updated) {
							$data=array(
										'data'=>array(
										'message'=>Mage::helper('csmarketplace')->__('Total of %d amount(s) have been requested for payment.', $updated),
										'success'=>true

									)
							);
						$data;
						} else {
							$data=array(
										'data'=>array(
										'message'=>Mage::helper('csmarketplace')->__('Payment(s) have been already requested for payment.'),
										'success'=>false

									)
							);
						$data;
						}
	                } catch (Exception $e) {
	                    $data=array(
										'data'=>array(
										'message'=>$e->getMessage(),
										'success'=>false

									)
							);
						$data;
	                }
	            }
	        }
	    }
	    $response=json_encode($data);
		$this->getResponse()->setBody($response);
	}

	public function massRequestPaymentAction(){
		$vendorId=	$this->getRequest()->getParam('vendor_id');
		$validate=array(
			'vendor_id'=>$vendorId,
			'hash'=>$this->getRequest()->getParam('hashkey')
			);
		$validateRequest=$this->validate($validate);
		$orderids = array();
		$vendor = Mage::getModel('csmarketplace/vendor')->load($vendorId);
		if ($vendorId) {
			$pendingPayments = $vendor->getAssociatedOrders()
									->addFieldToFilter('order_payment_state',array('eq'=>Mage_Sales_Model_Order_Invoice::STATE_PAID))
									->addFieldToFilter('payment_state',array('eq'=>Ced_CsMarketplace_Model_Vorders::STATE_OPEN))
									->setOrder('created_at', 'ASC');
			$main_table=Mage::helper('csmarketplace')->getTableKey('main_table');
			$order_total=Mage::helper('csmarketplace')->getTableKey('order_total');
			$shop_commission_fee=Mage::helper('csmarketplace')->getTableKey('shop_commission_fee');
			$pendingPayments->getSelect()->columns(array('net_vendor_earn' => new Zend_Db_Expr("({$main_table}.{$order_total} - {$main_table}.{$shop_commission_fee})")));
			foreach ($pendingPayments as $_payment){
				$orderids[] = $_payment->getEntityId();
			}
		}
		//var_dump($orderids);die;
		if(isset($vendorId)){
			if(!Mage::getStoreConfig('ced_csmarketplace/general/request_active',Mage::app()->getStore()->getId())){
				$data=array(
					'data'=>array(
						'message'=>Mage::helper('csmarketplace')->__('Payment Request Is Not Enabled By Vendor'),
						'success'=>false

						)
					);
				$data;
			}
			//var_dump($orderids);
			if (!is_array($orderids)) { 
	            $data=array(
					'data'=>array(
						'message'=>Mage::helper('csmarketplace')->__('Please select amount(s).'),
						'success'=>false

						)
					);
				$data;
	        } else {
	            if (!empty($orderids)) {
	                try {
						$updated = 0;
	                    foreach ($orderids as $orderId) {
							$model = Mage::getModel('csmarketplace/vpayment_requested')->loadByField(array('vendor_id','order_id'),array($vendorId,$orderId));
							if($model && $model->getId()){
							} else {
								$amount = 0.000;
								$pendingPayments = $vendor->getAssociatedOrders()
														->addFieldToFilter('order_payment_state',array('eq'=>Mage_Sales_Model_Order_Invoice::STATE_PAID))
														->addFieldToFilter('payment_state',array('eq'=>Ced_CsMarketplace_Model_Vorders::STATE_OPEN))
														->addFieldToFilter('entity_id',array('eq'=> $orderId))
														->addFieldToFilter('vendor_id',array('eq'=> $vendorId))
														->setOrder('created_at', 'ASC');
								$main_table=Mage::helper('csmarketplace')->getTableKey('main_table');
								$order_total=Mage::helper('csmarketplace')->getTableKey('order_total');
								$shop_commission_fee=Mage::helper('csmarketplace')->getTableKey('shop_commission_fee');
								$pendingPayments->getSelect()->columns(array('net_vendor_earn' => new Zend_Db_Expr("({$main_table}.{$order_total} - {$main_table}.{$shop_commission_fee})")));
								
								if(count($pendingPayments) > 0) {
									$amount = $pendingPayments->getFirstItem()->getNetVendorEarn();
								}
								$data = array('vendor_id'=>$vendorId, 'order_id'=>$orderId, 'amount'=>$amount, 'status'=>Ced_CsMarketplace_Model_Vpayment_Requested::PAYMENT_STATUS_REQUESTED,'created_at'=>Mage::getModel('core/date')->date('Y-m-d H:i:s'));
								Mage::getModel('csmarketplace/vpayment_requested')->addData($data)->save();
								$updated++;
							}
	                    }
						if($updated) {
							$data=array(
										'data'=>array(
										'message'=>Mage::helper('csmarketplace')->__('Total of %d amount(s) have been requested for payment.', $updated),
										'success'=>true

									)
							);
						$data;
						} else {
							$data=array(
										'data'=>array(
										'message'=>Mage::helper('csmarketplace')->__('Payment(s) have been already requested for payment.'),
										'success'=>false

									)
							);
						$data;
						}
	                } catch (Exception $e) {
	                    $data=array(
										'data'=>array(
										'message'=>$e->getMessage(),
										'success'=>false

									)
							);
						$data;
	                }
	            }
	        }
	    }
	    $response=json_encode($data);
		$this->getResponse()->setBody($response);
	}
}

?>