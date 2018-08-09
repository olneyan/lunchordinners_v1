<?php

/**
 * Delivery Boy main block
 *
 * @category   Delivery
 * @package    Delivery_Boy
 * @author     <rajeeb.saraswati@gmail.com>
 */
class Delivery_Boy_Block_Adminhtml_Boy extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'delivery_boy';
        $this->_controller = 'adminhtml_boy';
        $this->_headerText = Mage::helper('delivery_boy')->__('Manage Delivery Boys');
        parent::__construct();
        $this->_updateButton('add', 'label', Mage::helper('delivery_boy')->__('Add New Delivery Boy'));
    }
}