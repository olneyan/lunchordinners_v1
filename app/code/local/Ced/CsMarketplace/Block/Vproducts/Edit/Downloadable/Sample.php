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
 * CsMarketplace Downloadable Product Sample Edit block
 *
 * @category   Ced
 * @package    Ced_CsMarketplace
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Block_Vproducts_Edit_Downloadable_Sample extends Ced_CsMarketplace_Block_Vendor_Abstract
{
	
	public function getDownloadableProductSamples($_product){
		return Mage::getModel('downloadable/product_type')->getSamples($_product);
	}
	
	public function getDownloadableHasSamples($_product){
		return Mage::getModel('downloadable/product_type')->hasSamples($_product);
	}
	
}
