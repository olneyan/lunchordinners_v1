<?php

require_once 'Mage/Checkout/controllers/CartController.php';

class Ced_CsMarketplace_CartController extends Mage_Checkout_CartController {

    public function addAction() {

        $cart = $this->_getCart();
        $params = $this->getRequest()->getParams();

        if ($this->getRequest()->isXmlHttpRequest()) {

            $eligible = $this->eligible($params['product']);
            if ($eligible == "success") {
                $response = array();
                try {
                    if (isset($params['qty'])) {
                        $filter = new Zend_Filter_LocalizedToNormalized(
                                array('locale' => Mage::app()->getLocale()->getLocaleCode())
                        );
                        $params['qty'] = $filter->filter($params['qty']);
                    }

                    $product = $this->_initProduct();
                    $related = $this->getRequest()->getParam('related_product');

                    /**
                     * Check product availability
                     */
                    if (!$product) {
                        $response['status'] = 'ERROR';
                        $response['message'] = $this->__('Unable to find Product ID');
                    }

                    $cart->addProduct($product, $params);
                    if (!empty($related)) {
                        $cart->addProductsByIds(explode(',', $related));
                    }

                    $cart->save();

                    $this->_getSession()->setCartWasUpdated(true);

                    /**
                     * @todo remove wishlist observer processAddToCart
                     */
                    Mage::dispatchEvent('checkout_cart_add_product_complete', array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
                    );

                    if (!$cart->getQuote()->getHasError()) {
                        $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
                        $response['status'] = 'SUCCESS';
                        $response['message'] = $message;
                        //New Code Here
                        $this->loadLayout();
                        $toplink = $this->getLayout()->getBlock('top.links')->toHtml();
                        $sidebar_block = $this->getLayout()->getBlock('cart_sidebar');
                        Mage::register('referrer_url', $this->_getRefererUrl());
                        $sidebar = $sidebar_block->toHtml();
                        $response['toplink'] = $toplink;
                        $response['sidebar'] = $sidebar;
                    }
                } catch (Mage_Core_Exception $e) {
                    $msg = "";
                    if ($this->_getSession()->getUseNotice(true)) {
                        $msg = $e->getMessage();
                    } else {
                        $messages = array_unique(explode("\n", $e->getMessage()));
                        foreach ($messages as $message) {
                            $msg .= $message . '<br/>';
                        }
                    }

                    $response['status'] = 'ERROR';
                    $response['message'] = $msg;
                } catch (Exception $e) {
                    $response['status'] = 'ERROR';
                    $response['message'] = $this->__('Cannot add the item to shopping cart.');
                    Mage::logException($e);
                }
                $response["cart_sidebar"] = $this->getLayout()
                        ->createBlock('checkout/cart_sidebar')
                        ->setTemplate('csmarketplace/cart/minicart.phtml')
                        ->addItemRender("default", "checkout/cart_item_renderer", "checkout/cart/minicart/default.phtml")
                        ->addItemRender("simple", "checkout/cart_item_renderer", "checkout/cart/minicart/default.phtml")
                        ->addItemRender("grouped", "checkout/cart_item_renderer_grouped", "checkout/cart/minicart/default.phtml")
                        ->addItemRender("configurable", "checkout/cart_item_renderer_configurable", "checkout/cart/minicart/default.phtml")
                        ->toHtml();

                $cart = Mage::getSingleton('checkout/session')->getQuote();
                //$cart->getAllItems() to get ALL items, parent as well as child, configurable as well as it's simple associated item
                $qty = 0;
                foreach ($cart->getAllVisibleItems() as $item) {
                    $qty = $qty + $item->getQty();
                }

                //   $count = count($cart->getAllItems());

                $response['qty'] = $qty;

                //print_r($response);die("k");
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
                return;
            } else {
                $response = array();
                $response['status'] = "ERROR";
                $response['message'] = 'Only One Vendors Products Can be added into Cart';
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            }
        } else {
            return parent::addAction();
        }
    }

    public function eligible($products) {

        $cart = Mage::getSingleton('checkout/session')->getQuote();
        //$cart->getAllItems() to get ALL items, parent as well as child, configurable as well as it's simple associated item
        if (count($cart->getAllVisibleItems())) {

            foreach ($cart->getAllVisibleItems() as $item) {

                try {
                    $itemId = $item->getId();
                    $product = $item->getProduct();
                    $id = $product->getId();
                    $name = $product->getName();
                    $sku = $product->getSku();
                    $paramProductId = $products;

                    $vproduct = Mage::getModel('csmarketplace/vproducts')->getCollection()->addFieldToFilter('product_id', ['in' => $paramProductId])->addFieldToFilter('check_status', ['nin' => 3])->getFirstItem();
                    $vid = $vproduct->getVendorId();

                    $cartProduct = Mage::getModel('csmarketplace/vproducts')->getCollection()->addFieldToFilter('product_id', ['in' => $id])->addFieldToFilter('check_status', ['nin' => 3])->getFirstItem();
                    $cprovid = $cartProduct->getVendorId();
                    //   echo $cprovid; echo "<br>"; echo $vid; die("ifg");
                    if ($vid != $cprovid) {


                        $error = 'Only One Vendors Products Can be added into Cart';
                        //set false if you not want to add product to cart
                        //Mage::app()->getRequest()->setParam('product', false);
                        return "error";
                    } else {
                        return "success";
                    }
                } catch (Exception $e) {
                    Mage::getSingleton('checkout/session')->addError($e);
                }
            }
        } else {
            return "success";
        }
    }
}
