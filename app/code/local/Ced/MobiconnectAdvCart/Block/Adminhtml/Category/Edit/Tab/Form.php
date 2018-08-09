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
class Ced_MobiconnectAdvCart_Block_Adminhtml_Category_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('category_banner_form', array('legend'=>Mage::helper('mobiconnectadvcart')->__('Banner information')));
     
      $fieldset->addField('banner_title', 'text', array(
          'label'     => Mage::helper('mobiconnectadvcart')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'banner_title',
      ));
      if (! Mage::app ()->isSingleStoreMode ()) {
      $fieldset->addField ( 'store', 'multiselect', array (
          'name' => 'store[]',
          'label' => Mage::helper ( 'mobiconnect' )->__ ( 'Store View' ),
          'title' => Mage::helper ( 'mobiconnect' )->__ ( 'Store View' ),
          
          'values' => Mage::getSingleton ( 'adminhtml/system_store' )->getStoreValuesForForm ( false, true ) 
      ) );
    } else {
      $fieldset->addField ( 'store', 'hidden', array (
          'name' => 'store[]',
          'value' => Mage::app ()->getStore ( true )->getId () 
      ) );
    }
    $fieldset->addField('show_on', 'select', array(
          'label'     => Mage::helper('mobiconnectadvcart')->__('Show On'),
          'name'      => 'show_on',
          'values'    => array(
              array(
                  'value'     => 'TOP',
                  'label'     => Mage::helper('mobiconnectadvcart')->__('Top'),
              ),

              array(
                  'value'     => 'BOTTOM',
                  'label'     => Mage::helper('mobiconnectadvcart')->__('Bottom'),
              ),
              /*array(
                  'value'     => 'BOTH',
                  'label'     => Mage::helper('mobiconnectadvcart')->__('Both'),
              ),*/
          ),
      ));
    $fieldset->addField('priority', 'text', array(
          'label'     => Mage::helper('mobiconnectadvcart')->__('Priority'),
          'class'=> 'required-entry validate-number',
          'required'  => true,
          'name'      => 'priority',
      ));
    $fieldset->addField('banner_status', 'select', array(
          'label'     => Mage::helper('mobiconnectadvcart')->__('Status'),
          'name'      => 'banner_status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('mobiconnectadvcart')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('mobiconnectadvcart')->__('Disabled'),
              ),
          ),
      ));
      if (Mage::getSingleton('adminhtml/session')->getFormData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getFormData());
          Mage::getSingleton('adminhtml/session')->setGroupData(null);
      } elseif ( Mage::registry('banner_data') ) {
          $form->setValues(Mage::registry('banner_data')->getData());
      }
      return parent::_prepareForm();
  }
}
