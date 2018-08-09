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
 
class Ced_CsVendorReview_RatingController extends Mage_Core_Controller_Front_Action
{	

	/**
     * Initialize requested vendor object
     *
     * @return Ced_CsMarketplace_Model_Vendor
     */
    protected function _initVendor()
    {      
        if(!Mage::getStoreConfig('ced_csmarketplace/vendorreview/activation')){
        	return false;
		}
		$id = $this->getRequest()->getParam('id');
        $vendor = Mage::getModel('csmarketplace/vendor')
				->setStoreId(Mage::app()->getStore()->getId())
				->load($id);
			
        if (!Mage::helper('csmarketplace')->canShow($vendor)) {
            return false;
        }
        Mage::register('current_vendor', $vendor);
        return $vendor;
    }

	/**
     * List Action 
     */
	public function listAction() {
		if($this->_initVendor()){
			$this->loadLayout();
			$this->renderLayout();
		}
		else{
			$this->_getSession()->addError('Vendor or vendor rating system is not active.');
			$this->_redirect('csmarketplace/vshops/');
		}
    }
	
	/**
     * Post Action
     */
	public function postAction() {
		$customerSession = Mage::getSingleton('customer/session');
		if($customerSession->isLoggedIn()){
			if($data = $this->getRequest()->getPost()){
				if(Mage::getStoreConfig('ced_csmarketplace/vendorreview/vendorapprval')){
					$msg = 'Your review has been submited for approval';
					$data['status'] = 0;
				}
				else{
					$msg = 'Your review has been submitted successfully';
					$data['status'] = 1;
				}
				try{
					$data['created_at'] = date("Y-m-d H:i:s");
					$review = Mage::getModel('csvendorreview/review');
					$review->addData($data);
					$review->save();
					$this->_getSession()->addSuccess($msg);
					$this->_redirect('*/*/list',array('id'=>$data['vendor_id']));
				}
				catch(Exception $e){
					$this->_getSession()->addError($ex->getMessage());
					$this->_redirect('*/*/list');
				}
			}
		}else{
			$this->_redirect('customer/account/login');
		}
    }
	
	/**
     * Get Core session
     */
	public function _getSession()
	{
		return Mage::getSingleton('core/session');
	}
}
