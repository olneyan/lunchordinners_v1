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
 
class Ced_CsVendorReview_Block_Adminhtml_Rating extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	/**
	 * Initialize Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_controller = 'adminhtml_rating';
		$this->_blockGroup = 'csvendorreview';
		$this->_headerText = Mage::helper('csvendorreview')->__('Manage Rating Items');
	}
	
}