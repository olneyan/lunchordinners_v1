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
 
class Ced_CsMarketplace_Block_Adminhtml_Vpayments_Edit_Tab_Addorder_Search extends Mage_Adminhtml_Block_Sales_Order_Create_Search
{
	public function getButtonsHtml()
	{
		$addButtonData = array(
				'label' => Mage::helper('sales')->__('Add Selected Amount(s) for Payment'),
				'onclick' => 'addorder()',
				'class' => 'add',
		);
		return $this->getLayout()->createBlock('adminhtml/widget_button')->setData($addButtonData)->toHtml();
	}
	
	public function getHeaderText(){
		return $this->__('Please Select Amount(s) to Add');
	}
}