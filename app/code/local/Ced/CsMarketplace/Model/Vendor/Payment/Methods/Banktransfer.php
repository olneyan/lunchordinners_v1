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
 * Vendor payment method banktransfer model
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Model_Vendor_Payment_Methods_Banktransfer extends Ced_CsMarketplace_Model_Vendor_Payment_Methods_Abstract
{
    protected $_code = 'vbanktransfer';
	
	/**
	 * Retreive input fields
	 *
	 * @return array
	 */
	public function getFields() {
		$fields = parent::getFields();
		$fields['bank_name'] = array('type'=>'text');
		$fields['bank_branch_number'] = array('type'=>'text');
		$fields['bank_swift_code'] = array('type'=>'text');
		$fields['bank_account_name'] = array('type'=>'text');
		$fields['bank_account_number'] = array('type'=>'text');
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
			case 'label' : return Mage::helper('csmarketplace')->__('Bank Transfer');break;
			case 'bank_name' : return Mage::helper('csmarketplace')->__('Bank Name');break;
			case 'bank_branch_number' : return Mage::helper('csmarketplace')->__('Bank Branch Number');break;
			case 'bank_swift_code' : return Mage::helper('csmarketplace')->__('Bank Swift Code');break;
			case 'bank_account_name' : return Mage::helper('csmarketplace')->__('Bank Account Name');break;
			case 'bank_account_number' : return Mage::helper('csmarketplace')->__('Bank Account Number');break;
			default : return parent::getLabel($key); break;
		}
	}
}
