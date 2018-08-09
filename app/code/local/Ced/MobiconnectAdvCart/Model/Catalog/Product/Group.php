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
  * @category  Ced
  * @package   Ced_MobiconnectAdvCart
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_MobiconnectAdvCart_Model_Catalog_Product_Group extends Mage_Catalog_Model_Config {

	public function getGroupedProduct($_product) {
		// $id = 439; //product id
		// $_product = Mage::getModel('catalog/product')->load($id);
		$baseCurrency = Mage::app ()->getStore ()->getDefaultCurrencyCode ();
		$currency_symbol = Mage::app ()->getLocale ()->currency ( $baseCurrency )->getSymbol ();
		$_associatedProducts = $_product->getTypeInstance ( true )->getAssociatedProducts ( $_product );
		$_hasAssociatedProducts = count ( $_associatedProducts ) > 0;
		$product_counter = 0;
		foreach ( $_associatedProducts as $group_product ) {
			$group_product_id = $group_product->getId ();
			$group_product_name = $group_product->getName ();
			$group_description = $group_product->getDescription ();
			$group_short_description = $group_product->getShortDescription ();
			$group_type = $group_product->getTypeId ();
			if ($group_product->isSaleable ()) {
				$group_stock = 'IN STOCK';
			} else {
				$group_stock = 'OUT OF STOCK';
			}
			if ($group_product->getSpecialPrice () != '' && $group_product->getSpecialPrice () < $group_product->getPrice ()) {
				$group_price = array (
						'special_price' =>$currency_symbol.Mage::helper ( 'tax' )->getPrice ( $group_product, $group_product->getSpecialPrice () ),
						
						'regular_price' =>$currency_symbol.Mage::helper ( 'tax' )->getPrice ( $group_product, $group_product->getPrice () ) 
				)
				;
			} else {
				$group_price = array (
						'special_price' => null,
						
						'regular_price' => $currency_symbol.Mage::helper ( 'tax' )->getPrice ( $group_product, $group_product->getPrice () ) 
				)
				;
			}
			$groupedproduct [] = array (
					'group-prod-img' => Mage::helper ( 'catalog/image' )->init ( $group_product, 'image' )->__toString (),
					// 'product-image' => $mediaImage,
					'group-product-id' => $group_product_id,
					'group-product-name' => $group_product_name,
					'group-description' => $group_description,
					'group-short-description' => $group_short_description,
					'griup_product_type' => $group_type,
					'stock' => $group_stock,
					'price' => $group_price 
			// 'currency_symbol'=>$currency_symbol,
			// 'cms-content' => Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_WEB, true ) . 'mobiconnect/category/cmsContent/id/' . $cmsContent,
			// 'status' => 'enabled'
						)

			;
		}
		return $groupedproduct;
	}
}