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
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */  
 
class Ced_CsMarketplace_Block_Adminhtml_Vpayments_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
    {
	  parent::__construct();
	  $this->setId('vpaymentGrid');
	  $this->setDefaultSort('created_at');
	  $this->setDefaultDir('DESC');
	  $this->setUseAjax(true);
	  $this->setSaveParametersInSession(true);
    }



  
  protected function _prepareCollection()
  {
	$vendor_id = $this->getRequest()->getParam('vendor_id',0);
    $collection = Mage::getModel('csmarketplace/vpayment')->getCollection();
	if($vendor_id) {
		$collection->addFieldToFilter('vendor_id',array('eq'=>$vendor_id));
	}
      $this->setCollection($collection);
  	
  	return parent::_prepareCollection();
  }
  protected function _prepareColumns()
  {
  

	$this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Transaction Date'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));
		
	$this->addColumn('transaction_id', array(
  			'header'    => Mage::helper('csmarketplace')->__('Transaction ID#'),
  			'align'     => 'left',
   			'index'     => 'transaction_id',
  			'filter_index'  => 'transaction_id',

  	));
	
	$this->addColumn('vendor_id', array(
  			'header'    => Mage::helper('csmarketplace')->__('Vendor Name'),
  			'align'     => 'left',
   			'index'     => 'vendor_id',
			'renderer' => 'Ced_CsMarketplace_Block_Adminhtml_Vorders_Grid_Renderer_Vendorname',
			'filter_condition_callback' => array($this, '_vendornameFilter'),
		));
		
	$this->addColumn('payment_method', array(
  			'header'    => Mage::helper('csmarketplace')->__('Payment Mode'),
  			'align'     => 'left',
   			'index'     => 'payment_method',
  			'filter_index'  => 'payment_method',
			'type'  => 'options',
			'options' => Ced_CsMarketplace_Helper_Acl::$PAYMENT_MODES,
			/* 'renderer' => 'Ced_CsMarketplace_Block_Adminhtml_Vpayments_Grid_Renderer_PaymentMode',
			'filter_condition_callback' => array($this, '_paymentModeFilter'), */
  	));
	
	$this->addColumn('transaction_type',
        array(
            'header'=> Mage::helper('csmarketplace')->__('Transaction Type'),
            'index' => 'transaction_type',
			'type'  => 'options',
			'options' => Ced_CsMarketplace_Model_Vpayment::getStates(),
    ));
	
	$this->addColumn('base_amount',
        array(
            'header'=> Mage::helper('csmarketplace')->__('Amount'),
            'index' => 'base_amount',
			'type'          => 'currency',
            'currency' => 'base_currency'
    ));
	
	
	$this->addColumn('base_fee',
        array(
            'header'=> Mage::helper('csmarketplace')->__('Adjustment Amount'),
            'index' => 'base_fee',
			'type'          => 'currency',
           'currency' => 'base_currency'
    ));
	
	
	$this->addColumn('base_net_amount',
        array(
            'header'=> Mage::helper('csmarketplace')->__('Net Amount'),
            'index' => 'base_net_amount',
			'type'          => 'currency',
            'currency' => 'base_currency'
    ));
	/*
	$this->addColumn('amount_desc',
		array(
				'header'=> Mage::helper('csmarketplace')->__('Amount Description'),
				'index' => 'amount_desc',
				'type'          => 'text',
				'renderer'=> $this->getLayout()->createBlock('csmarketplace/adminhtml_vpayments_grid_renderer_orderdesc'),
		));
	*/		
	$this->addColumn('action',
			array(
					'header'    =>  Mage::helper('csmarketplace')->__('Action'),
					'width'     => '100',
					'type'      => 'action',
					'getter'    => 'getId',
					'actions'   => array(
							array(
									'caption'   => Mage::helper('csmarketplace')->__('View'),
									'url'       => array('base'=> '*/*/details'),
									'field'     => 'id'
							)
					),
					'filter'    => false,
					'sortable'  => false,
					'index'     => 'stores',
					'is_system' => true,
			));
		
  	return parent::_prepareColumns();
  }
  
	 protected function _vendornameFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
 			$vendorIds = 	Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToFilter('name', array('like' => '%'.$column->getFilter()->getValue().'%'))->getAllIds();
 
 	if(count($vendorIds)>0)
        $this->getCollection()->addFieldToFilter('vendor_id', array('in', $vendorIds));
	else	
		$this->getCollection()->addFieldToFilter('vendor_id');
		
        return $this;
    }
    
   /*  protected function _paymentModeFilter($collection, $column)
    {
    	if (!$value = $column->getFilter()->getValue()) {
    		return $this;
    	}
    	$ids=Mage::helper('csmarketplace/acl')->getDefaultPaymentTypeValue($value);
    	$this->getCollection()->addFieldToFilter('payment_method', array('in' =>$ids));
    	return $this;
    } */

  public function getRowUrl($row) {
  	return $this->getUrl('*/adminhtml_vpayments/details', array('_secure'=>true, '_current'=>true,'id'=>$row->getId()));
  }
  
  public function getGridUrl() {
  	return $this->getUrl('*/adminhtml_vpayments/grid', array('_secure'=>true, '_current'=>true));
  }
}