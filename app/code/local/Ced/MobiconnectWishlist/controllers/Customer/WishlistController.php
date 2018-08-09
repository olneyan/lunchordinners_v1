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
  * @package   Ced_MobiconnectWishlist
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_MobiconnectWishlist_Customer_WishlistController extends Ced_MobiconnectWishlist_Controller_Action {
    public function addToWishlistAction() {
        $data = array (
                'prodID' => $this->getRequest ()->getParam ( 'prodID' ),
                'customer' => $this->getRequest ()->getParam ( 'customer_id' ),
                'hash' => $this->getRequest ()->getParam ( 'hashkey' ) 
        );
        
        $validateRequest = $this->validate ( $data );
        $wishlist = Mage::getModel ( 'mobiconnectwishlist/customer_wishlist' )->addToWishlist ( $data );
        // $this->printJsonData ( $wishlist );
        if (is_array ( $wishlist )) {
            $this->printJsonData ( $wishlist );
        }
    }
    public function listWishlistAction() {
        $data = array (
                'customer' => $this->getRequest ()->getParam ( 'customer_id' ),
                'hash' => $this->getRequest ()->getParam ( 'hashkey' ) 
        );
        $validateRequest = $this->validate ( $data );
        $wishlist = Mage::getModel ( 'mobiconnectwishlist/customer_wishlist' )->customerWishlist ( $data );
        if (is_array ( $wishlist ))
            $this->printJsonData ( $wishlist );
    }
    public function removeItemAction() {
        $data = array (
                'customer' => $this->getRequest ()->getParam ( 'customer_id' ),
                'hash' => $this->getRequest ()->getParam ( 'hashkey' ),
                'itemId' => $this->getRequest ()->getParam ( 'item_id' ) 
        );
        $validateRequest = $this->validate ( $data );
        $remove = Mage::getModel ( 'mobiconnectwishlist/customer_wishlist' )->removeItem ( $data );
        if (is_array ( $remove )) {
            $this->printJsonData ( $remove );
        }
    }
    public function clearWishlistAction() {
        $data = array (
                'customer' => $this->getRequest ()->getParam ( 'customer_id' ),
                'hash' => $this->getRequest ()->getParam ( 'hashkey' ) 
        );
        $validateRequest = $this->validate ( $data );
        $clear = Mage::getModel ( 'mobiconnectwishlist/customer_wishlist' )->clearWishlist ( $data );
        if (is_array ( $remove )) {
            $this->printJsonData ( $clear );
        }
    }
    public function updateWishlistAction() {
        $data = array (
                'customer' => $this->getRequest ()->getParam ( 'customer_id' ),
                'item_id' => $this->getRequest ()->getParam ( 'item_id' ),
                'item_qty' => $this->getRequest ()->getParam ( 'item_qty' ),
                'hash' => $this->getRequest ()->getParam ( 'hashkey' ) 
        );
        $validateRequest = $this->validate ( $data );
        $update = Mage::getModel ( 'mobiconnectwishlist/customer_wishlist' )->updatedWishlist ( $data );
        if (is_array ( $update )) {
            $this->printJsonData ( $update );
        }
    }
    protected function _getWishlist($wishlistId = null) {
        $wishlist = Mage::registry ( 'wishlist' );
        if ($wishlist) {
            return $wishlist;
        }
        
        try {
            if (! $wishlistId) {
                $wishlistId = $this->getRequest ()->getParam ( 'wishlist_id' );
            }
            $customerId = Mage::getSingleton ( 'customer/session' )->getCustomerId ();
            /* @var Mage_Wishlist_Model_Wishlist $wishlist */
            $wishlist = Mage::getModel ( 'wishlist/wishlist' );
            if ($wishlistId) {
                $wishlist->load ( $wishlistId );
            } else {
                $wishlist->loadByCustomer ( $customerId, true );
            }
            
            if (! $wishlist->getId () || $wishlist->getCustomerId () != $customerId) {
                $wishlist = null;
                Mage::throwException ( Mage::helper ( 'wishlist' )->__ ( "Requested wishlist doesn't exist" ) );
            }
            
            Mage::register ( 'wishlist', $wishlist );
        } catch ( Mage_Core_Exception $e ) {
            Mage::getSingleton ( 'wishlist/session' )->addError ( $e->getMessage () );
            return false;
        } catch ( Exception $e ) {
            Mage::getSingleton ( 'wishlist/session' )->addException ( $e, Mage::helper ( 'wishlist' )->__ ( 'Wishlist could not be created.' ) );
            return false;
        }
        
        return $wishlist;
    }
    public function cartAction() {
        
        // var_dump($this->getRequest()->getParams();
        $itemId = '174';
        
        /* @var $item Mage_Wishlist_Model_Item */
        $item = Mage::getModel ( 'wishlist/item' )->load ( $itemId );
        if (! $item->getId ()) {
            return $this->_redirect ( '*/*' );
        }
        $wishlist = $this->_getWishlist ( $item->getWishlistId () );
        if (! $wishlist) {
            return $this->_redirect ( '*/*' );
        }
        
        // Set qty
        $qty = $this->getRequest ()->getParam ( 'qty' );
        if (is_array ( $qty )) {
            if (isset ( $qty [$itemId] )) {
                $qty = $qty [$itemId];
            } else {
                $qty = 1;
            }
        }
        // $qty = $this->_processLocalizedQty($qty);
        if ($qty) {
            $item->setQty ( $qty );
        }
        
        /* @var $session Mage_Wishlist_Model_Session */
        $session = Mage::getSingleton ( 'wishlist/session' );
        $cart = Mage::getSingleton ( 'checkout/cart' );
        
        $redirectUrl = Mage::getUrl ( '*/*' );
        
        try {
            $options = Mage::getModel ( 'wishlist/item_option' )->getCollection ()->addItemFilter ( array (
                    $itemId 
            ) );
            // var_dump($options->getData());die;
            $item->setOptions ( $options->getOptionsByItem ( $itemId ) );
            
            $buyRequest = Mage::helper ( 'catalog/product' )->addParamsToBuyRequest ( $this->getRequest ()->getParams (), array (
                    'current_config' => $item->getBuyRequest () 
            ) );
            
            $item->mergeBuyRequest ( $buyRequest );
            if ($item->addToCart ( $cart, true )) {
                $cart->save ()->getQuote ()->collectTotals ();
            }
            
            $wishlist->save ();
            Mage::helper ( 'wishlist' )->calculate ();
            
            if (Mage::helper ( 'checkout/cart' )->getShouldRedirectToCart ()) {
                $redirectUrl = Mage::helper ( 'checkout/cart' )->getCartUrl ();
            }
            Mage::helper ( 'wishlist' )->calculate ();
            
            $product = Mage::getModel ( 'catalog/product' )->setStoreId ( Mage::app ()->getStore ()->getId () )->load ( $item->getProductId () );
            $productName = Mage::helper ( 'core' )->escapeHtml ( $product->getName () );
            $message = $this->__ ( '%s was added to your shopping cart.', $productName );
            Mage::getSingleton ( 'catalog/session' )->addSuccess ( $message );
        } catch ( Mage_Core_Exception $e ) {
            if ($e->getCode () == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                $session->addError ( $this->__ ( 'This product(s) is currently out of stock' ) );
            } else if ($e->getCode () == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                Mage::getSingleton ( 'catalog/session' )->addNotice ( $e->getMessage () );
                $redirectUrl = Mage::getUrl ( '*/*/configure/', array (
                        'id' => $item->getId () 
                ) );
            } else {
                Mage::getSingleton ( 'catalog/session' )->addNotice ( $e->getMessage () );
                $redirectUrl = Mage::getUrl ( '*/*/configure/', array (
                        'id' => $item->getId () 
                ) );
            }
        } catch ( Exception $e ) {
            Mage::logException ( $e );
            $session->addException ( $e, $this->__ ( 'Cannot add item to shopping cart' ) );
        }
        
        Mage::helper ( 'wishlist' )->calculate ();
        
        return $this->_redirectUrl ( $redirectUrl );
    }
}


