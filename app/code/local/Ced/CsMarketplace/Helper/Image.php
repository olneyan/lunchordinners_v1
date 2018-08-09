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

	public function UploadImage() {
		$data = array();
		$vendorPost=Mage::app()->getRequest()->getParam('vendor');
		if(isset($_FILES['vendor']['name'])) {
			foreach($_FILES['vendor']['name'] as $fieldName=>$value) {
				if(isset($_FILES['vendor']['name'][$fieldName]) && (file_exists($_FILES['vendor']['tmp_name'][$fieldName]))) {
					
					$uploader = new Varien_File_Uploader("vendor[{$fieldName}]");
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything
				 	
				 
					$uploader->setAllowRenameFiles(false);
				 
					$uploader->setFilesDispersion(false);
				   
					$path = Mage::getBaseDir('media') . DS .'ced' . DS . 'csmaketplace' . DS . 'vendor' . DS;
					$extension=pathinfo($_FILES['vendor']['name'][$fieldName], PATHINFO_EXTENSION);
					$fileName = $fieldName.time().'.'.$extension;
					
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