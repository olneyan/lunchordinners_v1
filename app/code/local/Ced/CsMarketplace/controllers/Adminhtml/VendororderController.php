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
 
class Ced_CsMarketplace_Adminhtml_VendororderController extends Ced_CsMarketplace_Controller_Adminhtml_AbstractController
{

 	/**
     * Initialize order model instance
     *
     * @return Mage_Sales_Model_Order || false
     */
    protected function _initOrder(){
        $id = $this->getRequest()->getParam('id');
		$vorder = Mage::getModel('csmarketplace/vorders')->load($id);
        $order = Mage::getModel('sales/order')->loadByIncrementId($vorder->getOrderId());
        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }
	
	/**
     * Order Grid as per Vendor
     */
	public function indexAction() {
		
		$this->loadLayout()->_setActiveMenu('csmarketplace/vendororder');
		$this->getLayout()->getBlock('head')->setTitle(Mage::helper('csmarketplace')->__('Vendor Orders'));
		$this->renderLayout();
	}
	
	/**
	 * Vendor's order grid action
	 */
	public function gridAction() {
		$this->loadLayout()->_setActiveMenu('csmarketplace/vendororder');
		$this->renderLayout();
	}
}