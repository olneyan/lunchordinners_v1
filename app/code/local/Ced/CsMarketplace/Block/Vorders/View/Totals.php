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
class Ced_CsMarketplace_Block_Vorders_View_Totals extends Mage_Core_Block_Template
{

	/**
	 * Associated array of totals
	 * array(
	 *  $totalCode => $totalObject
	 * )
	 *
	 * @var array
	 */
	protected $_totals;
	protected $_order = null;
	protected $_vorder = null;
	
	/**
	 * Initialize self totals and children blocks totals before html building
	 *
	 * @return Mage_Sales_Block_Order_Totals
	 */
	protected function _beforeToHtml()
	{
		$this->_initTotals();
		foreach ($this->getChild() as $child) {
			if (method_exists($child, 'initTotals')) {
				$child->initTotals();
			}
		}
		return parent::_beforeToHtml();
	}
	
	/**
	 * Get order object
	 *
	 * @return Mage_Sales_Model_Order
	 */
	public function getOrder()
	{
		if ($this->_order === null) {
			if ($this->hasData('order')) {
				$this->_order = $this->_getData('order');
			} elseif (Mage::registry('current_order')) {
				$this->_order = Mage::registry('current_order');
			} elseif ($this->getParentBlock()->getOrder()) {
				$this->_order = $this->getParentBlock()->getOrder();
			}
		}
		return $this->_order;
	}
	
	/**
	 * Get order object
	 *
	 * @return Ced_CsMarketplace_Model_Vorders
	 */
	public function getVOrder()
	{
		if ($this->_vorder === null) {
			if ($this->hasData('vorder')) {
				$this->_vorder = $this->_getData('vorder');
			} elseif (Mage::registry('current_vorder')) {
				$this->_vorder = Mage::registry('current_vorder');
			} elseif ($this->getParentBlock()->getOrder()) {
				$orderId = (int) $this->getRequest()->getParam('order_id');
				$this->_vorder = Mage::getModel('csmarketplace/vorders')->load($orderId);
			}
		}
		return $this->_vorder;
	}
	
	public function setOrder($order)
	{
		$this->_order = $order;
		return $this;
	}
	
	/**
	 * Get totals source object
	 *
	 * @return Mage_Sales_Model_Order
	 */
	public function getSource()
	{
		return $this->getOrder();
	}
	
	/**
	 * Get totals vsource object
	 *
	 * @return Ced_CsMarketplace_Model_Vorders
	 */
	public function getVSource()
	{
		return $this->getVOrder();
	}
	
	/**
	 * Format total value based on order currency
	 *
	 * @param   Varien_Object $total
	 * @return  string
	 */
	public function formatValue($total)
	{
		if (!$total->getIsFormated()) {
			return $this->displayPrices(
					$this->getOrder(),
					$total->getBaseValue(),
					$total->getValue()
			);
		}
		return $total->getValue();
	}
	
	/**
	 * Get "double" prices html (block with base and place currency)
	 *
	 * @param   Varien_Object $dataObject
	 * @param   float $basePrice
	 * @param   float $price
	 * @param   bool $strong
	 * @param   string $separator
	 * @return  string
	 */
	public function displayPrices($dataObject, $basePrice, $price, $strong = false, $separator = '<br/>')
	{
		$order = false;
		if ($dataObject instanceof Mage_Sales_Model_Order) {
			$order = $dataObject;
		} else {
			$order = $dataObject->getOrder();
		}
	
		if ($order && $order->isCurrencyDifferent()) {
			$res= $order->formatBasePrice($basePrice);
			$res.= $separator;
			$res.= '['.$order->formatPrice($price).']';
		} elseif ($order) {
			$res = $order->formatPrice($price);
			if ($strong) {
				$res = $res;
			}
		} else {
			$res = Mage::app()->getStore()->formatPrice($price);
			if ($strong) {
				$res = $res;
			}
		}
		return $res;
	}
	
	/**
	 * Initialize order totals array
	 *
	 * @return Mage_Sales_Block_Order_Totals
	 */
	protected function _initTotals()
	{
		$vsource = $this->getVSource();
		$source = $this->getSource();
		$this->_totals = array();
		$this->_totals['subtotal'] = new Varien_Object(array(
				'code'      => 'subtotal',
				'value'     => $vsource->getPurchaseSubtotal(),
				'base_value'=> $vsource->getBaseSubtotal(),
				'label'     => $this->helper('sales')->__('Subtotal')
		));
	
		/**
		 * Add shipping
		*/
		/*if (!$this->getSource()->getIsVirtual() && ((float) $this->getSource()->getShippingAmount() || $this->getSource()->getShippingDescription()))
		{
			$this->_totals['shipping'] = new Varien_Object(array(
					'code'      => 'shipping',
					'value'     => $vsource->getShippingAmount(),
					'base_value'=> $vsource->getBaseShippingAmount(),
					'label' => $this->helper('sales')->__('Shipping & Handling')
			));
		}*/
	
		/**
		 * Add discount
		 */
		if (((float)$vsource->getBaseDiscountAmount()) != 0) {
			if ($this->getSource()->getDiscountDescription()) {
				$discountLabel = $this->helper('sales')->__('Discount (%s)', $this->getSource()->getDiscountDescription());
			} else {
				$discountLabel = $this->helper('sales')->__('Discount');
			}
			$this->_totals['discount'] = new Varien_Object(array(
					'code'      => 'discount',
					'value'     => $vsource->getPurchaseDiscountAmount(),
					'base_value'=> $vsource->getBaseDiscountAmount(),
					'label'     => $discountLabel
			));
		}
	
		$this->_totals['grand_total'] = new Varien_Object(array(
				'code'      => 'grand_total',
				'strong'    => true,
				'value'     =>$vsource->getPurchaseGrandTotal(),
				'base_value'=> $vsource->getBaseGrandTotal(),
				'label'     => $this->helper('sales')->__('Grand Total'),
				'area'      => 'footer'
		));
	
		/*$this->_totals['paid'] = new Varien_Object(array(
				'code'      => 'paid',
				'strong'    => true,
				'value'     => $this->getSource()->getTotalPaid(),
				'base_value'=> $this->getSource()->getBaseTotalPaid(),
				'label'     => $this->helper('sales')->__('Total Paid'),
				'area'      => 'footer'
		));
		$this->_totals['refunded'] = new Varien_Object(array(
				'code'      => 'refunded',
				'strong'    => true,
				'value'     => $this->getSource()->getTotalRefunded(),
				'base_value'=> $this->getSource()->getBaseTotalRefunded(),
				'label'     => $this->helper('sales')->__('Total Refunded'),
				'area'      => 'footer'
		));
		$this->_totals['due'] = new Varien_Object(array(
				'code'      => 'due',
				'strong'    => true,
				'value'     => $this->getSource()->getTotalDue(),
				'base_value'=> $this->getSource()->getBaseTotalDue(),
				'label'     => $this->helper('sales')->__('Total Due'),
				'area'      => 'footer'
		));*/
		return $this;
	}
	
	/**
	 * Add new total to totals array after specific total or before last total by default
	 *
	 * @param   Varien_Object $total
	 * @param   null|string|last|first $after
	 * @return  Mage_Sales_Block_Order_Totals
	 */
	public function addTotal(Varien_Object $total, $after=null)
	{
		if ($after !== null && $after != 'last' && $after != 'first') {
			$totals = array();
			$added = false;
			foreach ($this->_totals as $code => $item) {
				$totals[$code] = $item;
				if ($code == $after) {
					$added = true;
					$totals[$total->getCode()] = $total;
				}
			}
			if (!$added) {
				$last = array_pop($totals);
				$totals[$total->getCode()] = $total;
				$totals[$last->getCode()] = $last;
			}
			$this->_totals = $totals;
		} elseif ($after=='last')  {
			$this->_totals[$total->getCode()] = $total;
		} elseif ($after=='first')  {
			$totals = array($total->getCode()=>$total);
			$this->_totals = array_merge($totals, $this->_totals);
		} else {
			$last = array_pop($this->_totals);
			$this->_totals[$total->getCode()] = $total;
			$this->_totals[$last->getCode()] = $last;
		}
		return $this;
	}
	
	/**
	 * Add new total to totals array before specific total or after first total by default
	 *
	 * @param   Varien_Object $total
	 * @param   null|string $after
	 * @return  Mage_Sales_Block_Order_Totals
	 */
	public function addTotalBefore(Varien_Object $total, $before=null)
	{
		if ($before !== null) {
			if (!is_array($before)) {
				$before = array($before);
			}
			foreach ($before as $beforeTotals) {
				if (isset($this->_totals[$beforeTotals])) {
					$totals = array();
					foreach ($this->_totals as $code => $item) {
						if ($code == $beforeTotals) {
							$totals[$total->getCode()] = $total;
						}
						$totals[$code] = $item;
					}
					$this->_totals = $totals;
					return $this;
				}
			}
		}
		$totals = array();
		$first = array_shift($this->_totals);
		$totals[$first->getCode()] = $first;
		$totals[$total->getCode()] = $total;
		foreach ($this->_totals as $code => $item) {
			$totals[$code] = $item;
		}
		$this->_totals = $totals;
		return $this;
	}
	
	/**
	 * Get Total object by code
	 *
	 * @@return Varien_Object
	 */
	public function getTotal($code)
	{
		if (isset($this->_totals[$code])) {
			return $this->_totals[$code];
		}
		return false;
	}
	
	/**
	 * Delete total by specific
	 *
	 * @param   string $code
	 * @return  Mage_Sales_Block_Order_Totals
	 */
	public function removeTotal($code)
	{
		unset($this->_totals[$code]);
		return $this;
	}
	
	/**
	 * Apply sort orders to totals array.
	 * Array should have next structure
	 * array(
	 *  $totalCode => $totalSortOrder
	 * )
	 *
	 *
	 * @param   array $order
	 * @return  Mage_Sales_Block_Order_Totals
	 */
	public function applySortOrder($order)
	{
		return $this;
	}
	
	/**
	 * get totals array for visualization
	 *
	 * @return array
	 */
	public function getTotals($area=null)
	{
		$totals = array();
		if ($area === null) {
			$totals = $this->_totals;
		} else {
			$area = (string)$area;
			foreach ($this->_totals as $total) {
				$totalArea = (string) $total->getArea();
				if ($totalArea == $area) {
					$totals[] = $total;
				}
			}
		}
		return $totals;
	}
   

    /**
     * Initialize order totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
   /* protected function _initTotals()
    {
        $vsource = $this->getVSource();
		$source = $this->getSource();
        $this->_totals = array();
        $this->_totals['subtotal'] = new Varien_Object(array(
            'code'  => 'subtotal',
            'value' => $vsource->getBaseSubtotal(),
            'label' => $this->__('Subtotal')
        ));


        
        if (!$source->getIsVirtual() && ((float) $source->getShippingAmount() || $source->getShippingDescription()))
        {
            $this->_totals['shipping'] = new Varien_Object(array(
                'code'  => 'shipping',
                'field' => 'shipping_amount',
                'value' => $this->getSource()->getShippingAmount(),
                'label' => $this->__('Shipping & Handling')
            ));
        }

        
        if (((float)$this->getSource()->getDiscountAmount()) != 0) {
            if ($this->getSource()->getDiscountDescription()) {
                $discountLabel = $this->__('Discount (%s)', $source->getDiscountDescription());
            } else {
                $discountLabel = $this->__('Discount');
            }
            $this->_totals['discount'] = new Varien_Object(array(
                'code'  => 'discount',
                'field' => 'discount_amount',
                'value' => $vsource->getBaseDiscountAmount(),
                'label' => $discountLabel
            ));
        }

        $this->_totals['grand_total'] = new Varien_Object(array(
            'code'  => 'grand_total',
            'field'  => 'grand_total',
            'strong'=> true,
            'value' => $vsource->getBaseGrandTotal(),
            'label' => $this->__('Grand Total')
        ));

        
        if ($this->getOrder()->isCurrencyDifferent()) {
            $this->_totals['base_grandtotal'] = new Varien_Object(array(
                'code'  => 'base_grandtotal',
                'value' => $this->getOrder()->formatBasePrice($vsource->getBaseGrandTotal()),
                'label' => $this->__('Grand Total to be Charged'),
                'is_formated' => true,
            ));
        }
        return $this;
    }*/
}
