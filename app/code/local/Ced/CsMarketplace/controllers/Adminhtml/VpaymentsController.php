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
 
class Ced_CsMarketplace_Adminhtml_VpaymentsController extends Ced_CsMarketplace_Controller_Adminhtml_AbstractController
{
	protected function _initAction() {
		$this->loadLayout()
			 ->_setActiveMenu('csmarketplace/vendor/entity')
			 ->_addBreadcrumb(Mage::helper('csmarketplace')->__('Vendor'), Mage::helper('csmarketplace')->__('Vendor'));
		return $this;
	}
	
	/**
     * Show Payment to Vendor Grid
     */
	public function indexAction() {
		$this->_initAction();
		$this->getLayout()->getBlock('head')->setTitle(Mage::helper('csmarketplace')->__('Vendor Transactions'));
		$this->renderLayout();
	}
	
	/**
     * View transaction details action
     */
    public function detailsAction()
    {
        $rowId = $this->getRequest()->getParam('id');
        $row = Mage::getModel('csmarketplace/vpayment')->load($rowId);
        if (!$row->getId()) {
            $this->_redirect('*/*/',array('_secure' => true));
            return;
        }
		Mage::register('csmarketplace_current_transaction', $row);
		$this->_initAction()
			->_title($this->__('Transaction Details'))
			->_addContent($this->getLayout()->createBlock('csmarketplace/adminhtml_vpayments_details', 'csmarketplaceTransactionDetails'))
			->renderLayout();
    }
	
	/**
     * Payment Edit Form
     */
	public function editAction() {
			$this->loadLayout()->_setActiveMenu('csmarketplace/vpayments');
			$this->_addContent($this->getLayout()->createBlock('csmarketplace/adminhtml_vpayments_edit'))
				->_addLeft($this->getLayout()->createBlock('csmarketplace/adminhtml_vpayments_edit_tabs'));
			$this->renderLayout();
	}
	
	/**
     * New Payment edit form
     */
	public function newAction() {
		$this->_forward('edit');
	}
 	/**
     * Save payment form
     */
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$params = $this->getRequest()->getParams();
			Mage::dispatchEvent('transaction_save_before',array('data' => $data, 'param' => $params));
			$type = isset($params['type']) && in_array($params['type'],array_keys(Ced_CsMarketplace_Model_Vpayment::getStates()))?$params['type']:Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_CREDIT;
		
			$model = Mage::getModel('csmarketplace/vpayment');
			$amount_desc = isset($data['amount_desc'])?$data['amount_desc']:json_encode(array());
			$total_amount = json_decode($amount_desc);
			
			
			$baseCurrencyCode = Mage::app()->getBaseCurrencyCode();
			$allowedCurrencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies();
			$rates = Mage::getModel('directory/currency')->getCurrencyRates($baseCurrencyCode, array_values($allowedCurrencies));
			$data['base_to_global_rate'] = isset($data['currency'])&& isset($rates[$data['currency']]) ? $rates[$data['currency']] : 1;
			if($type == Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_DEBIT) {
				Mage::helper('csmarketplace')->logProcessedData($total_amount, Ced_CsMarketplace_Helper_Data::VPAYMENT_TOTAL_AMOUNT);
				$base_amount = 0;
				if(count($total_amount) > 0) {
					foreach($total_amount as $key=>$value) {
						$base_amount += $value;
					}
				}
				if($base_amount != $data['base_amount']) {
					Mage::getSingleton('adminhtml/session')->addError('Amount entered should be equal to the sum of all selected order(s)');
					$this->_redirect('*/*/edit', array('vendor_id' => $this->getRequest()->getParam('vendor_id'),'type'=>$type));
		        		return;
				}
			}
			else{
				Mage::helper('csmarketplace')->logProcessedData($total_amount, Ced_CsMarketplace_Helper_Data::VPAYMENT_TOTAL_AMOUNT);
				$orderArray = Mage::getSingleton('admin/session')->getVendorTransAmountOrder();
				$trans_sum = 0;
				$serviceable_amount = 0;
				foreach($orderArray['values'] as $info){
					foreach($orderArray['pricing_columns'] as $key){
						$price_valu = isset($info[$key])?$info[$key]:0;
						$trans_sum += $price_valu;
						if($price_valu < 0){
							$serviceable_amount += $price_valu;
						}
					}
				}
				$service_tax = round($serviceable_amount * $orderArray['service_tax'] / 100, 2);
				$base_amount = $trans_sum + $service_tax;
				$orderArray['service_tax_amount'] = $service_tax;
				if(strval($base_amount) != $data['base_amount']) {
					Mage::getSingleton('adminhtml/session')->addError('Amount entered should be equal to the sum of all selected order(s)');
					$this->_redirect('*/*/edit', array('vendor_id' => $this->getRequest()->getParam('vendor_id'),'type'=>$type));
		        		return;
				}
			}
			
			$data['transaction_type'] = $type;
			$data['payment_method'] = isset($data['payment_method'])?$data['payment_method']:0;
			/*Will use it when vendor will pay in different currenncy  */
			
			list($currentBalance,$currentBaseBalance) = $model->getCurrentBalance($data['vendor_id']);
			$base_net_amount = $data['base_amount'] + $data['base_fee'];	
			if($type == Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_DEBIT) {
				/* Case of Deduct credit */
				if($currentBaseBalance > 0) $newBaseBalance = $currentBaseBalance - $base_net_amount;
				else $newBaseBalance = $base_net_amount;
				$base_net_amount = -$base_net_amount;
				if(-$base_net_amount <= 0.00) {
					Mage::getSingleton('adminhtml/session')->addError("Refund Net Amount can't be less than zero");
					$this->_redirect('*/*/edit', array('vendor_id' => $this->getRequest()->getParam('vendor_id'),'type'=>$type));
					return;
				}
			} else {
				$data['trans_credit_summary']= json_encode($orderArray);
				// Case of Add credit 
				$newBaseBalance = $currentBaseBalance + $base_net_amount;
				if($base_net_amount <= 0.00) {
					Mage::getSingleton('adminhtml/session')->addError("Net Amount can't be less than zero");
					$this->_redirect('*/*/edit', array('vendor_id' => $this->getRequest()->getParam('vendor_id'),'type'=>$type));
					return;
				}
			}	
			
			$data['base_currency']= $baseCurrencyCode;
			$data['base_net_amount'] = $base_net_amount;
			$data['base_balance'] = $newBaseBalance;
			
			$data['amount'] = $base_amount*$data['base_to_global_rate'];
			$data['balance'] = Mage::helper('directory')->currencyConvert($newBaseBalance, $baseCurrencyCode, $data['currency']);
			$data['fee'] = Mage::helper('directory')->currencyConvert($data['base_fee'], $baseCurrencyCode, $data['currency']);
			$data['net_amount'] = Mage::helper('directory')->currencyConvert($base_net_amount, $baseCurrencyCode, $data['currency']);
			
			$data['tax'] = 0.00;
			$data['payment_detail'] = isset($data['payment_detail'])?$data['payment_detail']:'n/a';
			
			$model->addData($data);
			$openStatus = $model->getOpenStatus();
			$model->setStatus($openStatus);
			try {
				$model->saveOrders($data);
				$model->save();
				Mage::helper('csmarketplace/mail')->sendSellerTransactionEmail($model);
				Mage::helper('csmarketplace')->logProcessedData($model->getData(), Ced_CsMarketplace_Helper_Data::VPAYMENT_CREATE);

				Mage::app()->cleanCache();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('csmarketplace')->__('Payment is  successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
				Mage::helper('csmarketplace')->logException($e);
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('csmarketplace')->__('Unable to find vendor to save'));
        $this->_redirect('*/*/');
	}
	
	public function addOrdersAction()
	{
		$this->getResponse()->setBody($this->getLayout()->createBlock('csmarketplace/adminhtml_vpayments_edit_tab_addorder')->getAddOrderBlock()); 
	}
	
	public function loadBlockAction()
    {	
        $request = $this->getRequest();
        $asJson= $request->getParam('json');
        $block = $request->getParam('block');

        $update = $this->getLayout()->getUpdate();
        if ($asJson) {
            $update->addHandle('adminhtml_sales_order_create_load_block_json');
        } else {
            $update->addHandle('adminhtml_sales_order_create_load_block_plain');
        }

        if ($block) {
            $blocks = explode(',', $block);
            if ($asJson && !in_array('message', $blocks)) {
                $blocks[] = 'message';
            }

            foreach ($blocks as $block) {
                $update->addHandle('adminhtml_sales_order_create_load_block_' . $block);
            }
        }
        $this->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();
        $result = $this->getLayout()->createBlock('csmarketplace/adminhtml_vpayments_edit_tab_addorder_search_grid')->toHtml();
        if ($request->getParam('as_js_varname')) {
            Mage::getSingleton('adminhtml/session')->setUpdateResult($result);
            $this->_redirect('*/*/showUpdateResult');
        } else {
            $this->getResponse()->setBody($result);
        }
    }
	
	public function getdetailAction() {
		$details = array();
		$method = $this->getRequest()->getParam('method','');
		$vendorId = $this->getRequest()->getParam('vendor_id',0);
		$paymentMethods = Mage::getModel('csmarketplace/vendor')->getPaymentMethods($vendorId);
		$method = isset($paymentMethods[$method]) ? $paymentMethods[$method] : '';
		
		if(!is_object($method)) return;
		$vendor_id=Mage::helper('csmarketplace')->getTableKey('vendor_id');
		$group=Mage::helper('csmarketplace')->getTableKey('group');
		$key_tmp=Mage::helper('csmarketplace')->getTableKey('key');
		$key = strtolower(Ced_CsMarketplace_Model_Vsettings::PAYMENT_SECTION.'/'.$method->getCode().'/');
		$settings = Mage::getModel('csmarketplace/vsettings')
						->getCollection()
						->addFieldToFilter($vendor_id,array('eq'=>$vendorId))
						->addFieldToFilter($group,array('eq'=>Ced_CsMarketplace_Model_Vsettings::PAYMENT_SECTION))
						->addFieldToFilter($key_tmp,array('like'=>$key.'%'));
						
		if(count($settings) > 0) {
			foreach($settings as $setting) {
				$key = explode('/',$setting->getKey());
				$key = end($key);
				if($key == 'active' && $setting->getValue()) {
					continue;
				} else if ($key == 'active' && !$setting->getValue()) {
					return;
				} else {
					$details[] = array('value'=>$setting->getValue(),'label'=>$method->getLabel($key));
				}
			}
		}
		$this->getResponse()->setBody($this->getLayout()->createBlock('csmarketplace/adminhtml_vpayments_edit_tab_paymentinformation_view')->setDetails($details)->toHtml());
	}
	
	/**
	 * Ajax Grid Action
	 */
	public function gridAction() {
		$this->loadLayout()->_setActiveMenu('csmarketplace/vpayments');
		$this->renderLayout();
	}


	public function validateAction(){

		$response       = new Varien_Object();
        $response->setError(0);
        
        $transactionId = $this->getRequest()->getPost('transaction_id');
		$collection = Mage::getModel('csmarketplace/vpayment')->getCollection();
		$collection->addFieldToFilter('transaction_id', $transactionId);
		if(count($collection)>0){
			$this->_getSession()->addError(
                    Mage::helper('csmarketplace')->__('Transaction Id must be unique.')
                );
           $response->setError(1);
		}
		if ($response->getError()) {
            $this->_initLayoutMessages('adminhtml/session');
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());

	}
}