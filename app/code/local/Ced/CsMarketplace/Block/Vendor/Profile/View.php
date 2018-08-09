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
 * CsMarketplace profile view block
 *
 * @category   Ced
 * @package    Ced_CsMarketplace
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Block_Vendor_Profile_View extends Ced_CsMarketplace_Block_Vendor_Abstract
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

		$entityTypeId  = $this->_vendor->getEntityTypeId();
		$setIds = Mage::getResourceModel('eav/entity_attribute_set_collection')
				->setEntityTypeFilter($entityTypeId)->getAllIds();
    
		$groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection');
		if(count($setIds) > 0) {
			$groupCollection->addFieldToFilter('attribute_set_id',array('in'=>$setIds));
		}
		if(version_compare(Mage::getVersion(), '1.6', '<')) {
			$groupCollection->getSelect()->order('main_table.sort_order');
		}
		else{
			$groupCollection->setSortOrder()->load();
		}
		return $groupCollection;
 	}
	
	public function getRegionFromId($region_id)
	{
		foreach (Mage::getResourceModel('directory/region_collection')->load() as $region)
		{
			if($region->getData('region_id') == $region_id)
				return $region->getData('default_name');
		}
	}
}
