<?php
/**
 * Description of Observer
 *
 * @author rajeeb
 */
class Lod_ProductAvailability_Model_Observer {
    
    /**
     * 
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_Catalog_Model_Observer
     */
    public function isAvailableNow(Varien_Event_Observer $observer)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getProduct();
        $isAvailable = Mage::helper('lod_product_availability')->isAvailableNow($product);
        if (!$isAvailable) {
            $product->setData('is_salable', false);
        }
        return $this;
    }
    
}
