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
 
class Ced_CsMarketplace_Helper_Image extends Mage_Core_Helper_Abstract
{
	const ALLOWED_IMAGE_TYPE_PATH = 'ced_csvattribute/vattribute/allowed_image_type';
	const ALLOWED_FILE_TYPE_PATH = 'ced_csvattribute/vattribute/allowed_file_type';
	
	public function getAllowedImageTypes($storeId = null) {
		if($storeId == null) $storeId = Mage::app()->getStore()->getId();
		$types = explode(',',Mage::getStoreConfig(self::ALLOWED_IMAGE_TYPE_PATH,$storeId));
		foreach($types as &$type){
			$type = trim($type);
		}
		return $types;
	}
	
	public function getAllowedFileTypes($storeId = null) {
		if($storeId == null) $storeId = Mage::app()->getStore()->getId();
		$types = explode(',',Mage::getStoreConfig(self::ALLOWED_FILE_TYPE_PATH,$storeId));
		foreach($types as &$type){
			$type = trim($type);
		}
		return $types;
	}
	
	public function UploadImage($image = '') {
		$data = array();
		$vendorPost=Mage::app()->getRequest()->getParam('vendor');
		if(isset($_FILES['vendor']['name'])) {

			foreach($_FILES['vendor']['name'] as $fieldName=>$value) {
				if(isset($_FILES['vendor']['tmp_name'][$fieldName]) && is_array($_FILES['vendor']['tmp_name'][$fieldName])){
					foreach($_FILES['vendor']['name'][$fieldName] as $key=>$fileData){
						$fileName = '';
						$allowedType = array();
						$keyName = '';
						if(isset($fileData['multifile'])) {
							$keyName = 'multifile';
							$fileName = $fileData['multifile'];
							$allowedType = $this->getAllowedFileTypes();
						} elseif (isset($fileData['multiimage'])) {
							$keyName = 'multiimage';
							$fileName = $fileData['multiimage'];
							$allowedType = $this->getAllowedImageTypes();
						}
						
						if(strlen($fileName) == 0 || count($allowedType) == 0 || strlen($keyName) == 0) continue;
						
						$uploader = new Varien_File_Uploader("vendor['{$fieldName}']['{$key}'][{$keyName}]");
						
						$uploader->setAllowedExtensions($allowedType); 
						
						$uploader->setAllowRenameFiles(false);
					 
						$uploader->setFilesDispersion(false);
					   
						$path = Mage::getBaseDir('media') . DS .'ced' . DS . 'csmarketplace' . DS . 'vendor' . DS;
						$extension=pathinfo($fileName, PATHINFO_EXTENSION);
						$fileName = $fieldName.$key.$extension;
						
						$uploader->save($path, $fileName);
						
						$data[$fieldName][] = 'ced/csmarketplace/vendor/'.$fileName;
					}
				} elseif (isset($_FILES['vendor']['name'][$fieldName]) && (file_exists($_FILES['vendor']['tmp_name'][$fieldName]))) {
					
					$uploader = new Varien_File_Uploader("vendor[{$fieldName}]");
					$uploader->setAllowedExtensions($this->getAllowedImageTypes()); // or pdf or anything
				 	
				 
					$uploader->setAllowRenameFiles(false);
				 
					$uploader->setFilesDispersion(false);
				   
					$path = Mage::getBaseDir('media') . DS .'ced' . DS . 'csmaketplace' . DS . 'vendor' . DS;
					$extension=pathinfo($_FILES['vendor']['name'][$fieldName], PATHINFO_EXTENSION);
					$fileName = $fieldName.time().$extension;
					
					$uploader->save($path, $fileName);
				 	
					$data[$fieldName] = 'ced/csmaketplace/vendor/'.$fileName;
				} else {
					
					if(isset($vendorPost[$fieldName]['delete']) && $vendorPost[$fieldName]['delete'] == 1) {
						
						$data[$fieldName] = '';
						$imageName = explode('/',$vendorPost[$fieldName]['value']);
						$imageName = $imageName[count($imageName)-1];
						unlink(Mage::getBaseDir('media') . DS .'ced' . DS . 'csmaketplace' . DS . 'vendor' . DS . $imageName);
					} else {
						unset($data[$fieldName]);
					}
					
				}
			}
		}
		return $data;
	}
	
}