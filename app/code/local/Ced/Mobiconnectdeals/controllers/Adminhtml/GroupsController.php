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
class Ced_Mobiconnectdeals_Adminhtml_GroupsController extends Ced_Mobiconnect_Controller_Adminhtml_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('mobiconnect/deals')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Deals Manager'), Mage::helper('adminhtml')->__('Deals Manager'));
		$this->getLayout()->getBlock('head')->setTitle($this->__('Mobile Deal Groups'));
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$group_id     = $this->getRequest()->getParam('group_id');
		$model  = Mage::getModel('mobiconnectdeals/group')->load($group_id);
		if ($model->getGroupId() || $group_id=='0') {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			Mage::register('group_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('mobiconnect/deals');
			if ($model->getGroupId()) {
				
				$this->getLayout()->getBlock('head')->setTitle($this->__('Edit '.$model->getTitle()));
			}else{
					$this->getLayout()->getBlock('head')->setTitle($this->__('New Group'));
			}
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Deals Manager'), Mage::helper('adminhtml')->__('Deals Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Deal Group'), Mage::helper('adminhtml')->__('Deal Group'));
			
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('mobiconnectdeals/adminhtml_group_edit'))
				->_addLeft($this->getLayout()->createBlock('mobiconnectdeals/adminhtml_group_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mobiconnectdeals')->__('Group does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			$this->loadLayout();
			$this->_setActiveMenu('mobiconnect/deals');		
			$this->getLayout()->getBlock('head')->setTitle($this->__('New Group'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Deals Manager'), Mage::helper('adminhtml')->__('Deals Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Deal Group'), Mage::helper('adminhtml')->__('Deal Group'));
			
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('mobiconnectdeals/adminhtml_group_edit'))
				->_addLeft($this->getLayout()->createBlock('mobiconnectdeals/adminhtml_group_edit_tabs'));

			$this->renderLayout();
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			if(isset($_FILES['group_image_name']['name']) && $_FILES['group_image_name']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('group_image_name');
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);					
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') .DS.'deals'.DS.'group'.DS ;
					$uploader->save($path, time().$_FILES['group_image_name']['name']);
					} catch (Exception $e) {
		      
		        }
		        $data['group_image_name'] = '/deals/group/'.time().$_FILES['group_image_name']['name'];
	  		}
	  		if(is_array($data['group_image_name'])){
				$data['group_image_name']=$data['group_image_name']['value'];
				}
			if(isset($data['group_deals']) && count($data['group_deals'])>0){
				$paramvalues=explode('&',$data['group_deals']);
				$dealids=array();
				foreach ($paramvalues as $key => $value) {
					$dealid=explode('=',$value);
					$dealids[]=$dealid['0'];
				}
				if(count($dealids)>0)
				$data['content']=implode(',',$dealids);
				else
				$data['content']='';	
			}
			$model = Mage::getModel('mobiconnectdeals/group');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('group_id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				/*if(isset($data['selected_deals']) && count($data['selected_deals'])>0){
					foreach ($data['selected_deals'] as $key => $ids) {
						 $groupobj = Mage::getSingleton('mobiconnectdeals/deals')
                        ->load($ids)
                        ->setDealGroup($this->getRequest()->getParam('group_id'))
                        ->setIsMassupdate(true)
                        ->save();
					}
				}*/
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mobiconnectdeals')->__('Group was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData($data);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('group_id' => $model->getGroupId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('group_id' => $this->getRequest()->getParam('group_id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mobiconnectdeals')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('group_id') > 0 ) {
			try {
				$model = Mage::getModel('mobiconnectdeals/group');
				 
				$model->setId($this->getRequest()->getParam('group_id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Group was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('group_id' => $this->getRequest()->getParam('group_id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $group_ids = $this->getRequest()->getParam('group_id');
        if(!is_array($group_ids)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($group_ids as $group_id) {
                    $groupobj = Mage::getModel('mobiconnectdeals/group')->load($group_id);
                    $groupobj->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($group_ids)
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
        $groups = $this->getRequest()->getParam('group_id');
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
    public function gridAction()
	{
    $this->loadLayout();
    $this->getResponse()->setBody(
           $this->getLayout()->createBlock('mobiconnectdeals/adminhtml_group_grid')->toHtml()
    ); 
	}
	public function dealsgridAction()
	{
    $this->loadLayout();
    $this->getResponse()->setBody(
           $this->getLayout()->createBlock('mobiconnectdeals/adminhtml_group_edit_tab_assign')->toHtml()
    ); 
	}
}