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
class Ced_Mobiconnect_Adminhtml_SupportController extends Ced_Mobiconnect_Controller_Adminhtml_Action {

  const XML_PATH_EMAIL_RECIPIENT  = 'sanjeevkumar@cedcoss.com';
  const XML_PATH_EMAIL_TEMPLATE   = 'mobiconnect/general/support_template';
  public function supportAction(){
    $this->loadLayout ()->_setActiveMenu ( 'mobiconnect/set_time' );
    $this->renderLayout();
  }
  public function submitAction(){
    $post = $this->getRequest()->getPost();
    if ( $post ) {
      $translate = Mage::getSingleton('core/translate');
      /* @var $translate Mage_Core_Model_Translate */
      $translate->setTranslateInline(false);
      try {
        $postObject = new Varien_Object();
        $postObject->setData($post);
        $sender = array('name'=>$post['name'],'email'=>$post['email']);
        $receiver = Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT);
        $error = false;
        if (!Zend_Validate::is(trim($post['domain']) , 'NotEmpty')) {
            $error = true;
        }
        if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
            $error = true;
        }
        if (!Zend_Validate::is(trim($post['message']) , 'NotEmpty')) {
            $error = true;
        }
        if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
            $error = true;
        }
        if (Zend_Validate::is(trim($post['subject']), 'NotEmpty')) {
            $error = true;
        }
        if ($error) {
            throw new Exception();
        }
        $mailTemplate = Mage::getModel('core/email_template');
        /* @var $mailTemplate Mage_Core_Model_Email_Template */
        $mailTemplate->setDesignConfig(array('area' => 'frontend'))
            ->setReplyTo($post['email'])
            ->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                $sender,
                $receiver,
                null,
                array('data' => $postObject)
            );
        if (!$mailTemplate->getSentSuccess()) { 
            throw new Exception();
        }
        $translate->setTranslateInline(true);
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mobiconnect')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
        $this->_redirect('*/*/support');
        return;
          } catch (Exception $e) {
              $translate->setTranslateInline(true);
              Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mobiconnect')->__('Unable to submit your request. Please, try again later'));
              $this->_redirect('*/*/support');
              return;
          }
      } else {
          $this->_redirect('*/*/support');
      }
    }
  }
