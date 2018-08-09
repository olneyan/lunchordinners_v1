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
 * Vendor Orders model
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Model_Vorders extends Ced_CsMarketplace_Model_Abstract
{
	/**
     * Payment states
     */
    const STATE_OPEN       = 1;
    const STATE_PAID       = 2;
    const STATE_CANCELED   = 3;
	const STATE_REFUND     = 4;
	const STATE_REFUNDED   = 5;
	
    const ORDER_NEW_STATUS=1;
    const ORDER_CANCEL_STATUS=3;
    
	protected $_items = null;
	
	protected static $_states;
	
	protected $_eventPrefix      = 'csmarketplace_vorders';
    protected $_eventObject      = 'vorder';
	/**
     * Initialize resource model
     */
	protected function _construct()
	{
		$this->_init('csmarketplace/vorders');
	}
	
	/**
     * Retrieve vendor order states array
     *
     * @return array
     */
    public static function getStates()
    {
        if (is_null(self::$_states)) {
            self::$_states = array(
                self::STATE_OPEN       => Mage::helper('sales')->__('Pending'),
                self::STATE_PAID       => Mage::helper('sales')->__('Paid'),
                self::STATE_CANCELED   => Mage::helper('sales')->__('Canceled'),
				self::STATE_REFUND     => Mage::helper('csmarketplace')->__('Refund'),
				self::STATE_REFUNDED   => Mage::helper('csmarketplace')->__('Refunded'),
            );
        }
        return self::$_states;
    }
	
	/**
     * Check vendor order pay action availability
     *
     * @return bool
     */
    public function canPay()
    {
        return $this->getOrderPaymentState() == Mage_Sales_Model_Order_Invoice::STATE_PAID 
				&& 
			   $this->getPaymentState() == self::STATE_OPEN;
    }
	
	/**
     * Check vendor order cancel action availability
     *
     * @return bool
     */
    public function canCancel()
    {
        return $this->getPaymentState() == self::STATE_OPEN;
    }
	
	/**
     * Check vendor order refund action availability
     *
     * @return bool
     */
    public function canMakeRefund()
    {
        return $this->getOrderPaymentState() == Mage_Sales_Model_Order_Invoice::STATE_PAID 
				&& 
			   $this->getPaymentState() == self::STATE_PAID;
    }
	
	/**
     * Check vendor order refund action availability
     *
     * @return bool
     */
    public function canRefund()
    {
        return $this->getOrderPaymentState() == Mage_Sales_Model_Order_Invoice::STATE_PAID 
				&& 
			   $this->getPaymentState() == self::STATE_REFUND;
    }
	
	/**
     * Get Ordered Items associated to customer
	 * params: $order Object, $vendorId int
	 * return order_item_collection
     */
	 public function getItemsCollection($filterByTypes = array(), $nonChildrenOnly = false)
    {
        
		$incrementId = $this->getOrderId();
		$vendorId = $this->getVendorId();
		
		$order  = $this->getOrder();
		if (is_null($this->_items)) {
            $this->_items = Mage::getResourceModel('sales/order_item_collection')
                ->setOrderFilter($order)
				->addFieldToFilter('vendor_id', $vendorId);

            if ($filterByTypes) {
                $this->_items->filterByTypes($filterByTypes);
            }
            if ($nonChildrenOnly) {
                $this->_items->filterByParent();
            }

            if ($this->getId()) {
                foreach ($this->_items as $item) {
					if($item->getVendorId() == $vendorId)
	                    $item->setOrder($order);
                }
            }
        }
        return $this->_items;
    }
	
	/**
     * Get Ordered Items associated to customer
	 * params: $order Object, $vendorId int
	 * return order_item_collection
     */
	public function getOrder($incrementId = false){
		if(!$incrementId) $incrementId = $this->getOrderId();
		$order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
		return $order;
		
	}
	
	/**
     * Get Vordered Subtotal
	 * return float
     */
	public function getPurchaseSubtotal(){
		$items = $this->getItemsCollection();
		$subtotal  = 0;
		foreach($items as $_item){
			$subtotal +=$_item->getRowTotal();
		}
		return $subtotal;
	}
	
	/**
	 * Get Vordered base Subtotal
	 * return float
	 */
	public function getBaseSubtotal(){
		$items = $this->getItemsCollection();
		$basesubtotal  = 0;
		foreach($items as $_item){
			$basesubtotal +=$_item->getBaseRowTotal();
		}
		return $basesubtotal;
	}
	
	
	/**
     * Get Vordered Grandtotal
	 * return float
     */
	public function getPurchaseGrandTotal(){
		$items = $this->getItemsCollection();
		$grandtotal  = 0;
		foreach($items as $_item){
			$grandtotal +=$_item->getRowTotal()+ $_item->getTaxAmount()+ $_item->getHiddenTaxAmount()+ $_item->getWeeeTaxAppliedRowAmount()- $_item->getDiscountAmount();
		}
		return $grandtotal;
	}
	
	/**
	 * Get Vordered base Grandtotal
	 * return float
	 */
	public function getBaseGrandTotal(){
		$items = $this->getItemsCollection();
		$basegrandtotal  = 0;
		foreach($items as $_item){
			$basegrandtotal +=$_item->getBaseRowTotal()+ $_item->getBaseTaxAmount() + $_item->getBaseHiddenTaxAmount() + $_item->getBaseWeeeTaxAppliedRowAmount() - $_item->getBaseDiscountAmount();
		}
		return $basegrandtotal;
	}
	
	
	
	/**
	 * Get Vordered tax
	 * return float
	 */
	public function getPurchaseTaxAmount(){
		$items = $this->getItemsCollection();
		$tax  = 0;
		foreach($items as $_item){
			$tax +=$_item->getTaxAmount()+ $_item->getHiddenTaxAmount()+ $_item->getWeeeTaxAppliedRowAmount();
		}
		return $tax;
	}
	
	/**
	 * Get Vordered tax
	 * return float
	 */
	public function getBaseTaxAmount(){
		$items = $this->getItemsCollection();
		$tax  = 0;
		foreach($items as $_item){
			$tax +=$_item->getBaseTaxAmount()+ $_item->getBaseHiddenTaxAmount()+ $_item->getBaseWeeeTaxAppliedRowAmount();
		}
		return $tax;
	}
	
	/**
	 * Get Vordered Discount
	 * return float
	 */
	public function getPurchaseDiscountAmount(){
		$items = $this->getItemsCollection();
		$discount  = 0;
		foreach($items as $_item){
			$discount +=$_item->getDiscountAmount();
		}
		return $discount;
	}
	
	/**
	 * Get Vordered Discount
	 * return float
	 */
	public function getBaseDiscountAmount(){
		$items = $this->getItemsCollection();
		$discount  = 0;
		foreach($items as $_item){
			$discount +=$_item->getBaseDiscountAmount();
		}
		return $discount;
	}
	
	/**
	 * Calculate the commission fee
	 *
	 * @return Ced_CsMarketplace_Model_Vorders
	 */
	public function collectCommission() {
		if ($this->getData('vendor_id') && $this->getData('base_to_global_rate') && $this->getData('order_total')) {
			$order = $this->getOrder();
			$helper = Mage::helper('csmarketplace/acl')->setStoreId($order->getStoreId())->setOrder($order)->setVendorId($this->getData('vendor_id'));
			$commissionSetting = $helper->getCommissionSettings($this->getData('vendor_id'));
			$commissionSetting['item_commission'] = $this->getData('item_commission');
			$commission = $helper->calculateCommission($this->getData('order_total'),$this->getData('base_order_total'),$this->getData('base_to_global_rate'),$commissionSetting) ;
			/* print_r($commission);die; */
			$this->setShopCommissionTypeId($commissionSetting['type']);
			$this->setShopCommissionRate($commissionSetting['rate']);
			$this->setShopCommissionBaseFee($commission['base_fee']);
			$this->setShopCommissionFee($commission['fee']);
			$this->setPaymentState(self::STATE_OPEN);
			if(isset($commission['item_commission'])) {
				$this->setItemsCommission($commission['item_commission']);
			}
			$this->setOrderPaymentState(Mage_Sales_Model_Order_Invoice::STATE_OPEN);
		}
	}
	
}

?>