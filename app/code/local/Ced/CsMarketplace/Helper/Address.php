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
 * CsMarketplace address helper
 *
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Helper_Address extends Mage_Core_Helper_Abstract
{
    /**
     * VAT Validation parameters XML paths
     */
    const XML_PATH_VIV_DISABLE_AUTO_ASSIGN_DEFAULT = 'csmarketplace/create_account/viv_disable_auto_group_assign_default';
    const XML_PATH_VIV_ON_EACH_TRANSACTION         = 'csmarketplace/create_account/viv_on_each_transaction';
    const XML_PATH_VAT_VALIDATION_ENABLED          = 'csmarketplace/create_account/auto_group_assign';
    const XML_PATH_VIV_TAX_CALCULATION_ADDRESS_TYPE = 'csmarketplace/create_account/tax_calculation_address_type';
    const XML_PATH_VAT_FRONTEND_VISIBILITY = 'csmarketplace/create_account/vat_frontend_visibility';

    /**
     * Array of CsMarketplace Address Attributes
     *
     * @var array
     */
    protected $_attributes;

    /**
     * CsMarketplace address config node per website
     *
     * @var array
     */
    protected $_config          = array();

    /**
     * CsMarketplace Number of Lines in a Street Address per website
     *
     * @var array
     */
    protected $_streetLines     = array();
    protected $_formatTemplate  = array();

    /**
     * Addresses url
     */
    public function getBookUrl()
    {

    }

    public function getEditUrl()
    {

    }

    public function getDeleteUrl()
    {

    }

    public function getCreateUrl()
    {

    }

    public function getRenderer($renderer)
    {
        if(is_string($renderer) && $className = Mage::getConfig()->getBlockClassName($renderer)) {
            return new $className();
        } else {
            return $renderer;
        }
    }

    /**
     * Return csmarketplace address config value by key and store
     *
     * @param string $key
     * @param Mage_Core_Model_Store|int|string $store
     * @return string|null
     */
    public function getConfig($key, $store = null)
    {
        $websiteId = Mage::app()->getStore($store)->getWebsiteId();

        if (!isset($this->_config[$websiteId])) {
            $this->_config[$websiteId] = Mage::getStoreConfig('csmarketplace/address', $store);
        }
        return isset($this->_config[$websiteId][$key]) ? (string)$this->_config[$websiteId][$key] : null;
    }

    /**
     * Return Number of Lines in a Street Address for store
     *
     * @param Mage_Core_Model_Store|int|string $store
     * @return int
     */
    public function getStreetLines($store = null)
    {
        $websiteId = Mage::app()->getStore($store)->getWebsiteId();
        if (!isset($this->_streetLines[$websiteId])) {
            $attribute = Mage::getSingleton('eav/config')->getAttribute('csmarketplace_address', 'street');
            $lines = (int)$attribute->getMultilineCount();
            if($lines <= 0) {
                $lines = 2;
            }
            $this->_streetLines[$websiteId] = min(4, $lines);
        }

        return $this->_streetLines[$websiteId];
    }

    public function getFormat($code)
    {
        $format = Mage::getSingleton('csmarketplace/address_config')->getFormatByCode($code);
        return $format->getRenderer() ? $format->getRenderer()->getFormat() : '';
    }

    /**
     * Determine if specified address config value can be shown
     *
     * @param string $key
     * @return bool
     */
    public function canShowConfig($key)
    {
        return (bool)$this->getConfig($key);
    }

    /**
     * Return array of CsMarketplace Address Attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $this->_attributes = array();
            /* @var $config Mage_Eav_Model_Config */
            $config = Mage::getSingleton('eav/config');
            foreach ($config->getEntityAttributeCodes('csmarketplace_address') as $attributeCode) {
                $this->_attributes[$attributeCode] = $config->getAttribute('csmarketplace_address', $attributeCode);
            }
        }
        return $this->_attributes;
    }

    /**
     * Get string with frontend validation classes for attribute
     *
     * @param string $attributeCode
     * @return string
     */
    public function getAttributeValidationClass($attributeCode)
    {
        /** @var $attribute Ced_CsMarketplace_Model_Attribute */
        $attribute = isset($this->_attributes[$attributeCode]) ? $this->_attributes[$attributeCode]
            : Mage::getSingleton('eav/config')->getAttribute('csmarketplace_address', $attributeCode);
        $class = $attribute ? $attribute->getFrontend()->getClass() : '';

        if (in_array($attributeCode, array('firstname', 'middlename', 'lastname', 'prefix', 'suffix', 'taxvat'))) {
            if ($class && !$attribute->getIsVisible()) {
                $class = ''; // address attribute is not visible thus its validation rules are not applied
            }

            /** @var $csmarketplaceAttribute Ced_CsMarketplace_Model_Attribute */
            $csmarketplaceAttribute = Mage::getSingleton('eav/config')->getAttribute('csmarketplace', $attributeCode);
            $class .= $csmarketplaceAttribute && $csmarketplaceAttribute->getIsVisible()
                ? $csmarketplaceAttribute->getFrontend()->getClass() : '';
            $class = implode(' ', array_unique(array_filter(explode(' ', $class))));
        }

        return $class;
    }

    /**
     * Convert streets array to new street lines count
     * Examples of use:
     *  $origStreets = array('street1', 'street2', 'street3', 'street4')
     *  $toCount = 3
     *  Result:
     *   array('street1 street2', 'street3', 'street4')
     *  $toCount = 2
     *  Result:
     *   array('street1 street2', 'street3 street4')
     *
     * @param array $origStreets
     * @param int   $toCount
     * @return array
     */
    public function convertStreetLines($origStreets, $toCount)
    {
        $lines = array();
        if (!empty($origStreets) && $toCount > 0) {
            $countArgs = (int)floor(count($origStreets)/$toCount);
            $modulo = count($origStreets) % $toCount;
            $offset = 0;
            $neededLinesCount = 0;
            for ($i = 0; $i < $toCount; $i++) {
                $offset += $neededLinesCount;
                $neededLinesCount = $countArgs;
                if ($modulo > 0) {
                    ++$neededLinesCount;
                    --$modulo;
                }
                $values = array_slice($origStreets, $offset, $neededLinesCount);
                if (is_array($values)) {
                    $lines[] = implode(' ', $values);
                }
            }
        }

        return $lines;
    }

    /**
     * Check whether VAT ID validation is enabled
     *
     * @param Mage_Core_Model_Store|string|int $store
     * @return bool
     */
    public function isVatValidationEnabled($store = null)
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_VAT_VALIDATION_ENABLED, $store);
    }

    /**
     * Retrieve disable auto group assign default value
     *
     * @return bool
     */
    public function getDisableAutoGroupAssignDefaultValue()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_VIV_DISABLE_AUTO_ASSIGN_DEFAULT);
    }

    /**
     * Retrieve 'validate on each transaction' value
     *
     * @param Mage_Core_Model_Store|string|int $store
     * @return bool
     */
    public function getValidateOnEachTransaction($store = null)
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_VIV_ON_EACH_TRANSACTION, $store);
    }

    /**
     * Retrieve csmarketplace address type on which tax calculation must be based
     *
     * @param Mage_Core_Model_Store|string|int|null $store
     * @return string
     */
    public function getTaxCalculationAddressType($store = null)
    {
        return (string)Mage::getStoreConfig(self::XML_PATH_VIV_TAX_CALCULATION_ADDRESS_TYPE, $store);
    }

    /**
     * Check if VAT ID address attribute has to be shown on frontend (on CsMarketplace Address management forms)
     *
     * @return boolean
     */
    public function isVatAttributeVisible()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_VAT_FRONTEND_VISIBILITY);
    }
}
