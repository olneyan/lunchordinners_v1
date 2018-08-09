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
  * @package   Ced_Mobiconnect
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobiconnect_Block_Adminhtml_Mobiconnect_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct ();
		$this->setId ( 'mobiconnectGrid' );
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
		$collection = Mage::getModel ( 'mobiconnect/banner' )->getCollection ();
		$this->setCollection ( $collection );
		return parent::_prepareCollection ();
	}
	public function getlayoutType(){
		$options=Mage::getModel('mobiconnect/banner')->getLayoutType();
		/*$options=array(
			'simple'=>'simple',
			'core'=>'core',
			);*/
		return $options;
	}
	/**
	 * prepare columns for this grid
	 *
	 * @return Ced_Mobiconnect_Block_Adminhtml_Mobiconnect_Grid
	 */
	protected function _prepareColumns() {
		$this->addColumn ( 'id', array (
				'header' => Mage::helper ( 'mobiconnect' )->__ ( 'ID' ),
				'align' => 'right',
				'width' => '50px',
				'index' => 'id' 
		) );
		
		$this->addColumn ( 'title', array (
				'header' => Mage::helper ( 'mobiconnect' )->__ ( 'Title' ),
				'align' => 'center',
				'index' => 'title',
				'type' => 'banner',
				'escape' => true,
				'sortable' => false,
				'width' => '150px' 
		) );
		
		$this->addColumn ( 'image', array (
				'header' => Mage::helper ( 'mobiconnect' )->__ ( 'Banner Image' ),
				'width' => '150px',
				'index' => 'image',
				'renderer' => 'mobiconnect/adminhtml_mobiconnect_edit_tab_renderer_image' 
		) );
		
		$this->addColumn ( 'description', array (
				'header' => Mage::helper ( 'mobiconnect' )->__ ( 'Description' ),
				'width' => '150px',
				'index' => 'description' 
		) );
		$this->addColumn ( 'home_layout', array (
				'header' => Mage::helper ( 'mobiconnect' )->__ ( 'Layout Type' ),
				'align' => 'left',
				'width' => '80px',
				'index' => 'home_layout',
				'type' => 'options',
				'options' => $this->getlayoutType(),
		) );
		$this->addColumn ( 'status', array (
				'header' => Mage::helper ( 'mobiconnect' )->__ ( 'Status' ),
				'align' => 'left',
				'width' => '80px',
				'index' => 'status',
				'type' => 'options',
				'options' => array (
						1 => 'Enabled',
						2 => 'Disabled' 
				) 
		) );
		
		$this->addColumn ( 'show_in', array (
				'header' => Mage::helper ( 'mobiconnect' )->__ ( 'Link to' ),
				'width' => '150px',
				'index' => 'show_in',
				'type' => 'options',
				'options' => Mage::getModel('mobiconnect/show')->toOptionArray(),
				'renderer' => 'mobiconnect/adminhtml_mobiconnect_edit_tab_renderer_website' 
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
				'label' => Mage::helper ( 'mobiconnect' )->__ ( 'Delete' ),
				'url' => $this->getUrl ( '*/*/massDelete' ),
				'confirm' => Mage::helper ( 'mobiconnect' )->__ ( 'Are you sure?' ) 
		) );
		$this->getMassactionBlock ()->addItem ( 'status', array (
				'label' => Mage::helper ( 'catalog' )->__ ( 'Change Status' ),
				'url' => $this->getUrl ( '*/*/massVisibilty', array (
						'_current' => true 
				) ),
				'additional' => array (
						'visibility' => array (
								'name' => 'status',
								'type' => 'select',
								'class' => 'required-entry',
								'label' => Mage::helper ( 'catalog' )->__ ( 'Status' ),
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
	//protected $_massactionBlockName = 'adminhtml/widget_grid_massaction';
	public function getRowUrl($row) {
		return $this->getUrl ( '*/*/edit', array (
				'id' => $row->getId () 
		) );
	}
	public function getGridUrl(){
     	return $this->getUrl('*/*/grid', array('_current' => true));
    }
}
?>