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
class Ced_CsMarketplace_Block_Adminhtml_Vpayments_Edit_Tab_Paymentinformation_Default extends Mage_Core_Block_Template
{

	/**
     * Get amount description HTML
     *
     * @return string
     */
	 public function getHtml()
	 {
        $post = $this->getRequest()->getPost();
       // print_r($post) ; die("fi");
	 	$order_enable = $this->IsOrderEnable();
	 	if($order_enable)
	 	{
	 		$orderArray = $this->prepareOrderArrayWithCsOrder();
	 	
	 	}
	 	
	 	else
	 	{
	 		$orderArray = $this->prepareOrderArrayWithoutCsOrder();
	 	}
	 	$html = "";	
		$html .= '<div class="entry-edit">
					<div class="entry-edit-head">
						
						<h4 class="icon-head head-cart">'.Mage::helper("csmarketplace")->__("Order Amount(s)").'</h4>
					</div>
					<div class="grid" id="order-items_grid">
						<table cellspacing="0" class="data order-tables">
		 
							<col width="100" />
							<col width="40" />
							<col width="100" />
							<col width="80" />
							<thead>
								<tr class="headings">';
								foreach($orderArray['headers'] as $title ){
								$html.='<th class="no-link">'.Mage::helper("csmarketplace")->__($title).'</th>';
								}
								$html.='<th class="no-link">Total</th>	
								</tr>
							</thead>
							<tbody>';
							$class='';
							$trans_sum = 0;
							$serviceable_amount = 0;
							$commissionFee = 0;
							foreach($orderArray['values'] as $info){
								$class = ($class == 'odd')? 'even':'odd';
								$html.='<tr class="'.$class.'">';

								foreach($orderArray['headers'] as $key=>$title){
									
									$value=isset($info[$key])?$info[$key]:'';
									if($key == 'increment_id')
									$html.='<td>#'. $value.' <input type="hidden" name="processed_orders['.$value.']" value="'.$info['admin_fee'].'"></td>';
									else	
										$html.='<td>'. $this->formatPrice($value).'</td>';

									if($key == 'commission_fee'){
										$commissionFee += $value;
									}

								}
								$total = 0;
								foreach($orderArray['pricing_columns'] as $key){
									$price_valu = isset($info[$key])?$info[$key]:0;
									$total += $price_valu;
									if($price_valu < 0){
										$serviceable_amount += $price_valu;
									}
								}
								
								$html.='<td>'.$this->formatPrice($total).'</td></tr>';
								$html.= '<input type="hidden" name="commission_total" value="'.$commissionFee.'" />';
								
								$trans_sum += $total;
							}
							
		$html.='</tbody></table></div></div>';
		$service_tax = round($serviceable_amount * $orderArray['service_tax'] / 100, 2);
		$trans_sum += $service_tax;
		$html.='<h3>'.Mage::helper("csmarketplace")->__('Service Tax').' : '.$this->formatPrice($service_tax).'</h3>';
		$html.='<h3>'.Mage::helper("csmarketplace")->__('Total Amount').' : '.$this->formatPrice($trans_sum).'</h3>';
		
		$html .= '<script>document.getElementById("base_amount").value = '.$trans_sum.';</script>';
		return $html;
	 }

	 /*
	  * Prepare Array for order description
	  * @return string
	  */
	 
	 public function prepareOrderArrayWithCsOrder(){
	 	$orderArray = array();
	 	$data =$this->getRequest()->getPost();
	 	$shippingprice =array();
	 	$price = array();
	 	$comission = array();
	 	$ids =array();
	 	 if(isset($data['shippingcheck']))
	 	{
	 		foreach($data['shippingcheck'] as $key=>$value)
	 		{
	 			$ids[] =$key;
	 		}
	 	} 
	 
	 	 if(isset($data['shippings']))
	 	{
	 		foreach($data['shippings'] as $_id=>$shippingvalue)
	 		{
	 			if(in_array($_id,$ids))
	 			{
	 				$incrementid = Mage::getModel('csmarketplace/vorders')->load($_id)->getOrderId();
	 				$_ids = Mage::getModel('sales/order')->loadByIncrementId($incrementid)->getId();
	 				$shippingprice[$_ids] = $shippingvalue;
	 			}
	 			else 
	 			{
	 				
	 				$incrementid = Mage::getModel('csmarketplace/vorders')->load($_id)->getOrderId();
	 				$_ids = Mage::getModel('sales/order')->loadByIncrementId($incrementid)->getId();
	 				$shippingprice[$_ids] = 0;
	 			}
	 		}
	 	} 
	
	 	foreach($data['orders'] as $key=>$value)
	 	{
	 		if(is_array($value))
	 		{
	 			$item_price = 0;
	 			foreach($value as $itemId=>$itemPrice)
	 			{
	 				$item_price += $itemPrice;
	 			}
	 			$price[$key] = $item_price;
	 			 
	 		}
	 	}
	
	 	if(isset($data['comissionfee']))
	 	{
	 		foreach($data['comissionfee'] as $orderid=>$comissionfess)
	 		{
	 			
	 			$comissionprice_price = 0;
	 			if(is_array($comissionfess))
	 			{
		 			foreach($comissionfess as $itemid=>$comissionPrice)
		 			{
		 				$comissionprice_price += $comissionPrice;
		 			}
		 			
		 			$comission[$orderid] = $comissionprice_price;
	 			}
	 			
	 			else 
	 			{
	 				$comission[$orderid] = 0;
	 			}
	 		}
	
	 	}
	 	
	 	$vendorId = $this->getRequest()->getParam('vendor_id');
	 	$orderIds = $this->getRequest()->getParam('orders');
	 	$order_enable = Mage::getStoreConfig('ced_vorders/general/vorders_active');
		$orderArray['service_tax'] = Mage::getStoreConfig('ced_vpayments/general/service_tax');
	 	$orderArray['headers'] = array('increment_id'=>'Order Id', 'order_grand_total'=>'Grand Total', 'commission_fee'=>'Commision Fee');


	 	$orderArray['pricing_columns'] = array('order_grand_total', 'commission_fee');

	 	if($order_enable){
	 		$shipCheck = $this->getRequest()->getPost('shippingcheck');
	 		$shippings = $this->getRequest()->getPost('shippings');
	 		$shippingInfo = array();
	 		if(is_array($shipCheck))
	 		{
	 			$orderArray['headers']['shipping_fee'] = 'Shipping Fee';
	 			$orderArray['pricing_columns'][] = 'shipping_fee';
	 			foreach($shipCheck as $key=>$value)
	 			{
	 				if(isset($shippings[$key])){
	 					$shippingInfo[$key] = $shippings[$key];
	 				}
	 			}
	 		}
	 	}

	 	foreach($orderIds as $id=>$amount)
	 	{
	 		
	 		 if($order_enable){
	 			$inc_id = Mage::getModel('sales/order')->load($id)->getIncrementId();
	 		} 
	
	 		$order = Mage::getModel('csmarketplace/vorders')->getCollection()->addFieldToFilter('order_id', $inc_id)
								->addFieldToFilter('vendor_id', $vendorId)->getFirstItem();
	 	
	 			$orderArray['values'][$inc_id] = array('increment_id'=>$inc_id, 'order_grand_total'=>$price[$id],
								'commission_fee'=>$comission[$id]*-1);

	 		if($order_enable){
	 			if(isset($shippingprice[$id])){
	 				$orderArray['values'][$inc_id]['shipping_fee'] = +$shippingprice[$id];
	 			}
	 		}
	 		
	 	}	
	
	 	$object = new Varien_Object();
     		$object->setOrderArray($orderArray);
     		Mage::dispatchEvent('transaction_amountdescription_edit_prepare_orderarray', array('vendor_trans'=>$object));
		Mage::getSingleton('admin/session')->setVendorTransAmountOrder($object->getOrderArray());
	 	return $object->getOrderArray();
	 } 
	 
	 public function prepareOrderArrayWithoutCsOrder(){
	 	$orderArray = array();
	 	$vendorId = $this->getRequest()->getParam('vendor_id');
	 	$orderIds = $this->getRequest()->getParam('orders');
	 	$order_enable = $this->IsOrderEnable();
	 	 
	 	$orderArray['service_tax'] = Mage::getStoreConfig('ced_vpayments/general/service_tax');
	 	$orderArray['headers'] = array('increment_id'=>'Order Id', 'order_grand_total'=>'Grand Total', 'commission_fee'=>'Commision Fee');
	 	$orderArray['pricing_columns'] = array('amount');
	 
	 	if($order_enable){
	 		$shipCheck = $this->getRequest()->getPost('shippingcheck');
	 		$shippings = $this->getRequest()->getPost('shippings');
	 		$shippingInfo = array();
	 		if(is_array($shipCheck))
	 		{
	 			$orderArray['headers']['shipping_fee'] = 'Shipping Fee';
	 			$orderArray['pricing_columns'][] = 'shipping_fee';
	 			foreach($shipCheck as $key=>$value)
	 			{
	 				if(isset($shippings[$key])){
	 					$shippingInfo[$key] = $shippings[$key];
	 				}
	 			}
	 		}
	 	}
	 
	 	foreach($orderIds as $id=>$amount)
	 	{
	 		if($order_enable){
	 			$id = Mage::getModel('sales/order')->load($id)->getIncrementId();
	 		}
	 
	 		$order = Mage::getModel('csmarketplace/vorders')->getCollection()
	 		->addFieldToFilter('vendor_id', $vendorId)
	 		->addFieldToFilter('order_id', $id)->getFirstItem();
	 		$orderArray['values'][$id] = array('increment_id'=>$id, 'order_grand_total'=>$order->getBase_order_total(),
	 				'commission_fee'=>$order->getShop_commission_base_fee()*-1,'amount'=>$amount);
	 		if($order_enable){
	 			if(isset($shippingInfo[$order->getId()])){
	 				$orderArray['values'][$id]['shipping_fee'] = $shippingInfo[$order->getId()];
	 			}
	 		}
	 	}
	 	$object = new Varien_Object();
	 	//print_r($orderArray); die("dfl");
	 	$object->setOrderArray($orderArray);
	 	Mage::dispatchEvent('transaction_amountdescription_edit_prepare_orderarray', array('vendor_trans'=>$object));
	 	Mage::getSingleton('admin/session')->setVendorTransAmountOrder($object->getOrderArray());
	 	return $object->getOrderArray();
	 }
	 
	 public function IsOrderEnable(){
	 	if( Mage::helper('core')->isModuleEnabled('Ced_CsOrder') && Mage::helper('core')->isModuleEnabled('Ced_CsTransaction') && Mage::getStoreConfig('ced_vorders/general/vorders_active')){
	 		return true;
	 	}
	 	return false;
	 }
	 
	/**
     * Round price
     *
     * @param mixed $price
     * @return double
     */
    public function formatPrice($price)
    {
    	if(!$price)
    		return "-";
    	$baseCurrency = Mage::app()->getStore()->getBaseCurrencyCode();
        return Mage::app()->getLocale()
        		->currency($baseCurrency)
        		->toCurrency($price);
    }
	 
}