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
  * @package   Ced_Mobiconnectdeals
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobiconnectdeals_Adminhtml_DealsController extends Ced_Mobiconnect_Controller_Adminhtml_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('mobiconnect/deals')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Deals Manager'), Mage::helper('adminhtml')->__('Deals Manager'));
		$this->getLayout()->getBlock('head')->setTitle($this->__('Mobile Deals'));
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('deal_id');
		$model  = Mage::getModel('mobiconnectdeals/deals')->load($id);
		if ($model->getId()) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			Mage::register('deal_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('mobiconnect/deals');
			if ($model->getId()) {
				
				$this->getLayout()->getBlock('head')->setTitle($this->__('Edit '.$model->getTitle()));
			}else{
					$this->getLayout()->getBlock('head')->setTitle($this->__('New Deal'));
			}
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Deals Manager'), Mage::helper('adminhtml')->__('Deals Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Deal'), Mage::helper('adminhtml')->__('Deal '));
			
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('mobiconnectdeals/adminhtml_deals_edit'))
				->_addLeft($this->getLayout()->createBlock('mobiconnectdeals/adminhtml_deals_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mobiconnectdeals')->__('Deal does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			$this->loadLayout();
			$this->_setActiveMenu('mobiconnect/deals');
			$this->getLayout()->getBlock('head')->setTitle($this->__('New Deal'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Deals Manager'), Mage::helper('adminhtml')->__('Deals Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Deal'), Mage::helper('adminhtml')->__('Deal '));
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('mobiconnectdeals/adminhtml_deals_edit'))
				->_addLeft($this->getLayout()->createBlock('mobiconnectdeals/adminhtml_deals_edit_tabs'));
			$this->renderLayout();
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$products = Mage::helper('core/string')->parseQueryStr($data['deals_products']);
			if(count($products)){
				$data['product_link']=json_encode($products);
			}
			if($data['category_ids'] && $data['category_ids']!=''){
				$categoryids=explode(',',$data['category_ids']);
				$category_ids_array=array_unique($categoryids);
				$data['category_link'] =implode(',',$category_ids_array);
			}  

			if(isset($_FILES['deal_image_name']['name']) && $_FILES['deal_image_name']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('deal_image_name');
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(false);					
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') .DS.'deals'.DS.'deal'.DS;
					$uploader->save($path, time().$_FILES['deal_image_name']['name']);
					
				} catch (Exception $e) {
		      
		        }
	  			$data['deal_image_name'] = '/deals/deal/'.time().$_FILES['deal_image_name']['name'];
			}
			if(is_array($data['deal_image_name'])){
				$data['deal_image_name']=$data['deal_image_name']['value'];
			}
			$model = Mage::getModel('mobiconnectdeals/deals');		
			if(isset($data['id']))
			unset($data['id']);
			$model->setData($data)
				->setId($this->getRequest()->getParam('deal_id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mobiconnectdeals')->__('Deal was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData($data);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('deal_id' => $model->getId()));
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
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mobiconnectdeals')->__('Unable to find item to save'));
        $this->_redirect('*/*/');      
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('mobiconnectdeals/group');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Deal was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $ids = $this->getRequest()->getParam('id');
        if(!is_array($ids)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $groupobj = Mage::getModel('mobiconnectdeals/group')->load($id);
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
        $groups = $this->getRequest()->getParam('id');
        if(!is_array($groups)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($groups as $group) {
                    $groupobj = Mage::getSingleton('mobiconnectdeals/group')
                        ->load($group)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($groups))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
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
	public function chooseProductsAction() {
		$request = $this->getRequest ();
		$block = $this->getLayout ()->createBlock ( 'mobiconnectdeals/adminhtml_deals_edit_tab_product', 'promo_widget_chooser_sku', array (
				'js_form_object' => $request->getParam ( 'form' ) 
		) );
		
		if ($block) {
			$this->getResponse ()->setBody ( $block->toHtml () );
		}
	}
	public function chooseCategoriesAction() {
		$request = $this->getRequest ();
		$id = $request->getParam ( 'selected', array () );
		$block = $this->getLayout ()->createBlock ( 'mobiconnectdeals/adminhtml_deals_edit_tab_categories', 'maincontent_category', array (
				'js_form_object' => $request->getParam ( 'form' ) 
		) )->setCategoryIds ( $id );
		if ($block) {
			$this->getResponse ()->setBody ( $block->toHtml () );
		}
	}
	
    public function gridAction()
	{
    $this->loadLayout();
    $this->getResponse()->setBody(
           $this->getLayout()->createBlock('mobiconnectdeals/adminhtml_deals_grid')->toHtml()
    ); 
	}
}
