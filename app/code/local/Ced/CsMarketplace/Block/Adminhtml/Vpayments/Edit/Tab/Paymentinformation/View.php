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
class Ced_CsMarketplace_Block_Adminhtml_Vpayments_Edit_Tab_Paymentinformation_View extends Mage_Adminhtml_Block_Widget_Form
{	

	protected function _toHtml() {
		$html = '';
		$details = $this->getDetails();
		if(count($details) > 0) {
			$html .= '<div class="config-heading" style="width: 241.5px;">';
			foreach($details as $field=>$detail) {
				$html .= isset($detail['label'])?'<b>'.$detail['label'].' : </b>':'<b>'.$field.' : </b>';
				$html .= isset($detail['value'])?$this->renderer($detail['value']).'<br/>':'<br/>';
			}
			$html .= '</div>';
		}
		$html .= '<input type="hidden" name="payment_detail" value="'.$this->escapeHtml($html).'"/>';
		return $html;
	}
	
	protected function renderer($value){
		if (preg_match("/^(http|https|ftp):\/\/(([A-Z0-9]([A-Z0-9_-]*[A-Z0-9]|))(\.[A-Z0-9]([A-Z0-9_-]*[A-Z0-9]|))*)(:(\d+))?(\/[A-Z0-9~](([A-Z0-9_~-]|\.)*[A-Z0-9~]|))*\/?(.*)?$/i",$value)) {
			return '<a target="_blank" href="'.$value.'">'.$value.'</a>';
		} else if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
			return '<a href="mailto:'.$value.'">'.$value.'</a>';
		} else {
			return '<a href="javascript:void(0);" style="text-decoration: none;">'.$value.'</a>';
		}
	}

}