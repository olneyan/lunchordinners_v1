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
class Ced_CsMarketplace_Block_Adminhtml_Vendor_Entity_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	const VAR_NAME_FILTER = 'vendor_filter';
	
	public function __construct()
	{
	  parent::__construct();
	  $this->setId('vendorGrid');
	  $this->setDefaultSort('created_at');
	  $this->setDefaultDir('DESC');
	  $this->_varNameFilter = self::VAR_NAME_FILTER;
	  $this->setSaveParametersInSession(true);
	  $this->setUseAjax(true);
	}

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToSelect('*')->addAttributeToSort('created_at', 'desc');
	  $this->setCollection($collection); 
	 
	  return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
  	$this->addColumn('id', array(
  			'header'    => Mage::helper('csmarketplace')->__('Id'),
  			'align'     =>'right',
  			'width'     => '50px',
  			'index'     => 'entity_id',
  			
  	));
		$this->addColumn('created_at', array(
			'header'    => Mage::helper('csmarketplace')->__('Created At'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'created_at',
			'filter_index' => 'created_at',
			'type'	  => 'datetime',
        ));
		
	    $this->addColumn('name', array(
            'header'        => Mage::helper('csmarketplace')->__('Store'),
            'align'         => 'left',
            'type'          => 'text',
            'index'         => 'public_name',
			'filter_index' => 'public_name',
        ));
        $this->addColumn('email', array(
            'header'    => Mage::helper('csmarketplace')->__('Vendor Email'),
            'align'     =>'left',
            'index'     => 'email',
			'filter_index' => 'email',
        ));
	  /*   $this->addColumn('group', array(
            'header'        => Mage::helper('csmarketplace')->__('Vendor Group'),
            'align'     	=> 'left',
			'index'         => 'group',
			'filter_index' => 'group',
            'type'          => 'options',
			'options'		=> Mage::getModel('csmarketplace/system_config_source_group')->toFilterOptionArray(),
        )); */
	    if (!Mage::app()->isSingleStoreMode()) {
	    	$this->addColumn('website_id', array(
	    			'header'    => Mage::helper('customer')->__('Website'),
	    			'align'     => 'center',
	    			'width'     => '80px',
	    			'type'      => 'options',
	    			'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
	    			'index'     => 'website_id',
	    	));
	    }
		$this->addColumn('status', array(
					'header'        => Mage::helper('csmarketplace')->__('Vendor Status'),
					'align'     	=> 'left',
					'index'         => 'status',
					'filter_index'  => 'status',
					'type'          => 'options',
					'options'		=> Mage::getModel('csmarketplace/system_config_source_status')->toFilterOptionArray(true),
				));
		$this->addColumn('member_id', array(
				'header'        => Mage::helper('csmarketplace')->__('Member ID'),
				'align'       => 'left',
				'index'         => 'member_id',
				'filter_index'  => 'member_id',
				//'type'          => 'options',
				//'options'   => Mage::getModel('csmarketplace/system_config_source_status')->toFilterOptionArray(true),
		));
		$this->addColumn('approve', array(
					'header'        => Mage::helper('csmarketplace')->__('Approve'),
					'align'     	=> 'left',
					'index'         => 'entity_id',
					'filter'   	 	=> false,
					'sortable'  	=> false,
					'type'          => 'text',
					'renderer' => 'Ced_CsMarketplace_Block_Adminhtml_Vendor_Entity_Grid_Renderer_Approve',
				));
  		if (Mage::getStoreConfig('ced_csmarketplace/general/shopurl_active'))
        {
      		$this->addColumn('shop_disable', array(
      				'header'        => Mage::helper('csmarketplace')->__('Vendor Shop Status'),
      				'align'     	=> 'left',
      				'index'         => 'shop_disable',
      				'filter'   	 	=> false,
      				'sortable'  	=> false,
      				'type'          => 'text',
      				'renderer' => 'Ced_CsMarketplace_Block_Adminhtml_Vendor_Entity_Grid_Renderer_Disableshop',
              //'is_system'   => true,
      		));
       	}
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('csmarketplace')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
					array(
                        'caption'   => Mage::helper('csmarketplace')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'vendor_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('csmarketplace')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('csmarketplace')->__('XML'));
	  
      return parent::_prepareColumns();
  }
  
  protected function _prepareMassaction()
  {
  	$this->setMassactionIdField('vendor_id');
  	$this->getMassactionBlock()->setFormFieldName('vendor_id');
  
  	$this->getMassactionBlock()->addItem('delete', array(
  			'label'    => Mage::helper('csmarketplace')->__('Delete Vendor(s)'),
  			'url'      => $this->getUrl('*/*/massDelete'),
  			'confirm'  => Mage::helper('csmarketplace')->__('Are you sure?')
  	));
  
  	$statuses = Mage::getModel('csmarketplace/system_config_source_status')->toFilterOptionArray();
  	
  	$this->getMassactionBlock()->addItem('status', array(
  			 'label'=> Mage::helper('csmarketplace')->__('Change Vendor(s) Status'),
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
  	$this->getMassactionBlock()->addItem('shop_disable', array(
  			'label'=> Mage::helper('csmarketplace')->__('Change Vendor Shops'),
  			'url'  => $this->getUrl('*/*/massDisable/', array('_current'=>true)),
  			'additional' => array(
  					'visibility' => array(
  							'name' => 'shop_disable',
  							'type' => 'select',
  							'class' => 'required-entry',
  							'label' => Mage::helper('csmarketplace')->__('Vendor Shop Status'),
  							'default'=>'-1',
  							'values' =>array(array('value' => Ced_CsMarketplace_Model_Vshop::ENABLED, 'label'=>Mage::helper('csmarketplace')->__('Enabled')),
  									array('value' => Ced_CsMarketplace_Model_Vshop::DISABLED, 'label'=>Mage::helper('csmarketplace')->__('Disabled'))),
  					)
  			)
  	));
  	return $this;
  }
  
  public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_secure'=>true, '_current'=>true));
    }
	
  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('vendor_id' => $row->getId()));
  }

}
