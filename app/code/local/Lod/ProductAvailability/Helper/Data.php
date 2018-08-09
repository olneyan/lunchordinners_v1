<?php

/**
 * Description of Data
 *
 * @author rajeeb
 */
class Lod_ProductAvailability_Helper_Data extends Mage_Core_Helper_Data {

    private $now;
    private $ymd;
    
    
    public function __construct() {
        $this->now = Mage::getModel('core/date')->timestamp(now());
        $this->ymd = date('Y-m-d', $this->now);
    }
    
    
    /**
     * checks if product is available for sale right now or not
     * 
     * @param Mage_Catalog_Model_Product $product
     * @return boolean
     */
    public function isAvailableNow($product) {
        $sections = explode(",", $product->getAvailability());
        foreach ($sections as $section) {
            $fromTo = explode("-", $section);
            $from = trim(str_pad(str_pad($fromTo[0], 2, "0", STR_PAD_LEFT), 9, ":00", STR_PAD_RIGHT), ":");
            $to = trim(str_pad(str_pad($fromTo[1], 2, "0", STR_PAD_LEFT), 9, ":00", STR_PAD_RIGHT), ":");
            if ('00:00:00' == $from && '00:00:00' == $to) {
                $isAvailable = true;
            } else {
                $from = $this->ymd . ' ' . $from;
                $to = $this->ymd . ' ' . $to;
                $diff1 = strtotime(date("Y-m-d H:i:s", $this->now)) - strtotime($from);
                $diff2 = strtotime($to) - strtotime(date("Y-m-d H:i:s", $this->now));
                $isAvailable = ( ($diff1 >= 0) && ($diff2 >= 0) ) ? true : false;
            }
            if ($isAvailable) {
                break;
            }
        }
        return $isAvailable;
    }
}    