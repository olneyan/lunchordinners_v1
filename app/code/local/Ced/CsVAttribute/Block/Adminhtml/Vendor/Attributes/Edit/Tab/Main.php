<?php 

/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License (AFL 3.0)
  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/afl-3.0.php
  *
  * @category    Ced
  * @package     Ced_CsVAttribute
  * @author   	 CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
class Ced_CsVAttribute_Block_Adminhtml_Vendor_Attributes_Edit_Tab_Main extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
{
	/**
     * Adding vendor form elements for editing attribute
     *
     * @return Ced_CsVAttribute_Block_Adminhtml_Vendor_Attributes_Edit_Tab_Main
     */
	protected function _prepareForm() {
        parent::_prepareForm();
        $attributeObject = $this->getAttributeObject();
        /* @var $form Varien_Data_Form */
        $form = $this->getForm();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->getElement('base_fieldset');   
		
        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
		
		$fieldsett = $form->addFieldset('vendor_registration', array('legend'=>Mage::helper('csmarketplace')->__('Vendor Registration Form'))); 
		if(Mage::registry('entity_attribute') && Mage::registry('entity_attribute')->getId() && in_array(Mage::registry('entity_attribute')->getAttributeCode(), array('shop_url','public_name'))) {
			$fieldsett->addField('use_in_registration', 'select', array(
				'name'      => 'use_in_registration',
				'label'     => Mage::helper('csmarketplace')->__('Use in Vendor Registration Form'),
				'title'     => Mage::helper('csmarketplace')->__('Use in Vendor Registration Form'),
				'values'    => $yesnoSource,
				'note'		=> Mage::helper('csmarketplace')->__('If yes then it will shown as input in vendor registration form.'),
				'disabled'  => true,
			));
		} else {
			$fieldsett->addField('use_in_registration', 'select', array(
				'name'      => 'use_in_registration',
				'label'     => Mage::helper('csmarketplace')->__('Use in Vendor Registration Form'),
				'title'     => Mage::helper('csmarketplace')->__('Use in Vendor Registration Form'),
				'values'    => $yesnoSource,
				'note'		=> Mage::helper('csmarketplace')->__('If yes then it will shown as input in vendor registration form.'),
			));
		}
		
		
		$fieldsett->addField('position_in_registration', 'text', array(
            'name' => 'position_in_registration',
            'label' => Mage::helper('csmarketplace')->__('Position in Vendor Registration Form'),
            'title' => Mage::helper('csmarketplace')->__('Position in Vendor Registration Form'),
            'note' => Mage::helper('csmarketplace')->__('Position of attribute in Vendor Registration Form'),
            'class' => 'validate-digits',
        ));
		
		$fieldsett = $form->addFieldset('vendor_frontend', array('legend'=>Mage::helper('csmarketplace')->__('Vendor Profile Edit Form')));
		
		$fieldsett->addField('is_visible', 'select', array(
            'name'      => 'is_visible',
            'label'     => Mage::helper('csmarketplace')->__('Use in Vendor Profile Edit Form'),
            'title'     => Mage::helper('csmarketplace')->__('Use in Vendor Profile Edit Form'),
            'values'    => $yesnoSource,
			'note'		=> Mage::helper('csmarketplace')->__('If yes then it will shown as input in vendor profile edit form.'),
        ));
		
		$fieldsett->addField('position', 'text', array(
            'name' => 'position',
            'label' => Mage::helper('csmarketplace')->__('Position in Vendor Profile Edit Form'),
            'title' => Mage::helper('csmarketplace')->__('Position in Vendor Profile Edit Form'),
            'note' => Mage::helper('csmarketplace')->__('Position of attribute in Vendor Profile Edit Form'),
            'class' => 'validate-digits',
        ));

  $fieldsett = $form->addFieldset('use_in_invoicee', array('legend'=>Mage::helper('csmarketplace')->__('Use Attribute In Invoice')));
      $fieldsett->addField('use_in_invoice', 'select', array(
            'name' => 'use_in_invoice',
            'label' => Mage::helper('csmarketplace')->__('Use In Invoice'),
            'title' => Mage::helper('csmarketplace')->__('Use In Invoice'),
            'note' => Mage::helper('csmarketplace')->__('Use this attribute in Invoice form'),
       'values'    => $yesnoSource,
            
        ));
		
		$fieldsett = $form->addFieldset('vendor_left_profile', array('legend'=>Mage::helper('csmarketplace')->__('Vendor Left Profile View(On Shop Pages)'))); 
		
		$fieldsett->addField('use_in_left_profile', 'select', array(
            'name'      => 'use_in_left_profile',
            'label'     => Mage::helper('csmarketplace')->__('Use in Vendor Left Profile View'),
            'title'     => Mage::helper('csmarketplace')->__('Use in Vendor Left Profile View'),
            'values'    => $yesnoSource,
			'note'		=> Mage::helper('csmarketplace')->__('If yes then it will shown in vendor left profile view at vendor shop pages.'),
        ));
		
		$fieldsett->addField('fontawesome_class_for_left_profile', 'text', array(
            'name' => 'fontawesome_class_for_left_profile',
            'label' => Mage::helper('catalog')->__('Font Awesome class for Left Profile View'),
            'title' => Mage::helper('catalog')->__('Font Awesome class for Left Profile View'),
            'note' => Mage::helper('csmarketplace')->__('Font Awesome class for Left Profile of attribute in Vendor Left Profile View at vendor shop pages.<br/>You can get the class name from here <a href="https://fortawesome.github.io/Font-Awesome/icons/" target="_blank">https://fortawesome.github.io/Font-Awesome/icons/</a>.'),
        ));
		
		$fieldsett->addField('position_in_left_profile', 'text', array(
            'name' => 'position_in_left_profile',
            'label' => Mage::helper('catalog')->__('Position in Vendor Left Profile View'),
            'title' => Mage::helper('catalog')->__('Position in Vendor Left Profile View'),
            'note' => Mage::helper('csmarketplace')->__('Position of attribute in Vendor Left Profile View at vendor shop pages'),
            'class' => 'validate-digits',
        ));
		
        $fieldset->addField('is_global', 'hidden', array(
            'name'  => 'is_global',
        ), 'attribute_code');

       

        // frontend properties fieldset
        $frontendValues = $form->getElement('frontend_input')->getValues();
        
        /**
         * customization for client
         * 
         */
        
        $customVal[] = ['value'=>'time','label'=>'Time'];
        $frontendValues = array_merge($frontendValues,$customVal);
        
        
		$frontendValues['image'] = Mage::helper('csmarketplace')->__('Image Type');
		/* $frontendValues['multiimage'] = Mage::helper('csmarketplace')->__('Multi Image Type'); */
		$frontendValues['file'] = Mage::helper('csmarketplace')->__('File Type');
		/* $frontendValues['multifile'] = Mage::helper('csmarketplace')->__('Multi File Type'); */
		$form->getElement('frontend_input')->setValues($frontendValues);
		
		$form->getElement('frontend_input')->setLabel(Mage::helper('csmarketplace')->__('Vendor Input Type for Store Owner'));
		$form->getElement('is_unique')->setNote(Mage::helper('csmarketplace')->__('Not shared with other vendors'));
        $classValidation = $form->getElement('frontend_class')->getValues();
		$classValidation['validate-shopurl'] = Mage::helper('csmarketplace')->__('Vendor Shop URL');
		$form->getElement('frontend_class')->setValues($classValidation);
        
        Mage::dispatchEvent('csmarketplace_adminhtml_vendor_attribute_edit_prepare_form', array(
            'form'      => $form,
            'attribute' => $attributeObject
        ));

        return $this;
    }   
}
