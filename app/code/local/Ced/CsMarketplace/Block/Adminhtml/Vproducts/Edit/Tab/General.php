<?php 

/**
 * CedCommerce
 *  
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ced_CsMarketplace_Block_Adminhtml_Vproducts_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
	
  protected function _prepareForm()
  {
   	
     $form = new Varien_Data_Form(); 
     $this->setForm($form);
	 $csmarketplace = Mage::getModel('csmarketplace/vproducts');
	 $csmarketplace_row =  $csmarketplace->load($this->getRequest()->getParam('id'));
     $fieldset = $form->addFieldset('csmarketplace_general', array('legend'=>Mage::helper('csmarketplace')->__("Overview")));
     
		$fieldset->addField('vendor_id', 'text', array(
            'label'     => Mage::helper('csmarketplace')->__('vendor_id'),
	 		'class'     => 'required-entry',
	 		'required'  => true,
	 		'name'      => 'vendor_id'

        ));
		$fieldset->addField('product_id', 'text', array(
            'label'     => Mage::helper('csmarketplace')->__('product_id'),
	 		'class'     => 'required-entry',
	 		'required'  => true,
	 		'name'      => 'product_id'

        ));
		$fieldset->addField('product_type', 'text', array(
				'label'     => Mage::helper('csmarketplace')->__('Product type'),
				'class'     => 'required-entry',
				'required'  => true,
				'name'      => 'product_id'
		
		));
		$fieldset->addField('product_price', 'text', array(
            'label'     => Mage::helper('csmarketplace')->__('product_price'),
	 		'class'     => 'required-entry',
	 		'required'  => true,
	 		'name'      => 'product_price'

        ));$fieldset->addField('product_special_price', 'text', array(
            'label'     => Mage::helper('csmarketplace')->__('product_special_price'),
	 		'class'     => 'required-entry',
	 		'required'  => true,
	 		'name'      => 'product_special_price'

        ));
		$fieldset->addField('product_name', 'text', array(
            'label'     => Mage::helper('csmarketplace')->__('product_name'),
	 		'class'     => 'required-entry',
	 		'required'  => true,
	 		'name'      => 'product_name'

        ));
		$fieldset->addField('product_description', 'text', array(
            'label'     => Mage::helper('csmarketplace')->__('product_description'),
	 		'class'     => 'required-entry',
	 		'required'  => true,
	 		'name'      => 'product_description'

        ));
		$fieldset->addField('product_sku', 'text', array(
            'label'     => Mage::helper('csmarketplace')->__('product_sku'),
	 		'class'     => 'required-entry',
	 		'required'  => true,
	 		'name'      => 'product_sku'

        ));
		$fieldset->addField('product_image', 'text', array(
            'label'     => Mage::helper('csmarketplace')->__('product_image'),
	 		'class'     => 'required-entry',
	 		'required'  => true,
	 		'name'      => 'product_image'

        ));
		$fieldset->addField('product_small_image', 'text', array(
            'label'     => Mage::helper('csmarketplace')->__('product_small_image'),
	 		'class'     => 'required-entry',
	 		'required'  => true,
	 		'name'      => 'product_small_image'

        ));
		$fieldset->addField('product_thumbnail_image', 'text', array(
            'label'     => Mage::helper('csmarketplace')->__('product_thumbnail_image'),
	 		'class'     => 'required-entry',
	 		'required'  => true,
	 		'name'      => 'product_thumbnail_image'

        ));
		$fieldset->addField('status', 'text', array(
            'label'     => Mage::helper('csmarketplace')->__('status'),
	 		'class'     => 'required-entry',
	 		'required'  => true,
	 		'name'      => 'status'

        ));
		$fieldset->addField('product_qty', 'text', array(
            'label'     => Mage::helper('csmarketplace')->__('product_qty'),
	 		'class'     => 'required-entry',
	 		'required'  => true,
	 		'name'      => 'product_qty'

        ));
		$fieldset->addField('product_is_in_stock', 'text', array(
            'label'     => Mage::helper('csmarketplace')->__('product_is_in_stock'),
	 		'class'     => 'required-entry',
	 		'required'  => true,
	 		'name'      => 'product_is_in_stock'

        ));
	
		
		
	 	
	 	 
      if ( Mage::getSingleton('adminhtml/session')->getCsMarketplaceVproducts() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCsMarketplaceVproducts());
          Mage::getSingleton('adminhtml/session')->setCsMarketplaceVproducts(null);
      } elseif ( Mage::registry('csmarketplace_vproducts') ) {
          $form->setValues(Mage::registry('csmarketplace_vproducts')->getData());
      }
      return parent::_prepareForm();
  }
 
}