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

class Ced_VendorApi_Model_Order_Api extends Mage_Api_Model_Resource_Abstract
{	
	protected $vordersid=null;
	public function model()
	{
		return Mage::getResourceModel('vendorapi/vapisession');
	}
	
	public function items($vendorId,$offset,$filters = null)
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
		$curr_page = 1;
		$limit = '10';
		if ($offset != 'null' && $offset != '') {
			
			$curr_page = $offset;
		}
		$offset = ($curr_page - 1) * $limit;
		$filters=json_decode($filters,true);
		$vendor  = Mage::getModel('csmarketplace/vendor')->load($vendorId);
		if($vendor && $vendor->getId()) {
		$statusArray=Mage::getModel('sales/order_invoice')->getStates();
		$paymentarray=Mage::getModel('csmarketplace/vorders')->getStates();
		//var_dump($statusArray);echo '<hr>';
			$ordersCollection = $vendor->getAssociatedOrders()->setOrder('id', 'DESC');
			$ordersCollection->getSelect()->columns(array('net_vendor_earn' => new Zend_Db_Expr('(`main_table`.`order_total` - `main_table`.`shop_commission_fee`)')));	
			$orderCount = $ordersCollection->getSize();
		 	$ordersCollection->getSelect ()->limit ($limit, $offset);
			$filterCollection = $this->filterOrders($ordersCollection, $filters,$statusArray,$paymentarray)->getData();
			$count=0;
			foreach ($filterCollection as $key => $value) {
				$ordetStatus=($value['order_payment_state']!=''? $statusArray[$value['order_payment_state']] :'');
				$vendorpaymentstatus=($value['payment_state']!=''? $statusArray[$value['payment_state']] :'');
				$filterCollection[$count]['order_payment_state']=$ordetStatus;
				$filterCollection[$count]['payment_state']=$vendorpaymentstatus;
				$filterCollection[$count]['order_total'] = Mage::app()->getLocale()->currency($value['order_currency_code'])->toCurrency($value['order_total']);
				$filterCollection[$count]['created_at'] = Mage::helper('core')->formatDate( $value['created_at'] , 'medium', true);
				$filterCollection[$count]['shop_commission_fee'] = Mage::app()->getLocale()->currency($value['order_currency_code'])->toCurrency($value['shop_commission_fee']);
				$filterCollection[$count]['net_vendor_earn'] = Mage::app()->getLocale()->currency($value['order_currency_code'])->toCurrency($value['net_vendor_earn']);
				++$count;
			}
			if(count($filterCollection)>0){
				$data=array(
					'data'=>array(
                    'orderdata'=>$filterCollection,
                    'count'=>$orderCount,
                    'success'=>true
                    )   
                );	
			}
			else {
				$data = 'NO_ORDER';
			}
		}
		else{
			$data = array (
					'data' => array (
						'success' => false ,
						'message'=> 'You dont have any order yet'
								
							
					) 
				);
		}
		return $data;
	}
	
	public function filterOrders($ordersCollection, $filters= null,$statusArray,$paymentarray)
	{
		$statusArray = array_flip($statusArray);
		$paymentarray = array_flip($paymentarray);

		if(isset($filters) && is_array($filters)){
			$filters['order_payment_state'] = (isset($filters['order_payment_state']) && $filters['order_payment_state']) ? $statusArray[$filters['order_payment_state']]:'';
			
			$filters['payment_state'] =(isset($filters['payment_state']) && $filters['payment_state']) ?$paymentarray[$filters['payment_state']]:'';
		}

		//$params = Mage::getSingleton('core/session')->getData('order_filter');
		//var_dump($ordersCollection->getData());die;
		$params = $filters;
		if(is_array($params) && count($params)>0){

			foreach($params as $field=>$value){
				if($field=='__SID')
					continue;
				if(is_array($value)){
					if(isset($value['from']) && $value['from']!=""){
						$from = $value['from'];
						if($field=='created_at'){
							$from=date("Y-m-d 00:00:00",strtotime($from));
						}
						if($field=='net_vendor_earn')
							$ordersCollection->getSelect()->where("(`main_table`.`order_total`- `main_table`.`shop_commission_fee`) >='".$from."'");
						else
							$ordersCollection->addFieldToFilter($field, array('gteq'=>$from));
					}
					if(isset($value['to'])  && $value['to']!=""){
						$to = $value['to'];
						if($field=='created_at'){
							$to=date("Y-m-d 59:59:59",strtotime($to));
						}
						if($field=='net_vendor_earn')
							$ordersCollection->getSelect()->where("(`main_table`.`order_total`- `main_table`.`shop_commission_fee`) <='".$to."'");
						else
							$ordersCollection->addFieldToFilter($field, array('lteq'=>$to));
					}
				}else if($value!=""){
					$ordersCollection->addFieldToFilter($field, array("like"=>'%'.$value.'%'));
				}
	
			}
		}
		
		return $ordersCollection;
	}
	 public function formatDate($date = null, $format = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, $showTime = false)
    {
        return Mage::helper('core')->formatDate($date, $format, $showTime);
    }
  
	public function info($vendorId, $vsession, $orderIncrementId)
	{
		$orderDetail=array();
		if($vendorId==null) {
			$data = array (
					'data' => array (
							'success' => false ,
							'message'=> 'Vendor ID Is Empty'
							) 
					) ;
			return $data;
		}
		$orderIds = array();
		$vordersid=array();
		if ($vendorId) {
			$vendor  = Mage::getModel('csmarketplace/vendor')->load($vendorId);
			if($vendor && $vendor->getId()) {
				$ordersCollection = $vendor->getAssociatedOrders()->setOrder('id', 'DESC')->getData();
				foreach ($ordersCollection as $key=>$order) {
					$orderIds[$order['increment_id']] = $order['entity_id'];
					$vordersid[$order['increment_id']] = $order['id'];
				}
			}
			if(array_key_exists($orderIncrementId, $orderIds)){
				$order=Mage::getModel('sales/order')->load($orderIds[$orderIncrementId]);
				Mage::register('current_order',$order);
				$vid=$vordersid[$orderIncrementId];
				$orderDate = $this->formatDate($order->getCreatedAtStoreDate(), 'long');
        		$orderDetail['orderdate']=$orderDate;
        		$orderDetail['ordertitle']='Order #'.$orderIncrementId.' - '.$order->getStatusLabel();
        		$storeId = $order->getStoreId();
    			$store = Mage::app()->getStore($storeId);
	    		$name = array(
	    				$store->getWebsite()->getName(),
	    				$store->getGroup()->getName(),
	    				$store->getName()
	    		);
    			$name =implode(' ', $name);
        		$orderDetail['purchasedfrom']=$name;
        		$orderDetail['order_status']=$order->getStatusLabel();
        		if (!$order->getIsVirtual()) {
            		$shipping=$order->getShippingAddress();
            		$country=Mage::app()->getLocale()->getCountryTranslation($shipping->getCountryId());
            		$street=implode(" ",$shipping->getStreet());
            		$orderDetail['ship_to']=$shipping->getFirstname().' '.$shipping->getLastname();
            		$orderDetail['street']=$street;
            		$orderDetail['city']=$shipping->getCity();
            		$orderDetail['state']=$shipping->getRegion();
            		$orderDetail['pincode']=$shipping->getPostcode();
            		$orderDetail['country']=$country;
            		$orderDetail['mobile']=$shipping->getTelephone();
		            if ($order->getShippingDescription()) {
		                $orderDetail['shipping_method'] = $order->getShippingDescription();
		            } else {
		                $orderDetail['shipping_method'] = Mage::helper('sales')->__('No shipping information available');
		            }
        		}
        		$billing=$order->getBillingAddress();
            	$country=Mage::app()->getLocale()->getCountryTranslation($billing->getCountryId());
            	$street=implode(" ",$billing->getStreet());
            	$orderDetail['bill_to']=$billing->getFirstname().' '.$billing->getLastname();
            	$orderDetail['bill_street']=$street;
            	$orderDetail['bill_city']=$billing->getCity();
            	$orderDetail['bill_state']=$billing->getRegion();
            	$orderDetail['bill_pincode']=$billing->getPostcode();
            	$orderDetail['bill_country']=$country;
            	$orderDetail['bill_mobile']=$billing->getTelephone();  
        		$orderDetail= $this->_addPaymentMethodInfo($orderDetail,$order);
        		$orderDetail= $this->getOrderItems($orderDetail,$vid,$order);	
       			$orderDetail['shipping']=$order->getOrderCurrency()->formatPrecision($order->getShippingAmount(), 2, array(), false, false);
        		$orderDetail['tax_amount']=$order->getOrderCurrency()->formatPrecision($order->getTaxAmount(), 2, array(), false, false);
       			$data = array (
					'data' => array (

							'ordetail'=>array($orderDetail),
							'success' => true ,
							) 
					) ;
				return $data;
			}
			else{
				$data = array (
						'data' => array (

								'message'=>Mage::helper('vendorapi')->__('This Order doesnt belong to you.Please login with appropriate vendor to view order details'),
								'success' => false ,
								) 
						) ;
				return $data; 
			}
		}
		if($vendorId==null) {
			$data = array (
					'data' => array (
							'success' => false ,
							'message'=> 'Vendor ID Is Empty'
							) 
					) ;
			return $data;
		}
	}
	 public function displayPrices($basePrice, $price, $strong = false, $separator = '<br />')
    {
        return $this->displayRoundedPrices($basePrice, $price, 2, $strong, $separator);
    }

    /**
     * Display base and regular prices with specified rounding precision
     *
     * @param   float $basePrice
     * @param   float $price
     * @param   int $precision
     * @param   bool $strong
     * @param   string $separator
     * @return  string
     */
    public function getOrder(){
    	return Mage::registry('current_order');
    }
    public function displayRoundedPrices($basePrice, $price, $precision=2, $strong = false, $separator = '<br />')
    {
        if ($this->getOrder()->isCurrencyDifferent()) {
            $res = '';
            $res.= $this->getOrder()->formatBasePricePrecision($basePrice, $precision);
            $res.= $separator;
            $res.= $this->getOrder()->formatPricePrecision($price, $precision, true);
        }
        else {
            $res = $this->getOrder()->formatPricePrecision($price, $precision);
            if ($strong) {
                $res = '<strong>'.$res.'</strong>';
            }
        }
        return $res;
    }
	public function getOrderItems($orderDetail,$vorderid,$order){
        
        $vorder = Mage::getModel('csmarketplace/vorders')->load($vorderid);
 		$orderItems = $vorder->getItemsCollection(); 
        foreach ($orderItems as $item) {
            $options = array();
            if ($item->getProductOptions()) {
                $options = $this->getitemsOptions($item->getProductType(), $item->getProductOptions());
            }
           
            $product_id = $item->getProductId();
            if($product_id){
                $product=Mage::getModel('catalog/product')->load($product_id);
                $image= Mage::helper ( 'catalog/image' )->init ( $product, 'small_image' )->constrainOnly ( TRUE )->keepAspectRatio ( TRUE )->keepFrame ( FALSE )->resize ( 600, 600 )->__toString () ;
            }
            if(count($options)>0){
            $orderedProduct[] = array(
                'product_id' => $product_id,
                'product_name' => $item->getName(),
                'product_sku'  => $item->getSku(),
                'product_price' => $order->getOrderCurrency()->formatPrecision($item->getPrice(), 2, array(), false, false),
                'original_price' => $order->getOrderCurrency()->formatPrecision($item->getOriginalPrice(), 2, array(), false, false),
                'tax_amount' => $order->getOrderCurrency()->formatPrecision($item->getTaxAmount(), 2, array(), false, false),
                'discount_amount' => $order->getOrderCurrency()->formatPrecision($item->getDiscountAmount(), 2, array(), false, false),
                'product_image' => $image,
                'product_qty' => floatval($item->getQtyOrdered()),
                'option'=>$options,
                'rowsubtotal'=>$order->getOrderCurrency()->formatPrecision($item->getRowTotal(), 2, array(), false, false),
                'row_total'=>strip_tags($this->displayPrices($item->getBaseRowTotal() - $item->getBaseDiscountAmount() + $item->getBaseTaxAmount() + $item->getBaseWeeeTaxAppliedRowAmount(),
            $item->getRowTotal() - $item->getDiscountAmount() + $item->getTaxAmount() + $item->getWeeeTaxAppliedRowAmount()))
            );
            
            }
            else{
                $orderedProduct[] = array(
                'product_id' => $product_id,
                'product_name' => $item->getName(),
                'product_sku'  => $item->getSku(),
                'product_price' => $order->getOrderCurrency()->formatPrecision($item->getPrice(), 2, array(), false, false),
                'original_price' => $order->getOrderCurrency()->formatPrecision($item->getOriginalPrice(), 2, array(), false, false),
                'tax_amount' => $order->getOrderCurrency()->formatPrecision($item->getTaxAmount(), 2, array(), false, false),
                'discount_amount' => $order->getOrderCurrency()->formatPrecision($item->getDiscountAmount(), 2, array(), false, false),
                'product_image' => $image,
                'product_qty' => floatval($item->getQtyOrdered()),
                'rowsubtotal'=>$order->getOrderCurrency()->formatPrecision($item->getRowTotal(), 2, array(), false, false), 
                 'row_total'=>strip_tags($this->displayPrices($item->getBaseRowTotal() - $item->getBaseDiscountAmount() + $item->getBaseTaxAmount() + $item->getBaseWeeeTaxAppliedRowAmount(),
            $item->getRowTotal() - $item->getDiscountAmount() + $item->getTaxAmount() + $item->getWeeeTaxAppliedRowAmount()))
            );
            }
                    
        }
       $orderDetail['ordered_items']=$orderedProduct;
       $orderDetail['subtotal']=$order->getOrderCurrency()->formatPrecision($vorder->getPurchaseSubtotal(), 2, array(), false, false);
       $orderDetail['discount']=$order->getOrderCurrency()->formatPrecision($vorder->getPurchaseDiscountAmount(), 2, array(), false, false);
       	$orderDetail['grandtotal']=$order->getOrderCurrency()->formatPrecision($vorder->getPurchaseGrandTotal(), 2, array(), false, false);
       $orderDetail['grandtotal_earned']=Mage::app()->getLocale()->currency($vorder->getCurrency())->toCurrency($vorder->getOrderTotal());
       $orderDetail['commision_fee']=Mage::app()->getLocale()->currency($vorder->getCurrency())->toCurrency($vorder->getShopCommissionFee());
       $orderDetail['net_earned']=Mage::app()->getLocale()->currency($vorder->getCurrency())->toCurrency($vorder->getOrderTotal()-$vorder->getShopCommissionFee());
       return $orderDetail;
    }

    public function getitemsOptions($type, $options,$orderedProduct = array()) {
        $productoption = array();
        if ($type == 'bundle') {
            foreach ($options['bundle_options'] as $option) {
                foreach ($option['value'] as $value) {
                    $list[] = array(
                        'option_title' => $option['label'],
                        'option_value' => $value['title'],
                        'option_price' => $value['price'],
                    );
                }
            }
        } else {
            
            $optionsList = array();
            if (isset($options['additional_options'])) {
                $optionsList = $options['additional_options'];
            } elseif (isset($options['attributes_info'])) { 
                $optionsList = $options['attributes_info'];
            } elseif (isset($options['options'])) {
                $optionsList = $options['options'];
            }
           
            foreach ($optionsList as $option) { 
                $productoption[] = array(
                    'option_title' => $option['label'],
                    'option_value' => $option['value'],
                    'option_price' => isset($option['price']) == true ? $option['price'] : 0,
                );
            }
        }
        
        return $productoption;
    
    }
	protected function _addPaymentMethodInfo($orderDetail,$order){
    	$method = Mage::helper('payment')->getInfoBlock($order->getPayment())->getMethod();
        $payment = $order->getPayment();
         $orderDetail['method_code'] = $method->getCode();
         $orderDetail['method_title'] = $method->getTitle();
        //Get card type
         $orderDetail['credit_card_type']=$payment->getData('cc_type');
         $orderDetail['credit_card_number']=$payment->getData('cc_last4');
         $orderDetail['name_on_card']=$payment->getData('cc_owner');
		return $orderDetail;
    }

	public function payments($vendorId, $vsession, $filters=null)
	{
		if($vendorId==null || $vsession==null) {
			$this->_fault('data_invalid');
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