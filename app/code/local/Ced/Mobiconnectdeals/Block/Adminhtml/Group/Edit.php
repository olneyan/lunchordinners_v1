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
class Ced_Mobiconnectdeals_Block_Adminhtml_Group_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'group_id';
        $this->_blockGroup = 'mobiconnectdeals';
        $this->_controller = 'adminhtml_group';
        $this->_updateButton('save', 'label', Mage::helper('mobiconnectdeals')->__('Save '));
        $this->_updateButton('delete', 'label', Mage::helper('mobiconnectdeals')->__('Delete '));
		
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
    }

    public function getHeaderText()
    {
        if( Mage::registry('group_data') && Mage::registry('group_data')->getGroupId() ) {
            return Mage::helper('mobiconnectdeals')->__("Edit Deal Group '%s'", $this->htmlEscape(Mage::registry('group_data')->getTitle()));
        } else {
            return Mage::helper('mobiconnectdeals')->__('Add Deal Group');
        }
    }
}