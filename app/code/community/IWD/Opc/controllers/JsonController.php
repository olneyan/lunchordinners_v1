<?php

class IWD_Opc_JsonController extends Mage_Core_Controller_Front_Action
{

    public $opcLayouts = null;

    /**
     * @return Mage_Core_Model_Session
     */
    public function getCoreSession()
    {
        return Mage::getSingleton('core/session');
    }

    /**
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getOnepage()->getQuote();
    }

    /**
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    /**
     * @return IWD_Opc_Helper_Data
     */
    public function getOpcHelper()
    {
        return Mage::helper('iwd_opc');
    }

    /**
     * @return Mage_Checkout_Helper_Data
     */
    public function getCheckoutHelper()
    {
        return Mage::helper('checkout');
    }

    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->getRequest()->isAjax()) {
            $this->_redirectUrl(Mage::getBaseUrl('link', true));
            return $this;
        }

        if ($this->expireAjax()) {
            $this->getResponse()
            ->setHeader('HTTP/1.1', '403 Session Expired')
            ->setHeader('Login-Required', 'true')
            ->sendResponse();
            return $this;
        }

        if (!$this->getOpcHelper()->isEnable()) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return $this;
        }

        return $this;
    }

    public function expireAjax()
    {
        if (!$this->getOnepage()->getQuote()->hasItems()
            || $this->getOnepage()->getQuote()->getHasError()
            || $this->getOnepage()->getQuote()->getIsMultiShipping()
            || Mage::getSingleton('checkout/session')->getCartWasUpdated(true)
            ) {
            return true;
    }

    return false;
}

public function loadOpcLayout($name)
{
    if (!isset($this->opcLayouts[$name])) {
        Mage::app()->getCacheInstance()->cleanType('layout');
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load($name);
        $layout->generateXml();
        $layout->generateBlocks();
        $this->opcLayouts[$name] = $layout;
    }

    return $this->opcLayouts[$name];
}

public function getPaymentMethodsHtml()
{
    $store = $this->getQuote() ? $this->getQuote()->getStoreId() : null;
    if (count(Mage::helper('payment')->getStoreMethods($store, $this->getQuote())) == 1) {
        foreach (Mage::helper('payment')->getStoreMethods($store, $this->getQuote()) as $method) {
            $isApplicableToQuote = true;
            if ($this->getOpcHelper()->isNewMagento()) {
                $isApplicableToQuote = $method->isApplicableToQuote(
                    $this->getQuote(),
                    Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL
                    );
            }

            if ($this->canUseMethod($method) && $isApplicableToQuote) {
                $this->getCoreSession()->setData(
                    IWD_Opc_Helper_Data::IWD_OPC_CUSTOMER_PAYMENT_METHOD_CODE,
                    $method->getCode()
                    );
            }

            break;
        }
    }

    $layout = $this->loadOpcLayout('iwd_opc_onepage_payment_ajax');
//        $paymentMethods = $layout->getBlock('iwd_opc.onepage.payment.method');
    $paymentMethods = $layout->getBlock('iwd_opc.onepage.payment');
    return $paymentMethods->toHtml();
}

public function recollectQuoteTotals()
{
    if (!$this->getQuote()->getIsVirtual()) {
        $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
    }

    $this->getQuote()->setTotalsCollectedFlag(false)->collectTotals()->save();
    return $this;
}

public function canUseMethod($method)
{
    if (!$this->getOpcHelper()->isNewMagento()) {
        return true;
    }

    return $method->isApplicableToQuote(
        $this->getQuote(),
        Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY
        | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY
        | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX
        );
}

public function getShippingMethodsHtml()
{

 $shippingAddress = $this->getQuote()->getShippingAddress();
 if (!$shippingAddress->getShippingMethod()) {
    if (count($shippingAddress->getGroupedAllShippingRates()) == 1) {
        foreach ($shippingAddress->getGroupedAllShippingRates() as $rateGroupCode => $rates) {
            if (count($rates) == 1) {
                foreach ($rates as $rate) {
                    $this->getOnepage()->saveShippingMethod($rate->getCode());
                    $this->recollectQuoteTotals();
                    break;
                }

                break;
            } else {
                $this->getCoreSession()->setData(IWD_Opc_Helper_Data::IWD_OPC_CUSTOMER_SHIPPING_GROUP, $rateGroupCode);
            }
        }
    }
}

$layout = $this->loadOpcLayout('iwd_opc_onepage_shipping_method_ajax');
$shippingMethods = $layout->getBlock('iwd_opc.onepage.shipping.method');
return $shippingMethods->toHtml();

        /*$layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_shippingmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;*/

    }

    public function getReviewHtml()
    {
        $layout = $this->loadOpcLayout('iwd_opc_onepage_review_ajax');
        $review = $layout->getBlock('iwd_opc.onepage.review');
        return $review->toHtml();
    }

    public function saveBillingAddressAction()
    {
        $responseData = array();
        $responseData['status'] = false;
        try {
            $data = $this->prepareBillingData();

            if (!$this->getCustomerSession()->isLoggedIn()) {
                if (isset($data['create_account']) && $data['create_account'] == 1) {
                    $this->getOnepage()->saveCheckoutMethod(Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER);
                } else {
                    $this->getOnepage()->saveCheckoutMethod(Mage_Checkout_Model_Type_Onepage::METHOD_GUEST);
                }
            } else {
                $this->getOnepage()->saveCheckoutMethod(Mage_Checkout_Model_Type_Onepage::METHOD_CUSTOMER);
            }

            $billingAddressId = $this->getRequest()->getPost('billing_address_id', false);

            $result = $this->getOnepage()->saveBilling($data, $billingAddressId);
           // $result = $this->getOnepage()->saveShippingMethod('advflatrate_advflatrate');
            $this->getOnepage()->getQuote()->save();
            if (!isset($result['error'])) {
                $responseData['status'] = true;
                $responseData['shipping_methods'] = $this->getShippingMethodsHtml();
                $reloadShippingPaymentMethods = $this->getRequest()
                ->getPost('reload_shipping_and_payment_methods', false);
                if ($reloadShippingPaymentMethods) {
                    if (!$this->getQuote()->getIsVirtual()) {
                        $responseData['shipping_methods'] = $this->getShippingMethodsHtml();
                    }
                    $responseData['shipping_methods'] = $this->getShippingMethodsHtml();
                    $responseData['payment_methods'] = $this->getPaymentMethodsHtml();
                    $responseData['review'] = $this->getReviewHtml();
                }
            } else {
                $responseData['status'] = false;
                $responseData['message'] = $result['message'];
            }
        } catch (Exception $e) {
            $responseData['status'] = false;
            $responseData['message'] = $e->getMessage();
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }

    public function prepareBillingData()
    {
        $data = $this->getRequest()->getPost('billing', array());
        $data = $this->prepareData($data);
        if (!$this->getQuote()->getIsVirtual()) {
            if ($this->getOpcHelper()->isShowShippingForm()) {
                $data['use_for_shipping'] = 0;
            }

            if (!isset($data['use_for_shipping'])) {
                $data['use_for_shipping'] = 1;
            }
        }

        return $data;
    }

    public function preparePaymentData()
    {
        $data = $this->getRequest()->getPost('payment', array());
        $data = $this->prepareData($data);
        return $data;
    }

    public function prepareShippingData()
    {
        $data = $this->getRequest()->getPost('shipping', array());
        $data = $this->prepareData($data);
        if ($this->getOpcHelper()->isShowShippingForm()) {
            $data['same_as_billing'] = 0;
        }

        if (!isset($data['same_as_billing'])) {
            $data['same_as_billing'] = 0;
        }

        return $data;
    }

    public function prepareAgreementsData()
    {
        $data = $this->getRequest()->getPost('agreement', array());
        $data = $this->prepareData($data);
        return $data;
    }

    public function prepareData($data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->prepareData($value);
            } else {
                $data[$key] = trim($value);
            }
        }

        return $data;
    }

    public function saveShippingAddressAction()
    {
        $responseData = array();
        $responseData['status'] = false;
        try {
            $data = $this->prepareShippingData();
            $shippingAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $shippingAddressId);
            //$result = $this->getOnepage()->saveShippingMethod('advflatrate_advflatrate');
           // $this->getOnepage()->getQuote()->save();

           //print_r($this->getShippingMethodsHtml());

            if (!isset($result['error'])) {
                $responseData['status'] = true;
                $responseData['shipping_methods'] = $this->getShippingMethodsHtml();
                $reloadShippingPaymentMethods = $this->getRequest()
                ->getPost('reload_shipping_and_payment_methods', false);
                if ($reloadShippingPaymentMethods) {
                    $responseData['shipping_methods'] = $this->getShippingMethodsHtml();
                    $responseData['payment_methods'] = $this->getPaymentMethodsHtml();
                    $responseData['review'] = $this->getReviewHtml();
                }
            } else {
                $responseData['status'] = false;
                $responseData['message'] = $result['message'];
            }
        } catch (Exception $e) {
            $responseData['status'] = false;
            $responseData['message'] = $e->getMessage();
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }

    public function saveShippingMethodAction()
    {
        $responseData = array();
        $responseData['status'] = false;
        try {
            $shippingMethod = $this->getRequest()->getPost('shipping_method', '');
            $result = $this->getOnepage()->saveShippingMethod($shippingMethod);
            if (!$result) {
                $this->recollectQuoteTotals();
                $responseData['status'] = true;
                $responseData['review'] = $this->getReviewHtml();
                $responseData['payment_methods'] = $this->getPaymentMethodsHtml();
            }
        } catch (Exception $e) {
            $responseData['status'] = false;
            $responseData['message'] = $e->getMessage();
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }

    public function removeShippingMethodAction()
    {
        $responseData = array();
        $responseData['status'] = false;
        $shippingGroup = $this->getRequest()->getPost('shipping_group', '');
        $this->getCoreSession()->setData(IWD_Opc_Helper_Data::IWD_OPC_CUSTOMER_SHIPPING_GROUP, $shippingGroup);
        try {
            $quote = $this->getQuote();
            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setShippingAmount(0)
            ->setBaseShippingAmount(0)
            ->setShippingMethod('')
            ->setShippingDescription('');
            $this->recollectQuoteTotals();
            $responseData['review'] = $this->getReviewHtml();
            $responseData['payment_methods'] = $this->getPaymentMethodsHtml();
            $responseData['status'] = true;
        } catch (Exception $e) {
            $responseData['status'] = false;
            $responseData['message'] = $e->getMessage();
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }

    public function savePaymentAction()
    {
        $responseData = array();
        $responseData['status'] = false;
        try {
            $data = $this->preparePaymentData();
            $this->saveNewsletter();
            $result = $this->getOnepage()->savePayment($data);
            if (empty($result['error'])) {
                $responseData['status'] = true;
            } else {
                $responseData['message'] = $result['message'];
            }

            $layout = $this->loadOpcLayout('iwd_opc_centinel_authentication');
            $centinelFrame = $layout->getBlock('centinel.frame');
            if ($centinelFrame && trim($centinelFrame->toHtml())) {
                $responseData['centinel_frame'] = $centinelFrame->toHtml();
            }

            $redirectUrl = $this->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if ($redirectUrl) {
                $responseData['redirect_url'] = $redirectUrl;
            }
        } catch (Exception $e) {
            $responseData['status'] = false;
            $responseData['message'] = $e->getMessage();
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }

    public function savePaymentMethodCodeAction()
    {
        $paymentMethodCode = (string)$this->getRequest()->getPost('payment_method_code', '');
        $responseData = array();
        $responseData['status'] = false;
        try {
            if ($paymentMethodCode) {
                $this->getCoreSession()->setData(
                    IWD_Opc_Helper_Data::IWD_OPC_CUSTOMER_PAYMENT_METHOD_CODE, $paymentMethodCode
                    );
            } else {
                $this->getCoreSession()->unsetData(IWD_Opc_Helper_Data::IWD_OPC_CUSTOMER_PAYMENT_METHOD_CODE);
            }

            $responseData['status'] = true;
        } catch (Exception $e) {
            $responseData['status'] = false;
            $responseData['message'] = $e->getMessage();
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }

    public function saveOrderAction()
    {
        $responseData = array();
        $responseData['status'] = false;
        try {
            $requiredAgreements = $this->getCheckoutHelper()->getRequiredAgreementIds();
            if ($requiredAgreements) {
                $checkedAgreements = array_keys($this->prepareAgreementsData());
                if (array_diff($requiredAgreements, $checkedAgreements)) {
                    Mage::throwException(
                        $this->__('Please agree to all the terms and conditions before placing the order.')
                        );
                }
            }

            $this->saveNewsletter();
            $paymentData = $this->preparePaymentData();
            if ($paymentData) {
                if ($this->getOpcHelper()->isNewMagento()) {
                    $paymentData['checks'] = Mage_Payment_Model_Method_Abstract::CHECK_USE_CHECKOUT
                    | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY
                    | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY
                    | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX
                    | Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;
                }

                $this->getQuote()->getPayment()->importData($paymentData);
            }

            $this->getOnepage()->saveOrder();
            
            $this->getQuote()->setShippingMethod('flatrate_flatrate');

            $responseData['status'] = true;
            $redirectUrl = $this->getCheckoutSession()->getRedirectUrl();
        } catch (Mage_Payment_Model_Info_Exception $e) {
            Mage::logException($e);
            $this->getCheckoutHelper()->sendPaymentFailedEmail($this->getQuote(), $e->getMessage());
            $responseData['status'] = false;
            $responseData['message'] = $e->getMessage();
            $responseData['payment_methods'] = $this->getPaymentMethodsHtml();
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            $this->getCheckoutHelper()->sendPaymentFailedEmail($this->getQuote(), $e->getMessage());
            $responseData['status'] = false;
            $responseData['message'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $this->getCheckoutHelper()->sendPaymentFailedEmail($this->getQuote(), $e->getMessage());
            $responseData['status'] = false;
            $responseData['message'] =
            $this->__('There was an error processing your order. Please contact us or try again later.');
        }

        $this->getQuote()->save();

        if (isset($redirectUrl) && !empty($redirectUrl)) {
            $responseData['redirect_url'] = $redirectUrl;
        } else {
            $responseData['redirect_url'] = Mage::getUrl('checkout/onepage/success', array('_secure' => true));
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }

    public function prepareGiftMessageData()
    {
        $data = $this->getRequest()->getParam('giftmessage', array());
        $data = $this->prepareData($data);
        foreach ($data as $key => $arr) {
            if (isset($arr['type'])) {
                if (!isset($arr['from']) || !isset($arr['to']) || !isset($arr['message'])) {
                    $data[$key]['from'] = '';
                    $data[$key]['to'] = '';
                    $data[$key]['message'] = '';
                }
            }
        }

        $this->getRequest()->setParam('giftmessage', $data);
    }

    public function saveGiftMessageAction()
    {
        $responseData = array();
        $responseData['status'] = false;
        try {
            $this->prepareGiftMessageData();
            Mage::dispatchEvent(
                'checkout_controller_onepage_save_shipping_method',
                array(
                    'request' => $this->getRequest(),
                    'quote' => $this->getQuote()
                    )
                );
            $responseData['status'] = true;
        } catch (Exception $e) {
            $responseData['status'] = false;
            $responseData['message'] = $e->getMessage();
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }

    public function saveCommentAction()
    {
    	$databillingtip = Mage::app()->getRequest()->getParams();
    	$billingtip = $databillingtip['tip'];
    	Mage::getSingleton('core/session')->setBillingtip($billingtip);
        $comment = trim((string)$this->getRequest()->getPost('comment', ''));
        $responseData = array();
        $responseData['status'] = false;
        try {
            if ($comment) {
                $this->getCoreSession()->setData(IWD_Opc_Helper_Data::IWD_OPC_CUSTOMER_COMMENT, $comment);
            } else {
                $this->getCoreSession()->unsetData(IWD_Opc_Helper_Data::IWD_OPC_CUSTOMER_COMMENT);
            }

            $responseData['status'] = true;
        } catch (Exception $e) {
            $responseData['status'] = false;
            $responseData['message'] = $e->getMessage();
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }

    public function applyDiscountAction()
    {
        $couponCode = (string)$this->getRequest()->getPost('coupon_code', '');
        $responseData = array();
        $responseData['status'] = false;
        try {
            if (!empty($couponCode)) {
                if ($this->getOpcHelper()->isNewMagento()) {
                    $codeLength = strlen($couponCode);
                    $isCodeLengthValid = $codeLength && $codeLength <= Mage_Checkout_Helper_Cart::COUPON_CODE_MAX_LENGTH;
                    if (!$isCodeLengthValid) {
                        Mage::throwException(
                            $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($couponCode))
                            );
                    }
                }

                $this->getQuote()->setCouponCode($couponCode);
                $this->recollectQuoteTotals();
                if ($couponCode == $this->getQuote()->getCouponCode()) {
                    $responseData['status'] = true;
//                    $responseData['discount'] = $this->getDiscountHtml();
                    $responseData['review'] = $this->getReviewHtml();
                    $responseData['payment_methods'] = $this->getPaymentMethodsHtml();
                    if (!$this->getQuote()->getIsVirtual()) {
                        $responseData['shipping_methods'] = $this->getShippingMethodsHtml();
                    }
                } else {
                    Mage::throwException(
                        $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($couponCode))
                        );
                }
            } else {
                Mage::throwException($this->__('Please enter a coupon code.'));
            }
        } catch (Exception $e) {
            $responseData['status'] = false;
            $responseData['message'] = $e->getMessage();
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }

    public function removeDiscountAction()
    {
        $responseData = array();
        $responseData['status'] = false;
        try {
            $this->getQuote()->setCouponCode('');
            $this->recollectQuoteTotals();
            $responseData['status'] = true;
//            $responseData['discount'] = $this->getDiscountHtml();
            $responseData['review'] = $this->getReviewHtml();
            $responseData['payment_methods'] = $this->getPaymentMethodsHtml();
            if (!$this->getQuote()->getIsVirtual()) {
                $responseData['shipping_methods'] = $this->getShippingMethodsHtml();
            }
        } catch (Exception $e) {
            $responseData['status'] = false;
            $responseData['message'] = $e->getMessage();
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }

    public function saveNewsletter()
    {
        if ($this->getOpcHelper()->isShowSubscribe()) {
            $subscribe = (bool)$this->getRequest()->getPost('subscribe', false);
            if ($subscribe) {
                $this->getCoreSession()->setData(IWD_Opc_Helper_Data::IWD_OPC_CUSTOMER_SUBSCRIBE, $subscribe);
            } else {
                $this->getCoreSession()->unsetData(IWD_Opc_Helper_Data::IWD_OPC_CUSTOMER_SUBSCRIBE);
            }
        }
    }

    public function emailCheckAction()
    {
        $email = trim((string)$this->getRequest()->getPost('email'));
        $responseData = array();
        $responseData['status'] = false;
        try {
            $foundedEmails = $this->getCoreSession()->getData(IWD_Opc_Helper_Data::IWD_OPC_FOUNDED_CUSTOMERS);
            if (isset($foundedEmails[$email])) {
                $this->getCoreSession()->setData(IWD_Opc_Helper_Data::IWD_OPC_CUSTOMER_EMAIL, $email);
                $responseData['status'] = true;
            } elseif (Zend_Validate::is($email, 'EmailAddress')) {
                $this->getCoreSession()->setData(IWD_Opc_Helper_Data::IWD_OPC_CUSTOMER_EMAIL, $email);
                $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);
                if ($customer && $customer->getId()) {
                    $responseData['status'] = true;
                    $foundedEmails[$email] = $customer->getId();
                    $this->getCoreSession()
                    ->setData(IWD_Opc_Helper_Data::IWD_OPC_FOUNDED_CUSTOMERS, $foundedEmails);
                } else {
                    $responseData['status'] = false;
                }
            }
        } catch (Exception $e) {
            $responseData['status'] = false;
            $responseData['message'] = $e->getMessage();
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }

    public function loginAction()
    {
        $responseData = array();
        $responseData['status'] = false;
        $session = $this->getCustomerSession();
        $data = $this->getRequest()->getPost();
        $message = $this->__('Your login credentials are invalid.');
        if ($this->getOnepage()->getCheckoutMethod() != Mage_Checkout_Model_Type_Onepage::METHOD_GUEST) {
            $message = $this->__('You need to be logged in or create account during checkout to complete this order.');
        }

        if (!empty($data['email']) && !empty($data['password'])) {
            try {
                $responseData['status'] = $session->login($data['email'], $data['password']);
            } catch (Exception $e) {
                $responseData['status'] = false;
                $responseData['message'] = $message;
            }
        } else {
            $responseData['status'] = false;
            $responseData['message'] = $message;
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }

    public function resetPasswordAction()
    {
        $email = trim((string)$this->getRequest()->getPost('email'));
        $responseData = array();
        $responseData['status'] = false;
        $responseData['message'] = $this->__('Check your email for reset instructions.');
        try {
            if (Zend_Validate::is($email, 'EmailAddress')) {
                $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);
                if ($customer && $customer->getId()) {
                    $newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
                    $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                    $customer->sendPasswordResetConfirmationEmail();
                    $responseData['status'] = true;
                }
            }
        } catch (Exception $e) {
            $responseData['status'] = false;
            $responseData['message'] = $e->getMessage();
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }

    public function cancelAuthorizeNetPaymentAction()
    {
        $responseData = array();
        $responseData['status'] = false;
        try {
            $paymentMethod = Mage::helper('payment')->getMethodInstance(Mage_Paygate_Model_Authorizenet::METHOD_CODE);
            if ($paymentMethod) {
                $paymentMethod->cancelPartialAuthorization($this->getQuote()->getPayment());
            }

            $this->recollectQuoteTotals();
            $responseData['payment_methods'] = $this->getPaymentMethodsHtml();
            $responseData['status'] = true;
        } catch (Exception $e) {
            $responseData['status'] = false;
            $responseData['message'] = $e->getMessage();
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }

    public function applyStoreCreditAction()
    {
        $responseData = array();
        $responseData['status'] = false;
        try {
            $storeCredit = Mage::getModel('iwd_storecredit/storecredit')
            ->setCustomerId($this->getQuote()->getCustomerId())
            ->setWebsiteId($this->getQuote()->getStore()->getWebsiteId())
            ->loadByCustomer();
            if ($storeCredit->getId()) {
                $this->getQuote()->setUseStoreCredit(true);
                if (!$this->getCoreSession()->getData(IWD_Opc_Helper_Data::IWD_OPC_CUSTOMER_PAYMENT_METHOD_CODE)) {
                    $this->getCoreSession()->setData(
                        IWD_Opc_Helper_Data::IWD_OPC_CUSTOMER_PAYMENT_METHOD_CODE,
                        'free'
                        );
                }
            } else {
                $this->getQuote()->setUseStoreCredit(false);
            }

            $this->recollectQuoteTotals();
            $responseData['status'] = true;
            $responseData['review'] = $this->getReviewHtml();
            $responseData['payment_methods'] = $this->getPaymentMethodsHtml();
        } catch (Exception $e) {
            $responseData['status'] = false;
            $responseData['message'] = $e->getMessage();
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }

    public function removeStoreCreditAction()
    {
        $responseData = array();
        $responseData['status'] = false;
        try {
            $this->getQuote()->setUseStoreCredit(false);
            $this->recollectQuoteTotals();
            $responseData['status'] = true;
            $responseData['review'] = $this->getReviewHtml();
            $responseData['payment_methods'] = $this->getPaymentMethodsHtml();
        } catch (Exception $e) {
            $responseData['status'] = false;
            $responseData['message'] = $e->getMessage();
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }

    public function applyGiftCardAction()
    {
        $code = (string)$this->getRequest()->getPost('gift_code', '');
        $responseData = array();
        $responseData['status'] = false;
        try {
            if (!empty($code)) {
                $codeLength = strlen($code);
                $isCodeLengthValid = $codeLength &&
                $codeLength <= IWD_GiftCardAccount_Helper_Data::GIFT_CARD_CODE_MAX_LENGTH;
                if (!$isCodeLengthValid) {
                    Mage::throwException(
                        $this->__('Gift card code "%s" is not valid.', Mage::helper('core')->escapeHtml($code))
                        );
                }

                $gcModel = Mage::getModel('iwd_giftcardaccount/giftcardaccount');
                $gcModel->loadByCode($code)
                ->addToCart();
                if (!$this->getCoreSession()->getData(IWD_Opc_Helper_Data::IWD_OPC_CUSTOMER_PAYMENT_METHOD_CODE)) {
                    $this->getCoreSession()->setData(
                        IWD_Opc_Helper_Data::IWD_OPC_CUSTOMER_PAYMENT_METHOD_CODE,
                        'free'
                        );
                }

                $this->getQuote()->setGiftCardAccountApplied(true);
                $this->recollectQuoteTotals();
                $responseData['status'] = true;
                $responseData['review'] = $this->getReviewHtml();
                $responseData['payment_methods'] = $this->getPaymentMethodsHtml();
                if (!$this->getQuote()->getIsVirtual()) {
                    $responseData['shipping_methods'] = $this->getShippingMethodsHtml();
                }
            } else {
                Mage::throwException($this->__('Please enter a gift card code.'));
            }
        } catch (Exception $e) {
            $responseData['status'] = false;
            $responseData['message'] = $e->getMessage();
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }

    public function removeGiftCardAction()
    {
        $code = (string)$this->getRequest()->getPost('gift_code', '');
        $responseData = array();
        $responseData['status'] = false;
        try {
            if (!$code) {
                Mage::throwException($this->__('Please enter a gift card code.'));
            }

            $gcModel = Mage::getModel('iwd_giftcardaccount/giftcardaccount');
            $gcModel->loadByCode($code)
            ->removeFromCart();
            $this->recollectQuoteTotals();
            $responseData['status'] = true;
            $responseData['review'] = $this->getReviewHtml();
            $responseData['payment_methods'] = $this->getPaymentMethodsHtml();
        } catch (Exception $e) {
            $responseData['status'] = false;
            $responseData['message'] = $e->getMessage();
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }
    public function createaccountAction()
    {
        $data = Mage::app()->getRequest()->getParams();
        //print_r($data);
        //die();
        $store = Mage::app()->getStore();
        $website = Mage::app()->getWebsite();
        $websiteId = $website->getId();

        $firstName = $data['billing']['firstname'];
        $lastName = $data['billing']['lastname'];
        $email = $data['billing']['email'];
        $logFileName = 'my-log-file.log';

        $address = array(
            'customer_address_id' => '',
            'prefix' => '',
            'firstname' => $firstName,
            'middlename' => '',
            'lastname' => $lastName,
            'suffix' => '',
            'company' => '', 
            'street' => array(
                 '0' => $data['billing']['street'][0], // compulsory
                 '1' => '' // optional
                 ),
            'city' => $data['billing']['city'],
            'country_id' => $data['billing']['country_id'], // two letters country code
            'region' => '', // can be empty '' if no region
            'region_id' => $data['billing']['region_id'], // can be empty '' if no region_id
            'postcode' => $data['billing']['postcode'],
            'telephone' => $data['billing']['telephone'],
            'fax' => $data['billing']['fax'],
            'save_in_address_book' => $data['billing']['save_in_address_book'],
            );

// check if the email address is already registered
        $customer = Mage::getModel('customer/customer')
        ->setWebsiteId($websiteId)
        ->loadByEmail($email);

/** 
 * If email already registered, redirect to customer login page
 * Else create new customer 
 */ 
if ($customer->getId()) {        
    Mage::log('Customer with email '.$email.' is already registered.', null, $logFileName);
    
    if( !Mage::getSingleton('customer/session')->isLoggedIn() ) {             
        //Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('checkout/onepage')); 

        Mage::app()->getResponse()
        ->setRedirect(Mage::getUrl('customer/account/login'))
        ->sendResponse();

        //$this->_redirect('customer/account/login');        
    } else {         
        Mage::app()->getResponse()
        ->setRedirect(Mage::getUrl('customer/account'))
        ->sendResponse();
    }

} else {    
    $customer = Mage::getModel('customer/customer');
    
    $password = $data['billing']['login_password'];
    //$customer->generatePassword(); // you can write your custom password here instead of magento generated password
    
    $customer->setWebsiteId($websiteId)
    ->setStore($store)
    ->setFirstname($firstName)
    ->setLastname($lastName)
    ->setEmail($email)
    ->setPassword($password);

    try {
        // set the customer as confirmed
        $customer->setForceConfirmed(true);
        
        // save customer
        $customer->save();
        
        $customer->setConfirmation(null);
        $customer->save();
        
        // set customer address
        $customerId = $customer->getId();        
        $customAddress = Mage::getModel('customer/address');            
        $customAddress->setData($address)
        ->setCustomerId($customerId)
        ->setIsDefaultBilling('1')
        ->setIsDefaultShipping('1')
        ->setSaveInAddressBook('1');
        
        // save customer address
        $customAddress->save();
        
        // send new account email to customer    
        //$customer->sendNewAccountEmail();
        $storeId = $customer->getSendemailStoreId();
        $customer->sendNewAccountEmail('registered', '', $storeId);
        
        // set password remainder email if the password is auto generated by magento
        $customer->sendPasswordReminderEmail();
        
        // auto login customer
        //Mage::getSingleton('customer/session')->loginById($customer->getId());
        
        Mage::log('Customer with email '.$email.' is successfully created.', null, $logFileName);
        
        echo 'Customer with email '.$email.' is successfully created. <br />';
        
    } catch (Mage_Core_Exception $e) {
        if (Mage::getSingleton('customer/session')->getUseNotice(true)) {
            Mage::getSingleton('customer/session')->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
        } else {
            $messages = array_unique(explode("\n", $e->getMessage()));
            foreach ($messages as $message) {
                Mage::getSingleton('customer/session')->addError(Mage::helper('core')->escapeHtml($message));
            }
        }
    } catch (Exception $e) {
        //Zend_Debug::dump($e->getMessage());
        Mage::getSingleton('customer/session')->addException($e, $this->__('Cannot add customer'));
        Mage::logException($e);
        //$this->_goBack();
    } 
}
}


}
