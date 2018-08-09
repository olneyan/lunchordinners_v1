<?php

require_once "Mage/Checkout/controllers/OnepageController.php";

class Ced_CsMarketplace_Checkout_OnepageController extends Mage_Checkout_OnepageController {

    public function preDispatch() {
        parent::preDispatch();
        
        $cartItems = Mage::getModel('checkout/cart')->getItems();
//        Zend_Debug::dump($cartItems);
        $productAvailibilityHelper = Mage::helper('lod_product_availability');
        $notAvailableProducts = [];
        foreach ($cartItems as $item) {
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            if (!$productAvailibilityHelper->isAvailableNow($product)) {
                $notAvailableProducts[] = $product->getName();
            }
        }
        if (count($notAvailableProducts) > 0) {
            $notAvailableProductsStr = implode("\n", $notAvailableProducts);
            Mage::getSingleton('checkout/session')
                    ->addError($this->__("Some products in your cart are not available now."));
            Mage::getSingleton('checkout/session')
                    ->addError($notAvailableProductsStr);
            
            $this->_redirect('checkout/cart');
            return;
        }

        return $this;
    }

    public function indexAction() {

        $params = $this->getRequest()->getPost();

        if (count($params)) {
            if (Mage::getSingleton('core/session')->getCartParams()) {
                Mage::getSingleton('core/session')->unsCartParams();
            }
            Mage::getSingleton('core/session')->setCartParams($params);
        }


        if (!Mage::helper('checkout')->canOnepageCheckout()) {
            Mage::getSingleton('checkout/session')->addError($this->__('The onepage checkout is disabled.'));
            $this->_redirect('checkout/cart');
            return;
        }
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message') ?
                    Mage::getStoreConfig('sales/minimum_order/error_message') :
                    Mage::helper('checkout')->__('Subtotal must exceed minimum order amount');

            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }
        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure' => true)));
        $this->getOnepage()->initCheckout();
        $this->loadLayout();


        $this->_initLayoutMessages('customer/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Checkout'));
        $this->renderLayout();
    }

    public function saveBillingjjjjAction() {



        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('billing', array());
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

            if (isset($data['email'])) {
                $data['email'] = trim($data['email']);
            }
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

            if (!isset($result['error'])) {
                /* check quote for virtual */
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
                    //add default shipping method
                    // $data = Mage::helper('stackexchange_checkout')->getDefaultShippingMethod();
                    $result = $this->getOnepage()->saveShippingMethod('advflatrate_advflatrate');
                    $this->getOnepage()->getQuote()->save();
                    /*
                      $result will have erro data if shipping method is empty
                     */
                    if (!$result) {
                        Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request' => $this->getRequest(),
                            'quote' => $this->getOnepage()->getQuote()));
                        $this->getOnepage()->getQuote()->collectTotals();
                        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

                        $result['goto_section'] = 'payment';
                        $result['update_section'] = array(
                            'name' => 'payment-method',
                            'html' => $this->_getPaymentMethodsHtml()
                        );
                    }

                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                } else {
                    $result['goto_section'] = 'shipping';
                }
            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    public function saveShippingjjjAction() {

        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

            //  $data = Mage::helper('stackexchange_checkout')->getDefaultShippingMethod();
            $result = $this->getOnepage()->saveShippingMethod('advflatrate_advflatrate');
            $this->getOnepage()->getQuote()->save();

            if (!isset($result['error'])) {
                $result['goto_section'] = 'payment';
                $result['update_section'] = array(
                    'name' => 'payment-method',
                    'html' => $this->_getPaymentMethodsHtml()
                );
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    public function progressdddAction() {
        die("fgkj");
        // previous step should never be null. We always start with billing and go forward
        $prevStep = $this->getRequest()->getParam('prevStep', false);

        if ($this->_expireAjax() || !$prevStep) {
            return null;
        }

        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        /* Load the block belonging to the current step */
        $update->load('checkout_onepage_progress_' . $prevStep);
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        $this->getResponse()->setBody($output);
        return $output;
    }

    /**
     * Save checkout billing address
     */
    public function saveBillingAction() {
        //print_r(Mage::getSingleton('core/session')->getCartParams()); 
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('billing', array());
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

            if (isset($data['email'])) {
                $data['email'] = trim($data['email']);
            }
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

            if (!isset($result['error'])) {
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
                    
                    $result['goto_section'] = 'shipping_method';
                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml()
                    );

                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                } else {
                    //$result['goto_section'] = 'shipping';
                     $result['goto_section'] = 'shipping_method';
                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml()
                    );

                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                }
            }

            $this->_prepareDataJSON($result);
        }
    }

    /**
     * Shipping address save action
     */
    public function saveShippingAction() {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

            if (!isset($result['error'])) {
                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );
            }
            $this->_prepareDataJSON($result);
        }
    }

//     public function saveBillingxxxAction()
//     {
//         if ($this->_expireAjax()) {
//             return;
//         }
//         if ($this->getRequest()->isPost()) {
//             $data = $this->getRequest()->getPost('billing', array());
//             $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
//             if (isset($data['email'])) {
//                 $data['email'] = trim($data['email']);
//             }
//             $result = $this->getOnepage()->saveBilling($data, $customerAddressId);
//             if (!isset($result['error'])) {
//                 /* check quote for virtual */
//                 if ($this->getOnepage()->getQuote()->isVirtual()) {
//                     $result['goto_section'] = 'payment';
//                     $result['update_section'] = array(
//                         'name' => 'payment-method',
//                         'html' => $this->_getPaymentMethodsHtml()
//                     );
//                 } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
//                     //add default shipping method
//                    // $data = Mage::helper('stackexchange_checkout')->getDefaultShippingMethod();
//                     $result = $this->getOnepage()->saveShippingMethod('advflatrate_advflatrate');
//                     $this->getOnepage()->getQuote()->save();
//                     /*
//                     $result will have erro data if shipping method is empty
//                     */
//                     if(!$result) {
//                         Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method',
//                             array('request'=>$this->getRequest(),
//                                 'quote'=>$this->getOnepage()->getQuote()));
//                         $this->getOnepage()->getQuote()->collectTotals();
//                         $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
//                         $result['goto_section'] = 'payment';
//                         $result['update_section'] = array(
//                             'name' => 'payment-method',
//                             'html' => $this->_getPaymentMethodsHtml()
//                         );
//                     }
//                     $result['allow_sections'] = array('shipping');
//                     $result['duplicateBillingInfo'] = 'true';
//                 } else {
//                     $result['goto_section'] = 'shipping';
//                 }
//             }
//             $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
//         }
//     }
//     public function saveShippingxxAction()
//     {
//         if ($this->_expireAjax()) {
//             return;
//         }
//         if ($this->getRequest()->isPost()) {
//             $data = $this->getRequest()->getPost('shipping', array());
//             $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
//             $result = $this->getOnepage()->saveShipping($data, $customerAddressId);
//           //  $data = Mage::helper('stackexchange_checkout')->getDefaultShippingMethod();
//             $result = $this->getOnepage()->saveShippingMethod('advflatrate_advflatrate');
//             $this->getOnepage()->getQuote()->save();
//             if (!isset($result['error'])) {
//                 $result['goto_section'] = 'payment';
//                 $result['update_section'] = array(
//                     'name' => 'payment-method',
//                     'html' => $this->_getPaymentMethodsHtml()
//                 );
//             }
//             $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
//         }
//     }
}
