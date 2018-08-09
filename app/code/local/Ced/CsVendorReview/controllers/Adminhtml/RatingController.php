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
 
class Ced_CsVendorReview_Adminhtml_RatingController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Index Action
	 */
	public function indexAction()
	{
		$this->loadLayout()->_setActiveMenu('csmarketplace');
		$this->renderLayout();
    }
	
	/**
	 * New Action
	 */
	public function newAction()
	{
		$this->_forward('edit');
	}
	
	/**
	 * Edit Action
	 */
	public function editAction()
	{
		$id = $this->getRequest()->getParam('id', null);
		$model = Mage::getModel('csvendorreview/rating')->load($id);
		
		if ($model->getId() || $id == 0) {
		
			Mage::register('csvendorreview_data', $model);
            $this->loadLayout()->_setActiveMenu('csmarketplace');
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			
            $this->_addContent($this->getLayout()
					->createBlock('csvendorreview/adminhtml_rating_edit'))
					->_addLeft($this->getLayout()
					->createBlock('csvendorreview/adminhtml_rating_edit_tabs'));
			
			$this->renderLayout();
		}
		else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('csvendorreview')->__('This Rating Item does not exist'));
            $this->_redirect('*/*/');
        }
	}
	
	/**
	 * Save Action
	 */
	public function saveAction()
	{
		if ($data = $this->getRequest()->getPost()){
		
			$id = $this->getRequest()->getParam('id');
			if($id){
			
				$rating = Mage::getModel('csvendorreview/rating')->load($id);
				$rating->setRatingLabel($data['rating_label']);
				$rating->setSortOrder($data['sort_order']);
				$rating->save();
			}
			else
			{
				// new
				$rating = Mage::getModel('csvendorreview/rating');
				$collection = $rating->getCollection()->addFieldToFilter('rating_code',$data['rating_code']);
				if(count($collection) < 1){
					$rating->setData($data);
					$rating->save();
					$installer = New Ced_CsVendorReview_Model_Mysql4_Setup('core_setup');
					$installer->run(" ALTER TABLE `{$installer->getTable('csvendorreview/review')}` ADD `{$data['rating_code']}` INT");
					$installer->endSetup();
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('csvendorreview')->__('Rating Item has been created.'));
				}
				else{
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('csvendorreview')->__('Rating Item with the same code already exists.'));
				}
			}
		}
		$this->_redirect('*/*/index');
	}
	
	/**
	 * Action to delete the brands
	 */
	public function deleteAction()
	{
		if ($id = $this->getRequest()->getParam('id')) {
			try {
				
				$model = Mage::getModel('csvendorreview/rating')->load($id);
				$column = $model->getRatingCode();
				$model->delete();
				
				$installer = New Ced_CsVendorReview_Model_Mysql4_Setup('core_setup');
				$installer->run(" ALTER TABLE `{$installer->getTable('csvendorreview/review')}` DROP COLUMN `{$column}`");
				$installer->endSetup();
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('csvendorreview')->__('Rating Item has been deleted.'));
				$this->_redirect('*/*/index');
				return;
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/index', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('csvendorreview')->__('Unable to find the rating item to delete.'));
		$this->_redirect('*/*/index');
	}
	
}
