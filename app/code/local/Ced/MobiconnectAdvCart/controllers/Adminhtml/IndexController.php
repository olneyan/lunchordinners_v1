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
/**
* Override Controller of Mobiconnect for advance product system 
**/
require_once Mage::getModuleDir('controllers', 'Ced_Mobiconnect').DS.'Adminhtml'.DS.'IndexController.php';
class Ced_MobiconnectAdvCart_Adminhtml_IndexController extends Ced_Mobiconnect_Adminhtml_IndexController {
   	/**
	* Override addtocart of Mobiconnect if advance product system enable
	**/
	
   	public function saveAction() { 
		$model = Mage::getModel ( 'mobiconnect/banner' );
		
		if ($data = $this->getRequest ()->getPost ()) {
			$data['product_id']=end(explode('/',$data['product_id']));
			$data['category_id']=end(explode('/',$data['category_id']));
			$data['image']=end(explode('/',$data['image']['value']));
			if (isset ( $_FILES ['image'] ['name'] ) && $_FILES ['image'] ['name'] != '') {
				
				$imgName = $_FILES ['image'] ['name'];
				$imgName = str_replace ( ' ', '_', $imgName );
				$path = Mage::getBaseDir ( 'media' ) . "/Banners" . DS . "images" . DS;
				$uploader = new Varien_File_Uploader ( 'image' );
				$uploader->setAllowedExtensions ( array (
						'jpg',
						'JPG',
						'jpeg',
						'gif',
						'GIF',
						'png',
						'PNG' 
				) );
				
				//$uploader->addValidateCallback('size', $this, 'validateMaxSize');
				//die;
				$uploader->setAllowRenameFiles ( true );
				$uploader->setFilesDispersion ( false );
				/* destination file path */
				$destFile = $path . $imgName;
				$imgName = $model->getNewFileName ( $destFile );
				$uploader->save ( $path, $imgName );
				// We set media as the upload dir
				$uploader->save ( $path, $_FILES ['image'] ['name'] );
				// this way the name is saved in DB
				$data ['image'] = $_FILES ['image'] ['name'];
				
				// Save Image Tag in DB for GRID View
				$imgPath = Mage::getBaseUrl ( 'media' ) . "Banners/images/" . $_FILES ['image'] ['name'];
				$data ['filethumbgrid'] = '<img src="' . $imgPath . '" border="0" width="80" height="80" />';
			}
			if (isset ( $data ['store'] )) {
				if (in_array ( '0', $data ['store'] )) {
					$data ['store'] = '0';
				} else {
					$data ['store'] = implode ( ",", $data ['store'] );
				}
			}
			
			try {
				
				$id = ( int ) $this->getRequest ()->getParam ( 'id' );
				if ($id != null) {
					$model = Mage::getModel ( 'mobiconnect/banner' );
					$model->setData ( $data )->setId ( $this->getRequest ()->getParam ( 'id' ) );
					if ($model->getCreatedTime == NULL || $model->getUpdateTime () == NULL) {
						$model->setCreatedTime ( now () )->setUpdateTime ( now () );
					} else {
						$model->setUpdateTime ( now () );
					}
					$model->save ();
				} else {
					$models = Mage::getModel ( 'mobiconnect/banner' );
					$models->setData ( $data );
					$models->setCreatedTime ( now () )->setUpdateTime ( now () );
					$models->save ();
				}
				Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'mobiconnect' )->__ ( 'Banner was successfully saved' ) );
				Mage::getSingleton ( 'adminhtml/session' )->setFormData ( false );
				if ($this->getRequest ()->getParam ( 'back' )) {
					$this->_redirect ( '*/*/edit', array (
							'id' => $model->getId () 
					) );
					return;
				}
				$this->_redirect ( '*/*/' );
				return;
			} catch ( Exception $e ) {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
				Mage::getSingleton ( 'adminhtml/session' )->setFormData ( $data );
				$this->_redirect ( '*/*/edit', array (
						'id' => $this->getRequest ()->getParam ( 'id' ) 
				) );
				return;
			}
		}
		Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'mobiconnect' )->__ ( 'Unable to find Banner to save' ) );
		$this->_redirect ( '*/*/' );
	}
	public function validateMaxSize($filePath)
    {
        		$image_info = getimagesize($filePath);
				echo $image_width = $image_info[0];
				echo $image_height = $image_info[1];
				//die('lk');
    }
}
?>
