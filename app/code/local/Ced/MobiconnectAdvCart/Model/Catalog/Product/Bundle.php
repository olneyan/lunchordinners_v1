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
class Ced_MobiconnectAdvCart_Model_Catalog_Product_Bundle extends Mage_Catalog_Model_Config {

	public function getBundleData($id) {
		$baseCurrency = Mage::app ()->getStore ()->getDefaultCurrencyCode ();
		$currency_symbol = Mage::app ()->getLocale ()->currency ( $baseCurrency )->getSymbol ();
		$productid = $id;
		$store_id = Mage::app ()->getStore ()->getStoreId ();
		
		$product = Mage::getModel ( 'catalog/product' )->setStoreId ( $store_id )->load ( $productid );
		$options = Mage::getModel ( 'bundle/option' )->getResourceCollection ()->setProductIdFilter ( $productid )->setPositionOrder ();
		$options->joinValues ( $store_id );
		
		$selections = $product->getTypeInstance ( true )->getSelectionsCollection ( $product->getTypeInstance ( true )->getOptionsIds ( $product ), $product );
		
		foreach ( $options->getItems () as $option ) {
			
			$option_id = $option->getId ();
			
			foreach ( $selections as $selection ) {
				
				if ($option_id == $selection->getOptionId ()) {
					if ($product->getPriceType () == '0') {
						// echo $calcPrice;die;
						if ($selection->isSaleable ()) {
							// if($selection->getSpecialPrice()==''){
							
							$productSpecialpercentage = $product->getSpecialPrice ();
							$calcPrice = Mage::helper ( 'tax' )->getPrice ( $selection, $selection->getPrice () );
							$calcPrice = $calcPrice * ($productSpecialpercentage / 100);
							if (! isset ( $productSpecialpercentage )) {
								$calcPrice = Mage::helper ( 'tax' )->getPrice ( $selection, $selection->getPrice () );
							}
							// echo $calcPrice.'<hr>';
							if ($option->getType () != 'multi' && $option->getType () != 'checkbox') {
								$bundledata ['option name'] [$option->getTitle ()] ['selection_name'] [$selection->getName () . '+' . $calcPrice] = array (
										'default_qty' => $selection->getSelectionQty (),
										'user_can_change_qty' => $selection->getData ( 'selection_can_change_qty' ),
										'selection_is_default' => $selection->getData ( 'is_default' ),
										'selection_id' => $selection->getSelectionId (),
										'selection_price' => $calcPrice,
										'in_stock' => $selection->isSaleable () 
								);
							} else {
								$bundledata ['option name'] [$option->getTitle ()] ['selection_name'] [$selection->getName () . '+' . $calcPrice] = array (
										'default_qty' => $selection->getSelectionQty (),
										'user_can_change_qty' => '0',
										'selection_is_default' => $selection->getData ( 'is_default' ),
										'selection_id' => $selection->getSelectionId (),
										'selection_price' => $calcPrice,
										// 'selection_special_price'=> Mage::helper('tax')->getPrice($selection, $selection->getSpecialPrice()),
										'in_stock' => $selection->isSaleable () 
								);
							}
						}
					} else {
						if ($selection->isSaleable ()) {
							// if($selection->getSpecialPrice()==''){
							$productSpecialpercentage = $product->getSpecialPrice ();
							$calcPrice = Mage::helper ( 'tax' )->getPrice ( $selection, $selection->getSelectionPriceValue () );
							$calcPrice = $calcPrice * ($productSpecialpercentage / 100);
							if (! isset ( $productSpecialpercentage )) {
								$calcPrice = Mage::helper ( 'tax' )->getPrice ( $selection, $selection->getSelectionPriceValue () );
							}
							if ($option->getType () != 'multi' && $option->getType () != 'checkbox') {
								$bundledata ['option name'] [$option->getTitle ()] ['selection_name'] [$selection->getName () . '+' . $calcPrice] = array (
										'default_qty' => $selection->getSelectionQty (),
										'user_can_change_qty' => $selection->getData ( 'selection_can_change_qty' ),
										'selection_is_default' => $selection->getData ( 'is_default' ),
										'selection_id' => $selection->getSelectionId (),
										'selection_price' => $calcPrice,
										'in_stock' => $selection->isSaleable () 
								);
							} else {
								$bundledata ['option name'] [$option->getTitle ()] ['selection_name'] [$selection->getName () . '+' . $calcPrice] = array (
										'default_qty' => $selection->getSelectionQty (),
										'user_can_change_qty' => '0',
										'selection_is_default' => $selection->getData ( 'is_default' ),
										'selection_id' => $selection->getSelectionId (),
										'selection_price' => $calcPrice,
										// 'selection_special_price'=> Mage::helper('tax')->getPrice($selection, $selection->getSpecialPrice()),
										'in_stock' => $selection->isSaleable () 
								);
							}
							
							/*
							 * else{ if($option->getType()!='multi' && $option->getType()!='checkbox'){ $bundledata['option name'][$option->getTitle()]['selection_name'][$selection->getName().'+'.Mage::helper('tax')->getPrice($selection, $selection->getSpecialPrice())]=array( 'default_qty'=>$selection->getSelectionQty(), 'user_can_change_qty'=>$selection->getData('selection_can_change_qty'), 'selection_is_default'=>$selection->getData('is_default'), 'selection_id'=>$selection->getId(), 'selection_price'=>Mage::helper('tax')->getPrice($selection, $selection->getPrice()), 'selection_special_price'=> Mage::helper('tax')->getPrice($selection, $selection->getSpecialPrice()), 'in_stock'=>$selection->isSaleable () ); } else{ $bundledata['option name'][$option->getTitle()]['selection_name'][$selection->getName().'+'.Mage::helper('tax')->getPrice($selection, $selection->getSpecialPrice())]=array( 'default_qty'=>$selection->getSelectionQty(), 'user_can_change_qty'=>'0', 'selection_is_default'=>$selection->getData('is_default'), 'selection_id'=>$selection->getId(), 'selection_price'=>Mage::helper('tax')->getPrice($selection, $selection->getPrice()), 'selection_special_price'=> Mage::helper('tax')->getPrice($selection, $selection->getSpecialPrice()), 'in_stock'=>$selection->isSaleable () ); } } $selection_special_price='';
							 */
						}
						$bundledata ['regular-percent'] = $product->getSpecialPrice ();
					}
				}
			}
			$bundledata ['option name'] [$option->getTitle ()] ['required'] = $option->getRequired ();
			$bundledata ['option name'] [$option->getTitle ()] ['type'] = $option->getType ();
			$bundledata ['option name'] [$option->getTitle ()] ['id'] = $option->getId ();
		}
		$min = Mage::getModel ( 'bundle/product_price' )->getTotalPrices ( $product, 'min', 0 );
		$min = Mage::helper ( 'tax' )->getPrice ( $product, $min );
		$max = Mage::getModel ( 'bundle/product_price' )->getTotalPrices ( $product, 'max', 0 );
		$max = Mage::helper ( 'tax' )->getPrice ( $product, $max );
		$bundledata ['from_price'] = $min;
		$bundledata ['to_price'] = $max;
		return $bundledata;
	}
}