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
  * @package   Ced_Mobinotification
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobinotification_Block_Adminhtml_Sendnotification_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('sendnotificationGrid');
      $this->setDefaultSort('id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
      $this->setUseAjax(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('mobinotification/mobidevices')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('device_rowid', array(
          'header'    => Mage::helper('mobinotification')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'device_rowid',
      ));

      $this->addColumn('device_id', array(
          'header'    => Mage::helper('mobinotification')->__('Device Id'),
          'align'     =>'left',
          'width'     => '150px',
          'index'     => 'device_id',
      ));
	  
       $this->addColumn('email', array(
          'header'    => Mage::helper('mobinotification')->__('Email'),
          'align'     =>'left',
          'index'     => 'email',
      ));
      $this->addColumn('extra', array(
          'header'    => Mage::helper('mobiconnectdeals')->__('Device'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'extra',
          'type'      => 'options',
          'options'   => array(
              'ios' => 'IOS',
              'android' => 'Android',
              'window' => 'Window'
          ),
      ));
      if($this->getRequest()->getParam('id')){
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('mobinotification')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getDeviceRowid',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('mobinotification')->__('Send'),
                        'url'       => array('base'=> '*/*/send','params'=>array('message_id'=>$this->getRequest()->getParam('id'))),
                        'field'     => 'device_rowid'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
      }
      
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('send_device_rowid');
        $this->getMassactionBlock()->setFormFieldName('device_rowid');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('mobinotification')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('mobinotification')->__('Are you sure?')
        ));
         $this->getMassactionBlock()->addItem('status', array(
             'label'    => Mage::helper('mobinotification')->__('Send Notification'),
             'url'      => $this->getUrl('*/*/massSend'.'/message_id/'.$this->getRequest()->getParam('id')),
             'confirm'  => Mage::helper('mobinotification')->__('Are you sure?')
        ));
        return $this;
    }
  public function getGridUrl()
  {
      return $this->getUrl('*/*/grid', array('_current' => true));
  }

}