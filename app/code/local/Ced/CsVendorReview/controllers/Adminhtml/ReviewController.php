<?php 

/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License (AFL 3.0)
  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/afl-3.0.php
  *
  * @category    Ced
  * @package     Ced_CsVendorReview
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */  
 
class Ced_CsVendorReview_Adminhtml_ReviewController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Index Action
	 */
	public function indexAction(){

		$this->loadLayout()->_setActiveMenu('csmarketplace');
		$this->renderLayout();
    }
	
	/**
	 * Action to Edit the vendor review
	 */	
	public function editAction()
	{
		$id = $this->getRequest()->getParam('id', null);
		$model = Mage::getModel('csvendorreview/review')->load($id);
		
		if ($model->getId() || $id == 0) {
		
			Mage::register('csvendorreview_data', $model);
            $this->loadLayout()->_setActiveMenu('csmarketplace');
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			
            $this->_addContent($this->getLayout()
					->createBlock('csvendorreview/adminhtml_review_edit'))
					->_addLeft($this->getLayout()
					->createBlock('csvendorreview/adminhtml_review_edit_tabs'));
			
			$this->renderLayout();
		}
		else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('csvendorreview') ->__('This Rating Item does not exist'));
            $this->_redirect('*/*/');
        }
	}
	
	/**
	 * Action to Save the vendor review
	 */	
	public function saveAction()
	{
		if ($data = $this->getRequest()->getPost()){		
			$id = $this->getRequest()->getParam('id');
			if($id){
				$review = Mage::getModel('csvendorreview/review')->load($id);
				if($review && $review->getId()){
					$review->addData($data);
					$review->save();
				}
			}
			else{		
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('csvendorreview')->__('Review Item does not exists.'));
			}
		}
		$this->_redirect('*/*/index');
	}
	
	/**
	 * Action to delete the vendor review
	 */	 
	public function deleteAction()
	{
		if ($id = $this->getRequest()->getParam('id')) {
			try {
				
				$model = Mage::getModel('csvendorreview/review')->load($id);
				if($model && $model->getId()){
					$model->delete();		
				}							
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('csvendorreview')->__('Review has been deleted.'));
				$this->_redirect('*/*/index');
				return;
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/index', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('csvendorreview')->__('Unable to find the review to delete.'));
		$this->_redirect('*/*/index');
	}
	
	/**
	 * Vendor Reviews mass delete action
	 */	 
	public function massDeleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				foreach($this->getRequest()->getParam('id') as $id){	
					$reviews = Mage::getModel('csvendorreview/review')->load($id);
					if($reviews && $reviews->getId()){
						$reviews->delete();			
					}
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__("Reviews deleted successfully"));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
	
	/**
	 * Vendor's Review mass status change action
	 */	 
	public function massStatusAction()
	{
		$status = $this->getRequest()->getParam('status');
		$reviewIds = $this->getRequest()->getParam('id');
		if (!is_array($reviewIds)) {
			$this->_getSession()->addError(Mage::helper('csvendorreview')->__('Please select reviews.'));
		}
		else if(!empty($reviewIds)&& $status!='') {
			try{
				foreach($reviewIds as $id){	
					$reviews = Mage::getModel('csvendorreview/review')->load($id);
					if($reviews && $reviews->getId()){
						$reviews->setStatus($status);	
						$reviews->save();
					}
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('csmarketplace')->__("Status changed Successfully"));
			}
			catch(Exception $e)	{
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('%s',$e->getMessage()));
			}
		}			
		$this->_redirect('*/*/index');
	}

}
