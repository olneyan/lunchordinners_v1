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
 * Ced_CsMarketplace source updates type
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */ 
class Ced_CsMarketplace_Model_Source_Updates_Type extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	const TYPE_PROMO            = 'PROMO';
	const TYPE_NEW_RELEASE      = 'NEW_RELEASE';
	const TYPE_UPDATE_RELEASE   = 'UPDATE_RELEASE';
	const TYPE_INFO             = 'INFO';
	const TYPE_INSTALLED_UPDATE = 'INSTALLED_UPDATE';
	
	
	public function toOptionArray()
	{
	    $hlp = Mage::helper('csmarketplace');
		return array(
			array('value' => self::TYPE_INSTALLED_UPDATE, 'label' => $hlp->__('Only Installed Extension(s) Updates')),
			array('value' => self::TYPE_UPDATE_RELEASE,   'label' => $hlp->__('All Extensions Updates')),
			array('value' => self::TYPE_NEW_RELEASE,      'label' => $hlp->__('New Releases')),
			array('value' => self::TYPE_PROMO,            'label' => $hlp->__('Special Offers')),
			array('value' => self::TYPE_INFO,             'label' => $hlp->__('Other Information'))
		);
	}
	
	/**
     * Retrive all attribute options
     *
     * @return array
     */
    public function getAllOptions()
    {
    	return $this->toOptionArray();
	}
	
	
	/**
	 * Returns label for value
	 * @param string $value
	 * @return string
	 */
	public function getLabel($value)
	{
		$options = $this->toOptionArray();
		foreach($options as $v){
			if($v['value'] == $value){
				return $v['label'];
			}
		}
		return '';
	}
	
	/**
	 * Returns array ready for use by grid
	 * @return array 
	 */
	public function getGridOptions()
	{
		$items = $this->getAllOptions();
		$out = array();
		foreach($items as $item){
			$out[$item['value']] = $item['label'];
		}
		return $out;
	}
}
