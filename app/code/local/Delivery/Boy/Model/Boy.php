<?php

class Delivery_Boy_Model_Boy extends Mage_Core_Model_Abstract {
    
    protected function _construct() {
        $this->_init('delivery_boy/boy');
    }
    
    
    public function validate() {
        return true;
    }
}