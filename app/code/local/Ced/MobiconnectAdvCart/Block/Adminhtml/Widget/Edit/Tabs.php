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
class Ced_MobiconnectAdvCart_Block_Adminhtml_Widget_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
	public function __construct() {
		parent::__construct ();
		$this->setId ( 'notice_tabs' );
		$this->setDestElementId ( 'edit_form' );
		$this->setTitle ( Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Widget' ) );
	}
	/**
	 * prepare before render block to html
	 *
	 * @return Ced_Mobiconnect_Block_Adminhtml_Widget_Edit_Tabs
	 */
	protected function _beforeToHtml() {
		$jsondata='';
  		if($this->getRequest()->getParam('id')){
        $model=Mage::getModel('mobiconnectadvcart/layouts')->load($this->getRequest()->getParam('id'));
        $ids=array_flip(explode(',', $model->getBannerImage()));
        $jsondata=json_encode($ids);
      }
		$this->addTab ( 'General widget', array (
				'label' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'General' ),
				'title' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'General' ),
				'content' => $this->getLayout ()->createBlock ( 'mobiconnectadvcart/adminhtml_widget_edit_tab_widget' )->toHtml () 
		) );
		$this->addTab ( 'Banner', array (
				'label' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Assign Banner' ),
				'title' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Assign Banner' ),
				'content' => $this->getLayout()->createBlock('mobiconnectadvcart/adminhtml_widget_edit_tab_bannergrid')->toHtml().$this->getLayout()->createBlock('core/template')->setTemplate('mobiconnectadvcart/layout_image_assign_js.phtml')->setProductsJson($jsondata)->toHtml(),
				
		) );
		return parent::_beforeToHtml ();
	}
}
?>
