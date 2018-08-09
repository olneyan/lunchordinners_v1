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
 
class Ced_CsMarketplace_Block_Vproducts_Edit_Websites extends Ced_CsMarketplace_Block_Vproducts_Store_Switcher
{
    protected $_storeFromHtml;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('csmarketplace/vproducts/edit/websites.phtml');
    }

    /**
     * Retrieve edited product model instance
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    public function getStoreId()
    {
        return $this->getProduct()->getStoreId();
    }

    public function getProductId()
    {
        return $this->getProduct()->getId();
    }

    public function getWebsites()
    {
        return $this->getProduct()->getWebsiteIds();
    }

    public function hasWebsite($websiteId)
    {
    	$websiteIds = $this->getProduct()->getWebsiteIds();
    	if(!$this->getProduct()->getId() && Mage::getSingleton('customer/session')->getFormError()==true){
    		$productformdata=Mage::getSingleton('customer/session')->getProductFormData();
    		$websiteIds=isset($productformdata['product']['website_ids'])?$productformdata['product']['website_ids']:array();
    	}
        return in_array($websiteId,$websiteIds);
    }

    /**
     * Check websites block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->getProduct()->getWebsitesReadonly();
    }

    public function getStoreName($storeId)
    {
        return Mage::app()->getStore($storeId)->getName();
    }
}

