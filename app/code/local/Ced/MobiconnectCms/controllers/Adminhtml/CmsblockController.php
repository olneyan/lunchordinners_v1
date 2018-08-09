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
  * @package   Ced_MobiconnectCms
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_MobiconnectCms_Adminhtml_CmsblockController extends Ced_Mobiconnect_Controller_Adminhtml_Action {
	/**
	 * init layout and set active for current menu
	 *
	 * @return Ced_Mobiconnect_Adminhtml_CmsblockController
	 */
	protected function _initAction() {
		$this->loadLayout ()->_setActiveMenu ( 'mobiconnect/set_time' );
		return $this;
	}
	/**
	 * index action
	 */
	public function indexAction() { 
		$this->_initAction ();
		$this->renderLayout ();
	}
	public function editAction() {
		$testId = $this->getRequest ()->getParam ( 'id' );
		$testModel = Mage::getModel ( 'mobiconnectcms/cmsblock' )->load ( $testId );
		Mage::register ( 'cms_data', $testModel );
		$this->loadLayout ();
		$this->_setActiveMenu ( 'mobiconnect/items' );
		$this->getLayout ()->getBlock ( 'head' )->setCanLoadExtJs ( true );
		$this->_addContent ( $this->getLayout ()->createBlock ( 'mobiconnectcms/adminhtml_cmsblock_edit' ) )->_addLeft ( $this->getLayout ()->createBlock ( 'mobiconnectcms/adminhtml_cmsblock_edit_tabs' ) );
		$this->renderLayout ();
	}
	public function newAction() {
		$this->_forward ( 'edit' );
	}
	
	/**
	 * save item action
	 */
	public function saveAction() {
		if ($data = $this->getRequest ()->getParams ()) {
			
			if (isset ( $_FILES ['fileimage'] ['name'] ) && $_FILES ['fileimage'] ['name'] != '') {
				
				$imgName = $_FILES ['fileimage'] ['name'];
				$imgPath = Mage::getBaseDir ( 'media' ) . DS . "Mobiconnect" . DS . "cms" . DS;
				$uploader = new Varien_File_Uploader ( 'fileimage' );
				$uploader->setAllowedExtensions ( array (
						'jpg',
						'JPG',
						'jpeg',
						'gif',
						'GIF',
						'png',
						'PNG' 
				) );
				$uploader->setAllowRenameFiles ( true );
				$uploader->setFilesDispersion ( false );
				$uploader->save ( $imgPath, $imgName );
				$data ['image'] = $imgName;
			}
			if (isset ( $data ['image'] ['delete'] ) && $data ['image'] ['delete'] == 1) {
				Mage::helper ( 'mobiconnectcms' )->deleteFile ( $data ['image'] ['value'] );
				$data ['image'] = '';
			}
			if(isset($data['store']) && count($data['store'])>0){
					$data['store']=implode(',',$data['store']);
			}
			$model = Mage::getModel ( 'mobiconnectcms/cmsblock' );
			$model->setData ( $data )->setId ( $this->getRequest ()->getParam ( 'id' ) );
			
			try {

				$model->save ();
				Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'mobiconnectcms' )->__ ( 'Block was successfully saved' ) );
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
		Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'mobiconnectcms' )->__ ( 'Unable to find item to save' ) );
		$this->_redirect ( '*/*/' );
	}
	public function deleteAction() {
		if ($this->getRequest ()->getParam ( 'id' ) > 0) {
			try {
				$model = Mage::getModel ( 'mobiconnectcms/cmsblock' );
				$model->setId ( $this->getRequest ()->getParam ( 'id' ) )->delete ();
				Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Item was successfully deleted' ) );
				$this->_redirect ( '*/*/' );
			} catch ( Exception $e ) {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
				$this->_redirect ( '*/*/edit', array (
						'id' => $this->getRequest ()->getParam ( 'id' ) 
				) );
			}
		}
		$this->_redirect ( '*/*/' );
	}
}
?>