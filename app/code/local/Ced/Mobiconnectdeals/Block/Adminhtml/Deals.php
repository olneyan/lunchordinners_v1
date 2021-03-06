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
class Ced_Mobiconnectdeals_Block_Adminhtml_Deals extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	/**
     * Modify header & button labels
     *
     */
	public function __construct() {
		$this->_controller = 'adminhtml_deals';
		$this->_blockGroup = 'mobiconnectdeals';
		$this->_headerText = Mage::helper('mobiconnectdeals')->__("Deals List");
		parent::__construct();
	}
	
	/**
     * Redefine header css class
     *
     * @return string
     */
    public function getHeaderCssClass() {
        return 'icon-head head-customer-groups';
    }
}
