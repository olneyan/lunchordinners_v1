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
 
 /**
 * CsVendorReview model for the option array
 */

class Ced_CsVendorReview_Model_Options
{

	
	/**
	 * @Return array for Status of vendor review
	 */	
	public function getStatusOption()
    {
        return array(
			'0'	=> Mage::helper('csmarketplace')->__('Pending'),
			'1'	=> Mage::helper('csmarketplace')->__('Approved'),
			'2'	=> Mage::helper('csmarketplace')->__('Disapproved')
        );
    }
	
	/**
	 * Prepare the array of rating Option
	 * @Return array 
	 */	
	public function getRatingOption()
    {
        return array(
			'0'		=> Mage::helper('csmarketplace')->__('Please Select Option'),
			'20'	=> Mage::helper('csmarketplace')->__('1 OUT OF 5'),
			'40'	=> Mage::helper('csmarketplace')->__('2 OUT OF 5'),
			'60'	=> Mage::helper('csmarketplace')->__('3 OUT OF 5'),
			'80'	=> Mage::helper('csmarketplace')->__('4 OUT OF 5'),
			'100'	=> Mage::helper('csmarketplace')->__('5 OUT OF 5')
        );
    }
}
