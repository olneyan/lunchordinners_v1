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
  * @package   Ced_Mobiconnectcheckout
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Mage_Checkout_Helper_Url extends Mage_Core_Helper_Url
{
    /**
     * Retrieve shopping cart url
     *
     * @return string
     */
    public function getCartUrl()
    {
        return $this->_getUrl('checkout/cart');
    }

    /**
     * Retrieve checkout url
     *
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->_getUrl('checkout/onepage');
    }

    /**
     * Multi Shipping (MS) checkout urls
     */

    /**
     * Retrieve multishipping checkout url
     *
     * @return string
     */
    public function getMSCheckoutUrl()
    {
        return $this->_getUrl('checkout/multishipping');
    }

    public function getMSLoginUrl()
    {
        return $this->_getUrl('checkout/multishipping/login', array('_secure'=>true, '_current'=>true));
    }

    public function getMSAddressesUrl()
    {
        return $this->_getUrl('checkout/multishipping/addresses');
    }

    public function getMSShippingAddressSavedUrl()
    {
        return $this->_getUrl('checkout/multishipping_address/shippingSaved');
    }

    public function getMSRegisterUrl()
    {
        return $this->_getUrl('checkout/multishipping/register');
    }

    /**
     * One Page (OP) checkout urls
     */
    public function getOPCheckoutUrl()
    {
        return $this->_getUrl('checkout/onepage');
    }

    /**
     * Url to Registration Page
     *
     * @return string
     */
    public function getRegistrationUrl()
    {
        return $this->_getUrl('customer/account/create');
    }
}
