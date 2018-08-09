<?php 
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category  Ced
  * @package   Ced_Mobiconnectdeals
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobiconnectdeals_Block_Adminhtml_Deals_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('deal_group_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('mobiconnectdeals')->__('Deal Information'));
  }
  
	protected function _beforeToHtml() {
		/*if($this->getRequest()->getParam('id')){
			$model=Mage::getModel('mobiconnectdeals/deals')->load($this->getRequest()->getParam('id'));
			$jsondata=$model->getProductLink();
		}*/
		
		$this->addTab('group_info', array(
			'label'     => Mage::helper('mobiconnectdeals')->__('Deal Info'),
			'content'   => $this->getLayout()->createBlock('mobiconnectdeals/adminhtml_deals_edit_tab_form')->toHtml(),
		));
		/*
		if($this->getRequest()->getParam('id')){
			$this->addTab('deals_product', array(
			'label'     => Mage::helper('mobiconnectdeals')->__('Deal Products'),
			'content'   => $this->getLayout()->createBlock('mobiconnectdeals/adminhtml_deals_edit_tab_product')->toHtml().$this->getLayout()->createBlock('core/template')->setTemplate('mobiconnectdeals/product_js.phtml')->setProductsJson($jsondata)->toHtml(),
		));
		}
		if($this->getRequest()->getParam('id')){
			$this->addTab('deals_category', array(
			'label'     => Mage::helper('mobiconnectdeals')->__('Deal Categories'),
			'content'   => $this->getLayout()->createBlock('mobiconnectdeals/adminhtml_deals_edit_tab_categories')->toHtml(),
		));
		}*/
		return parent::_beforeToHtml();
	}
}
