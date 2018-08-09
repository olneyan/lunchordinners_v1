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
class Ced_Mobiconnect_Model_System_Config_Source_Target {
	/**
	 * Get Option Array
	 *
	 * @return array
	 */
	public function toOptionArray() {
		return array (
				array (
						'value' => 'new_window',
						'label' => Mage::helper ( 'mobiconnect' )->__ ( 'Open in New Window' ) 
				),
				array (
						'value' => 'self_window',
						'label' => Mage::helper ( 'mobiconnect' )->__ ( 'Open in same Window' ) 
				) 
		)
		;
	}
	/**
	 * Get options
	 *
	 * @return array
	 */
	public function toArray() {
		return array (
				0 => Mage::helper ( 'adminhtml' )->__ ( 'Data1' ),
				1 => Mage::helper ( 'adminhtml' )->__ ( 'Data2' ),
				3 => Mage::helper ( 'adminhtml' )->__ ( 'Data3' ) 
		);
	}
}
?>
