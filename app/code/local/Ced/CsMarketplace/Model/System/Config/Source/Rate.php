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
 
class Ced_CsMarketplace_Model_System_Config_Source_Rate extends Ced_CsMarketplace_Model_System_Config_Source_Abstract
{

	const XML_PATH_CED_CSMARKETPLACE_VENDOR_RATES = 'global/ced_csmarketplace/vendor/rates';
    
	/**
	 * Retrieve rates data form config.xml
	 * @return array
	 */
	 
	 public static function getRates() {
		$rates = Mage::app()->getConfig()->getNode(self::XML_PATH_CED_CSMARKETPLACE_VENDOR_RATES);
        return json_decode(json_encode($rates),true);
	 }
	/**
     * Retrieve Option values array
     *
     * @return array
     */
    public function toOptionArray()
    {
		$rates = array_keys(self::getRates());
		$options = array();
		foreach($rates as $rate) {
			$rate = strtolower(trim($rate));
			$options[] = array('value'=>$rate,'label'=>Mage::helper('csmarketplace')->__(ucfirst($rate)));
		}
		return $options;
    }

}