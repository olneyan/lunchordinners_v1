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
  * @package   Ced_MobiconnectAdvCart
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_MobiconnectAdvCart_Model_Advancemobicart extends Mage_Core_Model_Abstract {
  /**
  *Advance add to cart 
  **/
  function advanceaddtocart($data){
  	if(isset($data['type']) && $data['type']!=''){
  		$cart=Mage::getModel('mobiconnect/checkoutmobi')->_getCartmobi($data);
  		$storeId = Mage::app()->getStore();					
		$productId='';
		$qty='';
		$params=$data;
		if(isset($params['product_id']) && $params['product_id']!="" && isset($params['qty']) && $params['qty']!="" ){
			$productId=(int)$params['product_id'];
			$qty=$params['qty'];
		}
  		switch($cart){
  			case 'configurable':
  				
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
	            $related = $data['related_product'];
	            /**
	             * Check product availability
	             **/
	            if (!$product) {
	                return $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>'Product Not Found.')));
	            }

	            $cart->addProduct($product, $params);
	            if($custimerId)
	            $cart->getQuote()->setStore($storeId)->loadByCustomer($customer);
	            if (!empty($related)) {
	                $cart->addProductsByIds(explode(',', $related));
	            }

	            $cart->save();
	            $this->_getSession()->setCartWasUpdated(true);

	            if (isset($params['whishlist_id'])) {
	                $wishlist = $this->_getWishlist();
	                $id = (int) $params['whishlist_id'];
	                $item = Mage::getModel('wishlist/item')->load($id);

	                if ($item->getWishlistId() == $wishlist->getId()) {
	                    try {
	                        $item->delete();
	                        $wishlist->save();
	                    } catch (Mage_Core_Exception $e) {
	                        $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
	                    } catch(Exception $e) {
	                            $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>'An Error Occurred in removing product from wishlist.')));

	                    }
	                } else {
	              	return $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>'Specified item does not exist in wishlist.')));
	                }
	                Mage::helper('wishlist')->calculate();
		            }
		            if (!$this->_getSession()->getNoCartRedirect(true)) {
		                if (isset($wishlistMessage)) {
		                	return $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>$wishlistMessage)));
		                } else {
		                    $productName = Mage::helper('core')->htmlEscape($product->getName());
		                return  $jsonData = json_encode(array('cart_id'=>array('success'=>true,'cart_id'=> $this->_getQuote()->getEntityId(),'message'=>$productName.' has been added to your cart.')));              

		                    if ($cart->getQuote()->getHasError()) {
		                    	$errorMessages='';
		                    	foreach ($cart->getQuote()->getMessages() as $key => $value) {
		                    		$errorMessages=$errorMessages.$value->getCode();
		                    	}
		                    	return $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>$errorMessages)));
		                    }
		                }
		            }
		        } catch (Mage_Core_Exception $e) {
		            if ($this->_getSession()->getUseNotice(true)) {
		                return $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>$e->getMessage())));
		            } else {
		                $messageText = implode("\n", array_unique(explode("\n", $e->getMessage())));
		                return $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>$messageText)));
		            }
		        } catch (Exception $e) {

		        	return $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>'Can\'t add item to shopping cart'.$e->getMessage())));
		        }
  				break;
  			
  			default:
        
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
	            $related = $data['related_product'];
	            /**
	             * Check product availability
	             **/
	            if (!$product) {
	                return $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>'Product Not Found.')));
	            }

	            $cart->addProduct($product, $params);
	            if($custimerId)
	            $cart->getQuote()->setStore($storeId)->loadByCustomer($customer);
	            if (!empty($related)) {
	                $cart->addProductsByIds(explode(',', $related));
	            }

	            $cart->save();
	            $this->_getSession()->setCartWasUpdated(true);

	            if (isset($params['whishlist_id'])) {
	                $wishlist = $this->_getWishlist();
	                $id = (int) $params['whishlist_id'];
	                $item = Mage::getModel('wishlist/item')->load($id);

	                if ($item->getWishlistId() == $wishlist->getId()) {
	                    try {
	                        $item->delete();
	                        $wishlist->save();
	                    } catch (Mage_Core_Exception $e) {
	                        $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
	                    } catch(Exception $e) {
	                            $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>'An Error Occurred in removing product from wishlist.')));

	                    }
	                } else {
	              	return $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>'Specified item does not exist in wishlist.')));
	                }
	                Mage::helper('wishlist')->calculate();
		            }
		            if (!$this->_getSession()->getNoCartRedirect(true)) {
		                if (isset($wishlistMessage)) {
		                	return $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>$wishlistMessage)));
		                } else {
		                    $productName = Mage::helper('core')->htmlEscape($product->getName());
		                return  $jsonData = json_encode(array('cart_id'=>array('success'=>true,'cart_id'=> $this->_getQuote()->getEntityId(),'message'=>$productName.' has been added to your cart.')));              

		                    if ($cart->getQuote()->getHasError()) {
		                    	$errorMessages='';
		                    	foreach ($cart->getQuote()->getMessages() as $key => $value) {
		                    		$errorMessages=$errorMessages.$value->getCode();
		                    	}
		                    	return $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>$errorMessages)));
		                    }
		                }
		            }
		        } catch (Mage_Core_Exception $e) {
		            if ($this->_getSession()->getUseNotice(true)) {
		                return $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>$e->getMessage())));
		            } else {
		                $messageText = implode("\n", array_unique(explode("\n", $e->getMessage())));
		                return $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>$messageText)));
		            }
		        } catch (Exception $e) {

		        	return $jsonData = json_encode(array('cart_id'=>array('success'=>false,'message'=>'Can\'t add item to shopping cart'.$e->getMessage())));
		        }
		  		break;
		  		}
	  	}else{
	  		return json_encode(array('success'=>false,'message'=>'Product type parameters missing.'));
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
}
?>
