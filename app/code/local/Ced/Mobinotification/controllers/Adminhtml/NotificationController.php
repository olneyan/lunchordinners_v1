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
  * @package   Ced_Mobinotification
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobinotification_Adminhtml_NotificationController extends Ced_Mobiconnect_Controller_Adminhtml_Action
{
    public function indexAction()
    {   
       
        $this->loadLayout();
        $this->_setActiveMenu('mobiconnect/deals')->_addBreadcrumb(Mage::helper('adminhtml')->__('Notification Manager'), Mage::helper('adminhtml')->__('Notification Manager'));
        $this->getLayout()->getBlock('head')->setTitle($this->__('Mobile Notification'));
        $this->renderLayout();
    }
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
               $this->getLayout()->createBlock('mobinotification/adminhtml_notification_grid')->toHtml()
        );
    }
    public function editAction() {
        $id     = $this->getRequest()->getParam('notification_id');
        $model  = Mage::getModel('mobinotification/notification')->load($id);
        if ($model->getId()) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('mobinotification_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('mobinotification/notification');
            if ($model->getId()) {
                
                $this->getLayout()->getBlock('head')->setTitle($this->__('Edit '.$model->getTitle()));
            }else{
                    $this->getLayout()->getBlock('head')->setTitle($this->__('New Notification'));
            }
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Notification Manager'), Mage::helper('adminhtml')->__('Notification Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Notification'), Mage::helper('adminhtml')->__('Notification'));
            
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('mobinotification/adminhtml_notification_edit'))
                ->_addLeft($this->getLayout()->createBlock('mobinotification/adminhtml_notification_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mobinotification')->__('Notification does not exist'));
            $this->_redirect('*/*/');
        }
    }
 
    public function newAction() {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            $this->loadLayout();
            $this->_setActiveMenu('mobiconnect/deals');     
            $this->getLayout()->getBlock('head')->setTitle($this->__('New Notification'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Notification Manager'), Mage::helper('adminhtml')->__('Notification Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Notification'), Mage::helper('adminhtml')->__('Notification'));
            
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('mobinotification/adminhtml_notification_edit'))
                ->_addLeft($this->getLayout()->createBlock('mobinotification/adminhtml_notification_edit_tabs'));

            $this->renderLayout();
    }
    public function saveAction()
    {
        if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();   
            if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
                try {   
                    /* Starting upload */   
                    $uploader = new Varien_File_Uploader('image');
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);                   
                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') .DS.'notification'.DS.'images'.DS ;
                    $uploader->save($path, time().$_FILES['image']['name']);
                    } catch (Exception $e) {
              
                }
                $data['image'] = '/notification/images/'.time().$_FILES['image']['name'];
            }
            if(is_array($data['image'])){
                $data['image']=$data['image']['value'];
                }
            if(isset($data['id']))
            unset($data['id']);    
            $model = Mage::getModel('mobinotification/notification');      
            $model->setData($data)
                ->setId($this->getRequest()->getParam('notification_id'));
            
            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }   
                
                $model->save();
                if($model->getShedule()){
                    Mage::helper('mobinotification')->setSheduleCron($model);
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mobinotification')->__('Notification was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('notification_id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('notification_id' => $this->getRequest()->getParam('notification_id')));
                return;
            }
        }else{
           Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mobinotification')->__('Unable to find item to save'));
            $this->_redirect('*/*/'); 
        }
    }
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('mobinotification/notification');
                 
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                     
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Notification was successfully deleted'));
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
                    $groupobj = Mage::getModel('mobinotification/notification')->load($id);
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
                    $groupobj = Mage::getSingleton('mobinotification/notification')
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
}