<?php 
class Ced_CsMarketplace_Model_Vshop_Layer extends Mage_Catalog_Model_Layer
{
	public function getProductCollection()
	{
		if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
			$collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
		} else {
			$shop_url = Mage::app()->getRequest()->getParam('shop_url','');
			if($shop_url!=''){
				$collection = Mage::getModel('catalog/product')->getCollection();
			}else{
				$collection = $this->getCurrentCategory()->getProductCollection();
			}
			$this->prepareProductCollection($collection);
			$this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
		}
	
		return $collection;
	}
}
?>