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
class Ced_CsMarketplace_Model_System_Config_Backend_Vproducts_Website extends Mage_Core_Model_Config_Data
{
    public function _afterSave()
    {
		$website_share = false;
        $oldValue = Mage::getStoreConfig('ced_vproducts/general/website');;
        $oldValue = explode(',',$oldValue);
		$value = $this->getValue();
		$value = explode(',',$value);
		//$diff = array_diff($array1, $array2);;
		if($oldValue == $value){
		}else{
			$website_share = true;
		}
        
	 	if($website_share) {
			$config = new Mage_Core_Model_Config();
			$config->saveConfig(Ced_CsMarketplace_Model_Vendor::XML_PATH_VENDOR_WEBSITE_SHARE,1);
		}

        return parent::_afterSave();
    }
}