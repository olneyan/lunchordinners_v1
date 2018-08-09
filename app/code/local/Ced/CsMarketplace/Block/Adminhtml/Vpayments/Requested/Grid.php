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
 
class Ced_CsMarketplace_Block_Adminhtml_Vpayments_Requested_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
    {
	  parent::__construct();
	  $this->setId('vpaymentRequestedGrid');
	  $this->setDefaultSort('created_at');
	  $this->setDefaultDir('ASC');
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
	
	$collection = Mage::getModel('csmarketplace/vpayment_requested')
						->getCollection()
						->addFieldToFilter('status',array('eq'=>Ced_CsMarketplace_Model_Vpayment_Requested::PAYMENT_STATUS_REQUESTED));
	
	$main_table=Mage::helper('csmarketplace')->getTableKey('main_table');
	$order_id_column=Mage::helper('csmarketplace')->getTableKey('order_id');
	$amount_column=Mage::helper('csmarketplace')->getTableKey('amount');
	$collection->getSelect()->columns(array('order_ids' => new Zend_Db_Expr("IFNULL(GROUP_CONCAT(DISTINCT {$main_table}.{$order_id_column} SEPARATOR ','), '')")));
	$collection->getSelect()->columns("SUM({$main_table}.{$amount_column}) AS amounts");
	$collection->getSelect()->group("vendor_id");		
    $this->setCollection($collection);
  	return parent::_prepareCollection();
  }
  
  protected function _prepareColumns()
  {
	$this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Request Date'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));
		
	$this->addColumn('vendor_id', array(
  			'header'    => Mage::helper('csmarketplace')->__('Vendor Name'),
  			'align'     => 'left',
   			'index'     => 'vendor_id',
			'renderer' => 'Ced_CsMarketplace_Block_Adminhtml_Vorders_Grid_Renderer_Vendorname',
			'filter_condition_callback' => array($this, '_vendornameFilter'),
		));
		
	$this->addColumn('order_ids', array(
  			'header'    => Mage::helper('csmarketplace')->__('Order IDs#'),
  			'align'     => 'left',
   			'index'     => 'order_ids',
			'renderer'=> $this->getLayout()->createBlock('csmarketplace/adminhtml_vpayments_requested_grid_renderer_orderdesc')

  	));
	
	
	
	$this->addColumn('amounts',
        array(
            'header'=> Mage::helper('csmarketplace')->__('Amount To Pay'),
            'index' => 'amounts',
			'type'          => 'currency',
            'currency' => 'base_currency'
    ));
	
	$this->addColumn('status', array(
            'header'    => Mage::helper('sales')->__('Status'),
            'index'     => 'status',
			'filter_index'  => 'status',
            'type'      => 'options',
            'options'   => Ced_CsMarketplace_Model_Vpayment_Requested::getStatuses(),
			'renderer' => $this->getLayout()->createBlock('csmarketplace/adminhtml_vpayments_requested_grid_renderer_paynow'),
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
  
  public function getGridUrl() {
  	return $this->getUrl('*/adminhtml_vpayments_requested/grid', array('_secure'=>true, '_current'=>true));
  }
}