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
class Ced_MobiconnectAdvCart_Adminhtml_LayoutController extends Ced_Mobiconnect_Controller_Adminhtml_Action {
	/**
	 * init layout and set active for current menu
	 *
	 * @return 
	 */
	protected function _initAction() {
		$this->loadLayout ()->_setActiveMenu ( 'mobiconnect/set_time' );
		return $this;
	}
	public function _initProduct() {
		$productId = ( int ) $this->getRequest ()->getParam ( 'id' );
		$product = Mage::getModel ( 'mobiconnectadvcart/layouts' )->getCollection ();
		
		Mage::register ( 'product', $product );
		Mage::register ( 'current_product', $product );
		Mage::getSingleton ( 'cms/wysiwyg_config' )->setStoreId ( $this->getRequest ()->getParam ( 'store' ) );
		return $product;
	}
	
	/**
	 * widget action
	 */
	public function indexAction() {
		$this->_initAction ();
		$this->renderLayout ();
	}
	/**
	 * view and edit item action
	 */
	public function editAction() {
		
		$id = $this->getRequest ()->getParam ( 'id' );
		$widget = Mage::getModel ( 'mobiconnectadvcart/layouts' )->load ( $id );
		Mage::register ( 'widget_data', $widget );
		$this->loadLayout ();
		$this->_setActiveMenu ( 'mobiconnect/items' );
		$this->getLayout ()->getBlock ( 'head' )->setCanLoadExtJs ( true );
		$this->_addContent ( $this->getLayout ()->createBlock ( 'mobiconnectadvcart/adminhtml_widget_edit' ) )->_addLeft ( $this->getLayout ()->createBlock ( 'mobiconnectadvcart/adminhtml_widget_edit_tabs' ) );
		$this->renderLayout ();
	}
	public function newAction() {
		$this->_forward ( 'edit' );
	}
	public function bannergridAction() {
		$this->_initProduct ();
		$this->loadLayout ();
		$this->getLayout ()->getBlock ( 'widget.edit.tab.bannergrid' )->setBanner ( $this->getRequest ()->getPost ( 'banner', null ) );
		$this->renderLayout ();
	}
	public function bannerfilterAction() {
		$this->_initProduct ();
		$this->loadLayout ();
		$this->getLayout ()->getBlock ( 'widget.edit.tab.bannergrid' )->setProductsRelated ( $this->getRequest ()->getPost ( 'products_crosssell', null ) );
		$this->renderLayout ();
	}
	/**
	 * save action widget info
	 */
	public function saveAction() {
		if ($data = $this->getRequest ()->getPost ()) {
			 
			$model = Mage::getModel ( 'mobiconnectadvcart/layouts' );
			if(isset($data['home_banner']) && count($data['home_banner'])>0){
				$paramvalues=explode('&',$data['home_banner']);
				$imageids=array();
				foreach ($paramvalues as $key => $value) {
					$imageid=explode('=',$value);
					$imageids[]=$imageid['0'];
				}
				if(count($imageids)>0)
				$data['banner_image']=implode(',',$imageids);
				else
				$data['banner_image']='';	
			}
			if(isset($data['store']) && count($data['store'])>0){
					$data['store']=implode(',',$data['store']);
			}	
			
			//var_dump($data);die;
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedAt == NULL || $model->getUpdateAt() == NULL) {
					$model->setCreatedAt(now())
						->setUpdateAt(now());
				} else {
					$model->setUpdateAt(now());
				}	
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mobiconnectadvcart')->__('Layout was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mobiconnectadvcart')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
	public function massVisibiltyAction() {
		if ($this->getRequest ()->isPost ()) {
			$status = $this->getRequest ()->getPost ( 'status' );
			
			$bannerids = $this->getRequest ()->getPost ( 'banner_ids', array () );
			foreach ( $bannerids as $id ) {
				$banner = Mage::getModel ( 'mobiconnectadvcart/layouts' )->load ( $id );
				if ($banner->getId ()) {
					try {
						$banner->setData ( 'status', $status );
						$banner->save ();
						$this->_getSession ()->addSuccess ( Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Status successfully updated' ) );
					} catch ( Exception $e ) {
						$this->_getSession ()->addError ( $e->getMessage () );
					}
				} else {
					$this->_getSession ()->addError ( Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Invalid id supplied, %s', $id ) );
				}
			}
		}
		$this->_redirectReferer ();
	}
	public function massDeleteAction() {
		if ($this->getRequest ()->isPost ()) { // ass action
			$bannerIds = $this->getRequest ()->getPost ( 'banner_ids', array () );
			foreach ( $bannerIds as $id ) {
				$banner = Mage::getModel ( 'mobiconnectadvcart/layouts' )->load ( $id );
				if ($banner->getId ()) {
					try {
						$banner->delete ();
						$this->_getSession ()->addSuccess ( Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Banner %s successfully deleted.', $id ) );
					} catch ( Exception $e ) {
						$this->_getSession ()->addError ( $e->getMessage () );
					}
				} else {
					$this->_getSession ()->addError ( Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Invalid id supplied, %s', $id ) );
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
               $this->getLayout()->createBlock('mobiconnectadvcart/adminhtml_widget_grid')->toHtml()
        );
    }
    public function imageGridAction(){
    	$this->loadLayout();
    	$this->getResponse()->setBody(
        $this->getLayout()->createBlock('mobiconnectadvcart/adminhtml_widget_edit_tab_bannergrid')->toHtml()
    	); 
	}
}
?>
