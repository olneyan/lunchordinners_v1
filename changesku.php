<?php
require_once 'app/Mage.php';
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
if (($handle = fopen("skus.csv", "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$oldsku = $data[0];
		$newsku = $data[1];
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $oldsku);
		if($product) { 
			$product->setSku($newsku);
			$product->save();
			echo  $product->getName() . ' SKU updated';
			echo "\n";
			}
	}
}
?>
