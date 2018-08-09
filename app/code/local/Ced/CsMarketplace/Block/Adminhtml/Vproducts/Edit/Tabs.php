<?php 

/**
 * CedCommerce
 *  
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ced_CsMarketplace_Block_Adminhtml_Vproducts_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('csmarketplace_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('csmarketplace')->__('Product Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('general', array(
          'label'     => Mage::helper('csmarketplace')->__('General'),
          'title'     => Mage::helper('csmarketplace')->__('General'),
          'content'   => $this->getLayout()->createBlock('csmarketplace/adminhtml_vproducts_edit_tab_general')->toHtml(),
      ));
	 /*  $this->addTab('more_information', array(
          'label'     => Mage::helper('csmarketplace')->__('More Information'),
          'title'     => Mage::helper('csmarketplace')->__('More Information'),
          'content'   => $this->getLayout()->createBlock('csmarketplace/adminhtml_vproducts_edit_tab_more')->toHtml(),
      ));
	  $this->addTab('other', array(
          'label'     => Mage::helper('csmarketplace')->__('Other'),
          'title'     => Mage::helper('csmarketplace')->__('Other'),
          'content'   => $this->getLayout()->createBlock('csmarketplace/adminhtml_vproducts_edit_tab_other')->toHtml(),
      )); */
      return parent::_beforeToHtml();
  }
}