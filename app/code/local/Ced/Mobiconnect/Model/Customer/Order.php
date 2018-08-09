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
  * @category  Ced
  * @package   Ced_Mobiconnect
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobiconnect_Model_Customer_Order extends Mage_Core_Model_Abstract {
	/**
	 * Retrieve customer session model object
	 *
	 * @return Mage_Customer_Model_Session
	 */
	 
	protected function _getSession() {
		return Mage::getSingleton ( 'customer/session' );
	}
    public function formatDate($date = null, $format = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, $showTime = false)
    {
        return $this->helper('core')->formatDate($date, $format, $showTime);
    }
    public function helper($name)
    {
        if ($this->getLayout()) {
            return $this->getLayout()->helper($name);
        }
        return Mage::helper($name);
    }
	public function getCustomerOrder($data){ 
    $offset = $data ['offset'];
    $limit = '5';
    $curr_page = 1;
    //$filtername = $data ['filtername'];
    //$filterid = $data ['filterid'];
    
    if ($offset != 'null' && $offset != '') {
      
      $curr_page = $offset;
    }

    //$offset = ($curr_page - 1) * $limit;
            $orders = Mage::getResourceModel('sales/order_collection')->addFieldToSelect('*')->addFieldToFilter(
            'customer_id', $data['customer']
        )->addFieldToFilter('state', array(
            'in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()
        ))->setOrder('created_at', 'desc');
        
        //$orderregister=Mage::Registry();
      
       $orders->setCurPage($curr_page)->setPageSize($limit);

       $ordercollection = Mage::getResourceModel('sales/order_collection')->addFieldToSelect('*')->addFieldToFilter(
            'customer_id', $data['customer']
        )->addFieldToFilter('state', array(
            'in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()
        ))->setOrder('created_at', 'desc');
        $new= ceil(count($ordercollection)/$limit);
        
        if ($orders->count() && $curr_page<=$new) {
            foreach ($orders as $order) {
                 if ($order->getShippingAddress()) { 
                    $orderData[]=array(
                    'order_id' => $order->getId(),
                    'number' => $order->getRealOrderId(),
                    'date' => $this->formatDate($order->getCreatedAtStoreDate()),
                    'total_amount' => $order->getOrderCurrency()->formatPrecision($order->getGrandTotal(), 2, array(), false, false),
                    'order_status'=>$order->getStatusLabel(),
                    'ship_to'=>$order->getShippingAddress()->getName(),
            );
                }
                else{
                    $orderData[]=array(
                    'order_id' => $order->getId(),
                    'number' => $order->getRealOrderId(),
                    'date' => $this->formatDate($order->getCreatedAtStoreDate()),
                    'total_amount' => $order->getOrderCurrency()->formatPrecision($order->getGrandTotal(), 2, array(), false, false),
                    'order_status'=>$order->getStatusLabel(),
                    //'ship_to'=>$order->getShippingAddress()->getName(),
                    );
                }
                 
        }
   
            $data=array('data'=>array(
                    
                        'orderdata'=>$orderData,
                        'status'=>'success'
                    )   
                    );
        }
        else{
            $data=array('data'=>array(
                    
                        'message'=>'You have placed no orders.',
                        'status'=>'no_order'
                    )   
                    );
        }
        
        return $data;
    }
    public function getOrderDetail($data){


        $orderDetail=array();
        $order = $this->_getOrder();
       // $tree=$order->getBillingAddress();
       //var_dump($tree->getData());die;
        $orderDate = $this->formatDate($order->getCreatedAtStoreDate(), 'long');
        $orderDetail['orderdate']=$orderDate;
        $orderDetail['orderlabel']='Order #'.$order->getRealOrderId().'-'. $order->getStatusLabel();
        //$billing  = Mage::helper('mobiconnect')->trimLineBreaks($order->getBillingAddress()->format('text'));
        //var_dump($billing);die;
        //$orderDetail['billing_address']=$billing;
        if (!$order->getIsVirtual()) {
            //$shipping = Mage::helper('mobiconnect')->trimLineBreaks($order->getShippingAddress()->format('text'));
             //$orderDetail['shipping_address']=$shipping;
            //$shipping  = Mage::helper('mobiconnect')->trimLineBreaks($order->getBillingAddress()->format('text'));
            $shipping=$order->getShippingAddress();
            $country=Mage::app()->getLocale()->getCountryTranslation($shipping->getCountryId());
            $street=implode(" ",$shipping->getStreet());
            /*$shippingdata=array(
                'ship_to'=>$shipping->getFirstname().' '.$shipping->getLastname(),
                'street'=>$street,
                'city'=>$shipping->getCity(),
                'state'=>$shipping->getRegion(),
                'pincode'=>$shipping->getPostcode(),
                'country'=>$country,
                'mobile'=>$shipping->getTelephone()
                );*/
            $orderDetail['ship_to']=$shipping->getFirstname().' '.$shipping->getLastname();
            $orderDetail['street']=$street;
            $orderDetail['city']=$shipping->getCity();
            $orderDetail['state']=$shipping->getRegion();
            $orderDetail['pincode']=$shipping->getPostcode();
            $orderDetail['country']=$country;
            $orderDetail['mobile']=$shipping->getTelephone();
               // $orderDetail['shipping_address']=$shippingdata;
            if ($order->getShippingDescription()) {
                $orderDetail['shipping_method'] = $order->getShippingDescription();
            } else {
                $orderDetail['shipping_method'] = Mage::helper('sales')->__('No shipping information available');
            }
           
        }
       $orderDetail= $this->_addPaymentMethodInfo($orderDetail);
       $orderDetail= $this->getOrderItems($orderDetail);
       $orderDetail['subtotal']=$order->getOrderCurrency()->formatPrecision($order->getSubtotal(), 2, array(), false, false);
       $orderDetail['shipping']=$order->getOrderCurrency()->formatPrecision($order->getShippingAmount(), 2, array(), false, false);
        
        $orderDetail['tax_amount']=$order->getOrderCurrency()->formatPrecision($order->getTaxAmount(), 2, array(), false, false);
       $orderDetail['discount']=$order->getOrderCurrency()->formatPrecision($order->getDiscountAmount(), 2, array(), false, false);
       $orderDetail['grandtotal']=$order->getOrderCurrency()->formatPrecision($order->getGrandTotal(), 2, array(), false, false);
       $orderDetail=$this->getGiftDetail($orderDetail);
       $data=array(
                'data'=>array(
                    'orderview'=>array(
                        $orderDetail
                        )
                    )
        );
       return $data;
    }
    protected function _getOrder()
    {
        $order = Mage::registry('current_order');
        if (!($order instanceof Mage_Sales_Model_Order)) {
            Mage::throwException($this->__('Order is not available.'));
        }
        return $order;
    }

    protected function _addPaymentMethodInfo($orderDetail)
    {
        $order = $this->_getOrder();
       
        // TODO: it's need to create an info blocks for other payment methods (including Enterprise)
        $method = $this->helper('payment')->getInfoBlock($order->getPayment())->getMethod();
       
        $payment = $order->getPayment();
         $orderDetail['method_code'] = $method->getCode();
         $orderDetail['method_title'] = $method->getTitle();
        //Get card type
         $orderDetail['credit_card_type']=$payment->getData('cc_type');
         $orderDetail['credit_card_number']=$payment->getData('cc_last4');
         $orderDetail['name_on_card']=$payment->getData('cc_owner');
        
        /*$block = new Mage_Payment_Block_Info();
        $block->setInfo($order->getPayment());
      
            $specificInfo = array_merge(
                (array)$order->getPayment()->getAdditionalInformation(),
                (array)$block->getSpecificInformation()
            );
         
            if (!empty($specificInfo)) {
                foreach ($specificInfo as $label => $value) {
                    if ($value) {
                      $tree[]=  implode($block->getValueAsArray($value, true));
                    }
                }
              
            }*/
         

        return $orderDetail;
    }

    public function getOrderItems($orderDetail){
         $order = $this->_getOrder();

         $orderItems = $order->getAllVisibleItems();
        foreach ($orderItems as $item) {

            $options = array();
            if ($item->getProductOptions()) { 
                //echo '<hr><pre>';print_r($item->getProductOptions());
                $options = $this->getitemsOptions($item->getProductType(), $item->getProductOptions(),$item->getProductId());
            }
           
            $product_id = $item->getProductId();
            if($product_id){
                $product=Mage::getModel('catalog/product')->load($product_id);
                $image= Mage::helper ( 'catalog/image' )->init ( $product, 'small_image' )->constrainOnly ( TRUE )->keepAspectRatio ( TRUE )->keepFrame ( FALSE )->resize ( 600, 600 )->__toString () ;
            }
            if(count($options)>0){
            $orderedProduct[] = array(
                'product_id' => $product_id,
                'product_type'=>$product->getTypeId(),
                'product_name' => $item->getName(),
                'product_price' => $order->getOrderCurrency()->formatPrecision($item->getPrice(), 2, array(), false, false),
                'product_image' => $image,
                'product_qty' => floatval($item->getQtyOrdered()),
                'option'=>$options,
                'rowsubtotal'=>$order->getOrderCurrency()->formatPrecision($item->getRowTotal(), 2, array(), false, false),
                'item-gift-detail'=>$this->getitemGiftDetail($item)
            );
            
            }
            else{
                $orderedProduct[] = array(
                'product_id' => $product_id,
                'product_name' => $item->getName(),
                'product_price' => $order->getOrderCurrency()->formatPrecision($item->getPrice(), 2, array(), false, false),
                'product_image' => $image,
               'product_type'=>$product->getTypeId(),
                'product_qty' => floatval($item->getQtyOrdered()),
                'rowsubtotal'=>$order->getOrderCurrency()->formatPrecision($item->getRowTotal(), 2, array(), false, false),
                //'option'=>$options
                'item-gift-detail'=>$this->getitemGiftDetail($item)
                
            );
            }
                    
        }
       $orderDetail['ordered_items']=$orderedProduct;
       return $orderDetail;
    }

    public function getitemsOptions($type, $options,$productId) {
        $productoption = array();
        if ($type == 'bundle') {
            foreach ($options['bundle_options'] as $option) {
                foreach ($option['value'] as $value) {
                    $productoption[] = array(
                        'option_title' => $option['label'],
                        'option_value' => $value['title'],
                        'option_qty'   => $value['qty'],
                        'option_price' => $value['price'],
                    );
                }
            }
        }
        else if($type =='downloadable'){ 
            foreach ($options['links'] as $option) { 
                $links=Mage::getModel('downloadable/link')->getCollection()->addTitleToResult()->addFieldToFilter('main_table.link_id',array('eq'=>$option));
                    $productoption[] = array(
                        'link_title' => $links->getFirstItem()->getDefaultTitle(),
                    );
            }
        }
         else {
            
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
    public function getGiftDetail($orderDetail){
        $message = Mage::getModel('giftmessage/message');
        $order = $this->_getOrder();
        /* Add Gift Message*/
        $gift_message_id = $order->getGiftMessageId();
        if(!is_null($gift_message_id)) {
            $message->load((int)$gift_message_id);
            $gift_sender = $message->getData('sender');
            $gift_recipient = $message->getData('recipient');
            $gift_message = $message->getData('message');
            if(isset($gift_sender) && $gift_sender!='' || isset($gift_message) && $gift_message!='' || isset($gift_recipient) && $gift_recipient!=''){
                $orderDetail['gift_sender']=$gift_sender;
                $orderDetail['gift_recipient']=$gift_recipient;
                $orderDetail['gift_message']=$gift_message;
            }
       
        }
        return $orderDetail;
   } 
   public function getitemGiftDetail($item){
        $message = Mage::getModel('giftmessage/message');
       
        /* Add Gift Message*/
        $gift_message_id = $item->getGiftMessageId();
        $orderDetail = array();
        if(!is_null($gift_message_id)) {
            $message->load((int)$gift_message_id);
            $gift_sender = $message->getData('sender');
            $gift_recipient = $message->getData('recipient');
            $gift_message = $message->getData('message');
            if(isset($gift_sender) && $gift_sender!='' || isset($gift_message) && $gift_message!='' || isset($gift_recipient) && $gift_recipient!=''){
                $orderDetail['gift_sender']=$gift_sender;
                $orderDetail['gift_recipient']=$gift_recipient;
                $orderDetail['gift_message']=$gift_message;
            }
       
        }
        return $orderDetail;
   } 
}
?>
	
