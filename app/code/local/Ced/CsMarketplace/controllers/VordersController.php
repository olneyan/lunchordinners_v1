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
class Ced_CsMarketplace_VordersController extends Ced_CsMarketplace_Controller_AbstractController {
	
	 /**
     * Check order view availability
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  bool
     */
    protected function _canViewOrder($vorder)
    {
		if(!$this->_getSession()->getVendorId()) return;
		$vendorId = Mage::getSingleton('customer/session')->getVendorId();
		 
		$incrementId = $vorder->getOrder()->getIncrementId();
		
		$collection = Mage::getModel('csmarketplace/vorders')->getCollection();
		$collection->addFieldToFilter('id', $vorder->getId())
					->addFieldToFilter('order_id', $incrementId)
					->addFieldToFilter('vendor_id', $vendorId);
		
		if(count($collection)>0){
			return true;
		}else{
			return false;
		}
    }
	
	/**
     * Try to load valid order by order_id and register it
     *
     * @param int $orderId
     * @return bool
     */
    protected function _loadValidOrder($orderId = null)
    {
        if (null === $orderId) {
            $orderId = (int) $this->getRequest()->getParam('order_id',0);
        }
		$incrementId = 0;
        if ($orderId == 0) {
        	$incrementId = (int) $this->getRequest()->getParam('increment_id',0);
			 if (!$incrementId) {
				$this->_forward('noRoute');
				return false;
			}
        }

        if($orderId){
			$vorder = Mage::getModel('csmarketplace/vorders')->load($orderId);
        }
		else if($incrementId){
			$vendorId = Mage::getSingleton('customer/session')->getVendorId();
			$vorder = Mage::getModel('csmarketplace/vorders')->loadByField(array('order_id','vendor_id'),array($incrementId,$vendorId));
        }
		$order = $vorder->getOrder();
        if ($this->_canViewOrder($vorder)) {
	        Mage::register('current_order', $order);
			Mage::register('current_vorder', $vorder);
            return true;
        } else {
            $this->_redirect('*/*');
        }
        return false;
    }
	
	
	/**
	 * Default vendor products list page
	 */
	public function indexAction() {
		if(!$this->_getSession()->getVendorId()) return;
		$this->loadLayout ();
		$this->_initLayoutMessages ( 'customer/session' );
		$this->_initLayoutMessages ( 'catalog/session' );		
		$this->getLayout ()->getBlock ( 'head' )->setTitle ( Mage::helper('csmarketplace')->__ ('Orders List') );
		
		$params = $this->getRequest()->getParams();
		if(!isset($params['p']) && !isset($params['limit']) &&  is_array($params) ){
			Mage::getSingleton('core/session')->setData('order_filter',$params);
		}
	
		
		$this->renderLayout ();
	}
	
	
	/**
	 * Default vendor products list page
	 */
	public function viewAction() {
		if(!$this->_getSession()->getVendorId()) return;
        if (!$this->_loadValidOrder()) {
            return;
        }
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $navigationBlock = $this->getLayout()->getBlock('csmarketplace_vendor_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('csmarketplace/vorders/');
        }
        $this->renderLayout();
	}
	
	
	/**
	 * Export Payment Action
	 */
	public function exportCsvAction()
	{
		if(!$this->_getSession()->getVendorId()) return;
		$filename = 'vendor_orders.csv';
		$content = Mage::helper('csmarketplace/order')->getCsvData();
		$this->_prepareDownloadResponse($filename, $content,'text/csv');	
	
	}
	

	/**
	 * Print Order Action
	 */
	public function filterAction()
	{
		if(!$this->_getSession()->getVendorId()) return;
		$reset_filter = $this->getRequest()->getParam('reset_order_filter');
		$params = $this->getRequest()->getParams();
	
		if($reset_filter==1)
			Mage::getSingleton('core/session')->uns('order_filter');
		else if(!isset($params['p']) && !isset($params['limit']) &&  is_array($params) ){
			Mage::getSingleton('core/session')->setData('order_filter',$params);
		}
	
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
     * Print Order Action
     */
    public function printAction()
    {	
		if(!$this->_getSession()->getVendorId()) return;
        if (!$this->_loadValidOrder()) {
            return;
        }
        $this->loadLayout('print');
        $this->renderLayout();
    }
	
}
