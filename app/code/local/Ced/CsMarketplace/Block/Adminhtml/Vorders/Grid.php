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
 * CsMarketplace Orders List block
 *
 * @category   Ced
 * @package    Ced_CsMarketplace
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Block_Adminhtml_Vorders_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
    {
	  parent::__construct();
	  $this->setId('vordersGrid');
	  $this->setDefaultSort('created_at');
	  $this->setDefaultDir('DESC');
	  $this->setSaveParametersInSession(true);
	  $this->setUseAjax(true);
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

	protected function _prepareMassaction(){
		$statuses = Mage::getSingleton('csmarketplace/vproducts')->getOptionArray();
		$this->getMassactionBlock()->addItem('status', array(
				 'label'=> Mage::helper('csmarketplace')->__('Change status'),
				 'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
				 'additional' => array(
						 'visibility' => array(
								 'name' => 'status',
								 'type' => 'select',
								 'class' => 'required-entry',
								 'label' => Mage::helper('csmarketplace')->__('Status'),
								 'values' =>$statuses,
						 )
				 )
		 ));
		return $this;
	  }
	  
	  protected function _addColumnFilterToCollection($column)
	  {
	  	if ($this->getCollection()) {
	  		$field = ( $column->getFilterIndex() ) ? $column->getFilterIndex() : $column->getIndex();
	  		if ($column->getFilterConditionCallback()) {
	  			call_user_func($column->getFilterConditionCallback(), $this->getCollection(), $column);
	  		} else {
	  			$cond = $column->getFilter()->getCondition();
	  			if ($field && isset($cond)) {
	  				$this->getCollection()->addFieldToFilter($field , $cond);
	  			}
	  		}
	  	}
	  	return $this;
	  }
  
  
	protected function _prepareCollection(){
		$flag = false;
		$vendor_id = $this->getRequest()->getParam('vendor_id',0);
		if(!$flag) {
			$orderTable = Mage::getSingleton('core/resource')->getTableName('sales/order');
			$collection = Mage::getModel('csmarketplace/vorders')->getCollection();
			if($vendor_id) {
				$collection->addFieldToFilter('main_table.vendor_id',array('eq'=>$vendor_id));
			}
			$main_table=Mage::helper('csmarketplace')->getTableKey('main_table');
			$order_total=Mage::helper('csmarketplace')->getTableKey('order_total');
			$shop_commission_fee=Mage::helper('csmarketplace')->getTableKey('shop_commission_fee');
			$collection->getSelect()->columns(array('net_vendor_earn' => new Zend_Db_Expr("({$main_table}.{$order_total} - {$main_table}.{$shop_commission_fee})")));
			//$collection->getSelect()->joinLeft($orderTable , 'main_table.order_id ='.$orderTable.'.increment_id',array('*'));
			$collection->getSelect()->join($orderTable ,'main_table.order_id LIKE  CONCAT("%",'.$orderTable.".increment_id".' ,"%")',array('*'));
			$collection->getSelect()->where('main_table.order_id = '.$orderTable .'.increment_id');
			$this->setCollection($collection);
		}
		return parent::_prepareCollection();
	}
	  
  protected function _prepareColumns(){
	//$currency = (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
		$this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));
		/*$this->addColumn('relation_id', array(
  			'header'    => Mage::helper('csmarketplace')->__('ID'),
  			'align'     =>'right',
  			'width'     => '50px',
  			'index'     => 'id'
  			));*/
  		$this->addColumn('order_id', array(
  			'header'    => Mage::helper('csmarketplace')->__('Order ID#'),
  			'align'     => 'left',
   			'index'     => 'order_id',
  			'filter_index'  => 'order_id',
			'renderer' => 'Ced_CsMarketplace_Block_Adminhtml_Vorders_Grid_Renderer_Orderid'
  			)); 
		$this->addColumn('vendor_id', array(
  			'header'    => Mage::helper('csmarketplace')->__('Vendor Name'),
  			'align'     => 'left',
   			'index'     => 'vendor_id',
			'renderer' => 'Ced_CsMarketplace_Block_Adminhtml_Vorders_Grid_Renderer_Vendorname',
			'filter_condition_callback' => array($this, '_vendornameFilter'),
  			));
		
		$this->addColumn('base_order_total',array(
            'header'=> Mage::helper('catalog')->__('G.T. (Base)'),
            'index' => 'base_order_total',
			'type'          => 'currency',
             'currency' => 'base_currency_code',
		    
		    ));
		$this->addColumn('order_total',array(
            'header'=> Mage::helper('catalog')->__('G.T.'),
            'index' => 'order_total',
			'type'          => 'currency',
             'currency' =>'currency',
		    ));
		
			
		$this->addColumn('shop_commission_fee',array(
            'header'=> Mage::helper('catalog')->__('Commission Fee'),
            'index' => 'shop_commission_fee',
			'type'          => 'currency',
             'currency' =>'currency',
		    
		    ));
			
		$this->addColumn('net_vendor_earn',array(
				'header'=> Mage::helper('catalog')->__('Vendor Payment'),
				'index' => 'net_vendor_earn',
				'type'          => 'currency',
				 'currency' =>'currency',
		));
		
		/* $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Order Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        	)); */
		
		
		 $this->addColumn('order_payment_state', array(
            'header'    => Mage::helper('sales')->__('Order Payment State'),
            'index'     => 'order_payment_state',
			'filter_index'  => 'order_payment_state',
            'type'      => 'options',
            'options'   => Mage::getModel('sales/order_invoice')->getStates(),
        ));
		
		$this->addColumn('payment_state', array(
            'header'    => Mage::helper('sales')->__('Vendor Payment State'),
            'index'     => 'payment_state',
			'filter_index'  => 'payment_state',
            'type'      => 'options',
            'options'   => Mage::getModel('csmarketplace/vorders')->getStates(),
			//'renderer' => $this->getLayout()->createBlock('csmarketplace/adminhtml_vorders_grid_renderer_paynow'),
        ));
		
		$this->addColumn('order_payment_type', array(
				'header'    => Mage::helper('sales')->__('Order Payment Type'),
				'index'     => 'order_payment_type',
			//	'filter_index'  => 'payment_state',
			//	'type'      => 'options',
			//	'options'   => Mage::getModel('csmarketplace/vorders')->getStates(),
				'renderer' => $this->getLayout()->createBlock('csmarketplace/adminhtml_vorders_grid_renderer_paymenttype'),
		));
		
	  /* $this->addColumn('action',
            array(
                'header'    => Mage::helper('csmarketplace')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
               	'renderer' => 'Ced_CsMarketplace_Block_Adminhtml_Vorders_Grid_Renderer_View',
				'filter'    => false,
                'sortable'  => false
        )); */
  		return parent::_prepareColumns();
 	 }
  
  protected function _prepareLayout()
  {
	  $head = $this->getLayout()->getBlock('head');
	  if(is_object($head)){
		$this->getLayout()->getBlock('head')->addJs('ced/csmarketplace/adminhtml/popup.js');
	  }
  	  return parent::_prepareLayout();
  }
  
  protected function _vendornameFilter($collection, $column){
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
 		$vendorIds = 	Mage::getModel('csmarketplace/vendor')->getCollection()
		->addAttributeToFilter('name', array('like' => '%'.$column->getFilter()->getValue().'%'))
		->getAllIds();
 
		if(count($vendorIds)>0)
			$this->getCollection()->addFieldToFilter('vendor_id', array('in', $vendorIds));
		else{
			$this->getCollection()->addFieldToFilter('vendor_id');
		}	
			return $this;
	}
  
	public function getRowUrl($row){
		return 'javascript:void(0);';
		$order = Mage::getModel('sales/order')->loadByIncrementId($row->getOrderId()); 
		return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $order->getId()));
  	}
  	
  	public function getGridUrl() {
  		return $this->getUrl('*/adminhtml_vendororder/grid', array('_secure'=>true, '_current'=>true));
  	}
}