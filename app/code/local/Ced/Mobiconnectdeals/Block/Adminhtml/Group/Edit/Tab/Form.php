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
class Ced_Mobiconnectdeals_Block_Adminhtml_Group_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('group_form', array('legend'=>Mage::helper('mobiconnectdeals')->__('Group information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('mobiconnectdeals')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('group_image_name', 'image', array(
          'label'     => Mage::helper('mobiconnectdeals')->__('Image'),
          'required'  => false,
          'name'      => 'group_image_name',
	    ));
		  $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
      $fieldset->addField('group_start_date', 'date', array(
        'name'   => 'group_start_date',
        'label'  => Mage::helper('mobiconnectdeals')->__('Start Date'),
        'title'  => Mage::helper('mobiconnectdeals')->__('Start Date'),
        'image'  => $this->getSkinUrl('images/grid-cal.gif'),
        'input_format' => $dateFormatIso,
        'format'       => $dateFormatIso,
        'tabindex' => 1,
        'time' => true
      ));
      $fieldset->addField('group_end_date', 'date', array(
        'name'   => 'group_end_date',
        'label'  => Mage::helper('mobiconnectdeals')->__('End Date'),
        'title'  => Mage::helper('mobiconnectdeals')->__('End Date'),
        'image'  => $this->getSkinUrl('images/grid-cal.gif'),
        'input_format' => $dateFormatIso,
        'format'       => $dateFormatIso,
         'tabindex' => 1,
        'time' => true
      ));
      $fieldset->addField('group_status', 'select', array(
          'label'     => Mage::helper('mobiconnectdeals')->__('Status'),
          'name'      => 'group_status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('mobiconnectdeals')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('mobiconnectdeals')->__('Disabled'),
              ),
          ),
      ));
       $fieldset->addField('timer_status', 'select', array(
          'label'     => Mage::helper('mobiconnectdeals')->__('Show Timmer'),
          'name'      => 'timer_status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('mobiconnectdeals')->__('Yes'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('mobiconnectdeals')->__('No'),
              ),
          ),
      ));
        $fieldset->addField('view_all_status', 'select', array(
          'label'     => Mage::helper('mobiconnectdeals')->__('Show View All'),
          'name'      => 'view_all_status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('mobiconnectdeals')->__('Yes'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('mobiconnectdeals')->__('No'),
              ),
          ),
      ));
      $fieldset->addField('is_static', 'select', array(
          'label'     => Mage::helper('mobiconnectdeals')->__('Static Banner'),
          'name'      => 'is_static',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('mobiconnectdeals')->__('Yes'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('mobiconnectdeals')->__('No'),
              ),
          ),
      ));

     
      if((Mage::registry('group_data')) && Mage::registry('group_data')->getData('is_static')=='1'){
            $fieldset->addField('deal_link', 'text', array(
                'label'     => Mage::helper('mobiconnectdeals')->__('Static Deal Url'),
                'class'     => 'required-entry',
                'required'  => true,
                'name'      => 'deal_link',
            ));
      }else{
            $fieldset->addField('deal_link', 'text', array(
                'label'     => Mage::helper('mobiconnectdeals')->__('Static Deal Url'),
                'class'     => 'required-entry',
                'name'      => 'title',
            ));
      }
      $this->setChild('form_after', $this->getLayout()
     ->createBlock('adminhtml/widget_form_element_dependence')
        ->addFieldMap('is_static', 'is_static')
        ->addFieldMap('deal_link', 'deal_link')
        ->addFieldDependence('deal_link', 'is_static', 1) 
      );
      if (Mage::getSingleton('adminhtml/session')->getFormData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getFormData());
          Mage::getSingleton('adminhtml/session')->setGroupData(null);
      } elseif (Mage::registry('group_data')) {
          $form->setValues(Mage::registry('group_data')->getData());
      }
      return parent::_prepareForm();
  }
}
