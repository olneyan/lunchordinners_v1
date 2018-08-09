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
 * CsMarketplace vendor navigation statatics
 *
 * @category   Ced
 * @package    Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */

class Ced_CsMarketplace_Block_Vendor_Navigation_Statatics extends Ced_CsMarketplace_Block_Vendor_Abstract
{
	protected $_vendor;
	protected $_totalattr;
	protected $_savedattr;
	
	
	public function __construct()
	{	
		$this->_vendor = Mage::getModel('csmarketplace/vendor');
		$this->_totalattr = 0;
		$this->_savedattr = 0;
	}
	
	/**
     * Preparing collection of vendor attribute group vise
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Group_Collection
     */
	public function getVendorAttributeInfo() {

		$total = -6;
		$saved = -6;
		$vendorData = Mage::getSingleton("customer/session")->getVendor()->getData();
		foreach($vendorData as $val){
			if($val){
		    	$saved++;
		    }
			$total++;
		}
		  
		$this->_totalattr = $total;
		$this->_savedattr = $saved; 
	}
	
}
