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
class Ced_MobiconnectAdvCart_Adminhtml_CategoryController extends Ced_Mobiconnect_Controller_Adminhtml_Action {

protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('mobiconnectadvcart/category_banner')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Category Banner'), Mage::helper('adminhtml')->__('Category Banner'));
		$this->getLayout()->getBlock('head')->setTitle($this->__('Category Banner'));
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('mobiconnectadvcart/categorybanner')->load($id);

		if ($model->getId() || $id=='0') {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			Mage::register('banner_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('mobiconnectadvcart/category_bannner');
			if ($model->getId()) {
				
				$this->getLayout()->getBlock('head')->setTitle($this->__('Edit '.$model->getTitle()));
			}else{
					$this->getLayout()->getBlock('head')->setTitle($this->__('New Banner'));
			}
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Category Banner'), Mage::helper('adminhtml')->__('Category Banner'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Category Banner'), Mage::helper('adminhtml')->__('Category Banner '));
			
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('mobiconnectadvcart/adminhtml_category_edit'))
				->_addLeft($this->getLayout()->createBlock('mobiconnectadvcart/adminhtml_category_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mobiconnectadvcart')->__('Banner does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			$this->loadLayout();
			$this->_setActiveMenu('mobiconnectadvcart/category_banner');
			$this->getLayout()->getBlock('head')->setTitle($this->__('New Category Banner'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Category Banner'), Mage::helper('adminhtml')->__('Category Banner'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Category Banner'), Mage::helper('adminhtml')->__('Category Banner '));
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('mobiconnectadvcart/adminhtml_category_edit'))
				->_addLeft($this->getLayout()->createBlock('mobiconnectadvcart/adminhtml_category_edit_tabs'));
			$this->renderLayout();
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {

			$model = Mage::getModel('mobiconnectadvcart/categorybanner');
			if(isset($data['store']) && count($data['store'])>0){
					$data['store']=implode(',',$data['store']);
			}	
			if(isset($data['category_banner']) && count($data['category_banner'])>0){
				$paramvalues=explode('&',$data['category_banner']);
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
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mobiconnectadvcart')->__('Banner was successfully saved'));
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
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('mobiconnectadvcart/categorybanner');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Banner was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $ids = $this->getRequest()->getParam('banner_ids');
        if(!is_array($ids)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $groupobj = Mage::getModel('mobiconnectadvcart/categorybanner')->load($id);
                    $groupobj->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($ids)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        if ($this->getRequest ()->isPost ()) {
			
			$visibility = $this->getRequest ()->getPost ( 'visibility' );
			//var_dump($visibility);
			$ids = $this->getRequest ()->getPost ( 'banner_ids', array () );
			//echo '<hr>';var_dump($ids);die;
			foreach ( $ids as $id ) { 
				$status = Mage::getModel ( 'mobiconnectadvcart/categorybanner' )->load ( $id );
				if ($status->getId ()) {
					try {
						$status->setData ( 'banner_status', $visibility );
						$status->save ();
						
					} catch ( Exception $e ) {
						$this->_getSession ()->addError ( $e->getMessage () );
					}
				} else {
					$this->_getSession ()->addError ( Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Invalid id supplied, %s', $id ) );
				}
			}
		}
		$this->_getSession ()->addSuccess ( Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Status successfully updated' ) );
        $this->_redirect('*/*/index');
    }
    public function gridAction(){
    	$this->loadLayout();
    	$this->getResponse()->setBody(
        $this->getLayout()->createBlock('mobiconnectadvcart/adminhtml_category_grid')->toHtml()
    	); 
	}
	public function imageGridAction(){
    	$this->loadLayout();
    	$this->getResponse()->setBody(
        $this->getLayout()->createBlock('mobiconnectadvcart/adminhtml_category_edit_tab_assign')->toHtml()
    	); 
	}
}