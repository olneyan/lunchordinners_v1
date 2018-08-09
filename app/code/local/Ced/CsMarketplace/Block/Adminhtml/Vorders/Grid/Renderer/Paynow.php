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

class Ced_CsMarketplace_Block_Adminhtml_Vorders_Grid_Renderer_Paynow extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options
{
	/**
     * get pay now button html
     *
     * @return string
     */
	protected function getPayNowButtonHtml($url = '')
    {
       return '<input class="button sacalable save" style="cursor: pointer; background: #ffac47 url("images/btn_bg.gif") repeat-x scroll 0 100%;border-color: #ed6502 #a04300 #a04300 #ed6502;    border-style: solid;    border-width: 1px;    color: #fff;    cursor: pointer;    font: bold 12px arial,helvetica,sans-serif;    padding: 1px 7px 2px;text-align: center !important; white-space: nowrap;" type="button" onclick="setLocation(\''.$url.'\')" value="PayNow">';
    }
	
	/**
     * Get refund button html
     *
     * @return string
     */
	protected function getRefundButtonHtml($url = '',$label = '')
    {
       return '<input class="button sacalable save" style="cursor: pointer; background: #ffac47 url("images/btn_bg.gif") repeat-x scroll 0 100%;border-color: #ed6502 #a04300 #a04300 #ed6502;    border-style: solid;    border-width: 1px;    color: #fff;    cursor: pointer;    font: bold 12px arial,helvetica,sans-serif;    padding: 1px 7px 2px;text-align: center !important; white-space: nowrap;" type="button" onclick="setLocation(\''.$url.'\')" value="RefundNow">';
    }
	
	protected function getPayNowButtonHtml123($url = '')
    {
       return $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('csmarketplace')->__('PayNow'),
                    'onclick'   => "setLocation('".$url."')",
                    'class'     => 'save'
                    ))->toHtml();
    }
	
	protected function getRefundButtonHtml123($url = '',$label = '')
    {
       return $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => $label,
                    'onclick'   => "setLocation('".$url."')",
                    'class'     => 'go'
                    ))->toHtml();
    }
	
	/**
	* Return the Order Id Link
	*
	*/
	public function render(Varien_Object $row){
		$html = parent::render($row);
		if($row->getVendorId()!='') {
			if ($row->canPay()) {
				$url =  $this->getUrl('*/adminhtml_vpayments/new/', array('vendor_id' => $row->getVendorId(), 'order_ids'=>$row->getId(),'type' => Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_CREDIT));
				$html .="&nbsp;".$this->getPayNowButtonHtml($url);
			} elseif ($row->canRefund()) {
				$url =  $this->getUrl('*/adminhtml_vpayments/new/', array('vendor_id' => $row->getVendorId(), 'order_ids'=>$row->getId(),'type' => Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_DEBIT));
				$html = $this->getRefundButtonHtml($url,$html);
			}
		}
		return $html;	
	}
}