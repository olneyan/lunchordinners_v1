<?php

/**
 * Admihtml Manage Delivery Boys Controller
 *
 * @category   Delivery
 * @package    Delivery_Boy
 * @author     <rajeeb.saraswati@gmail.com>
 */
class Delivery_Boy_Adminhtml_Delivery_BoyController extends Mage_Adminhtml_Controller_Action {
    
    /**
     * Session getter
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession() {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * Load layout, set active menu and breadcrumbs
     *
     * @return Delivery_Boy_Adminhtml_Delivery_BoyController
     */
    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('delivery_boy/boys')
            ->_addBreadcrumb(
                Mage::helper('delivery_boy')->__('Delivery'),
                Mage::helper('delivery_boy')->__('Delivery')
            )
            ->_addBreadcrumb(
                Mage::helper('delivery_boy')->__('Boys'),
                Mage::helper('delivery_boy')->__('Boys')
            );
        return $this;
    }
    
    /**
     * Init delivery boy instance object and set it to registry
     *
     * @return Delivery_Boy_Model_Boy|boolean
     */
    protected function _initBoyInstance() {
        $this->_title($this->__('Delivery'))->_title($this->__('Boys'));

        /** @var $boy Delivery_Boy_Model_Boy */
        $boy = Mage::getModel('delivery_boy/boy');

        $entityId   = $this->getRequest()->getParam('entity_id', null);

        if ($entityId) {
            $boy->load($entityId);
            if (!$boy->getId()) {
                $this->_getSession()->addError(Mage::helper('delivery_boy')->__('Wrong delivery boy specified.'));
                return false;
            }
        }
        
        $postData = $this->getRequest()->getPost();
        if ($postData) {
            $boy->setData($postData);
        }
        
        Mage::register('current_delivery_boy', $boy);
        return $boy;
    }

    /**
     * Delivery Boys Grid
     *
     */
    public function indexAction() {
        $this->_title($this->__('Delivery'))->_title($this->__('Boys'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        
        $this->_initAction();
        
        /**
         * Append boys block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('delivery_boy/adminhtml_boy', 'boy')
        );
        
        $this->renderLayout();
    }

    
    public function gridAction() {
        $this->_initAction()
            ->renderLayout();
    }
    
    /**
     * New delivery boy instance action (forward to edit action)
     *
     */
    public function newAction() {
        $this->_forward('edit');
    }

    /**
     * Edit delivery boy instance action
     *
     */
    public function editAction() {
        $boy = $this->_initBoyInstance();
        if (!$boy) {
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($boy->getId() ? "{$boy->getFirstname()} {$boy->getLastname()}" : $this->__('New Delivery Boy'));

        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Set body to response
     *
     * @param string $body
     */
    private function setBody($body)
    {
        Mage::getSingleton('core/translate_inline')->processResponseBody($body);
        $this->getResponse()->setBody($body);
    }

    /**
     * Validate action
     *
     */
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);
        $boy = $this->_initBoyInstance();
        $result = $boy->validate();
        if ($result !== true && is_string($result)) {
            $this->_getSession()->addError($result);
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }
        $this->setBody($response->toJson());
    }

    /**
     * Save action
     *
     */
    public function saveAction()
    {
        $boy = $this->_initBoyInstance();
        if (!$boy) {
            $this->_redirect('*/*/');
            return;
        }
        try {
            $boy->save();
            $this->_getSession()->addSuccess(
                Mage::helper('delivery_boy')->__('The delivery boy has been saved.')
            );
            if ($this->getRequest()->getParam('back', false)) {
                $this->_redirect('*/*/edit', array(
                    'entity_id' => $boy->getId(),
                    '_current' => true
                ));
            } else {
                $this->_redirect('*/*/');
            }
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('An error occurred during saving delivery boy: %s', $e->getMessage()));
        }
        $this->_redirect('*/*/edit', array('_current' => true));
    }

    /**
     * Delete Action
     *
     */
    public function deleteAction()
    {
        $boy = $this->_initBoyInstance();
        if ($boy) {
            try {
                $boy->delete();
                $this->_getSession()->addSuccess(
                    Mage::helper('delivery_boy')->__('The delivery boy has been deleted.')
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
        return;
    }

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('delivery_boy/boys');
    }
}