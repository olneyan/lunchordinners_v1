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
 * rating item admin form tabs
 */

class Ced_CsVendorReview_Block_Adminhtml_Rating_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	/**
	 * Initialize Constructor
	 */
	public function __construct(){
		parent::__construct();
		$this->setId('csvendorreview_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('csvendorreview')->__('Rating Items Information'));
	}
	
	/**
	 * @return Html for the general tab
	 */
	protected function _beforeToHtml(){
		$this->addTab('general', array(
			'label'     => Mage::helper('csvendorreview')->__('General'),
			'title'     => Mage::helper('csvendorreview')->__('General'),
			'content'   => $this->getLayout()->createBlock('csvendorreview/adminhtml_rating_edit_tab_general')->toHtml(),
		));
 
		return parent::_beforeToHtml();
	}
}