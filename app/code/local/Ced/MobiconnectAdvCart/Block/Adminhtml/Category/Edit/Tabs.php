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
  * @package   Ced_MobiconnectAdvCart
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_MobiconnectAdvCart_Block_Adminhtml_Category_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('category_banner_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('mobiconnectadvcart')->__('Banner Information'));
  }
  
	protected function _beforeToHtml() {
		 $jsondata='';
      if($this->getRequest()->getParam('id')){
        $model=Mage::getModel('mobiconnectadvcart/categorybanner')->load($this->getRequest()->getParam('id'));
        $ids=array_flip(explode(',', $model->getBannerImage()));
        $jsondata=json_encode($ids);
      }
		
		$this->addTab('banner_info', array(
			'label'     => Mage::helper('mobiconnectadvcart')->__('Banner Info'),
			'content'   => $this->getLayout()->createBlock('mobiconnectadvcart/adminhtml_category_edit_tab_form')->toHtml(),
		));
		
        $this->addTab('category_banner', array(
      'label'     => Mage::helper('mobiconnectadvcart')->__('Assign Banner Images'),
      'content'   => $this->getLayout()->createBlock('mobiconnectadvcart/adminhtml_category_edit_tab_assign')->toHtml().$this->getLayout()->createBlock('core/template')->setTemplate('mobiconnectadvcart/image_assign_js.phtml')->setProductsJson($jsondata)->toHtml(),
       ));
      
		return parent::_beforeToHtml();
	}
}
