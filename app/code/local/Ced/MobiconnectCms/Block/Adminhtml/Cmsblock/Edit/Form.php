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
class Ced_MobiconnectCms_Block_Adminhtml_Cmsblock_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

	/**
	 * prepare form's information for block
	 *
	 * @return Ced_Mobiconnect_Block_Adminhtml_Cmsblock_Edit_Form
	 */
	protected function _prepareForm() {
		$form = new Varien_Data_Form(array(
				'id' => 'edit_form',
				'action' => $this->getUrl('*/*/save', array(
						'id' => $this->getRequest()->getParam('id'),
				)),
				'method' => 'post',
				'enctype' => 'multipart/form-data'
		));

		$form->setUseContainer(true);
		$this->setForm($form);
		return parent::_prepareForm();
	}

}
?>
