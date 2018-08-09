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

/**
 * Vendor Product model
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Model_Vshop extends Ced_CsMarketplace_Model_Abstract {
	
	const ENABLED = 1;
	const DISABLED = 2;	
	
	/**
	 * Initialize vproducts model
	 */
	protected function _construct() {
		$this->_init ( 'csmarketplace/vshop' );
	}
	
	public function saveShopStatus(array $vendorIds,$shop_disable)
	{
		$vendors=array();
		if(count($vendorIds)>0){
			foreach($vendorIds as $vendorId){
				$vendor_id_tmp=Mage::helper('csmarketplace')->getTableKey('vendor_id');
				$model = Mage::getModel('csmarketplace/vshop')->loadByField(array($vendor_id_tmp),array($vendorId));
				if($model && $model->getId()){
					if($model->getShopDisable()!=$shop_disable){
						$model->setShopDisable($shop_disable)->save();
						$vendors[]=$model->getVendorId();
						$vendor = Mage::getModel('csmarketplace/vendor')->load($model->getVendorId());
						if($vendor && $vendor->getId())
							Mage::helper('csmarketplace/mail')->sendShopEmail($model->getShopDisable(),'',$vendor);
					}
				}
				else{
					$vshop = Mage::getModel('csmarketplace/vshop');
					$vshop->setVendorId($vendorId)->setShopDisable($shop_disable)->save();
					$vendors[]=$vendorId;
					$vendor = Mage::getModel('csmarketplace/vendor')->load($model->getVendorId());
					if($vendor && $vendor->getId())
						Mage::helper('csmarketplace/mail')->sendShopEmail($model->getShopDisable(),'',$vendor);
				}
			}
		}
		if(count($vendors)>0)
			$this->changeProductsStatus($vendorIds,$shop_disable);
		return count($vendors);
	}
	
	/**
	 *Change Products Status (Hide/show products from frontend on vendor approve/disapprove)
	 *@params array $vendorIds,int $status
	 *@return boolean
	 */
	public function changeProductsStatus($vendorIds,$status){
		if(is_array($vendorIds)){
			foreach ($vendorIds as $vendorId){
				$collection = Mage::getModel('csmarketplace/vproducts')->getVendorProducts('',$vendorId,0,-1);
				foreach ($collection as $row){
					$productId=$row->getProductId();
					if($status==self::DISABLED){
						$statusCollection=Mage::getModel('csmarketplace/vproducts_status')->getCollection()->addFieldtoFilter('product_id',$productId);
						foreach ($statusCollection as $statusrow){
							Mage::getModel('catalog/product_status')->updateProductStatus($productId,$statusrow->getStoreId(),Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
						}
					}
					else if($status==self::ENABLED){
						$statusCollection=Mage::getModel('csmarketplace/vproducts_status')->getCollection()->addFieldtoFilter('product_id',$productId);
							foreach ($statusCollection as $statusrow){
								Mage::getModel('catalog/product_status')->updateProductStatus($productId,$statusrow->getStoreId(),$statusrow->getStatus());
							}
					}
				}
			}
		}
	}
	
}

?>