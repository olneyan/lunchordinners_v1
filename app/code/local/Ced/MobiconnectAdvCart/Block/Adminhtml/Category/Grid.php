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
class Ced_MobiconnectAdvCart_Block_Adminhtml_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct ();
		$this->setId ( 'categoryBannerGrid' );
		$this->setDefaultSort ( 'id' );
		$this->setDefaultDir ( 'asc' );
		$this->setSaveParametersInSession ( true );
		 $this->setUseAjax(true);
	}
	
	/**
	 * prepare collection for block to display
	 *
	 * @return Ced_Mobiconnect_Block_Adminhtml_Mobiconnect_Grid
	 */
	protected function _prepareCollection() {
		$collection = Mage::getModel ( 'mobiconnectadvcart/categorybanner' )->getCollection ();
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
		
		$this->addColumn ( 'banner_title', array (
				'header' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Title' ),
				'align' => 'center',
				'index' => 'banner_title',
				'type' => 'banner',
				'escape' => true,
				'sortable' => false,
				'width' => '150px' 
		) );
		
		$this->addColumn ( 'banner_image', array (
				'header' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Included Banner Image' ),
				'width' => '150px',
				'index' => 'banner_image',
				'renderer' => 'mobiconnectadvcart/adminhtml_category_edit_tab_renderer_selectedimage' 
		) );
		
		$this->addColumn ( 'show_on', array (
				'header' => Mage::helper ( 'mobiconnect' )->__ ( 'Show On' ),
				'width' => '150px',
				'index' => 'show_on',
		) );
		$this->addColumn ( 'banner_status', array (
				'header' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Status' ),
				'align' => 'left',
				'width' => '80px',
				'index' => 'banner_status',
				'type' => 'options',
				'options' => array (
						1 => 'Enabled',
						2 => 'Disabled' 
				) 
		) );
		$this->addColumn ( 'action', array (
        'header' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Action' ),
        'width' => '80px',
        'type' => 'action',
        'getter' => 'getId',
        'actions' => array (
            array (
                'caption' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'edit' ),
                'url' => array (
                    'base' => 'mobiconnectadvcart/adminhtml_category/edit' 
                ),
                'field' => 'id',
                'confirm' => Mage::helper ( 'mobiconnectadvcart' )->__ ( "Are you sure?\nThis operation will just delete the record from your local database." ) 
            ) 
        ),
        'filter' => false,
        'sortable' => false,
        'is_system' => true 
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
    array_unshift ( $visibility, array (
        'label' => '',
        'value' => '' 
    ) );
    $this->getMassactionBlock ()->addItem ( 'delete', array (
        'label' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Delete' ),
        'url' => $this->getUrl ( 'mobiconnectadvcart/adminhtml_category/massDelete' ) 
    ) )->addItem ( 'status', array (
        'label' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Change Visibility' ),
        'url' => $this->getUrl ( '*/*/massStatus', array (
            '_current' => true 
        ) ),
        'additional' => array (
            'visibility' => array (
                'name' => 'visibility',
                'type' => 'select',
                'class' => 'required-entry',
                'label' => Mage::helper ( 'catalog' )->__ ( 'Visibility' ),
                'values' => $visibility 
            ) 
        ) 
    ) );
    return $this;
  }
	
	/**
	 * get url for each row in grid
	 *
	 * @return string
	 */
	
	public function getGridUrl()
    {
    return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
?>