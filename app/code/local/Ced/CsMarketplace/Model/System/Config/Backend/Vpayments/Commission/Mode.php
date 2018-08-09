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
 * @category    Ced;
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
 * Backend for serialized array data
 *
 */
class Ced_CsMarketplace_Model_System_Config_Backend_Vpayments_Commission_Mode extends Mage_Core_Model_Config_Data
{
 	protected function _afterSave(){
 		    /* $storeCode   = $this->getStoreCode();
		   	$commission = Mage::app()->getStore($storeCode)->getConfig('ced_vpayments/general/commission_fee');
		   	$commissionMode = trim($this->getValue());
		   	 
		   	switch($commissionMode){
		   		case "percentage":
		   			$commission=min($commission,100);
		   			break;
		   	}
		   	Mage::throwException($commissionMode."----".$commission);
		    Mage::app()->getStore($storeCode)->setConfig('ced_vpayments/general/commission_fee',$commission); */
		   	return parent::_afterSave();
		   	 
	   }
}