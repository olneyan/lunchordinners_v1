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
class Ced_MobiconnectAdvCart_Block_Adminhtml_Widget_Edit_Tab_Widget extends Mage_Adminhtml_Block_Widget_Form {
	/**
	 * prepare tab form's information
	 *
	 * @return form
	 */
	protected function _prepareForm() {
		$form = new Varien_Data_Form ();
		$this->setForm ( $form );
		$fieldset = $form->addFieldset ( 'mobiconnect_form', array (
				'legend' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Widget' ) 
		) );
		
		$fieldset->addField ( 'widget_title', 'text', array (
				'label' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Title' ),
				'name' => 'widget_title',
				'required' => true 
		) );
		if (! Mage::app ()->isSingleStoreMode ()) {
			$fieldset->addField ( 'store', 'multiselect', array (
					'name' => 'store[]',
					'label' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Store View' ),
					'title' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Store View' ),
					
					'values' => Mage::getSingleton ( 'adminhtml/system_store' )->getStoreValuesForForm ( false, true ) 
			) );
		} else {
			$fieldset->addField ( 'store', 'hidden', array (
					'name' => 'store[]',
					'value' => Mage::app ()->getStore ( true )->getId () 
			) );
		}
		$fieldset->addField('layout_type', 'select', array(
          'label'     => Mage::helper('mobiconnectadvcart')->__('Home Page Layout'),
          'name'      => 'layout_type',
          'required' => true ,
          'values'    => array(
              array(
                  'value'     => 'simple',
                  'label'     => Mage::helper('mobiconnectadvcart')->__('Simple'),
              ),

              array(
                  'value'     => 'lite',
                  'label'     => Mage::helper('mobiconnectadvcart')->__('Lite'),
              ),
              array(
                  'value'     => 'core',
                  'label'     => Mage::helper('mobiconnectadvcart')->__('Core'),
              ),
              array(
                  'value'     => 'ultimate',
                  'label'     => Mage::helper('mobiconnectadvcart')->__('Ultimate'),
              ),
          ),
      ));
		$fieldset->addField ( 'priority', 'text', array (
				'label' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Priority' ),
				'name' => 'priority',
				'class'=> 'required-entry validate-number',
				'required' => true 
		) );
		$fieldset->addField ( 'status', 'select', array (
				'label' => Mage::helper ( 'mobiconnectadvcart' )->__ ( 'Status' ),
				'required' => true,
				'name' => 'status',
				'values' => Mage::getSingleton ( 'mobiconnect/banner' )->getStatus () 
		) );
		if ($data = Mage::registry ( 'widget_data' )) {
			$form->setValues ( Mage::registry ( 'widget_data' )->getData () );
		}
		return parent::_prepareForm ();
	}
}
