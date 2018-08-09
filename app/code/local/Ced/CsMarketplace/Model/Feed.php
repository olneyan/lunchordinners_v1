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
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * CsMarketplace Feed model
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Model_Feed extends Mage_AdminNotification_Model_Feed
{
    const XML_USE_HTTPS_PATH    = 'system/adminnotification/use_https';
    const XML_FEED_URL_PATH     = 'system/csmarketplace/feed_url';
    const XML_FREQUENCY_PATH    = 'system/csmarketplace/frequency';
    const XML_LAST_UPDATE_PATH  = 'system/csmarketplace/last_update';
	
	const XML_FEED_TYPES		= 'cedcore/feeds_group/feeds';
	const XML_PATH_INSTALLATED_MODULES = 'modules';

    const XML_SET_FEED_URL_PATH     = 'system/csmarketplace/set_feed_url';
    const XML_CED_INSTALLED_MODULES = 'system/csmarketplace/ced_installed_modules';

    /**
     * Feed url
     *
     * @var string
     */
    protected $_feedUrl;
    protected $_setfeedUrl;
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
     * Retrieve setfeed url
     *
     * @return string
     */
    public function getSetFeedUrl()
    {
        if (is_null($this->_setfeedUrl)) {
            $this->_setfeedUrl = (Mage::getStoreConfigFlag(self::XML_USE_HTTPS_PATH) ? 'https://' : 'http://')
                . Mage::getStoreConfig(self::XML_SET_FEED_URL_PATH);
        }
        return $this->_setfeedUrl;
    }

    public function isNewCedModuleInstalled() {
        
        $row = Mage::getStoreConfig(self::XML_CED_INSTALLED_MODULES);
        $installedModules = Mage::helper('csmarketplace')->getCedCommerceExtensions();
        if(!$row)
        {
            $data = array('modules' => $installedModules,'last_update' => time());
            $value = serialize($data);
            Mage::getConfig()->saveConfig(self::XML_CED_INSTALLED_MODULES, $value, 'default', 0);
            return true;
        }

        $fetched_data = unserialize($row);

        $lastUpdate = $fetched_data['last_update'];
        if(($this->getFrequency() + $lastUpdate) > time()) {

            $data = array('modules' => $installedModules,'last_update' => time());
            $value = serialize($data);
            Mage::getConfig()->saveConfig(self::XML_CED_INSTALLED_MODULES, $value, 'default', 0);

            $preInstalledModules = $fetched_data['modules'];
            if(count($preInstalledModules) != count($installedModules))
                return true;
            else
                return false;
        }
    }



    /**
     * Check feed for modification
     *
     * @return Mage_AdminNotification_Model_Feed
     */
    public function checkUpdate()
    {	

        if(!isset($_GET['testdev'])) {
            if (!$this->isNewCedModuleInstalled()) {
             return $this;
            }
        }
		
        $urlParams = Mage::helper('csmarketplace')->getEnvironmentInformation();

        $curl = new Varien_Http_Adapter_Curl();
        $curl->setConfig(array(
            'timeout'   => 3
        ));
        $body = '';
        if (is_array($urlParams) && count($urlParams) > 0) {
            $body = Mage::helper('csmarketplace')->addParams('', $urlParams);
            $body = trim($body, '?');
        }
    
        try {
          
            $curl->write(\Zend_Http_Client::POST, $this->getSetFeedUrl(), '1.1', array(), $body);
            $data = $curl->read();
           
            if ($data === false) {
                return false;
            }
            $curl->close();
        } catch (\Exception $e) {
            return false;
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
    public function getDate($rssDate)
    {
        return gmdate('Y-m-d H:i:s', strtotime($rssDate));
    }

    /**
     * Retrieve Update Frequency
     *
     * @return int
     */
    public function getFrequency()
    {
        return Mage::getStoreConfig(self::XML_FREQUENCY_PATH) * 3600;
    }

    /**
     * Retrieve Last update time
     *
     * @return int
     */
    public function getLastUpdate()
    {
        return Mage::app()->loadCache('ced_notifications_lastcheck');
    }

    /**
     * Set last update time (now)
     *
     * @return Mage_AdminNotification_Model_Feed
     */
    public function setLastUpdate()
    {
        Mage::app()->saveCache(time(), 'ced_notifications_lastcheck');
        return $this;
    }

    /**
     * Retrieve feed data as XML element
     *
     * @return SimpleXMLElement
     */
    public function getFeedData($urlParams = array())
    {
        $curl = new Varien_Http_Adapter_Curl();
        $curl->setConfig(array(
            'timeout'   => 3
        ));
		$body = '';
		if (is_array($urlParams) && count($urlParams) > 0) {
			$body = Mage::helper('csmarketplace')->addParams('',$urlParams);
			$body = trim($body,'?');
		}

		try {
			$curl->write(Zend_Http_Client::POST, $this->getFeedUrl(), '1.1',array(),$body);
			$data = $curl->read();
			if ($data === false) {
				return false;
			}
			$data = preg_split('/^\r?$/m', $data, 2);
			$data = trim($data[1]);
		
			$curl->close();

            $xml  = new SimpleXMLElement($data);
        } catch (Exception $e) {
			return false;
        }

        return $xml;
    }

    public function getFeedXml()
    {
        try {
            $data = $this->getFeedData();
            $xml  = new SimpleXMLElement($data);
        }
        catch (Exception $e) {
            $xml  = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?>');
        }

        return $xml;
    }
}
