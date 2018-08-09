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
 * @package   Ced_Mobiconnect
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
class Ced_Mobiconnect_Model_Feed extends Mage_AdminNotification_Model_Feed {

    const XML_USE_HTTPS_PATH = 'system/adminnotification/use_https';
    const XML_FEED_URL_PATH = 'system/csmarketplace/feed_url';
    const XML_FREQUENCY_PATH = 'system/csmarketplace/frequency';
    const XML_LAST_UPDATE_PATH = 'system/csmarketplace/last_update';
    const XML_FEED_TYPES = 'cedcore/feeds_group/feeds';
    const XML_PATH_INSTALLATED_MODULES = 'modules';

    /**
     * Feed url
     *
     * @var string
     */
    protected $_feedUrl;

    /**
     * Init model
     *
     */
    protected function _construct()
    {}
        
    /**
     * Retrieve feed url
     *
     * @return string
     */
    public function getFeedUrl()
    {
        if (is_null($this->_feedUrl)) {
            $this->_feedUrl = (Mage::getStoreConfigFlag(self::XML_USE_HTTPS_PATH) ? 'https://' : 'http://')
                    . Mage::getStoreConfig(self::XML_FEED_URL_PATH);
        }
        return $this->_feedUrl;
    }

    /**
     * Check feed for modification
     *
     * @return Mage_AdminNotification_Model_Feed
     */
    public function checkUpdate()
    {	
        $cedModules = Mage::helper('mobiconnect')->getCedCommerceExtensions();

        /* if(!isset($_GET['testdev'])) { 
          if (($this->getFrequency() + $this->getLastUpdate()) > time()) {
          return $this;
          }
          } */

        $feedData = array();

        $feed = array();

        $feedXml = $this->getFeedData(Mage::helper('mobiconnect')->getEnvironmentInformation());

        $allowedFeedType = explode(',', Mage::getStoreConfig(self::XML_FEED_TYPES));

        if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
            if (isset($_GET['testdev'])) {
                print_r($feedXml->channel->item);
                die;
            }
            foreach ($feedXml->channel->item as $item) {
                if (Mage::helper('mobiconnect')->isAllowedFeedType($item)) {
                    if (strlen(trim($item->module)) > 0) {
                        if (isset($feedData[trim((string) $item->module)]) && isset($feedData[trim((string) $item->module)]['release_version']) && strlen((string) $item->release_version) > 0 && version_compare($feedData[trim((string) $item->module)]['release_version'], trim((string) $item->release_version), '>') === true) {
                            continue;
                        }
                        $feedData[trim((string) $item->module)] = array(
                            'severity' => (int) $item->severity,
                            'date_added' => $this->getDate((string) $item->pubDate),
                            'title' => (string) $item->title,
                            'description' => (string) $item->description,
                            'url' => (string) $item->link,
                            'module' => (string) $item->module,
                            'release_version' => (string) $item->release_version,
                            'update_type' => (string) $item->update_type,
                        );
                        if (strlen((string) $item->warning) > 0) {
                            $feedData[trim((string) $item->module)]['warning'] = (string) $item->warning;
                        }

                        if (strlen((string) $item->product_url) > 0) {
                            $feedData[trim((string) $item->module)]['url'] = (string) $item->product_url;
                        }
                    }

                    $feed[] = array(
                        'severity' => (int) $item->severity,
                        'date_added' => $this->getDate((string) $item->pubDate),
                        'title' => (string) $item->title,
                        'description' => (string) $item->description,
                        'url' => (string) $item->link
                    );
                }
            }
            /* if(isset($_GET['testdev'])) {
              print_r($feed);
              print_r($feedData);
              die;
              } */
            if ($feed) {
                Mage::getModel('adminnotification/inbox')->parse(array_reverse($feed));
            }
            if ($feedData) {
                Mage::app()->saveCache(serialize($feedData), 'all_extensions_by_cedcommerce');
            }
        }
        $this->setLastUpdate();

        return $this;
    }

    /**
     * Retrieve DB date from RSS date
     *
     * @param string $rssDate
     * @return string YYYY-MM-DD YY:HH:SS
     */
    public function getDate($rssDate) {
        return gmdate('Y-m-d H:i:s', strtotime($rssDate));
    }

    /**
     * Retrieve Update Frequency
     *
     * @return int
     */
    public function getFrequency() {
        return Mage::getStoreConfig(self::XML_FREQUENCY_PATH) * 3600;
    }

    /**
     * Retrieve Last update time
     *
     * @return int
     */
    public function getLastUpdate() {
        return Mage::app()->loadCache('ced_notifications_lastcheck');
    }

    /**
     * Set last update time (now)
     *
     * @return Mage_AdminNotification_Model_Feed
     */
    public function setLastUpdate() {
        Mage::app()->saveCache(time(), 'ced_notifications_lastcheck');
        return $this;
    }

    /**
     * Retrieve feed data as XML element
     *
     * @return SimpleXMLElement
     */
    public function getFeedData($urlParams = array()) {
        $curl = new Varien_Http_Adapter_Curl();
        $curl->setConfig(array(
            'timeout' => 2
        ));
        $body = '';
        if (is_array($urlParams) && count($urlParams) > 0) {
            $body = Mage::helper('mobiconnect')->addParams('', $urlParams);
            $body = trim($body, '?');
        }
        if (isset($_GET['testdev'])) {
            print_r($body);
            die;
        }

        try {
            $curl->write(Zend_Http_Client::POST, $this->getFeedUrl(), '1.1', array(), $body);
            $data = $curl->read();
            if ($data === false) {
                return false;
            }
            $data = preg_split('/^\r?$/m', $data, 2);
            $data = trim($data[1]);

            $curl->close();

            $xml = new SimpleXMLElement($data);
        } catch (Exception $e) {
            return false;
        }

        return $xml;
    }

    public function getFeedXml() {
        try {
            $data = $this->getFeedData();
            $xml = new SimpleXMLElement($data);
        } catch (Exception $e) {
            $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?>');
        }

        return $xml;
    }

}
