<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced;
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_CsMarketplace_VshopsController extends Mage_Core_Controller_Front_Action {

    /**
     * Initialize requested vendor object
     *
     * @return Ced_CsMarketplace_Model_Vendor
     */
    protected $_vendorCollection;

    protected function _initVendor() {
        Mage::dispatchEvent('csmarketplace_controller_vshops_init_before', array(
            'controller_action' => $this
        ));

        if (!Mage::helper('csmarketplace/acl')->isEnabled())
            return false;
        $shopUrl = Mage::getModel('csmarketplace/vendor')->getShopUrlKey($this->getRequest()->getParam('shop_url', ''));
        if (!strlen($shopUrl)) {
            return false;
        }

        $vendor = Mage::getModel('csmarketplace/vendor')->setStoreId(Mage::app()->getStore()->getId())->loadByAttribute('shop_url', $shopUrl);
        if (!Mage::helper('csmarketplace')->canShow($vendor)) {
            return false;
        } else if (!Mage::helper('csmarketplace')->isShopEnabled($vendor)) {
            return false;
        }

        // Mage::getSingleton('catalog/session')->setLastVisitedCategoryId($category->getId());
        Mage::register('current_vendor', $vendor);
        // Mage::register('current_entity_key', $category->getPath());

        try {
            Mage::dispatchEvent('csmarketplace_controller_vshops_init_after', array(
                'vendor' => $vendor,
                'controller_action' => $this
            ));
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return false;
        }

        return $vendor;
    }

    /**
     * Vendor Shop list action
     */
    public function indexAction() {
        if (!Mage::getStoreConfig('ced_csmarketplace/general/shopurl_active')) {
            $this->_redirect('/');
            return;
        }
        if (!Mage::helper('csmarketplace/acl')->isEnabled()) {
            $this->_forward('noRoute');
            return;
        }

        $this->setInformation();

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('checkout/session');
        $title = Mage::getStoreConfig('ced_vshops/general/vshoppage_title', Mage::app()->getStore()->getId()) ? Mage::getStoreConfig('ced_vshops/general/vshoppage_title', Mage::app()->getStore()->getId()) : "CsMarketplace Vendor Shops";
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('csmarketplace')->__($title));
        if ($meta_description = Mage::getStoreConfig('ced_vseo/general/meta_description', Mage::app()->getStore()->getId()))
            $this->getLayout()->getBlock('head')->setDescription($meta_description);
        if ($meta_keywords = Mage::getStoreConfig('ced_vseo/general/meta_keywords', Mage::app()->getStore()->getId()))
            $this->getLayout()->getBlock('head')->setKeywords($meta_keywords);

        $this->renderLayout();
    }

    public function setInformation() {

        $this->_vendorCollection = Mage::getResourceModel('csmarketplace/vendor_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToSelect(array(
                'store_latitude',
                'store_longitude',
                'delivery_radius'
            ), 'left')
            ->addAttributeToFilter('status', array('eq' => Ced_CsMarketplace_Model_Vendor::VENDOR_APPROVED_STATUS));

        $sortBy = $this->getRequest()->getQuery('sortby', 'distance');
        $sortDir = $this->getRequest()->getQuery('sortdir', 'asc');
        if ($sortBy === 'distance' || $sortBy === 'opens_at') {
            $this->_vendorCollection->getSelect()->order("$sortBy $sortDir");
        } else {
            $this->_vendorCollection->setOrder($sortBy, $sortDir);
        }

        $this->_vendorCollection->joinTable(
            array('vshop'=> 'mg_ced_csmarketplace_vendor_shop'), 
            'vendor_id=entity_id', 
            array('shop_disable'=>'shop_disable')
        );

        $this->_vendorCollection->addFilterToMap('distance', 'e.distance');
        $this->_vendorCollection->addFilterToMap('shop_disable', 'vshop.shop_disable');
        $this->_vendorCollection->addFieldToFilter('shop_disable', array('eq' => 1));
        
        $char = $this->getRequest()->getParam('char');
        if (strlen($char)) {
            $this->_vendorCollection->addAttributeToFilter('public_name', array('like' => '%' . $char . '%'));
        }

        $fgroup = $this->getRequest()->getParam('fgroup');
        if (strlen($fgroup)) {
            $this->_vendorCollection->addAttributeToFilter('food_group', array('in' => $fgroup));
        }

        
        $rest = $this->getRequest()->getParam('rest');
        $validIds = [];
        $disatanceArray = [];
        if (strlen($rest)) {
            $origin = str_replace(' ', '', $rest);
            $geocodeCacheId = 'geocode_cache';
            $geocodeCollection = unserialize(Mage::app()->loadCache($geocodeCacheId));
            if (!$geocodeCollection || (is_array($geocodeCollection) && !array_key_exists($origin, $geocodeCollection) && Mage::app()->useCache('collections'))) {
                $address_url = "http://maps.google.com/maps/api/geocode/json?address=$origin&sensor=false";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $address_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $response = curl_exec($ch);
                curl_close($ch);
                $response_a = json_decode($response);
                $tags = array('collections'); //cache tags will be explain later in detail
                $lifetime = false; //false means infinity, or you can specify number of seconds
                $priority = 8; // number between 0-9, used by few backend cache models
                $geocodeCollection[$origin] = array(
                    'lat' => $response_a->results[0]->geometry->location->lat,
                    'lon' => $response_a->results[0]->geometry->location->lng
                );
                if ("OK" === $response_a->status) {
                    Mage::app()->saveCache(serialize($geocodeCollection), $geocodeCacheId, $tags, $lifetime, $priority);
                }
            }
//            Zend_Debug::dump($geocodeCollection);
            $response_a = $geocodeCollection[$origin];
            $lat1 = $response_a['lat'];
            $lon1 = $response_a['lon'];
            
            if (!is_null($lat1) && !is_null($lon1)) {
                Mage::register('got_geo_codes', true);
                $this->_vendorCollection->getSelect()
                    ->columns(array(
                        'distance' => new Zend_Db_Expr(
                            "round(((degrees(acos(sin(RADIANS($lat1)) * sin(RADIANS(`at_store_latitude`.`value`)) 
                                + cos(RADIANS($lat1)) * cos(RADIANS(`at_store_latitude`.`value`)) 
                                * cos(RADIANS($lon1 - `at_store_longitude`.`value`)))) * 60 * 1.5515) * 1.609344), 2)"
                        )
                ));
                
                $i = 0;
                if ($lat1) {
                    foreach ($this->_vendorCollection->getData() as $val) {
                        $lat2 = $val['store_latitude'];
                        $lon2 = $val['store_longitude'];
                        if (!$lat2) { continue; }
                        $unit = Mage::getStoreConfig('lod_configs/address_search_bar/radius_unit');
                        $theta = $lon1 - $lon2;
                        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
                        $dist = acos($dist);
                        $dist = rad2deg($dist);
                        $miles = $dist * 60 * 1.5515;
                        $unit = strtoupper($unit);
                        if ($unit == "K") {
                            $Adist = $miles * 1.609344;
                            if ($Adist <= $val['delivery_radius']) {
                                $validIds [] = $val['entity_id'];
                                $disatanceArray [$i] ['id'] = $val['entity_id'];
                                $disatanceArray [$i] ['distance'] = round($Adist, 2) . " " . "km";
                                $i++;
                            }
                        } else if ($unit === 'M') {
                            $Adist = $miles;
                            if ($Adist <= $val['delivery_radius']) {
                                $validIds [] = $val['entity_id'];
                                $disatanceArray [$i] ['id'] = $val['entity_id'];
                                $disatanceArray [$i] ['distance'] = round($Adist, 2) . " " . "miles";
                                $i++;
                            }
                        } else { // defaults to K
                            $Adist = $miles * 1.609344;
                            if ($Adist <= $val['delivery_radius']) {
                                $validIds [] = $val['entity_id'];
                                $disatanceArray [$i] ['id'] = $val['entity_id'];
                                $disatanceArray [$i] ['distance'] = round($Adist, 2) . " " . "km";
                                $i++;
                            }
                        }
                    }
                }
                asort($validIds);
                $this->_vendorCollection->addFieldToFilter('entity_id', array('in' => $validIds));
            } else {
                Mage::register('got_geo_codes', false);
                $this->_vendorCollection->getSelect()->columns(array('distance' => new Zend_Db_Expr(0)));
                $i = 0;
                foreach ($this->_vendorCollection->getData() as $val) {
                    $validIds [] = $val['entity_id'];
                    $disatanceArray [$i] ['id'] = $val['entity_id'];
                    $disatanceArray [$i] ['distance'] = "N/A";
                    $i++;
                }
                asort($validIds);
                $this->_vendorCollection->addFieldToFilter('entity_id', array('in' => $validIds));
            }
        } else {
            Mage::register('got_geo_codes', false);
            $this->_vendorCollection->getSelect()->columns(array('distance' => new Zend_Db_Expr(0)));
            asort($validIds);
            $this->_vendorCollection->addFieldToFilter('entity_id', array('in' => $validIds));
        }
        
        if (!Mage::helper('csmarketplace')->isSharingEnabled()) {
            $this->_vendorCollection->addAttributeToFilter('website_id', array(
                'eq' => Mage::app()->getStore()->getWebsiteId()
            ));
        }
        
        if (strlen($rest) == 1) {
            Mage::register('vshop_lists', [
                'vendor' => '',
                'distance' => $disatanceArray,
                'validIds' => $validIds
            ]);
         } elseif (strlen($rest) && !count($validIds)) {
             Mage::register('vshop_lists', [
                 'vendor' => '',
                 'distance' => $disatanceArray,
                 'validIds' => $validIds
             ]);
         } elseif (strlen($rest)) {
             Mage::register('vshop_lists', [
                 'vendor' => $this->_vendorCollection,
                 'distance' => $disatanceArray,
                 'validIds' => $validIds
             ]);
        } else {
            Mage::register('vshop_lists', [
                'vendor' => $this->_vendorCollection,
                'distance' => $disatanceArray,
                'validIds' => $validIds
            ]);
        }
    }

    /**
     * Vendor Shop view action
     */
    public function viewAction() {
        if (!Mage::getStoreConfig('ced_csmarketplace/general/shopurl_active')) {
            $this->_redirect('/');
            return;
        }
        if ($vendor = $this->_initVendor()) {
            if (Mage::registry('current_category') == null) {
                $category = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load(Mage::app()->getStore()->getRootCategoryId());
                Mage::register('current_category', $category);
            }
            $this->loadLayout();
            $this->getLayout()->getBlock('head')->setTitle($vendor->getPublicName() . " " . Mage::helper('csmarketplace')->__('Shop'));
            if ($vendor->getMetaDescription()) {
                $this->getLayout()->getBlock('head')->setDescription($vendor->getMetaDescription());
            }
            if ($vendor->getMetaKeywords()) {
                $this->getLayout()->getBlock('head')->setKeywords($vendor->getMetaKeywords());
            }
            $this->renderLayout();
        } else {
            $this->_forward('noRoute');
        }
    }

}
