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
  * @package     Ced_CsVAttribute
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

class Ced_CsVAttribute_Block_Adminhtml_Vendor_Attributes extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct() { 
		$this->_controller = 'adminhtml_vendor_attributes';
		$this->_blockGroup = 'csvattribute';
		$this->_headerText = Mage::helper('csmarketplace')->__("Manage Vendor Attributes");
		$this->_addButtonLabel = Mage::helper('csmarketplace')->__("Add Vendor Attribute");
		parent::__construct();
	}

	/**
     * Redefine header css class
     *
     * @return string
     */
    public function getHeaderCssClass() {
        return 'icon-head head-catalog-product-attribute';
    }
}