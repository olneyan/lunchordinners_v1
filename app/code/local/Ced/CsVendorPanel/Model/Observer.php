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
 * @package     Ced_CsVendorPanel
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * CsVendorPanel observer
 *
 * @category    Ced
 * @package     Ced_CsVendorPanel
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsVendorPanel_Model_Observer
{
	
	/**
     * Predispath csmarketplace route
     *
     * @param Varien_Event_Observer $observer
     */
    public function preDispatch(Varien_Event_Observer $observer)
    {	
		$package = Ced_CsMarketplace_Model_System_Config_Source_Theme_Design::DEFAULT_VENDOR_PACKAGE;
		$theme = 'default'; 
		
		if(Mage::getStoreConfig('ced_csmarketplace/vendor/vendor_theme')){
			list($package, $theme) = explode('/',Mage::getStoreConfig('ced_csmarketplace/vendor/vendor_theme'));
		}

		Mage::getDesign()->setPackageName($package);
		Mage::getDesign()->setTheme($theme);
		foreach (array('layout', 'template', 'skin', 'locale') as $type) {
			Mage::getDesign()->setTheme($type, $theme);
		}
    }
}
