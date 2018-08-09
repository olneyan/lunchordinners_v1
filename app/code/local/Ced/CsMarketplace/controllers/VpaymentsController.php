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
 * @category    Ced;
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_CsMarketplace_VpaymentsController extends Ced_CsMarketplace_Controller_AbstractController {
	
	 /**
     * Check order view availability
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  bool
     */
    protected function _canViewPayment($payment)
    {
		if(!$this->_getSession()->getVendorId()) return;
		$vendorId = Mage::getSingleton('customer/session')->getVendorId();
		$paymentId = $payment->getId();
		
		
		$collection = Mage::getModel('csmarketplace/vpayment')->getCollection();
		$collection->addFieldToFilter('id', $paymentId)
					->addFieldToFilter('vendor_id', $vendorId);

		if(count($collection)>0){
			return true;
		}else{
			return false;
		}
    }
	
	/**
     * Try to load valid order by order_id and register it
     *
     * @param int $orderId
     * @return bool
     */
    protected function _loadValidPayment($paymentId = null)
    {
		if(!$this->_getSession()->getVendorId()) return;
        if (null === $paymentId) {
            $paymentId = (int) $this->getRequest()->getParam('payment_id');
        }
        if (!$paymentId) {
            $this->_forward('noRoute');
            return false;
        }
		$payment = Mage::getModel('csmarketplace/vpayment')->load($paymentId);
        
        if ($this->_canViewPayment($payment)) {
	        Mage::register('current_vpayment', $payment);
            return true;
        } else {
            $this->_redirect('*/*');
        }
        return false;
    }
	
	
	/**
	 * Default vendor products list page
	 */
	public function indexAction() {
		if(!$this->_getSession()->getVendorId()) return;
		$this->loadLayout ();
		$this->_initLayoutMessages ( 'customer/session' );
		$this->_initLayoutMessages ( 'catalog/session' );		
		$this->getLayout ()->getBlock ( 'head' )->setTitle ( Mage::helper('csmarketplace')->__ ( 'View Transactions' ) );
		$this->renderLayout ();
	}
	
	/**
	 * Payments view page
	 */
	public function viewAction() {
        if(!$this->_getSession()->getVendorId()) return;
		if (!$this->_loadValidPayment()) {
            return;
        }
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
				$this->getLayout ()->getBlock ( 'head' )->setTitle ( Mage::helper('csmarketplace')->__ ( 'Transaction Details' ) );

        $navigationBlock = $this->getLayout()->getBlock('csmarketplace_vendor_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('csmarketplace/vpayments/');
        }

        $this->renderLayout();
	}
	
	
	
	/**
     * Export Payment Action
     */
    public function exportCsvAction()
    {
		if(!$this->_getSession()->getVendorId()) return;
		$filename = 'vendor_transactions.csv';
        $content = Mage::helper('csmarketplace/payment')->getVendorCommision();
        $this->_prepareDownloadResponse($filename, $content);

	}
	
	
	
	/**
     * Print Order Action
     */
    public function filterAction()
    {
		if(!$this->_getSession()->getVendorId()) return;
		$reset_filter = $this->getRequest()->getParam('reset_filter');
		$params = $this->getRequest()->getParams();
		
		if($reset_filter==1)
			Mage::getSingleton('core/session')->uns('payment_filter');
		else if(!isset($params['p']) && !isset($params['limit']) &&  is_array($params) ){
			Mage::getSingleton('core/session')->setData('payment_filter',$params);
		 }

         $this->loadLayout();
         $this->renderLayout();
    }
	
	public function requestFilterAction(){
		if(!$this->_getSession()->getVendorId()) return;
		if(!Mage::getStoreConfig('ced_csmarketplace/general/request_active',Mage::app()->getStore()->getId())){
			$this->_redirect('csmarketplace/vendor/index');
			return;
		}
		$reset_filter = $this->getRequest()->getParam('reset_filter');
		$params = $this->getRequest()->getParams();
		
		if($reset_filter==1)
			Mage::getSingleton('core/session')->uns('payment_request_filter');
		else if(!isset($params['p']) && !isset($params['limit']) &&  is_array($params) ){
			Mage::getSingleton('core/session')->setData('payment_request_filter',$params);
		 }

        $this->loadLayout();
        $this->renderLayout();
	}
	
	public function requestAction(){
		if(!$this->_getSession()->getVendorId()) return;
		if(!Mage::getStoreConfig('ced_csmarketplace/general/request_active',Mage::app()->getStore()->getId())){
			$this->_redirect('csmarketplace/vendor/index');
			return;
		}
		$this->loadLayout ();
		$this->_initLayoutMessages ( 'customer/session' );
		$this->_initLayoutMessages ( 'catalog/session' );		
		$this->getLayout ()->getBlock ( 'head' )->setTitle ( Mage::helper('csmarketplace')->__ ( 'Request Payments' ) );
		$this->renderLayout ();
		
	}
	
	public function requestPostAction(){
		if(!$this->_getSession()->getVendorId()) return;
		if(!Mage::getStoreConfig('ced_csmarketplace/general/request_active',Mage::app()->getStore()->getId())){
			$this->_redirect('csmarketplace/vendor/index');
			return;
		}
		$orderIds = $this->getRequest()->getParam('payment_request');
		if(strlen($orderIds) > 0) {
			$orderIds = explode(',',$orderIds);
		}

		if (!is_array($orderIds)) {
            $this->_getSession()->addError(Mage::helper('csmarketplace')->__('Please select amount(s).'));
        } else {
            if (!empty($orderIds)) {
                try {
					$updated = 0;
                    foreach ($orderIds as $orderId) {
						$model = Mage::getModel('csmarketplace/vpayment_requested')->loadByField(array('vendor_id','order_id'),array($this->_getSession()->getVendorId(),$orderId));
						if($model && $model->getId()){
						} else {
							$amount = 0.000;
							$pendingPayments = $this->_getSession()->getVendor()->getAssociatedOrders()
													->addFieldToFilter('order_payment_state',array('eq'=>Mage_Sales_Model_Order_Invoice::STATE_PAID))
													->addFieldToFilter('payment_state',array('eq'=>Ced_CsMarketplace_Model_Vorders::STATE_OPEN))
													->addFieldToFilter('id',array('eq'=> $orderId))
													->addFieldToFilter('vendor_id',array('eq'=> $this->_getSession()->getVendorId()))
													->setOrder('created_at', 'ASC');
							$main_table=Mage::helper('csmarketplace')->getTableKey('main_table');
							$order_total=Mage::helper('csmarketplace')->getTableKey('order_total');
							$shop_commission_fee=Mage::helper('csmarketplace')->getTableKey('shop_commission_fee');
							$pendingPayments->getSelect()->columns(array('net_vendor_earn' => new Zend_Db_Expr("({$main_table}.{$order_total} - {$main_table}.{$shop_commission_fee})")));
							
							if(count($pendingPayments) > 0) {
								$amount = $pendingPayments->getFirstItem()->getNetVendorEarn();
							}
							$data = array('vendor_id'=>$this->_getSession()->getVendorId(), 'order_id'=>$orderId, 'amount'=>$amount, 'status'=>Ced_CsMarketplace_Model_Vpayment_Requested::PAYMENT_STATUS_REQUESTED,'created_at'=>Mage::getModel('core/date')->date('Y-m-d H:i:s'));
							Mage::getModel('csmarketplace/vpayment_requested')->addData($data)->save();
							$updated++;
						}
                    }
					if($updated) {
						$this->_getSession()->addSuccess(
							$this->__('Total of %d amount(s) have been requested for payment.', $updated)
						);
					} else {
						$this->_getSession()->addSuccess(
							$this->__('Payment(s) have been already requested for payment.')
						);
					}
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
	}


	public function printinvoiceAction() {
		
		$id = $this->getRequest()->getParams();
		$collection = Mage::getModel('csmarketplace/vpayment')->load($id);
		//print_r($collection->getData());die;
		$orderData = array_keys(json_decode($collection->getAmountDesc(),true));
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderData[0]);
		/*$inv_coll = Mage::getModel('csorder/invoice')->getCollection()->addFieldToFilter('invoice_order_id',$order->getEntityId())
					->addFieldToFilter('vendor_id',$collection->getVendorId())->getData();
		$invoice = array();
		foreach($inv_coll as $invoices){
			$invoice = $invoices['invoice_id'];
		}*/
		//print_r($inv_coll);die('in index.php');
		//Mage::getModel('csorder/invoice')->updateTotal($invoice);
		$pdf = Mage::getModel('csmarketplace/order_pdf_invoice')->getAdminToSellerPdf($id,$order);
		$this->_prepareDownloadResponse('invoice'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').
				'.pdf', $pdf->render(), 'application/pdf');
		
	}
}
