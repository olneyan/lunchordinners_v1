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
class Ced_CsMarketplace_VreportsController extends Ced_CsMarketplace_Controller_AbstractController {
	
	/**
	 * Default vendor products list page
	 */
	public function vordersAction() {
		if(!$this->_getSession()->getVendorId()) return;
		$this->loadLayout ();
		$this->_initLayoutMessages ( 'customer/session' );
		$this->_initLayoutMessages ( 'catalog/session' );		
		$this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Orders Report' ) );
		$this->renderLayout ();
	}
	

	/**
	 * Default vendor products list page
	 */
	public function vproductsAction() {
		if(!$this->_getSession()->getVendorId()) return;
		$this->loadLayout ();
		$this->_initLayoutMessages ( 'customer/session' );
		$this->_initLayoutMessages ( 'catalog/session' );
		$this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Products Report' ) );
		$this->renderLayout ();
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
    
   /**
    * Export Vproducts Reports Action
    */
    public function exportVproductsCsvAction()
    {
		if(!$this->_getSession()->getVendorId()) return;
    	$filename = 'vendor_products_report.csv';
    	$content = Mage::helper('csmarketplace/vreports_vproducts')->getCsvData();
    	$this->_prepareDownloadResponse($filename, $content);
    
    
    }
    
    /**
     * Export Vorders Reports Action
     */
    public function exportVordersCsvAction()
    {
		if(!$this->_getSession()->getVendorId()) return;
    	$filename = 'vendor_orders_report.csv';
    	$content = Mage::helper('csmarketplace/vreports_vorders')->getCsvData();
    	$this->_prepareDownloadResponse($filename, $content);
    
    
    }
    
    /**
     * Filter Vproducts Reports Action
     */
    public function filterVproductsAction()
    {
		if(!$this->_getSession()->getVendorId()) return;
    	$params = $this->getRequest()->getParams();
    	if(!isset($params['p']) && !isset($params['limit']) &&  is_array($params) )
    	Mage::getSingleton('core/session')->setData('vproducts_reports_filter',$params);
    
    	$this->loadLayout();
    	$navigationBlock = $this->getLayout()->getBlock('csmarketplace_vendor_navigation');
    	if ($navigationBlock) {
    		$navigationBlock->setActive('csmarketplace/vreports/vorders');
    	}
    	$this->renderLayout();
    }
    
    /**
     * Filter Vorders Reports Action
     */
    public function filterVordersAction()
    {
		if(!$this->_getSession()->getVendorId()) return;
    	$params = $this->getRequest()->getParams();
    	if(!isset($params['p']) && !isset($params['limit']) &&  is_array($params) )
    		Mage::getSingleton('core/session')->setData('vorders_reports_filter',$params);
   
    	$this->loadLayout();
    	$this->renderLayout();
    }
	
}
