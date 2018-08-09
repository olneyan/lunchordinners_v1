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
  * @package   Ced_MobiconnectCms
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_MobiconnectCms_Block_Adminhtml_Cmsblock_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('mobiconnectGrid');
      $this->setDefaultSort('id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }
  protected function _prepareCollection()
  {
      $collection = Mage::getModel('mobiconnectcms/cmsblock')->getCollection();
      foreach($collection as $link){
        if($link->getStore() && $link->getStore() != 0 ){ 
            $link->setStore(explode(',',$link->getStore()));
        }
        else{
            $link->setStore(array('0'));
        }
    }
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }
  protected function _prepareColumns()
  {
      $this->addColumn('id', array(
          'header'    => Mage::helper('mobiconnectcms')->__('CMS ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'id',
      ));

	    $this->addColumn('title', array(
            'header' => Mage::helper('mobiconnectcms')->__('CMS Title'),
            'align' => 'center',
            'index' => 'title',
            'escape' => true,
            'sortable' => false,
            'width' => '150px',
        ));
      $this->addColumn('identifier', array(
            'header' => Mage::helper('mobiconnectcms')->__('Identifier'),
            'align' => 'center',
            'index' => 'identifier',
            'escape' => true,
            'sortable' => false,
            'width' => '150px',
        ));
	
	    if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store', array(
                'header'        => Mage::helper('mobiconnectcms')->__('Store View'),
                'index'         => 'store',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'width'         => '150px',
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),
            ));
        }
      $this->addColumn('status', array(
          'header'    => Mage::helper('mobiconnectcms')->__('CMS Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              0 => 'Disabled',
          ),
      ));
      return parent::_prepareColumns();
  }
  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
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