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
	public function payments($vendorId,$offset,$filters=null)
	{
		if($vendorId==null) {
			$data = array (
					'data' => array (
							'success' => false ,
							'message'=> 'Vendor ID Is Empty'
							) 
					) ;
			return $data;
		}
		$limit = '10';
		$curr_page = 1;
		if ($offset != 'null' && $offset != '') {
			$curr_page = $offset;
		}

		$offset = ($curr_page - 1) * $limit;
		$collection =  Mage::getModel('csmarketplace/vpayment')->getCollection()->addFieldToFilter('vendor_id',array('eq'=>$vendorId));
		$payments = $collection->setOrder('created_at', 'DESC');
		$paymentCount = count($payments);
		$payments->getSelect ()->limit($limit,$offset);
		$payments = $this->filterPayment($payments, $filters);
		$transaction=array();
		foreach ($payments as $key=>$_payment) {
			$transaction[]=array(
					'payment_id'=>$_payment['id'],
					'created_at'=>Mage::helper('core')->formatDate( $_payment['created_at'] , 'medium', true),
					'payment_mode' =>Mage::helper('csmarketplace/acl')->getDefaultPaymentTypeLabel($_payment['payment_method']),
					'transaction_id' => $_payment['transaction_id'],
					'amount'=>Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol().''.$_payment['amount'],
					'adjustment_amount'=>Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol().''.$_payment['fee'],
					'net_amount'=>Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol().''.$_payment['net_amount']

				);
		}
		if(count($transaction)>0){
				$data=array(
					'data'=>array(
                    'transactiondata'=>$transaction,
                    'payment_msg' =>'You  have  transaction',
                    'count' => $paymentCount,
                    'success'=>true
                    )   
                );	
		}
		else {
			$data = array (
				'data' => array (
						'success' => true ,
						"transactiondata"=>'NO_ORDER',
						'payment_msg' =>'You dont have any transaction yet',
						'message'=> 'You dont have any transaction yet'	
				) 
			);
		}
		$paymentHelper = Mage::helper('csmarketplace/payment');
 		$vendor = Mage::getModel('csmarketplace/vendor')->load($vendorId);
		$tcollection=$paymentHelper->_getTransactionsStats($vendor);
		$transstats=array();
		$pendingAmount = $paidAmount = $canceledAmount = $refundableAmount = $refundedAmount = 0;
		foreach ($tcollection as $stats) {
			switch($stats->getPaymentState()) {
				case Ced_CsMarketplace_Model_Vorders::STATE_OPEN : 
					$pendingAmount=$stats->getNetAmount();
					$pendingTransfer=$stats->getCount();
					break;
				case Ced_CsMarketplace_Model_Vorders::STATE_PAID :
					$paidAmount= $stats->getNetAmount();
					break;
				case Ced_CsMarketplace_Model_Vorders::STATE_CANCELED : 
					$canceledAmount=$stats->getNetAmount();
					break;
				case Ced_CsMarketplace_Model_Vorders::STATE_REFUND : 
					$refundableAmount=$stats->getNetAmount();
					break;
				case Ced_CsMarketplace_Model_Vorders::STATE_REFUNDED : 
					$refundedAmount = $stats->getNetAmount();
				   	break;
			}
		}
		
		$earnedAmount=Mage::app()->getLocale()->currency(Mage::app()->getBaseCurrencyCode())->toCurrency($vendor->getAssociatedPayments()->getFirstItem()->getBalance());
		if($pendingTransfer==null){
			$pendingTransfer=0;
		}
		if($pendingAmount==null){
			$pendingAmount=0;
		}
		if($earnedAmount==null){
			$earnedAmount=0;
		}
		$data['data']['pendingTransfer']=$pendingTransfer;
		$data['data']['pendingAmount']=Mage::app()->getLocale()->currency(Mage::app()->getBaseCurrencyCode())->toCurrency($pendingAmount);
		$data['data']['paidAmount']=$paidAmount;
		$data['data']['earnedAmount']=$earnedAmount;
		$data['data']['canceledAmount']=$canceledAmount;
		$data['data']['refundableAmount']=$refundableAmount;
		$data['data']['refundedAmount']=$refundedAmount;
		return $data;
	}
	public function filterPayment($payment, $filters){
		//$params = Mage::getSingleton('core/session')->getData('payment_filter');
		$filter = json_decode($filters,true);
		$params = $filter;
		if(is_array($params) && count($params)>0){
			foreach($params as $field=>$value){
				if(is_array($value)){
					if(isset($value['from']) && $value['from']!=""){
						$from = $value['from'];
						if($field=='created_at'){
							$from=date("Y-m-d 00:00:00",strtotime($from));
						}
	
						$payment->addFieldToFilter($field, array('gteq'=>$from));
					}
					if(isset($value['to'])  && $value['to']!=""){
						$to = $value['to'];
						if($field=='created_at'){
							$to=date("Y-m-d 59:59:59",strtotime($to));
						}
	
						$payment->addFieldToFilter($field, array('lteq'=>$to));
					}
				}else if($value!=""){
					if($field == 'payment_method') {
						$payment->addFieldToFilter($field, array("in"=>Mage::helper('csmarketplace/acl')->getDefaultPaymentTypeValue($value)));
					} else {
						$payment->addFieldToFilter($field, array("like"=>'%'.$value.'%'));
					}
				}
					
			}
		}
		return $payment->getData();
	}
	public function viewpayments($data){
		if($data['vendor_id']==null || $data['payment_id']==null) {
			$data = array (
					'data' => array (
							'success' => false ,
							'message'=> 'Vendor ID Is Empty'
							) 
					) ;
			return $data;
		}
		$model= Mage::getModel('csmarketplace/vpayment')->load($data['payment_id']);
		$renderOrderDesc = Mage::app()->getLayout()->createBlock('csmarketplace/adminhtml_vpayments_grid_renderer_orderdesc');
		$renderName = new Ced_CsMarketplace_Block_Adminhtml_Vorders_Grid_Renderer_Vendorname(true);
		if ($model->getBaseCurrency() != $model->getCurrency()) {
			$vendorname = $model->getId();
			$paymentmethod = $model->getData('payment_code');
			$paymentdetail = $model->getData('payment_detail');
			$transactionId = $model->getData('transaction_id');
			$createdat = $model->getData('created_at');
			$transactionmode=Mage::helper('csmarketplace/acl')->getDefaultPaymentTypeLabel($model->getData('payment_method'));
			$transactiontype = $model->getData('transaction_type') == 0?Mage::helper('csmarketplace')->__('Credit Type'):Mage::helper('csmarketplace')->__('Debit Type');
			$amount = Mage::app()->getLocale()->currency($model->getCurrency())->toCurrency($model->getData('amount'));
			$base_amount = Mage::app()->getLocale()->currency($model->getBaseCurrency())->toCurrency($model->getData('base_amount'));
			$fee = Mage::app()->getLocale()->currency($model->getCurrency())->toCurrency($model->getData('fee'));
			$base_fee = Mage::app()->getLocale()->currency($model->getBaseCurrency())->toCurrency($model->getData('base_fee'));
			$net_amount = Mage::app()->getLocale()->currency($model->getCurrency())->toCurrency($model->getData('net_amount'));
			$base_net_amount = Mage::app()->getLocale()->currency($model->getBaseCurrency())->toCurrency($model->getData('base_net_amount'));
			$notes = $model->getData('notes');
			$response=array(
				'vendorname'=>$renderName->render($model),
				'paymentmethod'=>$paymentmethod,
				'paymentdetail'=>$paymentdetail,
				'order_detail'=>$renderOrderDesc->render($model),
				'transactionId'=>$transactionId,
				'createdat'=>$createdat,
				'transactionmode'=>$transactionmode,
				'transactiontype'=>$transactiontype,
				'amount'=>$amount,
				'base_amount'=>$base_amount,
				'adjustment_amount'=>$fee,
				'base_fee'=>$base_fee,
				'net_amount'=>$net_amount,
				'base_net_amount'=>$base_net_amount,
				'notes'=>$notes
			);				
		} else {
				$vendor_id =  $model->getId();
				$payment_method = $model->getData('payment_code');
				$payment_detail = $model->getData('payment_detail');
				$transaction_id =$model->getData('transaction_id');
				$created_at = $model->getData('created_at');
				$transactionmode =  Mage::helper('csmarketplace/acl')->getDefaultPaymentTypeLabel($model->getData('payment_method'));
				$transaction_type = $model->getData('transaction_type') == 0?Mage::helper('csmarketplace')->__('Credit Type'):Mage::helper('csmarketplace')->__('Debit Type');
				$amount =  Mage::app()->getLocale()->currency($model->getCurrency())->toCurrency($model->getData('amount'));
				$adjustmentamount = Mage::app()->getLocale()->currency($model->getCurrency())->toCurrency($model->getData('fee'));
				$net_amount = Mage::app()->getLocale()->currency($model->getCurrency())->toCurrency($model->getData('net_amount'));
							
				$notes = $model->getData('notes');
				$response=array(
				'vendorname'=>$renderName->render($model),
				'paymentmethod'=>$payment_method,
				'paymentdetail'=>$payment_detail,
				'order_detail'=>$renderOrderDesc->render($model),
				'transactionId'=>$transaction_id,
				'createdat'=>$created_at,
				'transactionmode'=>$transactionmode,
				'transactiontype'=>$transaction_type,
				'amount'=>$amount,
				'adjustment_amount'=>$adjustmentamount,
				'net_amount'=>$net_amount,
				'notes'=>$notes
			);		
		}
		$data = array (
					'data' => array (
							'payment_detail'=> array($response),
							'success' => true ,
							) 
					) ;
		return $data;
	}
	public function requestPayments($data)
	{
		$offset	= $data['page'];
		$limit ='10';
		$curr_page = 1;
			if ($offset != 'null' && $offset != '') {
			
			$curr_page = $offset;
		}
		$offset = ($curr_page - 1) * $limit;
		$helper = Mage::helper ( 'csmarketplace' );
		if($data['vendor_id']==null) {
			$data = array (
					'data' => array (
							'success' => false ,
							'message'=> 'Vendor ID Is Empty'
							) 
					) ;
			return $data;
		}
		if(!Mage::getStoreConfig('ced_csmarketplace/general/request_active',Mage::app()->getStore()->getId())){
				$data=array(
					'data'=>array(
						'message'=>Mage::helper('csmarketplace')->__('Payment Request Is Not Enabled By Admin'),
						'success'=>false

						)
					);
				$data;
				return $data;
		}
		if($data['vendor_id']) {
			$productsCollection = array();
			$paymentHelper = Mage::helper('csmarketplace/payment');
			$vendor = Mage::getModel('csmarketplace/vendor')->load($data['vendor_id']);
			$collection = $paymentHelper->_getTransactionsStats($vendor);

			$pendingAmount = $canceledAmount = $refundableAmount = $refundedAmount = $paidAmount =  0;

			if(count($collection)>0) {
				foreach ($collection as $stats){
					switch($stats->getPaymentState()) {
						case Ced_CsMarketplace_Model_Vorders::STATE_OPEN : 
							$pendingAmount = $stats->getNetAmount();
							break;

						case Ced_CsMarketplace_Model_Vorders::STATE_PAID : 
							$paidAmount = $stats->getNetAmount();
							break;

						case Ced_CsMarketplace_Model_Vorders::STATE_CANCELED : 
							$canceledAmount= $stats->getNetAmount();
							break;
																			   
						case Ced_CsMarketplace_Model_Vorders::STATE_REFUND : 
							$refundableAmount = $stats->getNetAmount();
							break;
																			  
						case Ced_CsMarketplace_Model_Vorders::STATE_REFUNDED : 
							$refundedAmount = $stats->getNetAmount();
							break;											   
					}
				}
			}
			$amounts = 0.0000;
			$main_table=Mage::helper('csmarketplace')->getTableKey('main_table');
			$amount_column=Mage::helper('csmarketplace')->getTableKey('amount');
			$amounts = Mage::getModel('csmarketplace/vpayment_requested')
							->getCollection()
							->addFieldToFilter('vendor_id',array('eq'=>$data['vendor_id']))
							->addFieldToFilter('status',array('eq'=>Ced_CsMarketplace_Model_Vpayment_Requested::PAYMENT_STATUS_REQUESTED));
			$amounts->getSelect()->columns("SUM({$main_table}.{$amount_column}) AS amounts");
			if(count($amounts) > 0) {
				$amounts = $amounts->getFirstItem()->getData("amounts");
			}			

			$earnedAmount = $vendor->getAssociatedPayments()->getFirstItem()->getBalance();
			$requestedAmount = $amounts;

			$respnse['data']['pendingAmount']=Mage::app()->getLocale()->currency(Mage::app()->getBaseCurrencyCode())->toCurrency($pendingAmount);
	
			$respnse['data']['paidAmount']=Mage::app()->getLocale()->currency(Mage::app()->getBaseCurrencyCode())->toCurrency($paidAmount);
			$respnse['data']['earnedAmount']=Mage::app()->getLocale()->currency(Mage::app()->getBaseCurrencyCode())->toCurrency($earnedAmount);
			$respnse['data']['canceledAmount']=Mage::app()->getLocale()->currency(Mage::app()->getBaseCurrencyCode())->toCurrency($canceledAmount);
			$respnse['data']['refundableAmount']=Mage::app()->getLocale()->currency(Mage::app()->getBaseCurrencyCode())->toCurrency($refundableAmount);
			$respnse['data']['refundedAmount'] = Mage::app()->getLocale()->currency(Mage::app()->getBaseCurrencyCode())->toCurrency($refundedAmount);
			$respnse['data']['requestedAmount'] = Mage::app()->getLocale()->currency(Mage::app()->getBaseCurrencyCode())->toCurrency($requestedAmount);
			$pendingPayment=$this->getPendingPayment($data,$limit,$offset);
			$respnse['data']['pending_payment']=$pendingPayment;
			//if(!is_array($pendingPayment))
			$respnse['data']['payment_msg']=$pendingPayment;
			$respnse['data']['success']=true;
			return $respnse;
		}
	}
	public function getPendingPayment($data,$limit,$offset){
		$pendingPayments = array();
		$vendorId = $data['vendor_id'];
		$filter = $data['filter'];
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
			
			$pendingPayments->getSelect ()->limit ( $limit, $offset );
			$pendingPayments = $this->filterRequestPayment($pendingPayments,$data['filter']);
			if(count($pendingPayments)>0){
				$payment=array();
				foreach ($pendingPayments as $_payment){
					//var_dump($_payment->getData());die;
					if($_payment->getCurrency()=='0.0000'){
						$payment[]=array(
							'created_at'=>Mage::helper('core')->formatDate( $_payment->getCreatedAt() , 'medium', true),
							'order_id'=>$_payment->getOrderId(),
							'pending_amount'=>Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol().''.$_payment->getNetAmount(),
							'payment_id'=>	$_payment->getId(),
							'action'=>$this->__('Request Payment')
						);
					}
					else{ 
						$model = Mage::getModel('csmarketplace/vpayment_requested')->loadByField(array('vendor_id','order_id'),array($vendorId,$_payment->getEntityId()));
						if($model && $model->getId()) {
							$statuses = Ced_CsMarketplace_Model_Vpayment_Requested::getStatuses();
							if(isset($statuses[$model->getStatus()])){
								$action=$statuses[$model->getStatus()];
							}else {
								$action=Mage::helper('csmarketplace')->__('In Processing');
							}
						} else {
							$action=Mage::helper('csmarketplace')->__('Request Payment');
						}
						$payment[]=array(
						'created_at'=>Mage::helper('core')->formatDate( $_payment->getCreatedAt() , 'medium', true),
						'order_id'=>$_payment->getOrderId(),
						'pending_amount'=>Mage::app()->getLocale()->currency($_payment->getCurrency())->toCurrency($_payment->getNetVendorEarn()),
						'payment_id'=> $_payment->getEntityId(),
						'action'=>$action,
						);
					}
				}
			}
			else{
				$payment='No Transactions Available';
			}
		}
		return $payment;
	}
	public function filterRequestPayment($payment,$filter)
	{
		$filter = json_decode($filter,true);
		$params = $filter;
		//Mage::log($params,null,'vfilter.log');

		if( isset($params) &&  is_array($params) && count($params)>0)
		{
			if(isset($params['increment_id'])){
				$params['order_id'] = $params['increment_id'];
				unset($params['increment_id']);
			}

			foreach($params as $field=>$value){
				if($field=="__SID")
					continue;
				//var_dump($value);echo '<hr>';
				if(is_array($value)){
					if(isset($value['from']) && $value['from']=""){
						$from = $value['from'];					
						if($field=='created_at'){
							$from=date("Y-m-d 00:00:00",strtotime($from));
						} 
						
						$payment->addFieldToFilter($field, array('gteq'=>$from));
					}
					if(isset($value['to'])  && $value['to']!=""){
						$to = $value['to'];					
						if($field=='created_at'){
							$to=date("Y-m-d 59:59:59",strtotime($to));
						}
						
						$payment->addFieldToFilter($field, array('lteq'=>$to));
					}
				}else if($value!=""){
					if($field == 'payment_method') {

						$payment->addFieldToFilter($field, array("in"=>Mage::helper('csmarketplace/acl')->getDefaultPaymentTypeValue($value)));
					} else {
						$payment->addFieldToFilter($field, array("like"=>'%'.$value.'%'));
					}
				}
			
			}
		}
		return $payment;		
	}
}