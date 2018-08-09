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
  * @package   Ced_Mobiconnect
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobiconnect_Block_Adminhtml_Mobiconnect_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	public function __construct() {
		$this->_objectId = 'id';
		$this->_blockGroup = 'mobiconnect';
		$this->_controller = 'adminhtml_mobiconnect';
		
		/**
		 * define the label for the save and delete button
		 */
		$this->_updateButton ( 'save', 'label', 'save reference' );
		$this->_updateButton ( 'delete', 'label', 'Delete Banner' );
		parent::__construct ();
	}
	
	/**
	 * get text to show in header when edit an item
	 *
	 * @return string
	 */
	public function getHeaderText() {
		if (Mage::registry ( 'banners_data' ) && Mage::registry ( 'banners_data' )->getId ()) {
			return Mage::helper ( 'mobiconnect' )->__ ( "Edit Item '%s'", $this->htmlEscape ( Mage::registry ( 'banners_data' )->getTitle () ) );
		} else {
			return Mage::helper ( 'mobiconnect' )->__ ( 'Banner Image' );
		}
	}
}
?>