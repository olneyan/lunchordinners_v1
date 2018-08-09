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
  * @package   Ced_CustomApi
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_CustomApi_Model_Checkoutmobi extends Mage_Core_Model_Abstract {
    /**
    * Initialize quote on basis of customer data
    **/
    function _getQuotemobi($data)
    {
        if(isset($data['customer_id']) && $data['customer_id']){
            $customer_id = $data['customer_id'];
            try{
                   $session = Mage::getSingleton('customer/session');
                   $customer = Mage::getModel('customer/customer')->load($customer_id);
                   $session->setCustomerAsLoggedIn($customer);
                   $quote = Mage::getModel('sales/quote')->loadByCustomer($customer);
                   return $quote;
            }catch (Exception $e){
                die($e->getMessage());
            }
        } elseif (isset($data['cart_id']) &&  $data['cart_id']){
                    $cart_id=$data['cart_id'];
                    $store=Mage::app()->getStore();  
                    $quote = Mage::getModel('sales/quote')->setStore($store)->load($cart_id);
                    $quote   = $this->_getCart()->setQuote($quote);
                    $quote=$quote->getQuote();
                    return $quote;
        } else {
            return false; 
        }
    }

    protected function _getWishlist()
    {
        try {
            $wishlist = Mage::getModel('wishlist/wishlist')
                ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
            Mage::register('wishlist', $wishlist);
        } catch (Mage_Core_Exception $e) {
            $jsonData = 'Can\'t create wishlist.'.$e->getMessage();
            return json_encode(Mage::helper('mobiconnect')->__($jsonData));
            return false;
        } catch (Exception $e) {
            $jsonData = 'Can\'t create wishlist.'.$e->getMessage();
            return json_encode(Mage::helper('mobiconnect')->__($jsonData));
            return false;
        }
        return $wishlist;
    }
    /**
    * @param Post Data 
    **/
    function addtocart($params){

        $params['related_product']=array();
        //Mage::log('<============  Add To Cart Start  ===========>','1','Addtocart.log',TRUE);
        //Mage::log(json_encode($params),'1','Addtocart.log',TRUE);
        //Mage::log('<============  Add To Cart End  ===========>','1','Addtocart.log',TRUE);
        $cart=$this->_getCartmobi($params);
        $storeId = Mage::app()->getStore();                 
        $productId='';
        $qty='';
        if(isset($params['product_id']) && $params['product_id']!="" && isset($params['qty']) && $params['qty']!="" ){
            $productId=(int)$params['product_id'];
            $qty=$params['qty'];
        }else{
            $productId=(int)$params['product_id'];
        }
        try {
            if (isset($qty)) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $qty = $filter->filter($qty);
            }
            $product = null;
            if ($productId) {
                $_product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($productId);
                if ($_product->getId()) {
                    $product = $_product;
                }
            }
            $related = isset($data['related_product']) ? $data['related_product']:array();
            /**
             * Check product availability
             */
            if (!$product) {
                return $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=> Mage::helper('mobiconnect')->__('Product Not Found.'))));
            }
            
            // for configrable product 

            if ($product->isConfigurable()) {
            
                $request = $this->_getProductRequest($params);
               
                $qty = isset($params['qty']) ? $params['qty'] : 0;
                $requestedQty = ($qty > 1) ? $qty : 1;
                $subProduct = $product->getTypeInstance(true)
                    ->getProductByAttributes($request->getSuperAttribute(), $product);
                if($subProduct && $subProduct->getId()){
                    if ($requestedQty < ($requiredQty = $subProduct->getStockItem()->getMinSaleQty())) {
                        $requestedQty = $requiredQty;
                    }
                }
                 
                $params['qty'] = $requestedQty;
            }
            
            //Mage::log('<============  Add To Cart End oooo  ===========>'.json_encode($params),'1','Addtocart.log',TRUE);
            $cart->addProduct($product,$params);
            //Mage::log('<============  Add To Cart End 00--  ===========>'.json_encode( $cart->getQuote()),'1','Addtocart.log',TRUE);
            /*if($custimerId)
            $cart->getQuote()->setStore($storeId)->loadByCustomer($customer);*/
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();
            $this->_getSession()->setCartWasUpdated(true);
            if (isset($params['customer_id'])) {
                $wishlist = $this->_getWishlist();
                //$id = (int) $params['whishlist_id'];
                if(is_object($wishlist) && $wishlist->getId()){
                    $item = Mage::getModel ( 'wishlist/item' )->getCollection ()->addFieldToFilter ( 'wishlist_id', $wishlist->getId() )->addFieldToFilter ( 'product_id', $productId )->getData();
                    $item = Mage::getModel('wishlist/item')->load($item['0']['wishlist_item_id']);
                    if ($item->getWishlistId() == $wishlist->getId()) {
                        try {
                            $item->delete();
                            $wishlist->save();
                        } catch (Mage_Core_Exception $e) {
                            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
                        } catch(Exception $e) {
                            $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>Mage::helper('mobiconnect')->__('An Error Occurred in removing product from wishlist.'))));
                        }
                    } 
                    Mage::helper('wishlist')->calculate();
                }
            }
           // if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (isset($wishlistMessage)) {
                    return $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>Mage::helper('mobiconnect')->__($wishlistMessage))));
                } else {
                    $productName = Mage::helper('core')->htmlEscape($product->getName());
                return  $jsonData = json_encode(array('cart_id'=>array('success'=>true,'cart_id'=> $cart->getQuote()->getEntityId(),'message'=>Mage::helper('mobiconnect')->__($productName.' has been added to your cart.'),'items_count'=>(int)$cart->getQuote()->getItemsQty())));              
                              

                    if ($cart->getQuote()->getHasError()) {
                        $errorMessages='';
                        foreach ($cart->getQuote()->getMessages() as $key => $value) {
                            $errorMessages=$errorMessages.$value->getCode();
                        }
                        return $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>Mage::helper('mobiconnect')->__($errorMessages))));
                    }
                }
           // }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                return $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>Mage::helper('mobiconnect')->__($e->getMessage()))));
            } else {
                $messageText = implode("\n", array_unique(explode("\n", $e->getMessage())));
                return $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>Mage::helper('mobiconnect')->__($messageText))));
            }
        } catch (Exception $e) {
            return $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>Mage::helper('mobiconnect')->__('Can\'t add item to shopping cart'))));
        }
    }

     /**
     * Get request for product add to cart procedure
     *
     * @param mixed $requestInfo
     * @return Varien_Object
     */
    protected function _getProductRequest($requestInfo)
    {
        if ($requestInfo instanceof Varien_Object) {
            $request = $requestInfo;
        } elseif (is_numeric($requestInfo)) {
            $request = new Varien_Object();
            $request->setQty($requestInfo);
        } else {
            $request = new Varien_Object($requestInfo);
        }
        if (!$request->hasQty()) {
            $request->setQty(1);
        }
        return $request;
    }

    /**
    * Initialize cart on basis of customer data
    **/
    function _getCartmobi($data){
    	$customer_id ='';
    	if(isset($data['customer_id'])){
    		$customer_id=$data['customer_id'];
    	}
        
        if($customer_id){
            try{
                   $session = Mage::getSingleton('customer/session');
                   $customer = Mage::getModel('customer/customer')->load($customer_id);
                   $quote = Mage::getModel('sales/quote')->loadByCustomer($customer);
                   $this->isLoggedIn=true;
                   if($quote && $quote->getEntityId())
                    return Mage::getModel('checkout/cart')->setQuote($quote)->setActive();
                    else
                    return $this->_getCart();  
                   
                   
            }catch (Exception $e){
                die($e->getMessage());
            }
        }else{
            $cart_id=$data['cart_id'];
            if($cart_id){
                  $store=Mage::app()->getStore();  
                  $quote = Mage::getModel('sales/quote')->setStore($store)->load($cart_id);
                  $cart = Mage::getModel('checkout/cart')->setQuote($quote)->setActive();
               }
               else{
                 $cart   = $this->_getCart();
               }
            return $cart;
        }
    }

    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current active quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }
    
    function getcartcount($data)
    {
        $quote = $this->_getQuotemobi($data);
        $defaultStoreId = Mage::app()->getWebsite(true)->getDefaultGroup()->getDefaultStoreId();

        $storeLocaleCode = Mage::getStoreConfig('general/locale/code', $defaultStoreId);

        if(!is_object($quote))
        {
            $jsonData = json_encode(
                    array(
                        'success'=>false,
                        'default_store'=> $defaultStoreId,
                        'locale_code'=>$storeLocaleCode,
                        'message'=>Mage::helper('mobiconnect')->__('cart have some error .')
                    )
                );
            return $jsonData;
        } 
        
            if($quote->getHasError()){
                $errorMessages='';
                foreach ($quote->getMessages() as $key => $value) {
                    $errorMessages=$errorMessages.$value->getCode();
                }
                $jsonData = json_encode(array('success'=>false,'message'=>Mage::helper('mobiconnect')->__($errorMessages)));
                return $jsonData;
            }
            if($quote->getItemsCount()!=0 ){
                $response = array(
                        'success'=>true,
                        'default_store'=> $defaultStoreId,
                        'locale_code'=>$storeLocaleCode,
                        'items_count'=> (int)$quote->getItemsQty()
                    );
                return json_encode($response);
            } else {
                $jsonData = json_encode(
                    array(
                        'success'=>false,
                        'locale_code'=>$storeLocaleCode,
                        'default_store'=> $defaultStoreId,
                        'message'=>'no_count'
                    )
                );
                return $jsonData;
            }
    }
    
    function validate($data){
    	
        $error='success';
        if(isset($data['customer_id']) && $data['customer_id']>0){
        	
        	 $customer=Mage::getModel('customer/customer')->load($data['customer_id']);
        	
           if(!$customer->getId())
           $error=$error."Customer Id Does Not Exists.";
           else if($customer->getId()){
                if(isset($data['hash_key']) && strlen($data['hash_key'])>0){
                     $error=$error."Hash Key Not Found.";
                }            
           } 
        }
        if(isset($data['cart_id']) && $data['cart_id']>0){
           $store=Mage::app()->getStore();  
           $quote = Mage::getModel('sales/quote')->setStore($store)->load($data['cart_id']);
           if(!$quote->getEntityId())
           $error=$error."Cart Id Does Not Exists.";
        }
        if(trim($error)!='success'){
            return json_encode(array('success'=>false,'message'=>Mage::helper('mobiconnect')->__($error)));
        }else{
            return true;
        }
    }

    function viewcart($data){

        $quote=$this->_getQuotemobi($data);
       
            $response=array();
            if($quote!==false && $quote->getItemsCount()!=0 ){
                    $total="";
                   /* if(strlen($quote->getCouponCode())>0){                       
                        $total=Mage::helper('core')->currency($quote->getSubtotalWithDiscount(),true,false);
                    }else{
                         $total=Mage::helper('core')->currency($quote->getSubtotal(),true,false);
                    }*/

                    $price_tax_setting = $this->getTaxSettingsValue('price');
                    $subtotal_tax_setting = $this->getTaxSettingsValue('subtotal');
                    $grandtotal_tax_setting = $this->getTaxSettingsValue('grandtotal');
                    $shipping_tax_setting = $this->getTaxSettingsValue('shipping');

                    if($subtotal_tax_setting=='1'){
                        $total=Mage::helper('core')->currency($quote->getSubtotal(),true,false);
                    }elseif($subtotal_tax_setting=='2'){
                        $total=Mage::helper('core')->currency($quote->getSubtotal(),true,false);
                    }else{
                        $total=Mage::helper('core')->currency($quote->getSubtotal(),true,false);
                    }

                    if($grandtotal_tax_setting=='0'){
                        $response['data']['grandtotal']=Mage::helper('core')->currency($quote->getGrandTotal()+$quote->getShippingAddress()->getTaxAmount(), true, false);
                    }elseif($grandtotal_tax_setting=='1'){
                       // $response['data']['grandtotal']=Mage::helper('core')->currency($quote->getGrandTotal(), true, false);
                    	$response['data']['grandtotal']=Mage::helper('core')->currency($quote->getGrandTotal(), true, false);
                    	 
                    }

                    $shipping_amount_value=0;

                     if($shipping_tax_setting=='1'){
                      $shipping_amount_value = Mage::helper('core')->currency($quote->getShippingAddress()->getShippingAmount(),true,false);
                    }elseif($shipping_tax_setting=='2'){
                       $shipping_amount_value = Mage::helper('core')->currency($quote->getTotal()->getAddress()->getShippingInclTax(),true,false);
                    }else{
                        $shipping_amount_value = Mage::helper('core')->currency($quote->getShippingAddress()->getShippingAmount(),true,false);
                    }

                    $response['data']['total'][]=array('amounttopay'=> $total,'shipping_amount'=>$shipping_amount_value,'tax_amount'=>Mage::helper('core')->currency($quote->getShippingAddress()->getTaxAmount(),true,false),'discount_amount'=>Mage::helper('core')->currency($quote->getShippingAddress()->getDiscountAmount(),true,false));
                   
                    $response['data']['items_count']=(int)$quote->getItemsQty();
                    $response['data']['items_qty']=(int)$quote->getItemsQty();
                   
                    $items = $quote->getAllVisibleItems();
                     foreach($items as $item) {
                        $productattrarray=array();

                        if($price_tax_setting=='1' && $item->getRowTotalInclTax()){
                            $item_sub_total=Mage::helper('core')->currency($item->getRowTotal(),true,false);
                        }elseif($price_tax_setting=='2' || $item->getRowTotalInclTax()){
                            $item_sub_total=Mage::helper('core')->currency($item->getRowTotalInclTax(),true,false);
                        }else{
                            $item_sub_total=Mage::helper('core')->currency($item->getRowTotal(),true,false);
                        }

                        $productMediaConfig = Mage::getModel('catalog/product_media_config');
                        $smallImageUrl = $productMediaConfig->getMediaUrl($item->getProduct()->getSmallImage());
                        $productOptions= $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                      
                        if(isset($productOptions['attributes_info']) && count($productOptions['attributes_info'])!=0)
                        $productattrarray['options_selected']=$productOptions['attributes_info'];
                        else 
                        $productattrarray['options_selected']=$productOptions;
                        if(isset($productOptions['bundle_options']) && count($productOptions['bundle_options'])!=0)
                        $productattrarray['bundle_options']=$productOptions['bundle_options'];

                        if(isset($productOptions['bundle_options']) && count($productOptions['bundle_options'])!=0){
                            foreach ($productattrarray['bundle_options'] as $key1 => $value) {
                              foreach ($value as $key2 => $val) {
                                 foreach ($val as $key3 => $v) {
                                 if(isset($v['price'])){ 
                                    $productattrarray['bundle_options'][$key1][$key2][$key3]['price'] = Mage::helper('core')->currency($v['price'],true,false);
                                 }
                              }
                              }
                            }
                        }
                      
                        $productarray=array(
                            'product_id'=>$item->getProductId(),
                            'item_id'=>$item->getId(),
                            'product-name'=>$item->getName(),
                            'product_image'=>$smallImageUrl,
                            'quantity'=>$item->getQty(),
                            'sub-total'=>$item_sub_total,
                            'product_type'=>$item->getProduct()->getTypeId(),
                            'options_selected' =>isset($productattrarray['options_selected'])?$productattrarray['options_selected']:'',
                            'bundle_options' =>isset($productattrarray['bundle_options'])?$productattrarray['bundle_options']:''
                            );
                         $isGuestCheckoutEnabled=Mage::getStoreConfig('checkout/options/guest_checkout');
                        $response['data']['allowed_guest_checkout']=$isGuestCheckoutEnabled;
                        $response['data']['products'][]=$productarray;
                        
                       
                       // Mage::log('<============  View Cart Response data  ===========>'.json_encode($response),'1','Viewtocartnew.log',TRUE);
  
                     }
                    /**
                      * custom Code
                      */
                     
                   
                     $cart = $this->_getQuotemobi($data);
                     if(!count($cart->getData()))
                     {
                     	Mage::getSingleton('core/session')->unsCartParams();
                     }
                     $vid =[];$price=0;
                     foreach ($cart->getAllVisibleItems() as $item)
                     {
                     	$price = $price + $item->getPrice()*$item->getQty();
                     	$productId = $item->getProductId();
                     	$vproduct = Mage::getModel('csmarketplace/vproducts')->getCollection()->addFieldToFilter('product_id',$productId)->getFirstItem();
                     	$vid[] = $vproduct->getVendorId();
                     }
                     $vid =array_unique($vid);
                     if ($vid[0]) {
                     
                     	$vendor = Mage::getModel('csmarketplace/vendor')->load($vid[0])->toArray();
                     	//print_r($vendor); die("dfkg");
                     	$freeShipAmount = $vendor['min_freeship'];
                     }
                    
                     $attribute = Mage::getSingleton ( 'eav/config' )->getAttribute ( 'csmarketplace_vendor', 'delivery' );
                     
                     if ($attribute->usesSource ()) {
                     	$options = $attribute->getSource ()->getAllOptions ( false );
                     }
                     $deliveryOption = explode ( ',', $vendor ['delivery'] );
                     
                     $shippingOptions = ['Pickup'];
                     if (in_array ( $options [0] ['value'], $deliveryOption )) {
                     	if($vendor['min_freeship'] <= $quote->getSubtotal())
                     	{
                     		$shippingOptions[]='Delivery';
                     		 
                     	}
                     }
                 
                     
                    
                    
                     
                     


        $day = Mage::getSingleton('core/date')->date('w');
      
        if($day == 0)
        {
          $openTime = $vendor['sun_open_time'];
          $openTime = explode(',',$openTime);
          $openTime = implode(':',$openTime);
          $openTime = date("H:i", strtotime($openTime));
          
          $closeTime = $vendor['sun_close_time'];
          $closeTime = explode(',',$closeTime);
          $closeTime = implode(':',$closeTime);
          $closeTime = date("H:i", strtotime($closeTime));
        }
        elseif($day ==1)
        {
          $openTime = $vendor['mon_open_time'];
          $openTime = explode(',',$openTime);
          $openTime = implode(':',$openTime);
          $openTime = date("H:i", strtotime($openTime));
          
          $closeTime = $vendor['mon_close_time'];
          $closeTime = explode(',',$closeTime);
          $closeTime = implode(':',$closeTime);
          $closeTime = date("H:i", strtotime($closeTime));
        }
        elseif($day ==2)
        {

          $openTime = $vendor['tue_open_time'];
          $openTime = explode(',',$openTime);
          $openTime = implode(':',$openTime);
          $openTime = date("H:i", strtotime($openTime));
          
          $closeTime = $vendor['tue_close_time'];
          $closeTime = explode(',',$closeTime);
          $closeTime = implode(':',$closeTime);
          $closeTime = date("H:i", strtotime($closeTime));

        }
        elseif($day ==3)
        {
          $openTime = $vendor['wed_open_time'];
          $openTime = explode(',',$openTime);
          $openTime = implode(':',$openTime);
          $openTime = date("H:i", strtotime($openTime));
          
          $closeTime = $vendor['wed_close_time'];
          $closeTime = explode(',',$closeTime);
          $closeTime = implode(':',$closeTime);
          $closeTime = date("H:i", strtotime($closeTime));
        }
        elseif($day ==4)
        {
          $openTime = $vendor['thu_open_time'];
          $openTime = explode(',',$openTime);
          $openTime = implode(':',$openTime);
          $openTime = date("H:i", strtotime($openTime));
          
          $closeTime = $vendor['thu_close_time'];
          $closeTime = explode(',',$closeTime);
          $closeTime = implode(':',$closeTime);
          $closeTime = date("H:i", strtotime($closeTime));
        }
        elseif($day ==5)
        {
          $openTime = $vendor['fri_open_time'];
          $openTime = explode(',',$openTime);
          $openTime = implode(':',$openTime);
          $openTime = date("H:i", strtotime($openTime));
          
          $closeTime = $vendor['fri_close_time'];
          $closeTime = explode(',',$closeTime);
          $closeTime = implode(':',$closeTime);
          $closeTime = date("H:i", strtotime($closeTime));
        }
        elseif($day ==6)
        {
          $openTime = $vendor['sat_open_time'];
          $openTime = explode(',',$openTime);
          $openTime = implode(':',$openTime);
          $openTime = date("H:i", strtotime($openTime));
          
          $closeTime = $vendor['sat_close_time'];
          $closeTime = explode(',',$closeTime);
          $closeTime = implode(':',$closeTime);
          $closeTime = date("H:i", strtotime($closeTime));
        }
     
      $curDate =  Mage::getModel('core/date')->date('H:i');
     

     $curDate1 = explode(':',$curDate)  ;
    {
         if($curDate1[0] == '00')
         {
           $curDate ="24:".$curDate1[1];
         }
    }
    $openTime1 = explode(':',$openTime)  ;
    {
         if($openTime1[0] == '00')
         {
           $openTime ="24:".$openTime1[1];
         }
    }
   $closeTime1 = explode(':',$closeTime)  ;
    {
         if($closeTime1[0] == '00')
         {
           $closeTime ="24:".$closeTime1[1];
         }
    } 
  

 $curDate =  /*Mage::getModel('core/date')->timestamp*/strtotime($curDate); 
 $openTime = /*Mage::getModel('core/date')->timestamp*/strtotime($openTime);
 $closeTime = /*Mage::getModel('core/date')->timestamp*/strtotime($closeTime);
 

        $showNow = 'No';
        if($openTime > $closeTime) {

            // if current time is past start time or before end time

            if($curDate >= $openTime || $curDate < $closeTime){
                $showNow = 'Yes';
            }
            else{
              $showNow = 'No';
            }
        }

        // else time frame is within same day check if we are between start and end

        else if ($curDate >= $openTime && $curDate <= $closeTime) {
             $showNow = 'Yes';
        }
        
        $showTime = [];
        if($showNow == 'Yes')
        {
            $showTime [] = 'Now';
            $showTime [] = 'Later'; 
        }
        else{
              $showTime [] = 'Later';
        }
        
          $timingArray = array('00:00'=>'00:00', '00:15'=>'00:15', '00:30'=>'00:30', '00:45'=>'00:45', '01:00'=>'01:00',
        		'01:15'=>'01:15', '01:30'=>'01:30', '01:45'=>'01:45', '02:00'=>'02:00', '02:15'=>'02:15', '02:30'=>'02:30',
        		'02:45'=>'02:45', '03:00'=>'03:00', '03:15'=>'03:15', '03:30'=>'03:30', '03:45'=>'03:45', '04:00'=>'04:00',
        		'04:15'=>'04:15', '04:30'=>'04:30', '04:45'=>'04:45', '05:00'=>'05:00', '05:15'=>'05:15', '05:30'=>'05:30',
        		'05:45'=>'05:45', '06:00'=>'06:00', '06:15'=>'06:15', '06:30'=>'06:30', '06:45'=>'06:45', '07:00'=>'07:00',
        		'07:15'=>'07:15', '07:30'=>'07:30', '07:45'=>'07:45', '08:00'=>'08:00', '08:15'=>'08:15', '08:30'=>'08:30',
        		'08:45'=>'08:45', '09:00'=>'09:00', '09:15'=>'09:15', '09:30'=>'09:30', '09:45'=>'09:45', '10:00'=>'10:00',
        		'10:15'=>'10:15', '10:30'=>'10:30', '10:45'=>'10:45', '11:00'=>'11:00', '11:15'=>'11:15', '11:30'=>'11:30',
        		'11:45'=>'11:45', '12:00'=>'12:00', '12:15'=>'12:15', '12:30'=>'12:30', '12:45'=>'12:45', '13:00'=>'13:00',
        		'13:15'=>'13:15', '13:30'=>'13:30', '13:45'=>'13:45', '14:00'=>'14:00', '14:15'=>'14:15', '14:30'=>'14:30',
        		'14:45'=>'14:45', '15:00'=>'15:00', '15:15'=>'15:15', '15:30'=>'15:30', '15:45'=>'15:45', '16:00'=>'16:00',
        		'16:15'=>'16:15', '16:30'=>'16:30', '16:45'=>'16:45', '17:00'=>'17:00', '17:15'=>'17:15', '17:30'=>'17:30',
        		'17:45'=>'17:45', '18:00'=>'18:00', '18:15'=>'18:15', '18:30'=>'18:30', '18:45'=>'18:45', '19:00'=>'19:00',
        		'19:15'=>'19:15', '19:30'=>'19:30', '19:45'=>'19:45', '20:00'=>'20:00', '20:15'=>'20:15', '20:30'=>'20:30',
        		'20:45'=>'20:45', '21:00'=>'21:00', '21:15'=>'21:15', '21:30'=>'21:30', '21:45'=>'21:45', '22:00'=>'22:00',
        		'22:15'=>'22:15', '22:30'=>'22:30', '22:45'=>'22:45', '23:00'=>'23:00', '23:15'=>'23:15', '23:30'=>'23:30',
        		'23:45'=>'23:45');
       
        
      
       
        $curTime =  Mage::getModel('core/date')->date('H:i');
    
        if($showNow == "Yes")
        {
        	$j = 0;
        }
        elseif($showNow == "No" && $curTime < '12:00')
        {
        	$j = 0;
        }
        else{
        	$j=1;
        }
        $days =[];$hours=[];
        for($i=$j; $i<$j+7; $i++){
		
    $m= date("m"); // Month value

   $de= date("d"); //today's date

    $y= date("Y"); // Year value

		        $days[] =  strtoupper(date('d-m-y:D', mktime(0,0,0,$m,($de+$i),$y)));
	
        }
        
        foreach ($timingArray as $val)
        {
         $now = strtotime($val);
        if($openTime > $closeTime) {
           if($now  >= $openTime || $now  < $closeTime){
                
            }
            else{
              continue;
            }
        }
         else if($now  >= $openTime && $now  <= $closeTime) {
            
        }
        else{
          continue;
        }
        	
             $hours[] =  date("h:i a", strtotime($val));
        	
        	
        }
        
       
        $dietryInfo =  Mage::getStoreConfig('ced_vproducts/general/dietary_info');
       
         $response['cart_options'] = ['ship_options'=>$shippingOptions,'times'=> $showTime,'later_hours'=>['days'=> $days,'hour'=>$hours],'dietary_info'=>$dietryInfo];
                     
                return json_encode($response);
            }else{
                return  json_encode(array('success'=>false,'message'=>Mage::helper('mobiconnect')->__('Your Shopping Cart Is Empty.')));//$response='empty';
            }
            
    }
    /** 
    * for tax settings 
    * @return $amount
    **/

    public function getTaxSettingsValue($type){
        $cart_tax_setting=array();
        $tax_setting=Mage::helper('mobiconnect')->getTaxSetting();
        if(isset($tax_setting['cart_display']) && count($tax_setting['cart_display'])){
           $cart_tax_setting = $tax_setting['cart_display'];
        }
       
        if($type!='' && isset($cart_tax_setting[$type])){
        	
            return $cart_tax_setting[$type];
        }
    }

    /**
     * Initialize coupon
     *
     * @return null
     */
    public function applycoupon($data)
    {
        $quote=$this->_getQuotemobi($data);
        //Mage::log('<============  apply coupon start  ===========>','1','couponCode.log',TRUE);
        
        if (!$quote->getItemsCount()) {
            $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>Mage::helper('mobiconnect')->__('Shopping cart is Empty'))));
            
            return $jsonData;
        }
         
        $couponCode = (string) $data['coupon_code'];
        if ($data['remove'] == 1) {
            $couponCode = '';
        }
        //Mage::log('<============  apply coupon start  ===========>'.$couponCode,'1','couponCode.log',TRUE);
        
        $oldCouponCode = $quote->getCouponCode();
 //Mage::log('<============  apply coupon start  ===========>'.$oldCouponCode,'1','couponCode.log',TRUE);
       
        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>Mage::helper('mobiconnect')->__('Coupon code is empty.'))));
            return $jsonData;
        }

        try {
            $quote->getShippingAddress()->setCollectShippingRates(true);
            $quote->setCouponCode($couponCode)->collectTotals()->save();
            //Mage::log('<============  apply coupon start  ===========>'.json_encode($quote->getData()),'1','couponCode.log',TRUE);
  //Mage::log('<============  apply coupon start  ===========>'.$couponCode.'=='.$quote->getCouponCode(),'1','couponCode.log',TRUE);
  
            if ($couponCode) {
                if ($couponCode == $quote->getCouponCode()) {
                    $jsonData = json_encode(array('cart_id'=>array('success'=>true,'message'=>Mage::helper('mobiconnect')->__('Coupon code '.strip_tags($couponCode).' was applied.'))));
                    return $jsonData;
                } else {
                    $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>Mage::helper('mobiconnect')->__('Coupon code '. strip_tags($couponCode).' is not valid.'))));
                    return $jsonData;
                }
            } else {
                $jsonData = json_encode(array('cart_id'=>array('success'=>true,'message'=>Mage::helper('mobiconnect')->__('Coupon code was canceled.'))));
               return $jsonData;
            }

        } catch (Mage_Core_Exception $e) {
            $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>Mage::helper('mobiconnect')->__($e->getMessage()))));
            return $jsonData;
        } catch (Exception $e) {
            Mage::logException($e);
            $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>Mage::helper('mobiconnect')->__('Can\'t apply the coupon code.'))));
            return $jsonData;
        }
    }

    /**
    * For saving address at checkout 
    **/

    function savebillingshiping($data){
        //Mage::log('<============  savebillingshiping start  ===========>','1','savebillingshiping.log',TRUE);
        
        //Mage::log('<============  savebillingshiping Posted Data  ===========>'.json_encode($data),'1','savebillingshiping.log',TRUE);   
        $quote=$this->_getQuotemobi($data);
        if(!is_object($quote))
        {
            $jsonData = json_encode(array('success'=>false,'message'=>Mage::helper('mobiconnect')->__('cart have error in address.')));
            return $jsonData;
        }

        $jsonData=array();  
        $customerAddressId = $data['address_id'];
        $customer_id = $data['customer_id'];
        if ($customerAddressId){
        $_custom_address = Mage::getModel('customer/address')->load($customerAddressId);
        }else{
            $BilllingParams=$data['billingaddress'];
            //print_r($BilllingParams);
           /* $address=explode('_',$BilllingParams[0]);
            // for billing address 
            $country_name = trim($address[6]);
            $country_code =Zend_Locale_Data_Translation::$regionTranslation[$country_name];
            $_custom_address = array (
                'firstname' => $address[0],
                'lastname' => $address[1],
                'street' => array (
                    '0' => $address[2],
                ),
             
                'city' => $address[3],
                'region_id' => '',
                'region' => $address[4],
                'postcode' => $address[5],
                'country_id' => trim($address[6]) , 
                'telephone' => $address[7],
            );*/
            $_custom_address=json_decode($BilllingParams,true);
           
        }

        // save address when customer and  no customer id
        $newAddressId='';
        $customAddress='';
        if($customer_id && !$customerAddressId){
            //Mage::log('<============  savebillingshiping new customer  ===========>'.json_encode($_custom_address),'1','savebillingshiping.log',TRUE);   
        
            $customAddress = Mage::getModel('customer/address');
            $customAddress->setData($_custom_address)
                    ->setCustomerId($customer_id)
                    ->setIsDefaultBilling('1')
                    ->setIsDefaultShipping('1')
                    ->setSaveInAddressBook('1');
            try {
            $customAddress->save();
            $newAddressId=$customAddress->getId();
            }
            catch (Exception $e) {
               
               $jsonData = json_encode(array('success'=>false,'message'=>Mage::helper('mobiconnect')->__('Can\'t save vendor address.'.$e->getMessage())));
               return $jsonData;
            }        
        }
        if($customer_id){
            //for login user 
                $cart_id = $data['cart_id'];
                if($cart_id && ($quote->getEntityId()!=$cart_id)){
                    // guest quote of customer
                    $guestquote = Mage::getModel('sales/quote')->load($cart_id);
                    //merging both on checkout login
                    $quote->merge($guestquote);
                    $quote->collectTotals()->save();
                    if($quote->getHasError()){
                         $errorMessages='';
                        foreach ($quote->getMessages() as $key => $value) {
                            $errorMessages=$errorMessages.$value->getCode();
                        }
                        $jsonData = json_encode(array('success'=>false,'message'=>Mage::helper('mobiconnect')->__($errorMessages)));
                        return $jsonData;
                    }
                    $quote->collectTotals()->save();
                }
                
                if($customerAddressId){
                    $customer = Mage::getModel('customer/customer')->load($customer_id);
                    $customerAdd=Mage::getSingleton('sales/quote_address')->importCustomerAddress(Mage::getModel('customer/address')->load($customerAddressId));
                    $quote->getShippingAddress()->addData(Mage::getModel('customer/address')->load($customerAddressId)->getData());
                    $quote->getShippingAddress()->save();
                    $quote->getBillingAddress()->setSameAsBilling(1);
                    $quote->getBillingAddress()->addData(Mage::getModel('customer/address')->load($customerAddressId)->getData());
                    $quote->getBillingAddress()->save();
                    $quote->collectTotals()->save();
                    //Mage::log('<============  savebillingshiping customer having address ===========>'.json_encode($quote->getBillingAddress()->getData()),'1','savebillingshiping.log',TRUE);   
        
                    $addressValidation = $quote->getBillingAddress()->validate();
                    if ($addressValidation !== true) {
                        $jsonData = array('success'=>false,'message'=>$this->__('Please check billing address information. %s', implode(' ', $addressValidation))
                            );
                    }
                    if (!$quote->isVirtual()) {
                        $address = $quote->getShippingAddress();
                        $addressValidation = $address->validate();
                        if ($addressValidation !== true) {
                            $jsonData = array('success'=>false,'message'=>$this->__('Please check shipping address information. %s', implode(' ', $addressValidation))
                            );
                        }
                    }
                    if(!count($jsonData)){
                       $jsonData = array('success'=>true); 
                    }
                }else{
                    //Mage::log('<============  savebillingshiping customer new address ===========>'.json_encode($customAddress->getData()),'1','savebillingshiping.log',TRUE);   
        
                    $quote->getBillingAddress()->addData($customAddress->getData());
                    $quote->getBillingAddress()->save();
                    $quote->getBillingAddress()->setSameAsBilling(1);
                    $quote->getShippingAddress()->addData($customAddress->getData());
                    $quote->getShippingAddress()->save();
                    $quote->collectTotals()->save();
                    $addressValidation = $quote->getBillingAddress()->validate();
                    if ($addressValidation !== true) {
                        $jsonData = array('success'=>false,'message'=>$this->__('Please check billing address information. %s', implode(' ', $addressValidation))
                            );
                    }
                    if (!$quote->isVirtual()) {
                        $address = $quote->getShippingAddress();
                        $addressValidation = $address->validate();
                        if ($addressValidation !== true) {
                            $jsonData = array('success'=>false,'message'=>$this->__('Please check shipping address information. %s', implode(' ', $addressValidation))
                            );
                        }
                    }
                    if(!count($jsonData)){
                       $jsonData=array('success'=>true,'address_id'=>$newAddressId); 
                    }
                }
            }else{ 
              //Mage::log('<============  savebillingshiping guest customer ===========>','1','savebillingshiping.log',TRUE);   
        
              $quote->setCustomerEmail($data['email']);                   
              $quote->getBillingAddress()->addData($_custom_address);
              $quote->getBillingAddress()->save();
              $quote->getBillingAddress()->setSameAsBilling(1);
              $quote->getShippingAddress()->addData($_custom_address);
              $quote->getShippingAddress()->save();
              $quote->collectTotals()->save();
              //Mage::log('<============  savebillingshiping guest customer ===========>'.json_encode($quote->getShippingAddress()->getData()),'1','savebillingshiping.log',TRUE);   
              $addressValidation = $quote->getBillingAddress()->validate();
                    if ($addressValidation !== true) {
                        $jsonData = array('success'=>false,'message'=>Mage::helper('mobiconnect')->__('Please check billing address information. '.implode(' ', $addressValidation))
                            );
                    }
                    if (!$quote->isVirtual()) {
                        $address = $quote->getShippingAddress();
                        $addressValidation = $address->validate();
                        if ($addressValidation !== true) {
                            $jsonData = array('success'=>false,'message'=>Mage::helper('mobiconnect')->__('Please check shipping address information.'.implode(' ', $addressValidation))
                            );
                        }
                    }
                     //Mage::log('<============  savebillingshiping guest customer ===========>'.json_encode($jsonData),'1','savebillingshiping.log',TRUE);   
              
                    if(!count($jsonData)){
                      $jsonData = array('success'=>true);
                    }
            }    
       return json_encode($jsonData); 
    }

    /**
    * save shipping and  payment
    **/
    public function saveshippingpayament($data){
             
        $payment_menthod=$data['payment_method'];
        $shipping_menthod=$data['shipping_method'];
        $quote=$this->_getQuotemobi($data);
        if(!is_object($quote))
        {
            $jsonData = json_encode(array('success'=>false,'message'=>Mage::helper('mobiconnect')->__('cart have some error .')));
            return $jsonData;
        } 
        if($payment_menthod && $shipping_menthod){
                try{
                   $shippingAddress = $quote->getShippingAddress();
                   $shippingAddress->setShippingMethod($shipping_menthod);
                   $shippingAddress->setCollectShippingRates(true);
                   $shippingAddress->collectShippingRates();
                   $quote->getPayment()->setMethod($payment_menthod)->save();
                   $quote->collectTotals()->save();
                   if(!$quote->getHasError())
                   {
                    $jsonData=array('success'=>true);
                    return json_encode($jsonData); 
                   }else{
                     $errorMessages='';
                        foreach ($quote->getMessages() as $key => $value) {
                            $errorMessages=$errorMessages.$value->getCode();
                        }
                        $jsonData = json_encode(array('success'=>false,'message'=>Mage::helper('mobiconnect')->__($errorMessages)));
                        return $jsonData;
                   }
                    $address = $quote->getShippingAddress();
                    $method= $address->getShippingMethod();
                    $rate  = $address->getShippingRateByCode($method);
                    if (!$quote->isVirtual() && (!$method || !$rate)) {
                        $jsonData=array('success'=>false,'message'=>$this->__('Please specify a shipping method.'));
                    }
                    if (!($quote->getPayment()->getMethod())) {
                        $jsonData=array('success'=>false,'message'=>$this->__('Please select a valid payment method.'));
                    }
                }catch(Exception $e){
                     $jsonData=array('success'=>false,'message'=>$e->getMessage());
                    return json_encode($jsonData); 
                } 
            }else{
                $jsonData=array('success'=>false,'message'=>Mage::helper('mobiconnect')->__('select valid payment or shipping.'));
                return json_encode($jsonData);
            }

    } 

    /**
    *Save order of current customer
    * @param Postdata $data
    **/
    public function saveorder($data){
        $quote=$this->_getQuotemobi($data);
        if(!is_object($quote))
        {
            $jsonData = json_encode(array('success'=>false,'message'=>Mage::helper('mobiconnect')->__('cart have some error .')));
            return $jsonData;
        } 
        try{
            //check if quote have item 
            if($quote->getItemsCount()){
                $quote->collectTotals()->save();
                $service = Mage::getModel('sales/service_quote', $quote);
                $service->submitAll();
                $order = $service->getOrder();
                if($quote->getId() && $order->getIncrementId())
                {
                    try {
                        $quote = Mage::getModel("sales/quote")->load($quote->getId());
                        $quote->setIsActive(false);
                        $quote->delete();
                        $this->_sendOrderMail($order);// send mail
                        return $jsonData=json_encode(array('success'=>true,'order_id'=> $order->getIncrementId()));
                    } catch(Exception $e) {
                        return $e->getMessage();
                    }
                }
            }else{
                 return $jsonData=json_encode(array('success'=>false,'message'=>Mage::helper('mobiconnect')->__('Quote is empty.')));
            }  
        }catch(Exception $e){
            return $jsonData=json_encode(array('success'=>false,'message'=> Mage::helper('mobiconnect')->__($e->getMessage())));  
        }
        
    } 

    /**
    * get shipping and  payment
    **/
    public function getsaveshippingpayament($data){
        $quote=$this->_getQuotemobi($data);
        if(!is_object($quote))
        {
            $jsonData = json_encode(array('success'=>false,'message'=>Mage::helper('mobiconnect')->__('cart have some error .')));
            return $jsonData;
        } 
        // for payments
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();
        $methods = array();
        foreach ($payments as $paymentCode=>$paymentModel) {
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            if(trim($paymentCode)=='cashondelivery'||trim($paymentCode=='banktransfer')||trim($paymentCode=='checkmo')){
                $methods["methods"][] = array(
                'label' => $paymentTitle,
                'value' => $paymentCode,
                );
            }
        }
        // for shipping methods
        $address = $quote->getShippingAddress();
        $shipping = Mage::getModel('shipping/shipping');        
        $result=$shipping->collectRatesByAddress($address)->getResult();
        $newrateCodes = array();
        $shippingRates=$result->getAllRates();
        foreach ($shippingRates as $rate) {
            if ($rate instanceof Mage_Shipping_Model_Rate_Result_Error) {
                $errors[$rate->getCarrierTitle()] = 1;
            } else {
                $code = $rate->getCarrier() . '_' . $rate->getMethod();

                if ($address->getFreeShipping()) {
                    $price = 0;
                } else {
                    $price = $rate->getPrice();
                }
                if ($price) {
                    $price = Mage::helper('tax')->getShippingPrice($price, false, $address);
                }
                $newrateCodes["methods"][]=array(
                'label' => $rate->getCarrierTitle() . '_' . $rate->getMethodTitle()."(".$price.")",
                'value' => $code
                );
            }
        }
        
        $jsonData=array('success'=>true,
                        'payments'=>$methods,
                        'shipping'=> $newrateCodes);
        return json_encode($jsonData); 
    } 

    /**
    * Delete item from cart 
    *@param item_id
    **/
    function deletecart($data)
    { 
        $id = (int) $data['item_id'];
        if ($id) {
            try {
                $cart=$this->_getCartmobi($data);
                $cart->removeItem($id)->save();
                $jsonData = array('success'=>'removed_successfully','items_count'=>(int)$cart->getQuote()->getItemsQty());
                return json_encode($jsonData); 
            } catch (Exception $e) {
                $jsonData = 'Cannot remove the item.'.$e->getMessage();
                $jsonData = array('success'=>'false','message'=>$jsonData);
                return json_encode($jsonData);
                Mage::logException($e);
                return;
            }
        }else{
            $jsonData = 'Cannot remove the item.';
            $jsonData = array('success'=>'false','message'=>Mage::helper('mobiconnect')->__($jsonData));
            return json_encode($jsonData);
        }
    }
    function _sendOrderMail($order){
        try
        {
           $order->sendNewOrderEmail();
           }catch (Exception $e) {
                return $jsonData=array('success'=>false, 'message' => Mage::helper('mobiconnect')->__($e->getMessage()));
        }
    }
    function getCountryDropdown(){
        $countryList = Mage::getResourceModel('directory/country_collection')
        ->loadData()
        ->toOptionArray(false);
        return array('country_list'=>$countryList);
    }

    /*empty shopping cart*/
    public function emptyShoppingCart($data)
    {
        $cart = $this->_getCartmobi($data);
        try {
            $cart->truncate()->save();
            $jsonData = json_encode(
                            array(
                                'success'=>true,
                                'message'=>Mage::helper('mobiconnect')->__('Updated Shopping Cart')
                            )
                        );
            return $jsonData;

        } catch (Mage_Core_Exception $exception) {
            $jsonData = json_encode(
                            array(
                                'success'=>false,
                                'message'=>$exception->getMessage()
                            )
                        );
            return $jsonData;

        } catch (Exception $exception) {
            $jsonData = json_encode(
                            array(
                                'success'=>false,
                                'message'=>$this->__('Cannot update shopping cart.')
                            )
                        );
            return $jsonData;
        }
    }

    /*for update item qty*/

    function updateQty($data) 
    {
        $itemId = $data['item_id'];
        $qty = $data['qty'];
        if ($itemId) {
            try {

                $cart = $this->_getCartmobi($data);
                if (isset($qty)) {
                    $filter = new Zend_Filter_LocalizedToNormalized(
                        array('locale' => Mage::app()->getLocale()->getLocaleCode())
                    );
                    $qty = $filter->filter($qty);
                }

                $quoteItem = $cart->getQuote()->getItemById($itemId);
                if (!$quoteItem) {
                    Mage::throwException($this->__('Quote item is not found.'));
                }

                if ($qty == 0) {
                    $cart->removeItem($itemId);
                } else {
                    $quoteItem->setQty($qty)->save();
                }
                $cart->save();

                if (!$quoteItem->getHasError()) {
                    $jsonData = array(
                            'message' => Mage::helper('mobiconnect')->__('Item was updated successfully.'),
                            'qty' => (int)$cart->getSummaryQty(),
                            'success' => true
                        );
                    return json_encode($jsonData); 
                } else {

                    $jsonData = array('success'=>false,'message'=>Mage::helper('mobiconnect')->__($quoteItem->getMessage()));
                    return json_encode($jsonData); 
                }

            } catch (Exception $e) {

                $jsonData = array('success'=>false,'message'=>Mage::helper('mobiconnect')->__($e->getMessage()));
                return json_encode($jsonData); 
            }
        } else {
            $jsonData = array ( 
                    'message'=>Mage::helper('mobiconnect')->__('Item id not found'),
                    'status' => false 
                );
            return json_encode($jsonData);
        }
    }
    

}
        