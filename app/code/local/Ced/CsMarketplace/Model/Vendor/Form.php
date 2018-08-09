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
 * Vendor form model
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Model_Vendor_Form extends Ced_CsMarketplace_Model_Form
{
    /**
     * Set resource model and Id field name
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('csmarketplace/vendor_form');
    }
	
	public function insertMultiple($feedData = array()){
		$coreResource   = Mage::getSingleton('core/resource');
		$feedTable      = $coreResource->getTableName('csmarketplace/vendor_form');
		$conn = $coreResource->getConnection('write');
		if($conn->insertMultiple($feedTable, $feedData))
			return true;
		return false;
	}
}
