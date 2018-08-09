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
 * CsMarketplace Product edit block
 *
 * @category   Ced
 * @package    Ced_CsMarketplace
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Block_Vproducts_Edit extends Ced_CsMarketplace_Block_Vendor_Abstract
{

	
	/**
	 * Get set collection of products
	 *
	 */
	public function __construct(){
		parent::__construct();
		$vendorId=$this->getVendorId();
		$id=$this->getRequest()->getParam('id');
		$status=0;
		if($id){
			$vproductsCollection =Mage::getModel('csmarketplace/vproducts')->getVendorProducts('',$vendorId,$id);
			$status=$vproductsCollection->getFirstItem()->getCheckStatus();
		}
		$storeId=0;
		if($this->getRequest()->getParam('store')){
			$websiteId=Mage::getModel('core/store')->load($this->getRequest()->getParam('store'))->getWebsiteId();
			if($websiteId){
				if(in_array($websiteId,Mage::getModel('csmarketplace/vproducts')->getAllowedWebsiteIds())){
					$storeId=$this->getRequest()->getParam('store');
				}
			}
		}
		$product = Mage::getModel('catalog/product')->setStoreId($storeId);
		if($id){
			$product = $product->load($id);
		}
		$this->setVproduct($product);
		Mage::register('current_product',$product);		
		$this->setCheckStatus($status);
	}
		
	public function getDeleteUrl($product)
	{
		return $this->getUrl('*/*/delete', array('id' => $product->getId(),'_secure'=>true,'_nosid'=>true));
	}
	
	public function getBackUrl()
	{
		return $this->getUrl('*/*/index',array('_secure'=>true,'_nosid'=>true));
	}
	
	public function getDownloadableProductLinks($_product){
		return Mage::getModel('downloadable/product_type')->getLinks($_product);
	}
	
	public function getDownloadableHasLinks($_product){
		return Mage::getModel('downloadable/product_type')->hasLinks($_product);
	}
	
	public function getDownloadableProductSamples($_product){
		return Mage::getModel('downloadable/product_type')->getSamples($_product);
	}
	
	public function getDownloadableHasSamples($_product){
		return Mage::getModel('downloadable/product_type')->hasSamples($_product);
	}
	
}
