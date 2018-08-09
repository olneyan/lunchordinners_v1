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
  * @package   Ced_Mobiconnect
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobiconnect_Adminhtml_IndexController extends Ced_Mobiconnect_Controller_Adminhtml_Action {
	/**
	 * init layout and set active for current menu
	 *
	 * @return Ced_Mobileconnect_Adminhtml_ConnectorController
	 */
	protected function _initAction() {
		$this->loadLayout ()->_setActiveMenu ( 'mobiconnect/set_time' );
		return $this;
	}
	protected function _initCategory($getRootInstead = false) {
		$this->_title ( $this->__ ( 'Catalog' ) )->_title ( $this->__ ( 'Categories' ) )->_title ( $this->__ ( 'Manage Categories' ) );
		
		$categoryId = ( int ) $this->getRequest ()->getParam ( 'id', false );
		$storeId = ( int ) $this->getRequest ()->getParam ( 'store' );
		$category = Mage::getModel ( 'catalog/category' );
		$category->setStoreId ( $storeId );
		
		if ($categoryId) {
			$category->load ( $categoryId );
			if ($storeId) {
				$rootId = Mage::app ()->getStore ( $storeId )->getRootCategoryId ();
				if (! in_array ( $rootId, $category->getPathIds () )) {
					// load root category instead wrong one
					if ($getRootInstead) {
						$category->load ( $rootId );
					} else {
						$this->_redirect ( '*/*/', array (
								'_current' => true,
								'id' => null 
						) );
						return false;
					}
				}
			}
		}
		
		if ($activeTabId = ( string ) $this->getRequest ()->getParam ( 'active_tab_id' )) {
			Mage::getSingleton ( 'admin/session' )->setActiveTabId ( $activeTabId );
		}
		
		Mage::register ( 'category', $category );
		Mage::register ( 'current_category', $category );
		Mage::getSingleton ( 'cms/wysiwyg_config' )->setStoreId ( $this->getRequest ()->getParam ( 'store' ) );
		return $category;
	}
	/**
	 * index action
	 */
	public function indexAction() { 
		$this->_initAction ();
		$this->renderLayout ();
	}
	
	/**
	 * widget action
	 */
	public function widgetAction() {
		$this->_initAction ();
		$this->renderLayout ();
	}
	
	/**
	 * view and edit item action
	 */
	public function editAction() {
		$testId = $this->getRequest ()->getParam ( 'id' );
		$testModel = Mage::getModel ( 'mobiconnect/banner' )->load ( $testId );
		
		Mage::register ( 'banners_data', $testModel );
		
		$this->loadLayout ();
		
		$this->_setActiveMenu ( 'mobiconnect/items' );
		$this->getLayout ()->getBlock ( 'head' )->setCanLoadExtJs ( true );
		$this->_addContent ( $this->getLayout ()->createBlock ( 'mobiconnect/adminhtml_mobiconnect_edit' ) )->_addLeft ( $this->getLayout ()->createBlock ( 'mobiconnect/adminhtml_mobiconnect_edit_tabs' ) );
		$this->renderLayout ();
	}
	public function newAction() {
		$this->_forward ( 'edit' );
	}
	public function categoriesJsonAction() {
		if ($this->getRequest ()->getParam ( 'expand_all' )) {
			Mage::getSingleton ( 'admin/session' )->setIsTreeWasExpanded ( true );
		} else {
			Mage::getSingleton ( 'admin/session' )->setIsTreeWasExpanded ( false );
		}
		if ($categoryId = ( int ) $this->getRequest ()->getPost ( 'id' )) {
			$this->getRequest ()->setParam ( 'id', $categoryId );
			
			if (! $category = $this->_initCategory ()) {
				return;
			}
			$this->getResponse ()->setBody ( $this->getLayout ()->createBlock ( 'adminhtml/catalog_category_tree' )->getTreeJson ( $category ) );
		}
	}
	/**
	 * save item action
	 */
	public function saveAction() { 
		$model = Mage::getModel ( 'mobiconnect/banner' );
		
		if ($data = $this->getRequest ()->getPost ()) {
			$data['product_id']=end(explode('/',$data['product_id']));
			$data['category_id']=end(explode('/',$data['category_id']));
			$data['image']=end(explode('/',$data['image']['value']));
			try{
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
				
				Mage::dispatchEvent('demo_save_before', array('files'=>$_FILES,'posted_value'=>$data,'uploader'=>$uploader));
				$uploader->setAllowRenameFiles ( true );
				$uploader->setFilesDispersion ( false );
				$uploader->save ( $path, $imgName );
				// this way the name is saved in DB
				$data ['image'] = $imgName;
			}
			if (isset ( $data ['store'] )) {
				if (in_array ( '0', $data ['store'] )) {
					$data ['store'] = '0';
				} else {
					$data ['store'] = implode ( ",", $data ['store'] );
				}
			}
			
			
				
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
				
    }
	public function chooseProductsAction() {
		$request = $this->getRequest ();
		$block = $this->getLayout ()->createBlock ( 'mobiconnect/adminhtml_mobiconnect_edit_tab_products', 'promo_widget_chooser_sku', array (
				'js_form_object' => $request->getParam ( 'form' ) 
		) );
		
		if ($block) {
			$this->getResponse ()->setBody ( $block->toHtml () );
		}
	}
	public function chooseCategoriesAction() {
		$request = $this->getRequest ();
		$id = $request->getParam ( 'selected', array () );
		$block = $this->getLayout ()->createBlock ( 'mobiconnect/adminhtml_mobiconnect_edit_tab_categories', 'maincontent_category', array (
				'js_form_object' => $request->getParam ( 'form' ) 
		) )->setCategoryIds ( $id );
		if ($block) {
			$this->getResponse ()->setBody ( $block->toHtml () );
		}
	}
	
	/**
	 * delete action
	 */
	public function deleteAction() {
		if ($this->getRequest ()->getParam ( 'id' ) > 0) {
			try {
				$model = Mage::getModel ( 'mobiconnect/banner' );
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
	public function massVisibiltyAction() {
		if ($this->getRequest ()->isPost ()) {
			$status = $this->getRequest ()->getPost ( 'status' );
			
			$bannerids = $this->getRequest ()->getPost ( 'banner_ids', array () );
			foreach ( $bannerids as $id ) {
				$banner = Mage::getModel ( 'mobiconnect/banner' )->load ( $id );
				if ($banner->getId ()) {
					try {
						$banner->setData ( 'status', $status );
						$banner->save ();
						$this->_getSession ()->addSuccess ( Mage::helper ( 'mobiconnect' )->__ ( 'Status successfully updated' ) );
					} catch ( Exception $e ) {
						$this->_getSession ()->addError ( $e->getMessage () );
					}
				} else {
					$this->_getSession ()->addError ( Mage::helper ( 'mobiconnect' )->__ ( 'Invalid id supplied, %s', $id ) );
				}
			}
		}
		$this->_redirectReferer ();
	}
	public function massDeleteAction() {
		if ($this->getRequest ()->isPost ()) { // ass action
			$bannerIds = $this->getRequest ()->getPost ( 'banner_ids', array () );
			foreach ( $bannerIds as $id ) {
				$banner = Mage::getModel ( 'mobiconnect/banner' )->load ( $id );
				if ($banner->getId ()) {
					try {
						$banner->delete ();
						$this->_getSession ()->addSuccess ( Mage::helper ( 'mobiconnect' )->__ ( 'Banner %s successfully deleted.', $id ) );
					} catch ( Exception $e ) {
						$this->_getSession ()->addError ( $e->getMessage () );
					}
				} else {
					$this->_getSession ()->addError ( Mage::helper ( 'mobiconnect' )->__ ( 'Invalid id supplied, %s', $id ) );
				}
			}
		}
		
		$this->_redirectReferer ();
		return;
	}
	public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
               $this->getLayout()->createBlock('mobiconnect/adminhtml_mobiconnect_grid')->toHtml()
        );
    }
}
?>
