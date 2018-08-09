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
  * @author   	 CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
class Ced_CsVAttribute_Block_Adminhtml_Vendor_Attributes_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
  public function __construct()
  {
	
      parent::__construct();
      $this->setId('attributes_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('catalog')->__('Attribute Information'));
  }
  protected function _beforeToHtml()
  {	
      $this->addTab('main', array(
            'label'     => Mage::helper('catalog')->__('Properties'),
            'title'     => Mage::helper('catalog')->__('Properties'),
            'content'   => $this->getLayout()->createBlock('csvattribute/adminhtml_vendor_attributes_edit_tab_main')->toHtml(),
            
        ));
		
     $this->addTab('labels', array(
            'label'     => Mage::helper('catalog')->__('Manage Label / Options'),
            'title'     => Mage::helper('catalog')->__('Manage Label / Options'),
            'content'   => $this->getLayout()->createBlock('csvattribute/adminhtml_vendor_attributes_edit_tab_options')->toHtml(),
        ));
		
	 $this->addTab('group', array(
            'label'     => Mage::helper('catalog')->__('Manage Attribute Group'),
            'title'     => Mage::helper('catalog')->__('Manage Attribute Group'),
            'content'   => $this->getLayout()->createBlock('csvattribute/adminhtml_vendor_attributes_edit_tab_group')->toHtml(),
        ));
      return parent::_beforeToHtml();
	  
  }
}