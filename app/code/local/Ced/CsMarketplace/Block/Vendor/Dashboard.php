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
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * CsMarketplace dashboard block
 *
 * @category   Ced
 * @package    Ced_CsMarketplace
 * @author     CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Block_Vendor_Dashboard extends Ced_CsMarketplace_Block_Vendor_Abstract
{
    protected $_subscription = null;

    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

	/**
     * Get vendor url in vendor dashboard
     *
     * @return string
     */
    public function getVendorUrl()
    {
        return Mage::getUrl('csmarketplace/vendor/edit', array('_secure'=>true));
    }

    /**
     * Get back url in vendor dashboard
     *
     * @return string
     */
    public function getBackUrl()
    {
        // the RefererUrl must be set in appropriate controller
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('csmarketplace/vendor/',array('_secure'=>true,'_nosid'=>true));
    }
}
