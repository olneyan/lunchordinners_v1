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
  * @package   Ced_Mobiconnectdeals
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobiconnectdeals_Block_Adminhtml_Group_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('groupGrid');
      $this->setDefaultSort('group_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
      $this->setUseAjax(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('mobiconnectdeals/group')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('group_id', array(
          'header'    => Mage::helper('mobiconnectdeals')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'group_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('mobiconnectdeals')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));
	  

      $this->addColumn('group_status', array(
          'header'    => Mage::helper('mobiconnectdeals')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'group_status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
       $this->addColumn('timer_status', array(
          'header'    => Mage::helper('mobiconnectdeals')->__('Show Timer'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'timer_status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Yes',
              2 => 'No',
          ),
      ));
      $this->addColumn('view_all_status', array(
          'header'    => Mage::helper('mobiconnectdeals')->__('Show View All Link'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'view_all_status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Yes',
              2 => 'No',
          ),
      ));
       $this->addColumn('is_static', array(
          'header'    => Mage::helper('mobiconnectdeals')->__('Is Static Banner'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'is_static',
          'type'      => 'options',
          'options'   => array(
              1 => 'Yes',
              2 => 'No',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('mobiconnectdeals')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getGroupId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('mobiconnectdeals')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'group_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('mobiconnectdeals')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('mobiconnectdeals')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('group_id');
        $this->getMassactionBlock()->setFormFieldName('group_id');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('mobiconnectdeals')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('mobiconnectdeals')->__('Are you sure?')
        ));

        $statuses = array();//Mage::getSingleton('mobiconnectdeals/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('mobiconnectdeals')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('mobiconnectdeals')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('group_id' => $row->getGroupId()));
  }
  public function getGridUrl()
  {
      return $this->getUrl('*/*/grid', array('_current' => true));
  }

}