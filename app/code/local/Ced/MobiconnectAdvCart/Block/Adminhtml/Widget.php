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
class Ced_MobiconnectAdvCart_Block_Adminhtml_Widget extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
		$this->_controller = 'adminhtml_widget';
		$this->_blockGroup = 'mobiconnectadvcart';
		$this->_headerText = Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Manage Layout' );
		$this->_addButtonLabel = Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Add Layout' );
		parent::__construct ();
	}
}
?>