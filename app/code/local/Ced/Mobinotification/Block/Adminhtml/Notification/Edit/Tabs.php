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
  * @package   Ced_Mobinotification
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobinotification_Block_Adminhtml_Notification_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('edit_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('mobinotification')->__('Mobinotification Information'));
  }
  
	protected function _beforeToHtml() {
		
		$this->addTab('notification', array(
			'label'     => Mage::helper('mobinotification')->__('Notification Info'),
			'content'   => $this->getLayout()->createBlock('mobinotification/adminhtml_notification_edit_tab_form')->toHtml(),
		));

		return parent::_beforeToHtml();
	}
}
