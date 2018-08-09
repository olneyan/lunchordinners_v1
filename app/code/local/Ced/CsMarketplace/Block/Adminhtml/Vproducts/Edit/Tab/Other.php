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

class Ced_CsMarketplace_Block_Adminhtml_Vproducts_Edit_Tab_Other extends Mage_Adminhtml_Block_Widget_Form
{
	
  protected function _prepareForm()
  {
   	
     $form = new Varien_Data_Form(); 
     $this->setForm($form);
	 $csmarketplace = Mage::getModel('csmarketplace/vproducts');
	 $csmarketplace_row =  $csmarketplace->load($this->getRequest()->getParam('id'));
     $fieldset = $form->addFieldset('csmarketplace_general', array('legend'=>Mage::helper('csmarketplace')->__("Other")));
     
		$fieldset->addField('assigned_to', 'text', array(
            'label'     => Mage::helper('csmarketplace')->__('Assigned To'),
	 		'value'      =>  $csmarketplace_row->getAssigned_to(),
	 		'name'      => 'assigned_to'

        ));
		$fieldset->addField('date_modified', 'text', array(
            'label'     => Mage::helper('csmarketplace')->__('Date Modified'),
	 		'value'      =>  $csmarketplace_row->getDate_modified(),
	 		'name'      => 'date_modified'

        ));
		$fieldset->addField('date_created', 'text', array(
            'label'     => Mage::helper('csmarketplace')->__('Date Created'),
	 		'value'      =>  $csmarketplace_row->getDate_created(),
	 		'name'      => 'date_created'

        ));
		
		
	 	
	 	 
      if ( Mage::getSingleton('adminhtml/session')->getCsMarketplaceData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCsMarketplaceData());
          Mage::getSingleton('adminhtml/session')->setCsMarketplaceData(null);
      } elseif ( Mage::registry('CsMarketplace_data') ) {
          $form->setValues(Mage::registry('csmarketplace_data')->getData());
      }
      return parent::_prepareForm();
  }
 
}