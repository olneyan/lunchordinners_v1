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
 
class Ced_CsMarketplace_Block_Adminhtml_Vproducts_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
    {
	  parent::__construct();
	  $this->setId('productGrid');
	  $this->setDefaultSort('entity_id');
	  $this->setDefaultDir('DESC');
	  $this->setSaveParametersInSession(true);
	  $this->setUseAjax(true);
    }

  protected function _prepareMassaction()
  {
  	$this->setMassactionIdField('entity_id');
  	$this->getMassactionBlock()->setFormFieldName('entity_id');
  
  	$this->getMassactionBlock()->addItem('delete', array(
  			'label'    => Mage::helper('csmarketplace')->__('Delete'),
  			'url'      => $this->getUrl('*/*/massDelete'),
  			'confirm'  => Mage::helper('csmarketplace')->__('Are you sure?')
  	));
  
  	$statuses = Mage::getSingleton('csmarketplace/vproducts')->getMassActionArray();
  	
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
  
  protected function _getStore()
  {
  	$storeId = (int) $this->getRequest()->getParam('store', 0);
  	return Mage::app()->getStore($storeId);
  }
  
  
   protected function _prepareCollection()
    {
		$vendor_id = $this->getRequest()->getParam('vendor_id',0);
		$allowedIds = array();
    	if(Mage::registry('usePendingProductFilter')){
    			$vproducts = Mage::getModel('csmarketplace/vproducts')->getVendorProducts(Ced_CsMarketplace_Model_Vproducts::PENDING_STATUS,0,0,-1);
    			Mage::unregister('usePendingProductFilter');
    			Mage::unregister('useApprovedProductFilter');
    	} elseif(Mage::registry('useApprovedProductFilter') ){
    			$vproducts = Mage::getModel('csmarketplace/vproducts')->getVendorProducts(Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS,0,0,-1);
    			Mage::unregister('useApprovedProductFilter');
    			Mage::unregister('usePendingProductFilter');
    	} else {
			$vproducts = Mage::getModel('csmarketplace/vproducts')->getVendorProducts('',0,0,-1);
		}
		foreach($vproducts as $vproduct) {
			$allowedIds[] = $vproduct->getProductId();
		}		
    	   
        $store = $this->_getStore();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
			->addAttributeToFilter('entity_id',array('in'=>$allowedIds));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        if ($store->getId()) {
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $adminStore
            );
            $collection->joinAttribute(
                'custom_name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'price',
                'catalog_product/price',
                'entity_id',
                null,
                'left',
                $store->getId()
            );
        }
        else {
            $collection->addAttributeToSelect('price');
        }
        $collection->joinField('check_status','csmarketplace/vproducts', 'check_status','product_id=entity_id',null,'left');
        $collection->joinField('vendor_id','csmarketplace/vproducts', 'vendor_id','product_id=entity_id',null,'left');
		if($vendor_id) {
			$collection->addFieldToFilter('vendor_id',array('eq'=>$vendor_id));
		}
        $this->setCollection($collection);

        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }
	
	protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField('websites',
                    'catalog/product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left');
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }
 
  protected function _prepareColumns()
  {
  	 $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '10px',
                'type'  => 'number',
            	'align'     => 'left',
                'index' => 'entity_id',
        ));
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'index' => 'name',
        ));
        
        $this->addColumn('vendor_id',
        		array(
        	'header'    => Mage::helper('csmarketplace')->__('Vendor Name'),
  			'align'     => 'left',
        	'width' => '100px',
   			'index'     => 'vendor_id',
			'renderer' => 'Ced_CsMarketplace_Block_Adminhtml_Vorders_Grid_Renderer_Vendorname',
        	'filter_condition_callback' => array($this, '_vendornameFilter'),
        		));

       
        $this->addColumn('type_id',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '60px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
        ));

        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'index' => 'sku',
        ));

        $store = $this->_getStore();
        $this->addColumn('price',
            array(
                'header'=> Mage::helper('catalog')->__('Price'),
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
        ));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->addColumn('qty',
                array(
                    'header'=> Mage::helper('catalog')->__('Qty'),
                    'width' => '50px',
                    'type'  => 'number',
                    'index' => 'qty',
            ));
        }
        if (!Mage::app()->isSingleStoreMode()) {
        	$this->addColumn('websites',
        			array(
        					'header'=> Mage::helper('catalog')->__('Websites'),
        					'width' => '100px',
        					'sortable'  => false,
        					'index'     => 'websites',
        					'type'      => 'options',
        					'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
        			));
        }
        if(!Mage::registry('usePendingProductFilter')&&!Mage::registry('useApprovedProductFilter')){
        $this->addColumn('check_status',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '70px',
                'index' => 'check_status',
                'type'  => 'options',
                'options' => Mage::getSingleton('csmarketplace/vproducts')->getOptionArray(),
        ));
        }
  	
        $this->addColumn('action',
        		array(
        				'header'    => Mage::helper('catalog')->__('Action'),
        				'type'      => 'text',
        				'align'     => 'center',
        				'filter'    => false,
        				'sortable'  => false,
        				'renderer'=>'csmarketplace/adminhtml_vproducts_renderer_action',
        				'index'     => 'action',
        		));
        $this->addColumn('view',
        		array(
        				'header'    => Mage::helper('catalog')->__('View'),
        				'type'      => 'text',
        				'align'     => 'center',
        				'filter'    => false,
        				'sortable'  => false,
        				'renderer'=>'csmarketplace/adminhtml_vproducts_renderer_view',
        				'index'     => 'view',
        		));
  	 
  	return parent::_prepareColumns();
  }
  
  protected function _prepareLayout()
  {
  	$this->getLayout()->getBlock('head')->addJs('ced/csmarketplace/adminhtml/popup.js');
  
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
  
  public function getGridUrl() {
  	if($this->getRequest()->getActionName()=="approved"){
  		return $this->getUrl('*/adminhtml_vproducts/gridapproved', array('_secure'=>true, '_current'=>true));
  	}
  	elseif($this->getRequest()->getActionName()=="pending"){
  		return $this->getUrl('*/adminhtml_vproducts/gridpending', array('_secure'=>true, '_current'=>true));
  	}
  	return $this->getUrl('*/adminhtml_vproducts/grid', array('_secure'=>true, '_current'=>true));	
  }
}