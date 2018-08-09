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
  * @package     Ced_CsVendorReview
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

/**
 * Rating Item admin side general tab of form
 */
 

class Ced_CsVendorReview_Block_Adminhtml_Review_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare form for the review
	 */
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array());
		$this->setForm($form);
		$fieldset = $form->addFieldset('id' , array('legend'=> Mage::helper('csvendorreview')->__('Rating Infromation')));
		$rating_item = Mage::getModel('csvendorreview/rating')->getCollection();
		
		$fieldset->addField('vendor_name', 'text', array(
					'label'     => Mage::helper('csvendorreview')->__('Vendor Name'),
					'readonly' => true,
					'disabled'	=> 'disabled',
					'name'      => 'vendor_name',
			));
			
		$fieldset->addField('customer_name', 'text', array(
					'label'     => Mage::helper('csvendorreview')->__('Customer Name'),
					'readonly' => true,
					'disabled'	=> 'disabled',
					'name'      => 'customer_name',
			));
		
		$rating_options = Mage::getModel('csvendorreview/options')->getRatingOption();
		foreach($rating_item as $key => $valu){
		
			$fieldset->addField($valu['rating_code'], 'select', array(
					'label' => Mage::helper('csvendorreview')->__($valu['rating_label']),
					'name'  => $valu['rating_code'],
					'options'   => $rating_options,
			)); 
		} 

		$fieldset->addField('subject', 'text', array(
					'label' => Mage::helper('csvendorreview')->__('Subject'),
					'name'  => 'subject',
			)); 
				
		$fieldset->addField('review', 'text', array(
					'label' => Mage::helper('csvendorreview')->__('Review'),
					'name'  => 'review',
			));
	
		$fieldset->addField('status', 'select', array(
				'label' => Mage::helper('csvendorreview')->__('Status'),
				'name'  => 'status',
				'options'   => Mage::getModel('csvendorreview/options')->getStatusOption(),
		));		
				
		if ( Mage::registry('csvendorreview_data') ){
			$form->setValues(Mage::registry('csvendorreview_data')->getData());
		}

		return parent::_prepareForm();
	}
 
}