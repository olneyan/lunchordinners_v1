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
class Ced_MobiconnectAdvCart_Model_Catalog_Product_RelatedProduct extends Mage_Catalog_Model_Config {

	public function getRelatedProduct($relatedProductIds){
		foreach ($relatedProductIds as $id){
				$relatedProduct=Mage::getModel('catalog/product')->load($id);
				$related_product_id  = $relatedProduct->getId ();
				$related_product_name = $relatedProduct->getName ();
				$related_description = $relatedProduct->getDescription ();
				$related_short_description= $relatedProduct->getShortDescription ();
				$related_type [] = $relatedProduct->getTypeId ();
				if ($relatedProduct->isSaleable ()) {
					$related_stock  = 'IN STOCK';
				} else {
					$related_stock = 'OUT OF STOCK';
				}
				if ($relatedProduct->getSpecialPrice () != '' && $relatedProduct->getSpecialPrice () < $relatedProduct->getPrice ()) {
					$related_price  = array (
							'special_price' => array (
									Mage::helper ( 'tax' )->getPrice ( $relatedProduct, $relatedProduct->getSpecialPrice () )
							),
							'regular_price' => array (
									Mage::helper ( 'tax' )->getPrice ( $relatedProduct, $relatedProduct->getPrice () )
							)
					);
				} else {
					$related_price = array (
							'special_price' => array (
									null
							),
							'regular_price' => array (
									Mage::helper ( 'tax' )->getPrice ( $relatedProduct, $relatedProduct->getPrice () )
							)
					);
				}
				$baseimg = Mage::helper ( 'catalog/image' )->init ( $relatedProduct, 'image' )->__toString ();
				
				$relatedProductsInfo[]=array(
						'related_product-id' => $related_product_id,
						'related_product-name' => $related_product_name,
						'related_description' => $related_description,
						'related_short-description' => $related_short_description,
						'related_type' => $related_type,
						'related_stock' => $related_stock,
						'related_price' => $related_price,
						'related_product_image'=>$baseimg,
						'related_currency_symbol' => $currency_symbol,
						'related_status' => 'enabled'
						
				);
			}
			return $relatedProductsInfo;
	}
}