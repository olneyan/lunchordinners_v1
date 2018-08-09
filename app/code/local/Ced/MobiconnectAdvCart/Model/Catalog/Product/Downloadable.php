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
class Ced_MobiconnectAdvCart_Model_Catalog_Product_Downloadable extends Mage_Catalog_Model_Config {

	public function downloadable($id){
	
		$product=Mage::getModel('catalog/product')->load($id);
	
		$_linksPurchasedSeparately = $product->getLinksPurchasedSeparately();
	
		$hasLinks= $product->getTypeInstance(true)->hasLinks($product);
	
		$_links= $product->getTypeInstance(true)->getLinks($product);
	
		$_isRequired=$product->getTypeInstance(true)->getLinkSelectionRequired($product);
		if ($product->getLinksTitle()) {
			$linktitle= $product->getLinksTitle();
		}
		else{
			$linktitle= Mage::getStoreConfig(Mage_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
		}
		 
	
		foreach ($_links as $_link) {
			$price = $_link->getPrice();
			$linkname=array();
			$linkname=explode('/',$_link->getLinkFile());
			
			$samplelinkfile=$linkname[count($linkname)-1];
			$links[]=array(
					'link-id'=>$_link->getId(),
					'link-title'=>$_link->getTitle(),
					'sample_link'=>$samplelinkfile,
					'link-price'=>Mage::helper('tax')->getPrice($_link->getProduct(), $price),
					'link-url'=>Mage::getUrl('downloadable/download/linkSample', array('link_id' => $_link->getId()))
			);
		}
		$_samples = $product->getTypeInstance(true)->getSamples($product);
		if ($product->getSamplesTitle()) {
			$samplestitle= $product->getSamplesTitle();
		}
		else{
			$samplestitle= Mage::getStoreConfig(Mage_Downloadable_Model_Sample::XML_PATH_SAMPLES_TITLE);
		}
		
		foreach ($_samples as $_sample){
			$sample[]=array(
					'sample-id'=>$_sample->getId(),
					'sample-title'=>$_sample->getTitle(),
					'sample-url'=>Mage::getUrl('downloadable/download/sample', array('sample_id' => $_sample->getId()))
			);
		}
		$downloadable_links=array(
				'is_selection_required'=>$_isRequired,
				'links_purchased_separately'=>$_linksPurchasedSeparately,
				'links'=>$links,
				'link_title'=>$linktitle,
				'samples_title'=>$samplestitle,
				'samples'=>$sample,
		);
	
		return $downloadable_links;
	
	}
}