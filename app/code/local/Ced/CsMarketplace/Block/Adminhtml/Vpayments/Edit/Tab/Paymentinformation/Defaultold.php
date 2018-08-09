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

	 	$orderArray = $this->prepareOrderArray();
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
							foreach($orderArray['values'] as $info){
								$class = ($class == 'odd')? 'even':'odd';
								$html.='<tr class="'.$class.'">';

								foreach($orderArray['headers'] as $key=>$title){
									$value=isset($info[$key])?$info[$key]:'';
									$html.='<td>'. $value.'</td>';
								}
								$total = 0;
								foreach($orderArray['pricing_columns'] as $key){
									$price_valu = isset($info[$key])?$info[$key]:0;
									$total += $price_valu;
									if($price_valu < 0){
										$serviceable_amount += $price_valu;
									}
								}
								$html.='<td>'.$total.'</td></tr>';
								$trans_sum += $total;
							}
							
		$html.='</tbody></table></div></div>';
		$service_tax = round($serviceable_amount * $orderArray['service_tax'] / 100, 2);
		$trans_sum += $service_tax;
		$html.='<h3>'.Mage::helper("csmarketplace")->__('Service Tax').' : '.$service_tax.'</h3>';
		$html.='<h3>'.Mage::helper("csmarketplace")->__('Total Amount').' : '.$trans_sum.'</h3>';
		
		$html .= '<script>document.getElementById("base_amount").value = '.$trans_sum.';</script>';
		return $html;
	 }

	 /*
	  * Prepare Array for order description
	  * @return string
	  */
	public function prepareOrderArray(){
	 	$orderArray = array();
	 	$vendorId = $this->getRequest()->getParam('vendor_id');
	 	$orderIds = $this->getRequest()->getParam('orders');
	 	$order_enable = $this->IsOrderEnable();
	 	
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
	 			$id = Mage::getModel('sales/order')->load($id)->getIncrementId();
	 		}
	 		
	 		$order = Mage::getModel('csmarketplace/vorders')->getCollection()
	 						->addFieldToFilter('vendor_id', $vendorId)
	 						->addFieldToFilter('order_id', $id)->getFirstItem();
	 		$orderArray['values'][$id] = array('increment_id'=>$id, 'order_grand_total'=>$order->getBase_order_total(),
								'commission_fee'=>$order->getShop_commission_base_fee()*-1);
	 		if($order_enable){
	 			if(isset($shippingInfo[$order->getId()])){
	 				$orderArray['values'][$id]['shipping_fee'] = $shippingInfo[$order->getId()];
	 			}
	 		}
	 	}
	 	$object = new Varien_Object();
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

}