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
  * @package   Ced_MobiconnectCms
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_MobiconnectCms_Block_Adminhtml_Cmsblock_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('notice_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('mobiconnectcms')->__('Cms Block'));
	}
	/**
	 * prepare before render block to html
	 *
	 * @return Ced_Mobiconnect_Block_Adminhtml_Cmsblock_Edit_Tabs
	 */
	protected function _beforeToHtml(){
		$this->addTab('form_section', array(
				'label'	 => Mage::helper('mobiconnectcms')->__('Cms Block'),
				'title'	 => Mage::helper('mobiconnectcms')->__('Cms Block'),
				'content'	 => $this->getLayout()->createBlock('mobiconnectcms/adminhtml_cmsblock_edit_tab_cmsblock')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}
?>
