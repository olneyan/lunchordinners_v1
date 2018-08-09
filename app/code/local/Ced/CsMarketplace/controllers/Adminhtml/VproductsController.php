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
class Ced_CsMarketplace_Adminhtml_VproductsController extends Ced_CsMarketplace_Controller_Adminhtml_AbstractController
{
	/**
	 * Vendor's All products grid page
	 */
	public function indexAction(){		
	 $this->loadLayout()->_setActiveMenu('csmarketplace/vproducts');
	 $this->_addContent($this->getLayout()->createBlock('csmarketplace/adminhtml_vproducts'));
	 $this->getLayout()->getBlock('head')->setTitle(Mage::helper('csmarketplace')->__('Vendor Products'));
	 $this->renderLayout();	
	}
	
	/**
	 * Vendor's Pending products grid page
	 */
	public function pendingAction(){
		$this->loadLayout()->_setActiveMenu('csmarketplace/vproducts');
		Mage::register('usePendingProductFilter', true);
		$this->_addContent($this->getLayout()->createBlock('csmarketplace/adminhtml_vproducts'));
		$this->getLayout()->getBlock('head')->setTitle(Mage::helper('csmarketplace')->__('Vendor Products'));
		$this->renderLayout();	
	}
	
	/**
	* Vendor's Approved products grid page
	*/
	public function approvedAction(){
		$this->loadLayout()->_setActiveMenu('csmarketplace/vproducts');
		Mage::register('useApprovedProductFilter', true);
		$this->_addContent($this->getLayout()->createBlock('csmarketplace/adminhtml_vproducts'));
		$this->getLayout()->getBlock('head')->setTitle(Mage::helper('csmarketplace')->__('Vendor Products'));
		$this->renderLayout();
	
	}
	
	/**
	 * Vendor's all products grid action
	 */
	public function gridAction() {
		$this->loadLayout()->_setActiveMenu('csmarketplace/vproducts');
		$this->renderLayout();
	}
	

	/**
	 * Vendor's Approved product grid action
	 */
	public function gridapprovedAction() {
		$this->loadLayout()->_setActiveMenu('csmarketplace/vproducts');
		Mage::register('useApprovedProductFilter', true);
		$this->renderLayout();
	}
	

	/**
	 * Vendor's Pending product grid action
	 */
	public function gridpendingAction() {
		$this->loadLayout()->_setActiveMenu('csmarketplace/vproducts');
		Mage::register('usePendingProductFilter', true);
		$this->renderLayout();
	}
	
	/**
	 * Vendor's product mass status change action
	 */
	public function massStatusAction()
	{
		$checkstatus=$this->getRequest()->getParam('status');
		$productIds=$this->getRequest()->getParam('entity_id');
		if (!is_array($productIds)) {
			$this->_getSession()->addError(Mage::helper('csmarketplace')->__('Please select products(s).'));
		}
		else if(!empty($productIds)&& $checkstatus!='') {
			try{
				$errors=Mage::getModel('csmarketplace/vproducts')->changeVproductStatus($productIds,$checkstatus);
				if($errors['success'])
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('csmarketplace')->__("Status changed Successfully"));
				 /*  event for fav seller starts */
					if($checkstatus==1){
					 Mage::dispatchEvent('product_approve', array(
					   'product_id'   => $productIds,
					 ));
					} 
				/*  event for fav seller ends */
				if($errors['error'])
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('csmarketplace')->__('Can\'t process approval/disapproval for some products.Some of Product\'s vendor(s) are disapproved or not exist.'));
			}
			catch(Exception $e)	{
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('%s',$e->getMessage()));
			}
		}
		$this->getResponse()->setRedirect($this->_getRefererUrl());
	}
	
	/**
	 * Vendor's product status change action
	 */
	public function changeStatusAction()
	{
		$checkstatus=$this->getRequest()->getParam('status');	
		if( $this->getRequest()->getParam('id') > 0 && $checkstatus!='') {
			try{
				$errors=Mage::getModel('csmarketplace/vproducts')->changeVproductStatus(array($this->getRequest()->getParam('id')),$checkstatus);
				/*  event for fav seller starts */
					if($checkstatus==1){
					 Mage::dispatchEvent('product_approve', array(
					   'product_id'   => $this->getRequest()->getParam('id'),
					 ));
					}
				/*  event for fav seller ends */
				if($errors['success'])
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('csmarketplace')->__("Status changed Successfully"));
				if($errors['error'])
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('csmarketplace')->__("Can't process approval/disapproval for the Product.The Product's vendor is disapproved or not exist."));
			}
			catch(Exception $e){
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('%s',$e->getMessage()));
			}
		}		
		$this->getResponse()->setRedirect($this->_getRefererUrl());
	}
	
	/**
	 * Vendor's product mass delete action
	 */
	public function massDeleteAction() {
		if( $this->getRequest()->getParam('entity_id') > 0 ) {
			$ids=array();
			try {
				foreach($this->getRequest()->getParam('entity_id') as $id){	
					$product=Mage::getModel('catalog/product')->load($id);
					if($product&&$product->getId()){
						$product->delete();			
						$ids[]=$id;
					}
				}
				$errors=Mage::getModel('csmarketplace/vproducts')->changeVproductStatus($ids,Ced_CsMarketplace_Model_Vproducts::DELETED_STATUS);
				if($errors['success'])
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__("CsMarketplace's Products deleted successfully"));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}

		$this->getResponse()->setRedirect($this->_getRefererUrl());
	}
	
	/**
	 * Vendor's configuration category action
	 */
	public function categoriesJsonAction()
	{
		$this->getResponse()->setBody(
				$this->getLayout()->createBlock('csmarketplace/adminhtml_system_config_frontend_vproducts_categories')
				->getCategoryChildrenJson($this->getRequest()->getParam('category'))
		);
	}
		
	
}