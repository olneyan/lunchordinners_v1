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
 * @category    Ced;
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_CsMarketplace_Helper_Vproducts_Image extends Mage_Core_Helper_Abstract {

	/**
	 * Save images to media gallery and set product default image 
	 * @params Mage_Catalog_Model_Product $product, array $data
	 */
	public function saveImages($product, $data) {
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		$defaultimage = '';
		//$savedDefaultImage='';
		$productid=$product->getId();
		$targetDir = Mage::getBaseDir ( 'media' ) . DS . 'ced' . DS . 'csmaketplace' . DS . 'vproducts' . DS . $productid . DS;
		$productModel = Mage::getModel('catalog/product')->load($productid);
		//$savedDefaultImage=$productModel->getImage();
		if($productModel && $productModel->getId()){
			if (isset ( $_FILES['images'] ) && count ( $_FILES['images'] ) > 0 && 
					is_array($_FILES["images"]["tmp_name"]) && count($_FILES["images"]["tmp_name"])>0) {
				foreach($_FILES["images"]["tmp_name"]  as $key => $value){
					if (isset($_FILES['images']['name'][$key]) && file_exists($_FILES['images']['tmp_name'][$key] )) {
						$uploader = new Varien_File_Uploader ( "images[{$key}]" );
						$uploader->setAllowRenameFiles ( false );
						$uploader->setFilesDispersion ( false );
						$uploader->setAllowedExtensions ( array (
								'jpg',
								'jpeg',
								'gif',
								'png',
						) );
						$image = md5 ( $_FILES['images']['tmp_name'][$key] ).$_FILES['images']['name'][$key];
						try {
							if($result=$uploader->save ( $targetDir, $image )){
								$fetchTarget = $targetDir .$result['file'];
								$productModel->addImageToMediaGallery ( $fetchTarget, array (
										'image',
										'small_image',
										'thumbnail'
								), true, false );
								if (isset ( $data ['defaultimage'] ) && $data ['defaultimage']!='') {
									if ($data ['defaultimage']=="images[{$key}]"){
										$defaultimage = $result['file'];
									}
								}
							}
						}
						catch ( Exception $e ) {
							echo $e->getMessage ();
						}
					}
				}
			}
			$productModel->save();
			// set default image
			if (isset ( $data ['defaultimage'] ) && $data ['defaultimage']!='') {
				//case when no new uploads
				if($defaultimage==''){
						$defaultimage=$data ['defaultimage'];
				}
				if($defaultimage!==''){
					$mediaGallery = $productModel->getMediaGallery();
					//if there are images
					if (isset($mediaGallery['images'])){
						//loop through the images
						foreach ($mediaGallery['images'] as $image){
							if (strpos($image['file'],$defaultimage) !== false){
								Mage::getSingleton('catalog/product_action')->updateAttributes(array($productid), array('image'=>$image['file'],'small_image'=>$image['file'],'thumbnail'=>$image['file']), $product->getStoreId());
								break;
							}
						}
					}
				}
			}
			else{
				Mage::getSingleton('catalog/product_action')->updateAttributes(array($productid), array('image'=>'','small_image'=>'','thumbnail'=>''),$product->getStoreId());
			}
		}
	}
}
