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

class Ced_Mobiconnectcheckout_Block_Onepage_Totals extends Mage_Checkout_Block_Cart_Totals
{
    protected $_totalRenderers;
    protected $_defaultRenderer = 'checkout/total_default';
    protected $_totals = null;

    public function getTotals()
    {
        if (is_null($this->_totals)) {
            return parent::getTotals();
        }
        return $this->_totals;
    }

    public function setTotals($value)
    {
        $this->_totals = $value;
        return $this;
    }

    protected function _getTotalRenderer($code)
    {

        $blockName = $code.'_total_renderer';
 
        $block = $this->getLayout()->getBlock($blockName);

        if (!$block) { 
            $block = $this->_defaultRenderer;
            if($code=='grand_total'){
                $config = 'mobiconnectcheckout/checkout_'.'grandtotal';
            }
            else{
                $config = 'mobiconnectcheckout/checkout_'.$code;
            }
            if ($config) {
                $block = (string) $config;
            }
           
            $block = $this->getLayout()->createBlock($block, $blockName);

        }
        /**
         * Transfer totals to renderer
         */
   
       $block->setTotals($this->getTotals());
        return $block;
    }

    public function renderTotal($total, $area = null, $colspan = 1)
    {
        $code = $total->getCode();
        if ($total->getAs()) {
            $code = $total->getAs();
        }
        return $this->_getTotalRenderer($code)
            ->setTotal($total)
            ->setColspan($colspan)
            ->setRenderingArea(is_null($area) ? -1 : $area)
            ->toHtml();
    }

    /**
     * Render totals html for specific totals area (footer, body)
     *
     * @param   null|string $area
     * @param   int $colspan
     * @return  string
     */
    public function renderTotals($area = null, $colspan = 1)
    {
        $html = '';
        foreach($this->getTotals() as $total) {
            if ($total->getArea() != $area && $area != -1) {
                continue;
            }
            $html .= $this->renderTotal($total, $area, $colspan);
        }
        
        return $html;
    }

}
