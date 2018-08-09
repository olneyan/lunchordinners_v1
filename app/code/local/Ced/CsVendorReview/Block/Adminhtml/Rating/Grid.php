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
 
class Ced_CsVendorReview_Block_Adminhtml_Rating_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	 * Initialize Constructor
	 */
	public function __construct()
    {
		parent::__construct();
		$this->setId('_rating');
		$this->setDefaultSort('id');
		$this->setDefaultDir('INC');
		$this->setSaveParametersInSession(true);
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
	 * Get Collection of rating
	 * @return collection object
	 */
	protected function _prepareCollection($flag = false){

		$collection = Mage::getModel('csvendorreview/rating')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	/**
	 * Prepare the column of the grid
	 */
	protected function _prepareColumns(){

		$this->addColumn('id', array(
            'header'    => Mage::helper('csvendorreview')->__('Id'),
            'index'     => 'id',
            'width'     => '90px',
			));						
			
		$this->addColumn('rating_label',array(
            'header'    => Mage::helper('csvendorreview')->__('Rating Label'),
            'index'     => 'rating_label',
			'width'     => '120px',
		    ));
		
			
		$this->addColumn('rating_code',array(
            'header'    => Mage::helper('csvendorreview')->__('Rating Code'),
            'index'     => 'rating_code',
		    'width'     => '120px',
		    ));
			
		$this->addColumn('sort_order',array(
            'header'    => Mage::helper('csvendorreview')->__('Sort Order'),
            'index'     => 'sort_order',
		    'width'     => '100px',
		    ));	
			
		$this->addColumn('action',
  			array(
  					'header'    =>  Mage::helper('csvendorreview')->__('Action'),
  					'width'     => '150',
  					'type'      => 'action',
  					'getter'    => 'getId',
  					'actions'   => array(
  							array(
  									'caption'   => Mage::helper('csvendorreview')->__('Edit'),
  									'url'       => array('base'=> 'adminhtml/adminhtml_rating/edit'),
  									'field'     => 'id'
  							)
  					),
  					'filter'    => false,
  					'sortable'  => false,
  					'index'     => 'action',
  					'is_system' => true,
					'width'     => '90px',
  			));
							
  		return parent::_prepareColumns();
 	}

}