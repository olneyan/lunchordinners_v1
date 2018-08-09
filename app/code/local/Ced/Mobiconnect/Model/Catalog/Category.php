<?php
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License (AFL 3.0)
  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/afl-3.0.php
  *
  * @category    Ced
  * @package     Ced_Mobiconnect
  * @author   CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
class Ced_Mobiconnect_Model_Catalog_Category extends Mage_Core_Model_Abstract {
	protected $_width = 135;
	protected $_height = 135;
	protected $_limit;
	public function _construct() {
		parent::_construct ();
		$this->_limit = Mage::helper ( 'mobiconnect' )->getProductLimit ();
	}
	/**
	 *
	 * @param unknown $id        	
	 * @return return level 2 categories
	 */
	public function getCategories($id) {
		if (! $id) { 
			$categories = Mage::getModel ( 'catalog/category' )->getCollection ()->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'level', '2' )->addAttributeToFilter ( 'is_active', '1' )->addAttributeToFilter('include_in_menu','1')
				;
			if ($categories) {
				$category = $this->getMainCategories ( $categories );

				$data = array (
						'data' => array (
								'categories' => $category 
						),
						'status' => 'success' 
				);
				
				return $data;
			}
			else{
				$data = array (
						'data' => array (
								'categories' => 'You dont have any categories' 
						),
						'status' => 'success' 
				);
				
				return $data;
			}
		} else {

			$categories = Mage::getModel ( 'catalog/category' )->load ( $id );
			$subCategories = $categories->getChildrenCategories ();
			
			$category = $this->getSubCategories ( $subCategories, $id );
			// var_dump($categories->getData());die;
			return $category;
		}
	}
	/**
	 *
	 * @param unknown $categories        	
	 * @return cateogry info array
	 */
	public function getMainCategories($categories) {
		foreach ( $categories as $cat ) {
			
			if (! $cat->hasChildren ()) {
				$categdata [] = array (
						'category_id' => $cat->getId (),
						'category_name' => $cat->getName (),
						'category_image' => $cat->getImageUrl (),
						'product_count'=>'0',
						'has_child' => 'NO' 
				);
			} else {
				$categdata [] = array (
						'category_id' => $cat->getId (),
						'category_name' => $cat->getName (),
						'category_image' => $cat->getImageUrl (),
						'product_count'=>'0',
						'has_child' => 'YES' 
				);
			}
		}
		return $categdata;
	}
	/**
	 *
	 * @param unknown $categories        	
	 * @param unknown $id        	
	 * @return subcategories info array
	 */
	public function getSubCategories($categories, $id) {
		$baseCurrency = Mage::app ()->getStore ()->getDefaultCurrencyCode ();
		$currency_symbol = Mage::app ()->getLocale ()->currency ( $baseCurrency )->getSymbol ();

		$categdata = array();
		foreach ( $categories as $cat ) {
			
			if (! $cat->hasChildren ()) {
				$categdata [] = array (
						'category_id' => $cat->getId (),
						'category_name' => $cat->getName (),
						'category_image' => $cat->getImageUrl (),
						'product_count'=>'0',
						'has_child' => 'NO' 
				);
			} else {
				$categdata [] = array (
						'category_id' => $cat->getId (),
						'category_name' => $cat->getName (),
						'category_image' => $cat->getImageUrl (),
						'product_count'=>'0',
						'has_child' => 'YES' 
				);
			}
		}
		$category = Mage::getModel ( 'catalog/category' )->load ( $id );
		
		$products = $category->getProductCollection ()
							->addAttributeToSelect ( '*' )
							->addAttributeToFilter('type_id','simple')
							->addAttributeToSort('position','ASC')
							->setOrder ( 'price', 'ASC' )
							->setPageSize ( $this->_limit );
	
		Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $products );
			Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInSearchFilterToCollection ( $products );
		
		$productData = array();
		foreach ( $products as $_product ) {
			
			$productData [] = array (
					'product_id' => $_product->getId (),
					'product_name' => $_product->getName (),
					'product_image' => Mage::helper ( 'catalog/image' )->init ( $_product, 'small_image' )->__toString (),
					'product_price' => Mage::helper('core')->currency(Mage::helper ( 'tax' )->getPrice ( $_product, $_product->getFinalPrice()),true,false),
					
			);
		}
	

		$data = array (
				'data' => array (
						'subcategory' => $categdata,
						'product' => $productData,
						'status' => 'success' 
				) 
		);
		if(count($productData)>0)
	{
		$data['data']['havingproduct']=array('IN');
	}
	else{
		$data['data']['havingproduct']=array('OUT');
	}
		return $data;
	}
}

?>
