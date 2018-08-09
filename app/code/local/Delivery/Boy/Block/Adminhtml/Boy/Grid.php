<?php

/**
 * Delivery Boy grid block
 *
 * @category    Delivery
 * @package     Delivery_Boy
 * @author      <rajeeb.saraswati@gmail.com>
 */
class Delivery_Boy_Block_Adminhtml_Boy_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    
    public function __construct() {
        parent::__construct();
        $this->setId('deliveryBoyGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return Delivery_Boy_Block_Adminhtml_Boy_Grid
     */
    protected function _prepareCollection()
    {
        /* @var $collection Delivery_Boy_Model_Resource_Boy_Collection */
        $collection = Mage::getResourceModel('delivery_boy/boy_collection')
                ->addAttributeToSelect('*');
        $collection->getSelect()->group('e.entity_id');
        $this->setCollection($collection);
        $grid = parent::_prepareCollection();
        return $grid;
    }

    /**
     * Prepare grid columns
     *
     * @return Delivery_Boy_Block_Adminhtml_Boy_Grid
     */
    protected function _prepareColumns()
    {
        $_helper = Mage::helper('delivery_boy');
        $this->addColumn('entity_id', array(
            'header'    => $_helper->__('ID'),
            'align'     => 'left',
            'index'     => 'entity_id',
        ));

        $this->addColumn('firstname', array(
            'header'    => $_helper->__('First Name'),
            'align'     => 'left',
            'index'     => 'firstname',
        ));

        $this->addColumn('lastname', array(
            'header'    => $_helper->__('Last Name'),
            'align'     => 'left',
            'index'     => 'lastname',
        ));

        $this->addColumn('email', array(
            'header'    => $_helper->__('Email'),
            'align'     => 'left',
            'index'     => 'email',
        ));
        
        $this->addColumn('mobile', array(
            'header'    => $_helper->__('Mobile'),
            'align'     => 'right',
            'index'     => 'mobile',
        ));

        return parent::_prepareColumns();
    }

    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=> true));
    }


    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('entity_id' => $row->getId()));
    }
}