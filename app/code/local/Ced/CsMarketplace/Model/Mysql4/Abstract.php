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
 * CsMarketplace abstract resource model
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Model_Mysql4_Abstract extends Mage_Eav_Model_Entity_Abstract
{
	/**
     * Get attribute value through entity identifiers
     *
	 * @param  String $attributeCode
     * @param  array $productIds
     * @return array
     */
    /* public function validateMassAttribute($attributeCode = '',array $vendorIds, $entityModel = '')
    {
		echo (Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToSelect(array('entity_id','shop_url'))->getSelect());
		die;
		$attributeTable = '';
		$attributeTable = $this->getAttribute($attributeCode)->getBackend()->getTable();
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable($entityModel), array('entity_id', $attributeCode));
		if(strlen($attributeTable))
			$select = $select->joinLeft
            ->where($attributeCode.' IN (?)', $vendorIds);
		echo $select;die;
        return $this->_getReadAdapter()->fetchAll($select);
    }
	 */
}