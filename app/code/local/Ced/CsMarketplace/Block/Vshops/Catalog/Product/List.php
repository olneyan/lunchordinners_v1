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
 * CsMarketplace profile product list Blpck
 *
 * @category   Ced
 * @package    Ced_CsMarketplace
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Block_Vshops_Catalog_Product_List extends Mage_Core_Block_Template
{
	
	public function getProductCollection()
    {
    	
      
            $vendorId=Mage::registry('current_vendor')->getId();
            $collection = Mage::getModel('csmarketplace/vproducts')->getVendorProducts(Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS,$vendorId,0,-1);
            $products=array();
            $statusarray=array();
            foreach($collection as $data){
                array_push($products,$data->getProductId());
            }
            $productcollection = Mage::getModel('catalog/product')->getCollection()
                                ->addAttributeToSelect(Mage::getSingleton('catalog/config')
                                ->getProductAttributes())
                                ->addAttributeToFilter('entity_id',array('in'=>$products))
                                ->addStoreFilter(Mage::app()->getStore()->getId())
                                ->addAttributeToFilter('status',Mage_Catalog_Model_Product_Status::STATUS_ENABLED)->setOrder('sort_order',"ASC");
           // print_r($productcollection->getData()); die("kdfvg");
            $cat_id = $this->getRequest()->getParam('cat-fil');
            $productcollection->joinField(
            		'category_id', 'catalog/category_product', 'category_id',
            		'product_id = entity_id', null, 'left'
            )
            ->addAttributeToSelect('*');
           
            if(isset($cat_id)) {
            	$productcollection
            	->addAttributeToFilter('category_id', array(
            			array('finset', array('in'=>explode(',', $cat_id)))
            	));
            	$productcollection->getSelect()->group('e.entity_id');
            }
            
            $this->_productCollection = $productcollection;
       //    print_r($productcollection->getData()); die("kfg");

        return $this->_productCollection;
    }
    
    
    public function ggetMode()
    {
    	return 'grid';
    }
   
	
}
