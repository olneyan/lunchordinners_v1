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
 * CsVendorReview model for the csvendorreview/ratingitem collection
 */

class Ced_CsVendorReview_Model_MYsql4_Rating_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

	/**
	 * Initialize Constructor
	 */
	protected function _construct()
	{
		$this->_init('csvendorreview/rating');
	}
}