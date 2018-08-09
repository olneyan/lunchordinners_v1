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

 
class Ced_CsVendorReview_Block_Rating_Form extends Mage_Core_Block_Template
{

	/**
	 * Initialize Constructor
	 */
	public function __construct()
    {
        $customerSession = Mage::getSingleton('customer/session');
		$vendor_id = $this->getVendor()->getId();
        parent::__construct();

        $customer = $customerSession->getCustomer();
        if ($customer && $customer->getId()) {
            $this->setCustomername($customer->getFirstname());
			$this->setCustomerid($customer->getId());
        }
		
        $this->setAllowWriteReviewFlag($customerSession->isLoggedIn());
		$this->setCustomerIsVendor($customerSession->isLoggedIn() && $customer->getId() == $vendor_id);
    }
	
	/**
     * Retrieve Vendor form the current vendor registry
     * @return Object
     */
	public function getVendor(){
						
		return Mage::registry('current_vendor');
	}
	
	/**
     * Get Collection of rating items
     * @return Object
     */
	public function getRatings()
    {
        $ratingCollection = Mage::getModel('csvendorreview/rating')->getCollection();
        return $ratingCollection;
    }
	
	/**
     * Retrieve Form post Action
     * @return string
     */
	public function getAction()
    {
        return Mage::getUrl('csvendorreview/rating/post');
    }

}
