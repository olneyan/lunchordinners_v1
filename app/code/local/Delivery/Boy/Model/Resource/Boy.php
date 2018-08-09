<?php

class Delivery_Boy_Model_Resource_Boy extends Mage_Eav_Model_Entity_Abstract {
    
    public function _construct() {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('delivery_boy');
        $this->setConnection(
            $resource->getConnection('boy_read'),
            $resource->getConnection('boy_write')
        );
    }

}