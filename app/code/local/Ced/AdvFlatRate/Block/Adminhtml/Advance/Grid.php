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
 * @category    Ced;
 * @package     Ced_AdvFlatRate 
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com> 
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ced_AdvFlatRate_Block_Adminhtml_Advance_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected $_websiteId;

    protected $_conditionName;

    /**
     * Define grid properties
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setId('AdvanceRateGrid');
        $this->_exportPageSize = 10000;
    }

    /**
     * Set current website
     */
    public function setWebsiteId($websiteId)
    {
        $this->_websiteId = Mage::app()->getWebsite($websiteId)->getId();
        return $this;
    }

    /**
     * Retrieve current website id
     *
     * @return int
     */
    public function getWebsiteId()
    {
        if (is_null($this->_websiteId)) {
            $this->_websiteId = Mage::app()->getWebsite()->getId();
        }
        return $this->_websiteId;
    }

    /**
     * Set current condition for vendor
     */
    public function setConditionName($name)
    {
        $this->_conditionName = $name;
        return $this;
    }

    /**
     * Retrieve current condition for vendor
     */
    public function getConditionName()
    {
        return $this->_conditionName;
    }

    /**
     * Prepare shipping table rate collection
     */
    protected function _prepareCollection()
    {	
        $collection = Mage::getResourceModel('advflatrate/carrier_advancerate_collection');
       
		$collection->setConditionFilter($this->getConditionName())
            ->setWebsiteFilter($this->getWebsiteId());
			
		$region_collection = Mage::getResourceModel('directory/region_collection');
		
		$arr = array();
		$arr[0] = '*';
        foreach ($region_collection->getData() as $row) {
			$arr[$row['region_id']] = $row['code'];
			
        }

		foreach($collection as $item){
			$item->setRegionId($arr[$item->getRegionId()]);
		}
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare table columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('country_id', array(
            'header'    => Mage::helper('adminhtml')->__('Country'),
            'index'     => 'country_id',
            'default'   => '*',
        ));

		$this->addColumn('region_id', array(
            'header'    => Mage::helper('adminhtml')->__('Region/State'),
            'index'     => 'region_id',
            'default'   => '*',
        ));
		
        $this->addColumn('city', array(
            'header'    => Mage::helper('adminhtml')->__('City'),
            'index'     => 'city',
            'default'   => '*',
        ));

        $this->addColumn('zipcode', array(
            'header'    => Mage::helper('adminhtml')->__('Zip/Postal Code'),
            'index'     => 'zipcode',
            'default'   => '*',
        ));
		
		 $this->addColumn('price', array(
            'header'    => Mage::helper('adminhtml')->__('Price'),
            'index'     => 'price',
        ));

        return parent::_prepareColumns();
    }
}
