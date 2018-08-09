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
  * @package     Ced_CsVendorReview
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
  
class Ced_CsVendorReview_Block_Adminhtml_Review_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	 * Initialize Constructor
	 */
	public function __construct(){
	
		parent::__construct();
		$this->setId('_review');
		$this->setDefaultSort('id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
    }
	
	/**
	 * Prepare mass Action for the grid
	 * @return object
	 */
	protected function _prepareMassaction(){
	
		$this->setMassactionIdField('id');
		$this->getMassactionBlock()->setFormFieldName('id');
	  
		$this->getMassactionBlock()->addItem('delete', array(
				'label'    => Mage::helper('csmarketplace')->__('Delete'),
				'url'      => $this->getUrl('*/*/massDelete'),
				'confirm'  => Mage::helper('csmarketplace')->__('Are you sure?')
		));
	  
		$statuses = Mage::getSingleton('csvendorreview/options')->getStatusOption();
		
		$this->getMassactionBlock()->addItem('status', array(
				 'label'=> Mage::helper('csmarketplace')->__('Change status'),
				 'url'  => $this->getUrl('*/*/massStatus/', array('_current'=>true)),
				 'additional' => array(
						 'visibility' => array(
								 'name' => 'status',
								 'type' => 'select',
								 'class' => 'required-entry',
								 'label' => Mage::helper('csmarketplace')->__('Status'),
								'default'=>'-1',
								 'values' =>$statuses,
						 )
				 )
		 ));
		return $this;
	}
	
	/**
	 * Get current store
	 * @return Mage_Core_Model_Store
	 */
	public function getStore() {
		$storeId = (int) Mage::app()->getRequest()->getParam('store', 0);
        if($storeId)
			return Mage::app()->getStore($storeId);
		else 
			return Mage::app()->getStore();
	}
	  
	/**
	 * Prepare Collection for review items
	 * @return collection object
	 */
	protected function _prepareCollection($flag = false){

		$collection = Mage::getModel('csvendorreview/review')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	/**
	 * Prepare Columns for the grid
	 * @return Object
	 */
	protected function _prepareColumns(){

		$this->addColumn('id', array(
            'header'    => Mage::helper('csvendorreview')->__('Id'),
            'index'     => 'id',
            'width'     => '70px',
			));
			
			
		$this->addColumn('vendor_name', array(
  			'header'    => Mage::helper('csvendorreview')->__('Vendor Name'),
  			'align'     => 'left',
   			'index'     => 'vendor_name',
			'width'     => '150px',
  			));
		
		$this->addColumn('customer_name',array(
            'header'    => Mage::helper('csvendorreview')->__('Customer Name'),
            'index'     => 'customer_name',		
			'width'     => '150px',
		    ));
						
		$this->addColumn('subject',array(
			'header'    => Mage::helper('csvendorreview')->__('Review Subject'),
			'index'     => 'subject',
			'width'     => '120px',
		));
		
		$this->addColumn('review',array(
			'header'    => Mage::helper('csvendorreview')->__('Review Description'),
			'index'     => 'review',
		));
		
		$this->addColumn('status', array(
            'header'    => Mage::helper('csvendorreview')->__('Review Status'),
            'index'     => 'status',
            'type'      => 'options',
            'width'     => '90px',
            'options'   => Mage::getModel('csvendorreview/options')->getStatusOption(),
        	));
				
  		return parent::_prepareColumns();
 	}
 
	/**
	 * Row url of the grid
	 * @return String
	 */
	public function getRowUrl($row){

		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  	}
  	

}