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
class Ced_MobiconnectCms_Block_Adminhtml_Cmsblock_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

	public function __construct() {
		parent::__construct();

		$this->_objectId = 'id';
		$this->_blockGroup = 'mobiconnectcms';
		$this->_controller = 'adminhtml_cmsblock';
		$this->_updateButton('delete', 'label', Mage::helper('mobiconnectcms')->__('Delete'));
// 		$this->_addButton('saveandcontinue', array(
// 				'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
// 				'class' => 'save',
// 		), -100);
		$this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('notice_content') == null)
					tinyMCE.execCommand('mceAddControl', false, 'notice_content');
				else
					tinyMCE.execCommand('mceRemoveControl', false, 'notice_content');
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}

	/**
	 * get text to show in header when edit an item
	 *
	 * @return string
	 */
	public function getHeaderText() {
		if (Mage::registry('cms_data') && Mage::registry('cms_data')->getId()){

			
		return Mage::helper('mobiconnectcms')->__("Edit Block '%s'", $this->htmlEscape(Mage::registry('cms_data')->getTitle()));
		}
		return Mage::helper('mobiconnectcms')->__('Add Block');
	}

}
?>
