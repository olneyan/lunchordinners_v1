<?php

/**
 * admin delivery boy left menu
 *
 * @category   Delivery
 * @package    Delivery_Boy
 * @author     <rajeeb.saraswati@gmail.com>
 */
class Delivery_Boy_Block_Adminhtml_Boy_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('delivery_boy_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('delivery_boy')->__('Delivery Boy Information'));
    }
}
