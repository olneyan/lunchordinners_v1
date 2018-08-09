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

class Ced_CsMarketplace_Block_Adminhtml_Vorders_Grid_Renderer_Ack extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options
{
	public function render(Varien_Object $row){
		$html = parent::render($row);
		if($row->getVendorId()!='') {
			
			if($row->getPaymentState()==7){
				$url=Mage::getUrl('csorder/vorders');
			return '<input type="button"  onclick="acknowledge('.$row->getOrderId().')" value="Acknowledge">';
				
				}
				elseif ($row->getPaymentState()!=2 && $row->getOrderPaymentState()==2)
				{
					return $html.'<input type="button" onclick="request('.$row->getOrderId().')" value="Request For Payment">';
				}
				else{
					return $html;
				}
			}
	}
}