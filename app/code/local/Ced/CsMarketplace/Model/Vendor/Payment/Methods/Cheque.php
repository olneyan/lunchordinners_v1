<?php 

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Vendor payment method vcheque model
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Model_Vendor_Payment_Methods_Cheque extends Ced_CsMarketplace_Model_Vendor_Payment_Methods_Abstract
{
    protected $_code = 'vcheque';
	
	/**
	 * Retreive input fields
	 *
	 * @return array
	 */
	public function getFields() {
		$fields = parent::getFields();
		$fields['cheque_payee_name'] = array('type'=>'text');
		return $fields;
	}
	
	/**
	 * Retreive labels
	 *
	 * @param string $key
	 * @return string
	 */
	public function getLabel($key) {
		switch($key) {
			case 'label' : return Mage::helper('csmarketplace')->__('Check/Money Order');break;
			case 'cheque_payee_name' : return Mage::helper('csmarketplace')->__('Cheque Payee Name');break;
			default : return parent::getLabel($key); break;
		}
	}
}
