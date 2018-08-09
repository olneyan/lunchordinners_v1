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
 * CsMarketplace entity resource model
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Model_Mysql4_Vendor extends Ced_CsMarketplace_Model_Mysql4_Abstract
{
	/**
     * Resource initialization
     */
    public function __construct()
    {
        $this->setType('csmarketplace_vendor');
        $this->setConnection('csmarketplace_read', 'csmarketplace_write');
    }

	protected function _updateAttribute($object, $attribute, $valueId, $value) {
		return parent::_updateAttribute($object, $attribute, $valueId, $value);
		Mage::log('Object : '.print_r((array)$object,true).'Attribute Id: '.$attribute->getAttributeId().', Attribute Code : '. $attribute->getAttributeCode().', Value Id : '.$valueId.', Value :'. $value,null,'csmarketplace.log');
		$table = $attribute->getBackend()->getTable();
		if (!isset($this->_attributeValuesToSave[$table])) {
			$this->_attributeValuesToSave[$table] = array();
		}
		
		$entityIdField = $attribute->getBackend()->getEntityIdField();
		$data   = array(
			'entity_type_id'    => $object->getEntityTypeId(),
			$entityIdField      => $object->getId(),
			'attribute_id'      => $attribute->getId(),
			'value'             => $this->_prepareValueForSave($value, $attribute)
		);
		if ($valueId) {
			$data['value_id'] = $valueId;
			
		}

		$this->_attributeValuesToSave[$table][] = $data;

		return $this;
	}
	
	public function deleteFromGroup(Mage_Core_Model_Abstract $vendor)
    {
        if ( $vendor->getId() <= 0 ) {
            return $this;
        }
        if ( strlen($vendor->getGroup()) <= 0 ) {
            return $this;
        }
		$vendorGroup = Mage::getModel('csgroup/group')->loadByField('group_code',$vendor->getGroup());

        $dbh = $this->_getWriteAdapter();
        $condition = "{$this->getTable('csgroup/group')}.vendor_id = " . $dbh->quote($vendor->getId())
            . " AND {$this->getTable('csgroup/group')}.parent_id = " . $dbh->quote($vendorGroup->getGroupId());
       
		$dbh->delete($this->getTable('csgroup/group'), $condition);
        return $this;
    }
	
	public function groupVendorExists(Mage_Core_Model_Abstract $vendor)
    {
        if ( $vendor->getId() > 0 ) {
            $groupTable = $this->getTable('csgroup/group');
            
			$vendorGroup = Mage::getModel('csgroup/group')->loadByField('group_code',$vendor->getGroup());
			if($vendorGroup && $vendorGroup->getId())
			{
				$dbh    = $this->_getReadAdapter();
				$select = $dbh->select()->from($groupTable)
					->where("parent_id = {$vendorGroup->getGroupId()} AND vendor_id = {$vendor->getId()}");
				return $dbh->fetchCol($select);
			}
			else
			{
				return array();
			}
        } else {
            return array();
        }
    }
	
	public function add(Mage_Core_Model_Abstract $vendor)
    {
		//print_r($vendor->getData());die;
        $dbh = $this->_getWriteAdapter();

        $aGroups = $this->hasAssigned2Group($vendor);
        if ( sizeof($aGroups) > 0 ) {
            foreach($aGroups as $idx => $data){
                $dbh->delete($this->getTable('csgroup/group'), "group_id = {$data['group_id']}");
            }
        }

        if (strlen($vendor->getGroup()) > 0) {
            $group = Mage::getModel('csgroup/group')->loadByField('group_code',$vendor->getGroup());
        } else {
            $group = new Varien_Object();
            $group->setTreeLevel(0);
        }
		if($group && $group->getId())
		{
			//print_r($group->getData());die;
			$dbh->insert($this->getTable('csgroup/group'), array(
				'parent_id' => $group->getId(),
				'tree_level'=> ($group->getTreeLevel() + 1),
				'sort_order'=> 0,
				'group_type' => 'U',
				'vendor_id'  => $vendor->getId(),
				'group_code' => $vendor->getGroup(),
				'group_name' => $vendor->getName()
			));
		}
        return $this;
    }
	
	public function hasAssigned2Group($vendor)
    {
        if (is_numeric($vendor)) {
            $vendorId = $vendor;
        } else if ($vendor instanceof Mage_Core_Model_Abstract) {
            $vendorId = $vendor->getId();
        } else {
            return null;
        }

        if ( $vendorId > 0 ) {
            $dbh = $this->_getReadAdapter();
            $select = $dbh->select();
            $select->from($this->getTable('csgroup/group'))
                ->where("parent_id > 0 AND vendor_id = {$vendorId}");
            return $dbh->fetchAll($select);
        } else {
            return null;
        }
    }
	
	 public function vendorExists(Mage_Core_Model_Abstract $vendor)
    {
        $vendorsTable = $this->getTable('admin/vendor');
        $db = $this->_getReadAdapter();

        $select = $db->select()
            ->from(array('u' => $vendorsTable))
            ->where('u.vendor_id != ?', (int) $vendor->getId())
            ->where('u.vendorname = :vendorname OR u.email = :email')
        ;
        $row = $db->fetchRow($select, array(
            ':vendorname' => $vendor->getVendorname(),
            ':email'    => $vendor->getVendorname(),
        ));
        return $row;
    }
}