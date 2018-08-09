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
 * Vendor attribute model
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
 
class Ced_CsMarketplace_Model_Vendor_Attribute extends Mage_Eav_Model_Entity_Attribute 
{
	/**
     * Prefix of vendor attribute events names
     *
     * @var string
     */
    protected $_eventPrefix='csmarektplace_vendor_attribute';
	
	/**
	 * Current scope (store Id)
	 *
	 * @var int
	 */
    protected $_storeId;
	
	public function __construct() {
		parent::__construct();
		$this->setEntityTypeId(Mage::getModel('eav/entity')->setType('csmarketplace_vendor')->getTypeId());
		$setIds=Mage::getResourceModel('eav/entity_attribute_set_collection')->setEntityTypeFilter($this->getEntityTypeId())->getAllIds();
		$this->setAttributeSetIds($setIds); 
		return $this;
	}

    /**
     * Set store scope
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return Mage_Catalog_Model_Resource_Collection_Abstract
     */
	public function setStore($store) {
		$this->setStoreId(Mage::app()->getStore($store)->getId());
		return $this;
	}

    /**
     * Set store scope
     *
     * @param int|string|Mage_Core_Model_Store $storeId
     * @return Mage_Catalog_Model_Resource_Collection_Abstract
     */
	public function setStoreId($storeId) {
		if ($storeId instanceof Mage_Core_Model_Store) {
			$storeId=$storeId->getId();
		}
		$this->_storeId=(int)$storeId;
		return $this;
	}

    /**
     * Return current store id
     *
     * @return int
     */
	public function getStoreId() {
		if (is_null($this->_storeId)) {
			$this->setStoreId(Mage::app()->getStore()->getId());
		}
		return $this->_storeId;
	}

    /**
     * Retrieve default store id
     *
     * @return int
     */
    public function getDefaultStoreId() {
        return Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
    }
	
	/**
     * Load vendor's attributes into the object
     *
     * @param   Mage_Core_Model_Abstract $object
     * @param   integer $entityId
     * @param   array|null $attributes
     * @return  Ced_CsMarketplace_Model_Vendor_Attribute
     */
	public function load($entityId, $field=NULL) {
		parent::load($entityId, $field );
		if($this && $this->getId()) {			
			$joinFields=$this->_vendorForm($this);
			if(count($joinFields) > 0) {
				foreach($joinFields as $joinField) {
					$this->setData('is_visible',$joinField->getIsVisible());
					$this->setData('position',$joinField->getSortOrder());
					$this->setData('use_in_registration',$joinField->getData('use_in_registration'));
					$this->setData('position_in_registration',$joinField->getData('position_in_registration'));
					$this->setData('use_in_left_profile',$joinField->getData('use_in_left_profile'));
					$this->setData('fontawesome_class_for_left_profile',$joinField->getData('fontawesome_class_for_left_profile'));
					$this->setData('position_in_left_profile',$joinField->getData('position_in_left_profile'));
				}
			}
		}
        return $this;
	}
	
	public function _vendorForm($attribute) {
		$store=$this->getStoreId();		
		$fields=Mage::getModel('csmarketplace/vendor_form')
							->getCollection()
							->addFieldToFilter('attribute_id',array('eq'=>$attribute->getAttributeId()))
							->addFieldToFilter('attribute_code',array('eq'=>$attribute->getAttributeCode()))
							->addFieldToFilter('store_id',array('eq'=>$store));
		if(count($fields) == 0) {
			$data[]=array(
						'attribute_id' => $attribute->getId(),
						'attribute_code' => $attribute->getAttributeCode(),
						'is_visible'   => 0,
						'sort_order'   => 0,
						'store_id'	   => $store,
						'use_in_registration' => 0,
						'position_in_registration' => 0,
						'use_in_left_profile' => 0,
						'fontawesome_class_for_left_profile' => 'fa fa-circle-thin',
						'position_in_left_profile' => 0,
					);
			Mage::getModel('csmarketplace/vendor_form')->insertMultiple($data);
			return $this->_vendorForm($attribute);
		}
		return $fields;
	}
	
	/**
	 * Retrive Vendor attribute collection
	 *
	 * @return Mage_Eav_Model_Resource_Entity_Collection
	 */
	public function getCollection() {
		$collection=parent::getCollection();
		$typeId=Mage::getModel('csmarketplace/vendor')->getEntityTypeId();
		$collection=$collection->addFieldToFilter('entity_type_id',array('eq'=>$typeId));
		$labelTableName=Mage::getSingleton('core/resource')->getTableName('eav/attribute_label');
		$tableName=Mage::getSingleton('core/resource')->getTableName('csmarketplace/vendor_form');
		if($this->getStoreId()) {
			$availableStoreWiseIds=$this->getStoreWiseIds($this->getStoreId());
			$collection->getSelect()->join(array('vform'=>$tableName), 'main_table.attribute_id=vform.attribute_id', array('is_visible'=>'vform.is_visible','sort_order'=>'vform.sort_order','store_id'=>'vform.store_id','use_in_registration'=>'vform.use_in_registration', 'use_in_left_profile'=>'vform.use_in_left_profile','position_in_registration'=>'vform.position_in_registration', 'position_in_left_profile'=>'vform.position_in_left_profile', 'fontawesome_class_for_left_profile'=>'vform.fontawesome_class_for_left_profile'));
			$collection->getSelect()->where('(vform.attribute_id IN ("'.$availableStoreWiseIds.'") AND vform.store_id='.$this->getStoreId().') OR (vform.attribute_id NOT IN ("'.$availableStoreWiseIds.'") AND vform.store_id=0)');
			$collection->getSelect()->group('vform.attribute_id');
			$collection->getSelect()->joinLeft(array('vlabel'=>$labelTableName), 'main_table.attribute_id=vlabel.attribute_id && vlabel.store_id='.$this->getStoreId(), array('store_label'=>'vlabel.value'));
		} else {
			$collection->getSelect()->join(array('vform'=>$tableName), 'main_table.attribute_id=vform.attribute_id && vform.store_id=0', array('is_visible'=>'vform.is_visible','sort_order'=>'vform.sort_order','store_id'=>'vform.store_id','use_in_registration'=>'vform.use_in_registration', 'use_in_left_profile'=>'vform.use_in_left_profile','position_in_registration'=>'vform.position_in_registration', 'position_in_left_profile'=>'vform.position_in_left_profile', 'fontawesome_class_for_left_profile'=>'vform.fontawesome_class_for_left_profile'));
			$collection->getSelect()->joinLeft(array('vlabel'=>$labelTableName), 'main_table.attribute_id=vlabel.attribute_id && vlabel.store_id=0', array('store_label'=>'vlabel.value'));
		}
		/* $collection->addExpressionFieldToSelect("is_visible","(CASE WHEN `"."vform"."`.`is_visible` IS NULL THEN (SELECT `".$tableName."`.`is_visible` from `".$tableName."` WHERE `".$tableName."`.`store_id`=0 AND `".$tableName."`.`attribute_id`=`vform`.`attribute_id`) ELSE `"."vform"."`.`is_visible` END)", "");
		echo $collection->getSelect();die; */
		return $collection;
	}
	
	public function getStoreWiseIds($storeId=0) {
		if($storeId) {
			$allowed=array();
			foreach(Mage::getModel('csmarketplace/vendor_form')
						->getCollection()
						->addFieldToFilter('store_id',array('eq'=>$storeId))
					as $attribute
				   ) {
					$allowed[]=$attribute->getAttributeId();
			}
			return implode(',',$allowed);
		}
		return array();
	}
	
	public function addToGroup($group=array()) {
		if (count($group) > 0) {
			$setIds=Mage::getResourceModel('eav/entity_attribute_set_collection')->setEntityTypeFilter($this->getEntityTypeId())->getAllIds();
			/* print_r($setIds); */
			$setId=isset($setIds[0])?$setIds[0]:$this->getEntityTypeId();
			/* echo $setId;die; */
			$installer=Mage::getModel('csmarketplace/mysql4_setup','csmarketplace_setup');
			$installer->startSetup();
			
			if(!in_array($group , $this->getGroupOptions($setId,true))) {
				$installer->addAttributeGroup(
					'csmarketplace_vendor',
					$setId,
					$group
				);
			}
			$installer->addAttributeToGroup(
				'csmarketplace_vendor',
				$setId,
				$group, //Group Name
				$this->getAttributeId()
			);
			$installer->endSetup();
		}
	}
	
	protected function getGroupOptions($setId,$flag=false) {
		$groupCollection=Mage::getResourceModel('eav/entity_attribute_group_collection')
					->setAttributeSetFilter($setId);
		if(version_compare(Mage::getVersion(), '1.6', '<')) {
			$groupCollection->getSelect()->order('main_table.sort_order');
		}
		else{
			$groupCollection->setSortOrder()
			->load();
		}
		$options=array();
		if($flag) {
			foreach ($groupCollection as $group) {
				$options[]=$group->getAttributeGroupName();		
			}
		} else {
			foreach ($groupCollection as $group) {
				$options[$group->getId()]=$group->getAttributeGroupName();		
			}
		}
		return 	$options;
	}
	
	public function delete() {
		if ($this->getId()) {		
			$joinFields=$this->_vendorForm($this);
			if(count($joinFields) > 0) {
				foreach($joinFields as $joinField) {
					$joinField->delete();
				}
			}
		}
		return parent::delete();;
	}
	
	/**
     * Processing vendor attribute after save data
     *
     * @return Ced_CsMarketplace_Model_Vendor_Attribute
     */
	protected function _afterSave() {
		parent::_afterSave();
		if ($this->getId()) {		
			$joinFields=$this->_vendorForm($this);
			if(count($joinFields) > 0) {
				foreach($joinFields as $joinField) {
					$joinField->setData('is_visible',$this->getData('is_visible'));
					$joinField->setData('sort_order',$this->getData('position'));
					$joinField->setData('use_in_registration',$this->getData('use_in_registration'));
					$joinField->setData('position_in_registration',$this->getData('position_in_registration'));
					$joinField->setData('use_in_left_profile',$this->getData('use_in_left_profile'));
					$joinField->setData('fontawesome_class_for_left_profile',$this->getData('fontawesome_class_for_left_profile'));
					$joinField->setData('position_in_left_profile',$this->getData('position_in_left_profile'));
					$joinField->save();
				}
			}
		}
		return $this;
	}	
}