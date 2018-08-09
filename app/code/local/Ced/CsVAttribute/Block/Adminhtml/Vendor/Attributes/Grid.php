<?php 

/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License (AFL 3.0)
  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/afl-3.0.php
  *
  * @category    Ced
  * @package     Ced_CsVAttribute
  * @author   	 CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
class Ced_CsVAttribute_Block_Adminhtml_Vendor_Attributes_Grid extends Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract
{
  const VAR_NAME_FILTER = 'vendor_attribute';
  public function __construct()
  {
      parent::__construct();
      $this->setId('attributesGrid');
      $this->setDefaultSort('sort_order');
      $this->setDefaultDir('ASC');
	  $this->setUseAjax(true);
	  $this->_varNameFilter = self::VAR_NAME_FILTER;
	  $this->setSaveParametersInSession(true);
  }

  protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
	
    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
  protected function _prepareCollection()
  {
	$store = $this->_getStore();
	$this->setStoreId($store->getId());
	/* echo get_class(Mage::getModel('csmarketplace/vendor_attribute'));die; */
	$collection = Mage::getModel('eav/entity_attribute')->getCollection();
	$typeId = Mage::getModel('csmarketplace/vendor')->getEntityTypeId();
	$collection = $collection->addFieldToFilter('entity_type_id',array('eq'=>$typeId));
	/* echo $collection->getSelect();die; */
	$tableName = Mage::getSingleton('core/resource')->getTableName('csmarketplace/vendor_form');
	$collection->getSelect()->joinLeft(array('vform'=>$tableName), 'main_table.attribute_id = vform.attribute_id && vform.store_id ='.$this->getStoreId(), array('is_visible'=> 'vform.is_visible', 'use_in_registration'=>'vform.use_in_registration', 'use_in_left_profile'=>'vform.use_in_left_profile'));
	/* $collection = Mage::getModel('csmarketplace/vendor_attribute')
					->setStoreId($store->getId())
					->getCollection(); */
	
	$this->setCollection($collection);
	  
	parent::_prepareCollection();
	/* if($this->getRequest()->getParam('vendor_attribute')) {
	//echo $this->getCollection()->getSelect(); die;
	} */
	return $this;
  }

  /**
   * Add columns to grid
   *
   * @return Mage_Adminhtml_Block_Widget_Grid
   */
  protected function _prepareColumns() {
        parent::_prepareColumns();
		$this->getColumn('attribute_code')->setFilterIndex('main_table.attribute_code');
        $this->addColumnAfter('is_visible', array(
            'header'=>Mage::helper('catalog')->__('Use in Edit Form'),
            'sortable'=>true,
            'index'=>'is_visible',
            'type' => 'options',
            'options' => array(
                1 => Mage::helper('catalog')->__('Yes'),
                0 => Mage::helper('catalog')->__('No'),
            ),
            'align' => 'center',
			/* 'filter_index' => 'vform.is_visible', */
        ), 'frontend_label');

        $this->addColumnAfter('use_in_registration', array(
            'header'=>Mage::helper('catalog')->__('Use in Registration Form'),
            'sortable'=>true,
            'index'=>'use_in_registration',
            'type' => 'options',
            'options' => array(
                1 => Mage::helper('catalog')->__('Yes'),
                0 => Mage::helper('catalog')->__('No'),
            ),
            'align' => 'center',
			/* 'filter_index' => 'vform.is_visible', */
        ), 'is_visible');
		
		$this->addColumnAfter('use_in_left_profile', array(
            'header'=>Mage::helper('catalog')->__('Use in Left Profile'),
            'sortable'=>true,
            'index'=>'use_in_left_profile',
            'type' => 'options',
            'options' => array(
                1 => Mage::helper('catalog')->__('Yes'),
                0 => Mage::helper('catalog')->__('No'),
            ),
            'align' => 'center',
			/* 'filter_index' => 'vform.is_visible', */
        ), 'use_in_registration');
		
        return $this;
    }
	
    /**
     * Rerieve grid URL
     *
     * @return string
     */
	public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_secure'=>true, '_current'=>true));
    }
}