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
 * @category    Ced;
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_CsMarketplace_Block_Adminhtml_Vpayments_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('payment_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('csmarketplace')->__('Payment Information'));
  }
  protected function _beforeToHtml()
  {
	//$payment = Mage::getModel('csmarketplace/payment');
	$back = $this->getRequest()->getParam('back','');
	$vendorId = $this->getRequest()->getParam('vendor_id',0);
	$amount = $this->getRequest()->getPost('total',0);
	 if($back == 'edit' && $vendorId && $amount > 0) {
		 $this->addTab('form_section', array(
			  'label'     => Mage::helper('csmarketplace')->__('Payment Information'),
			  'title'     => Mage::helper('csmarketplace')->__('Payment Information'),
			  'content'   => $this->getLayout()->createBlock('csmarketplace/adminhtml_vpayments_edit_tab_paymentinformation')->setVendorId($vendorId)->toHtml(),
		  ));
	 } else {
		$this->addTab('order_section', array(
		  'label'     => Mage::helper('csmarketplace')->__('Payment Selection'),
		  'title'     => Mage::helper('csmarketplace')->__('Payment Selection'),
		  'content'   => $this->getLayout()->createBlock('csmarketplace/adminhtml_vpayments_edit_tab_addorder')->toHtml(),
		));
	  }
	  
	  
      
      return parent::_beforeToHtml();
  }
  
}