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
class Ced_Mobinotification_Block_Adminhtml_Notification_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {                
        $this->_objectId = 'id';
        $this->_blockGroup = 'mobinotification';
        $this->_controller = 'adminhtml_notification';
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        if($this->getRequest()->getParam('id')){
            $this->_addButton('sendNotification', array(
        'label'   => Mage::helper('mobinotification')->__('Send Notification'),
        'onclick' => "setLocation('{$this->getUrl('*/adminhtml_sendnotification/index',array('id'=>$this->getRequest()->getParam('id')))}')",
        'class'   => 'save'
        )); 
        }
        
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
		
		
        $this->_updateButton('save', 'label', Mage::helper('mobinotification')->__('Save Notification'));
        parent::__construct();
    }
	
    public function getHeaderText()
    {
        if( Mage::registry('notification_data') && Mage::registry('notification_data')->getId() ) {
            return Mage::helper('mobinotification')->__("Edit   '%s'", $this->htmlEscape(Mage::registry('notification_data')->getDealTitle()));
        } else {
            return Mage::helper('mobinotification')->__('New Notification ');
        }
    
    }
}
