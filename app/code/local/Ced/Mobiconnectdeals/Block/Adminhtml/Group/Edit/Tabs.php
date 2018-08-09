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
class Ced_Mobiconnectdeals_Block_Adminhtml_Group_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('group_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('mobiconnectdeals')->__('Group Information'));
  }

  protected function _beforeToHtml()
  {   
      $jsondata='';
      if($this->getRequest()->getParam('group_id')){
        $model=Mage::getModel('mobiconnectdeals/group')->load($this->getRequest()->getParam('group_id'));
        $ids=array_flip(explode(',', $model->getContent()));
        $jsondata=json_encode($ids);
      }

      $this->addTab('form_section', array(
          'label'     => Mage::helper('mobiconnectdeals')->__('Group Information'),
          'title'     => Mage::helper('mobiconnectdeals')->__('Group Information'),
          'content'   => $this->getLayout()->createBlock('mobiconnectdeals/adminhtml_group_edit_tab_form')->toHtml(),
      ));
      if($this->getRequest()->getParam('group_id')){
        $this->addTab('group_deals', array(
      'label'     => Mage::helper('mobiconnectdeals')->__('Assign Deals'),
      'content'   => $this->getLayout()->createBlock('mobiconnectdeals/adminhtml_group_edit_tab_assign')->toHtml().$this->getLayout()->createBlock('core/template')->setTemplate('mobiconnectdeals/deal_assign_js.phtml')->setProductsJson($jsondata)->toHtml(),
       ));
      }
      
     
      return parent::_beforeToHtml();
  }
}