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
 
class Ced_CsMarketplace_Model_System_Config_Source_Group extends Ced_CsMarketplace_Model_System_Config_Source_Abstract
{
	public static $GROUPS_ARRAY = array();
	
	const XML_PATH_CED_CSMARKETPLACE_VENDOR_GROUPS = 'global/ced_csmarketplace/vendor/groups';
    /**
	 * Retrieve groups data form config.xml
	 * @return array
	 */
	 
	 public static function getGroups() {
		$groups = Mage::app()->getConfig()->getNode(self::XML_PATH_CED_CSMARKETPLACE_VENDOR_GROUPS);
        self::$GROUPS_ARRAY = json_decode(json_encode($groups),true);
		Mage::dispatchEvent('ced_csmarketplace_vendor_group_prepare', array(
				'class' => 'Ced_CsMarketplace_Model_System_Config_Source_Group',
			));
		return self::$GROUPS_ARRAY;
	 }
	
	/**
     * Retrieve Option values array
     *
     * @return array
     */
    public function toOptionArray()
    {
		$groups = array_keys(self::getGroups());
		$options = array();
		foreach($groups as $group) {
			$group = strtolower(trim($group));
			$options[] = array('value'=>$group,'label'=>Mage::helper('csmarketplace')->__(ucfirst($group)));
		}
		return $options;
    }

}