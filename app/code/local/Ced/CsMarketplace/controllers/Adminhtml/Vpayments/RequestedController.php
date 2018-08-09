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
 
class Ced_CsMarketplace_Adminhtml_Vpayments_RequestedController extends Ced_CsMarketplace_Controller_Adminhtml_AbstractController
{
	protected function _initAction() {
		$this->loadLayout()
			 ->_setActiveMenu('csmarketplace/vendor/entity')
			 ->_addBreadcrumb(Mage::helper('csmarketplace')->__('Vendor'), Mage::helper('csmarketplace')->__('Vendor'));
		return $this;
	}
	
	/**
     * Show Payment to Vendor Grid
     */
	public function indexAction() {
		$this->_initAction();
		$this->getLayout()->getBlock('head')->setTitle(Mage::helper('csmarketplace')->__('Requested Payments'));
		$this->renderLayout();
	}
	
	/**
	 * Ajax Grid Action
	 */
	public function gridAction() {
		$this->loadLayout()->_setActiveMenu('csmarketplace/vpayments_requested');
		$this->renderLayout();
	}
}