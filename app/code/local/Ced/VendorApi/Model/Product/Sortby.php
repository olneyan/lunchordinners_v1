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
  * @category    Ced
  * @package     VendorApi
  * @author      CedCommerce Core Team <connect@cedcommerce.com>
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://cedcommerce.com/license-agreement.txt
  */

class Ced_VendorApi_Model_Product_Sortby extends Mage_Catalog_Model_Config
{

	public function getAttributeUsedForSortByArray() { 
		foreach ( $this->getAttributesUsedForSortBy () as $attribute ) {
			$options [$attribute->getAttributeCode ()] = $attribute->getAttributeCode ();
		}
		foreach ( $options as $key => $val ) {
			if ($val == 'name') {
				$sort [] = array (
						"name:A to Z" => array (
								'asc' 
						),
						"name:Z to A" => array (
								'desc' 
						) 
				);
			}
			if ($val == 'price') {
				$sort [] = array (
						"price:Low To High" => array (
								'asc' 
						),
						"price:High To Low" => array (
								'desc' 
						) 
				);
			}
			if ($val != 'price' && $val != 'name') {
				$sort [] = array (
						$val . ":" . ' asc' => array (
								'asc' 
						),
						$val . ":" . ' desc' => array (
								'desc' 
						) 
				);
			}
		}
		return $sort;
	}
	function getCategoryFilter(){
		$rootCatId = Mage::app()->getStore()->getRootCategoryId();
   		$catlistHtml = $this->getTreeCategories('2', false);
   		//var_dump($catlistHtml);die;
   		return $catlistHtml;  
	}	
	function getTreeCategories($parentId, $isChild){
	    $allCats = Mage::getModel('catalog/category')->getCollection()
	                ->addAttributeToSelect('*')
	                ->addAttributeToFilter('is_active','1')
	                ->addAttributeToFilter('include_in_menu','1')
	                ->addAttributeToFilter('parent_id',array('eq' => $parentId))
	                ->addAttributeToSort('position', 'asc');
	               
	    $class = ($isChild) ? "sub-cat-list" : "cat-list";
	   	$cat=array();
	   	$count='0';
	    foreach($allCats as $category)
	    {
	    	$cat[$count] =array('main_category'=>$category->getId().'#'.$category->getName()." (".Mage::getModel('csmarketplace/vproducts')->getProductCount($category->getId(),Ced_CsMarketplace_Model_Vproducts::AREA_FRONTEND).")");
	    	$subcats = $category->getChildren();
        	if($subcats != ''){ 
        		$cat[$count]['sub_categories']=$this->getTreeCategories($category->getId(), true);
       		} 
       		++$count;  
	    }
	   return $cat;
	}
}
	
	
