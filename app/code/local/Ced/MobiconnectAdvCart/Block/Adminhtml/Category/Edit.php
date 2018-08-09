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
class Ced_MobiconnectAdvCart_Block_Adminhtml_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {                
        $this->_objectId = 'id';
        $this->_blockGroup = 'mobiconnectadvcart';
        $this->_controller = 'adminhtml_category';
         $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
		
		
        $this->_updateButton('save', 'label', Mage::helper('mobiconnectadvcart')->__('Save Banner'));
        parent::__construct();
    }
	
    public function getHeaderText()
    {
        if( Mage::registry('banner_data') && Mage::registry('banner_data')->getId() ) {
            return Mage::helper('mobiconnectadvcart')->__("Edit Banner  '%s'", $this->htmlEscape(Mage::registry('banner_data')->getTitle()));
        } else {
            return Mage::helper('mobiconnectadvcart')->__('New Banner ');
        }
    
    }
}
