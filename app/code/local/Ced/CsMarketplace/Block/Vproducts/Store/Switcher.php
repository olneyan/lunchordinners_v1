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
 
class Ced_CsMarketplace_Block_Vproducts_Store_Switcher extends Mage_Core_Block_Template
{
    /**
     * @var array
     */
    protected $_storeIds;

    protected $_storeVarName = 'store';

    /**
     * @var bool
     */
    protected $_hasDefaultOption = true;

    public function __construct()
    {
        $this->setTemplate('csmarketplace/vproducts/store/switcher.phtml');
        $this->setUseConfirm(true);
        $this->setUseAjax(true);
        $this->setDefaultStoreName($this->__('Default Values'));
    }

    /**
     * Deprecated
     */
    public function getWebsiteCollection()
    {
        $collection = Mage::getModel('core/website')->getResourceCollection();

        $websiteIds = $this->getWebsiteIds();
        if (!is_null($websiteIds)) {
            $collection->addIdFilter($this->getWebsiteIds());
        }

        return $collection->load();
    }

    /**
     * Get websites
     *
     * @return array
     */
    public function getWebsites()
    {
        $websites = Mage::app()->getWebsites();
		$websitesIds = Mage::getStoreConfig('ced_vproducts/general/website');
		if(strlen($websitesIds) > 0) {
			$websitesIds = explode(',',$websitesIds);
		} else {
			$websitesIds = array();
		}
        /* $websiteIds=Mage::getModel('csmarketplace/vproducts')->getAllowedWebsiteIds(); */
    	if(Mage::registry('current_product')!=null){
    		$product=Mage::registry('current_product');
    		$prowebsites=$product->getWebsiteIds();
    		if(is_array($prowebsites) && count($prowebsites)){
				if(!in_array(0,$websitesIds)){
					$websiteIds = array_unique(array_intersect($websiteIds,$prowebsites));
				} else {
					$websiteIds = $prowebsites;
				}   		
    		} 
    	}
        if ($websitesIds) {
            foreach ($websites as $websiteId => $website) {
                if (!in_array($websiteId, $websitesIds)) {
                    unset($websites[$websiteId]);
                }
            }
        }
        return $websites;
    }

    /**
     * Deprecated
     */
    public function getGroupCollection($website)
    {
        if (!$website instanceof Mage_Core_Model_Website) {
            $website = Mage::getModel('core/website')->load($website);
        }
        return $website->getGroupCollection();
    }

    /**
     * Get store groups for specified website
     *
     * @param Mage_Core_Model_Website $website
     * @return array
     */
    public function getStoreGroups($website)
    {
        if (!$website instanceof Mage_Core_Model_Website) {
            $website = Mage::app()->getWebsite($website);
        }
        return $website->getGroups();
    }

    /**
     * Deprecated
     */
    public function getStoreCollection($group)
    {
        if (!$group instanceof Mage_Core_Model_Store_Group) {
            $group = Mage::getModel('core/store_group')->load($group);
        }
        $stores = $group->getStoreCollection();
        $_storeIds = $this->getStoreIds();
        if (!empty($_storeIds)) {
            $stores->addIdFilter($_storeIds);
        }
        return $stores;
    }

    /**
     * Get store views for specified store group
     *
     * @param Mage_Core_Model_Store_Group $group
     * @return array
     */
    public function getStores($group)
    {
        if (!$group instanceof Mage_Core_Model_Store_Group) {
            $group = Mage::app()->getGroup($group);
        }
        $stores = $group->getStores();
        if ($storeIds = $this->getStoreIds()) {
            foreach ($stores as $storeId => $store) {
                if (!in_array($storeId, $storeIds)) {
                    unset($stores[$storeId]);
                }
            }
        }
        return $stores;
    }

    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }
        return $this->getUrl('*/*/*', array('_current' => true, $this->_storeVarName => null,'_secure'=>true,'_nosid'=>true));
    }

    public function setStoreVarName($varName)
    {
        $this->_storeVarName = $varName;
        return $this;
    }

    public function getStoreId()
    {
        return $this->getRequest()->getParam($this->_storeVarName);
    }

    public function setStoreIds($storeIds)
    {
        $this->_storeIds = $storeIds;
        return $this;
    }

    public function getStoreIds()
    {
        return $this->_storeIds;
    }

    public function isShow()
    {
        return true;
    }

    protected function _toHtml()
    {
       	return parent::_toHtml();
    }

    /**
     * Set/Get whether the switcher should show default option
     *
     * @param bool $hasDefaultOption
     * @return bool
     */
    public function hasDefaultOption($hasDefaultOption = null)
    {
        if (null !== $hasDefaultOption) {
            $this->_hasDefaultOption = $hasDefaultOption;
        }
        return $this->_hasDefaultOption;
    }
}
