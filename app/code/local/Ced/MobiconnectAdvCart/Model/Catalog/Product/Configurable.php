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
class Ced_MobiconnectAdvCart_Model_Catalog_Product_Configurable extends Mage_Catalog_Model_Config {

public function getConfigAttribute($product){

		$productAttributeOptions = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
		$attributeOptions = array();
		foreach ($productAttributeOptions as $productAttribute) {
    		foreach ($productAttribute['values'] as $attribute) {
        				$attributeOptions[$productAttribute['attribute_code']][$attribute['value_index']] = $attribute['store_label'];
    		}
		}
		//var_dump($attributeOptions);die;
		return $attributeOptions;
}
public function getProductAvailability($product){
	
		$configattributes=$this->getConfigAttribute($product);
		
		$ids = Mage::getModel('catalog/product_type_configurable')
            ->getChildrenIds($product->getId());
            //var_dump($ids);die;
			$avabl=array();
			
            foreach ($configattributes as $key=>$val){
				$attcode=array_keys($configattributes);
				
                foreach ($val as $attid=>$attname){ 
                    $confiproduct=Mage::getModel('catalog/product')->getCollection()->addIdFilter($ids);
         			
                    $simpleproducts=$confiproduct->addAttributeToFilter($key,$attid)->addAttributeToSelect('*');
                     $simpleproducts->joinField('is_in_stock',
                'cataloginventory/stock_item',
                'is_in_stock',
                'product_id=entity_id',
                'is_in_stock=1',
                '{{table}}.stock_id=1',
                'left'); 
                  if(count($simpleproducts->getData())>1){
                    if (($keyids = array_search($key, $attcode)) !== false) { 
                    	
    					unset($attcode[$keyids]);
					}
					}
					foreach ($simpleproducts as $data){
                        foreach ($attcode as $av => $attr) {
                            if(trim($key)==trim($attr))
                       	    continue;
                            $avabl[$key][$attname][$attr][]=$data->getData($attr);
                        }
                    } 
                                   
                }
                        
               
            }
           return $avabl;
         

}
public function optionPrice($product){

	//$product=Mage::getModel('catalog/product')->load('403');
	$baseCurrency = Mage::app ()->getStore ()->getDefaultCurrencyCode ();
	$currency_symbol = Mage::app ()->getLocale ()->currency ( $baseCurrency )->getSymbol ();
	$optionsprice = array();
	// Get any super_attribute settings we need
	$productAttributeOptions = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
	 //var_dump($productAttributeOptions);die;
	$basePrice = Mage::helper('tax')
	->getPrice($product, $product->getFinalPrice());
	foreach ($productAttributeOptions as $key => $val) {
		foreach ($val['values'] as $key => $value) {
			$pricingtype='fixed';
			if($value['is_percent'])
			{
				$pricingtype='percent';
			}

			$pricingvalue=$value['pricing_value'];
			if($pricingvalue==''){
				$pricingvalue='no-value';
			}
			//echo $pricingvalue.'<hr>';
			$optionlabel=$val['attribute_code'];
			//if(!$pricingtype){
			// $newpricingvalue = $pricingvalue+$basePrice;

			$optionsprice[$optionlabel][$value['label']]['pricing-type']=$pricingtype;
			$optionsprice[$optionlabel][$value['label']]['option-price']=$pricingvalue;
			//$optionsprice[$optionlabel][$value['label']]['currency_symbol']=$currency_symbol;
			// }
			// else{
			//    $newpricingvalue = (float)$pricingvalue * $basePrice / 100;
			//   $newpricingvalue+=$basePrice;
			//   $optionsprice[$optionlabel][$value['label']]['new-price']=$currency_symbol.$newpricingvalue;
			//}
	}

}
return $optionsprice;
}
public function getConfigAttributeIds($product){
	$productAttributeOptions = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
	//var_dump($productAttributeOptions);die;
	$attributeOptionIds= array();
	foreach ($productAttributeOptions as $key=>$val) {
		$attributeOptionIds[]= array(
				$val['attribute_code']=>$val['attribute_id'],
		);
	}
	//var_dump($attributeOptionIds);die;
	return $attributeOptionIds;
}
}