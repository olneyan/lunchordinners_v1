<?php

class Delivery_Boy_IndexController extends Mage_Core_Controller_Front_Action {
    
    public function indexAction() {
        
        
//        $deliveryBoy = Mage::getModel('delivery_boy/boy');
//        Zend_Debug::dump($deliveryBoy);
//        
//        $deliveryBoy->setMobile('1234567891')
//            ->setEmail('rajeeb.saraswati@hotmail111.com')
//            ->setInitials('Mr.')
//            ->setFirstname('Rajeeb')
//            ->setMiddlename('Kumar')
//            ->setLastname('Sahoo')
//            ->setUsername('rajeeb0054')
//            ->setPassword('5ara3ati')
//            ->save();
////        
//        Zend_Debug::dump($deliveryBoy);
        
        $deliveryBoys = Mage::getResourceModel('delivery_boy/boy_collection');
        foreach ( $deliveryBoys->getItems() as $deliveryBoy ) {
            Zend_Debug::dump($deliveryBoy->load()->getData());
        }
        
        
//        echo get_class(Mage::helper('delivery_service'));
        
    }
}