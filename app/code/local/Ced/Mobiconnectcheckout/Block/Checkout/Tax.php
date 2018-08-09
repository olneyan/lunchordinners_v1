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
class Ced_Mobiconnectcheckout_Block_Checkout_Tax extends Mage_Tax_Block_Checkout_Tax
{
    /**
     * Template used in the block
     *
     * @var string
     */
    protected $_template = 'mobiconnectcheckout/checkout/tax.phtml';

    /**
     * The factory instance to get helper
     *
     * @var Mage_Core_Model_Factory
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

    /**
     * Get all FPTs
     *
     * @return array
     */
    public function getAllWeee()
    {
        $allWeee = array();
        $store = $this->getTotal()->getAddress()->getQuote()->getStore();
        $helper = $this->_factory->getHelper('weee');

        if (!$helper->includeInSubtotal($store)) {
            foreach ($this->getTotal()->getAddress()->getCachedItemsAll() as $item) {
                foreach ($helper->getApplied($item) as $tax) {
                    $weeeDiscount = isset($tax['weee_discount']) ? $tax['weee_discount'] : 0;
                    $title = $tax['title'];
                    $rowAmount = isset($tax['row_amount']) ? $tax['row_amount'] : 0;
                    $rowAmountInclTax = isset($tax['row_amount_incl_tax']) ? $tax['row_amount_incl_tax'] : 0;
                    $amountDisplayed = ($helper->isTaxIncluded()) ? $rowAmountInclTax : $rowAmount;
                    if (array_key_exists($title, $allWeee)) {
                        $allWeee[$title] = $allWeee[$title] + $amountDisplayed - $weeeDiscount;
                    } else {
                        $allWeee[$title] = $amountDisplayed - $weeeDiscount;
                    }
                }
            }
        }
        return $allWeee;
    }
}
