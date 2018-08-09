<?php
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category  Ced
  * @package   Ced_MobiconnectAdvCart
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_MobiconnectAdvCart_Block_Adminhtml_Widget_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct ();
		$this->setId ( 'mobiconnectadvGrid' );
		$this->setDefaultSort ( 'id' );
		$this->setDefaultDir ( 'ASC' );
		$this->setSaveParametersInSession ( true );
		$this->setUseAjax(true);
	}
	
	/**
	 * prepare collection for block to display
	 *
	 * @return Ced_Mobiconnect_Block_Adminhtml_Mobiconnect_Grid
	 */
	protected function _prepareCollection() {
		$collection = Mage::getModel ( 'mobiconnectadvcart/layouts' )->getCollection ();

		foreach($collection as $link){
        if($link->getStore() && $link->getStore() != 0 ){ 
            $link->setStore(explode(',',$link->getStore()));
        }
        else{ 
            $link->setStore(array('0'));
        }
    }
		$this->setCollection ( $collection );
		return parent::_prepareCollection ();
	}
	
	/**
	 * prepare columns for this grid
	 *
	 * @return Ced_Mobiconnect_Block_Adminhtml_Mobiconnect_Grid
	 */
	protected function _prepareColumns() {
		$this->addColumn ( 'id', array (
				'header' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'ID' ),
				'align' => 'right',
				'width' => '50px',
				'index' => 'id' 
		) );
		
		$this->addColumn ( 'widget_title', array (
				'header' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Title' ),
				'align' => 'right',
				'index' => 'widget_title',
				'type' => 'banner',
				'escape' => true,
				'sortable' => false,
				'width' => '200px' 
		) );
		
		if (! Mage::app ()->isSingleStoreMode ()) {
			$this->addColumn ( 'store', array (
					'header' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Store View' ),
					'index' => 'store',
					'type' => 'store',
					'store_all' => true,
					'store_view' => true,
					'sortable' => true,
					'filter_condition_callback' => array (
						$this,
							'_filterStoreCondition' 
					) 
			) );
		}
		$this->addColumn ( 'banner_image', array (
				'header' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Included Images' ),
				'index' => 'banner_image',
				'align' => 'right',
				'renderer' => 'mobiconnectadvcart/adminhtml_widget_edit_tab_renderer_selectedimage' 
		)
		 );
		$this->addColumn ( 'created_at', array (
				'header' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Created At' ),
				'index' => 'created_at',
				'align' => 'right' 
		)
		 );
		$this->addColumn ( 'updated_at', array (
				'header' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Updated At' ),
				'index' => 'updated_at',
				'align' => 'right' 
		)
		 );
		$this->addColumn ( 'status', array (
				'header' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Status' ),
				'align' => 'left',
				'width' => '80px',
				'index' => 'status',
				'type' => 'options',
				'options' => array (
						1 => 'Enabled',
						2 => 'Disabled' 
				) 
		) );
		
		return parent::_prepareColumns ();
	}
	protected function _prepareMassaction() {
		$visibility = array (
				'1' => 'Enabled',
				'2' => 'Disabled' 
		);
		$this->setMassactionIdField ( 'id' );
		$this->getMassactionBlock ()->setFormFieldName ( 'banner_ids' );
		$this->getMassactionBlock ()->setUseSelectAll ( true );
		$this->getMassactionBlock ()->addItem ( 'delete', array (
				'label' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Delete' ),
				'url' => $this->getUrl ( '*/*/massDelete' ),
				'confirm' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Are you sure?' ) 
		) );
		$this->getMassactionBlock ()->addItem ( 'status', array (
				'label' => Mage::helper ( 'catalog' )->__ ( 'Change Status' ),
				'url' => $this->getUrl ( '*/*/mobiconnectadvcart', array (
						'_current' => true 
				) ),
				'additional' => array (
						'visibility' => array (
								'name' => 'status',
								'type' => 'select',
								'class' => 'required-entry',
								'label' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Status' ),
								'values' => $visibility 
						) 
				) 
		) );
		return $this;
		return $this;
	}

	/**
	 * get url for each row in grid
	 *
	 * @return string
	 */
	public function getRowUrl($row) {
		return $this->getUrl ( '*/*/edit', array (
				'id' => $row->getId () 
		) );
	}
	
	public function getGridUrl(){
     	return $this->getUrl('*/*/grid', array('_current' => true));
    }
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }
}
?>
