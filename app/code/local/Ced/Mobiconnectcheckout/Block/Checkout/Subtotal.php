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
class Ced_Mobiconnectcheckout_Block_Checkout_Subtotal extends Mage_Tax_Block_Checkout_Subtotal
{
    /**
     *  Template for the block
     *
     * @var string
     */
    protected $_template = 'mobiconnectcheckout/checkout/subtotal.phtml';

    /**
     * The factory instance to get helper
     *
     * @var Mage_Core_Model_Factory
     *
     */
    protected $_factory;

    /**
     * Initialize factory instance
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('core/factory');
    }

    public function displayBoth()
    {
        return Mage::getSingleton('tax/config')->displayCartSubtotalBoth($this->getStore());
    }
}
