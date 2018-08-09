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
 * CsMarketplace Product List block
 *
 * @category   Ced
 * @package    Ced_CsMarketplace
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */
 
class Ced_CsMarketplace_Block_Vproducts extends Ced_CsMarketplace_Block_Vendor_Abstract
{
	protected $_filtercollection;
	
	/**
	 * Get set collection of products
	 *
	 */
	public function __construct(){
		parent::__construct();
		$vendorId=$this->getVendorId();
		$collection = Mage::getModel('csmarketplace/vproducts')->getVendorProducts('',$vendorId,0);
		if(count($collection)>0){
			$products=array();
			$statusarray=array();
			foreach($collection as $data){
				array_push($products,$data->getProductId());
				$statusarray[$data->getProductId()]=$data->getCheckStatus();
			}
			$currentStore=Mage::app()->getStore()->getId();
			Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
			$productcollection =Mage::getModel('catalog/product')->getCollection();
			$storeId=0;
			if($this->getRequest()->getParam('store')){
				$websiteId=Mage::getModel('core/store')->load($this->getRequest()->getParam('store'))->getWebsiteId();
				if($websiteId){
					if(in_array($websiteId,Mage::getModel('csmarketplace/vproducts')->getAllowedWebsiteIds())){
						$storeId=$this->getRequest()->getParam('store');
					}
				}
			}
			
			$productcollection->addAttributeToSelect('*')->addAttributeToFilter('entity_id',array('in'=>$products))->addAttributeToSort('entity_id', 'DESC');
			
			if($storeId){
				$productcollection->addStoreFilter($storeId);
				$productcollection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner', $storeId);
				$productcollection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $storeId);
				$productcollection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $storeId);
				$productcollection->joinAttribute('thumbnail', 'catalog_product/thumbnail', 'entity_id', null, 'left', $storeId);
			}
			
			if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
				$productcollection->joinField('qty',
						'cataloginventory/stock_item',
						'qty',
						'product_id=entity_id',
						'{{table}}.stock_id=1',
						'left');
			}
			$productcollection->joinField('check_status','csmarketplace/vproducts', 'check_status','product_id=entity_id',null,'left');
			
			$params = Mage::getSingleton('core/session')->getData('product_filter');
			if(isset($params) && is_array($params) && count($params)>0){
				foreach($params as $field=>$value){
					if($field=='store'||$field=='store_switcher'||$field=="__SID")
						continue;
					if(is_array($value)){
						if(isset($value['from']) && urldecode($value['from'])!=""){
							$from = urldecode($value['from']);
							$productcollection->addAttributeToFilter($field, array('gteq'=>$from));
						}
						if(isset($value['to'])  && urldecode($value['to'])!=""){
							$to = urldecode($value['to']);
							$productcollection->addAttributeToFilter($field, array('lteq'=>$to));
						}
					}else if(urldecode($value)!=""){
						$productcollection->addAttributeToFilter($field, array("like"=>'%'.urldecode($value).'%'));
					}
				}
			}
			Mage::app()->setCurrentStore($currentStore);
			$productcollection->setStoreId($storeId);
			if($productcollection->getSize()>0){
				$this->_filtercollection=$productcollection;
				$this->setVproducts($this->_filtercollection);
			}
		}
		
	}
	
	/**
	 * prepare product list layout
	 *@return Ced_CsMarketplace_Block_Vproducts
	 */
	protected function _prepareLayout() {
		parent::_prepareLayout();
		if($this->_filtercollection){
			if($this->_filtercollection->getSize()>0){
			$pager = $this->getLayout()->createBlock('csmarketplace/html_pager', 'custom.pager');
			$pager->setAvailableLimit(array(5=>5,10=>10,20=>20,'all'=>'all'));
			$pager->setCollection($this->_filtercollection);
			$this->setChild('pager', $pager);
			}
		}
		return $this;
	}
	
	/**
	 * Get pager HTML
	 *
	 */
	public function getPagerHtml() {
		return $this->getChildHtml('pager');
	}
	
	/**
	 * get Edit product url
	 *
	 */
	public function getEditUrl($product)
	{
		return $this->getUrl('*/*/edit', array('_secure'=>true,'_nosid'=>true,'id' => $product->getId(),'type'=>$product->getTypeId(),'store'=>$this->getRequest()->getParam('store',0)));
	}
	
	/**
	 * get Product Type url
	 *
	 */
	public function getProductTypeUrl()
	{
		return $this->getUrl('*/*/new/',array('_secure'=>true,'_nosid'=>true));
	}
	
	/**
	 * get Delete url
	 *
	 */
	public function getDeleteUrl($product)
	{
		return $this->getUrl('*/*/delete', array('_secure'=>true,'_nosid'=>true,'id' => $product->getId()));
	}
	
	/**
	 * back Link url
	 *
	 */
	public function getBackUrl()
	{
		return $this->getUrl('*/*/index',array('_secure'=>true,'_nosid'=>true));
	}
	
	
}
