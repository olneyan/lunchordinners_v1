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

class Ced_CsMarketplace_Block_Adminhtml_Vproducts_Edit_Tab_More extends Mage_Adminhtml_Block_Widget_Form
{
	
  protected function _prepareForm()
  {
   	
     $form = new Varien_Data_Form(); 
     $this->setForm($form);
	 $csmarketplace = Mage::getModel('csmarketplace/vproducts');
	 $csmarketplace_row =  $csmarketplace->load($this->getRequest()->getParam('id'));
     $fieldset = $form->addFieldset('other_information', array('legend'=>Mage::helper('csmarketplace')->__("Other Information")));
      
	 	 $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('csmarketplace')->__('Status'),
	 		'class'     => 'required-entry',
	 		'value'      =>  $csmarketplace_row->getStatus(),
	 		'required'  => true,
	 		'name'      => 'status',
			'options'   => $this->getStatus(),

        )); 
		
		$fieldset->addField('status_description', 'text', array(
            'label'     => Mage::helper('csmarketplace')->__('Status Description'),
	 		'value'      =>  $csmarketplace_row->getStatus_description(),
	 		'name'      => 'status_description'

        ));
		$fieldset->addField('daily_orders', 'text', array(
            'label'     => Mage::helper('csmarketplace')->__('Daily Orders'),
	 		'value'      =>  $csmarketplace_row->getDaily_orders(),
	 		'name'      => 'daily_orders'

        ));
		$fieldset->addField('compaign', 'text', array(
            'label'     => Mage::helper('csmarketplace')->__('Compaign'),
	 		'value'      =>  $csmarketplace_row->getCompaign(),
	 		'name'      => 'compaign'

        ));
		$fieldset->addField('lead_source', 'text', array(
            'label'     => Mage::helper('csmarketplace')->__('Lead Source'),
	 		'value'      =>  $csmarketplace_row->getLead_source(),
	 		'name'      => 'lead_source'

        ));
		$fieldset->addField('lead_source_description', 'text', array(
            'label'     => Mage::helper('csmarketplace')->__('Lead Source Description'),
	 		'value'      =>  $csmarketplace_row->getLead_source_description(),
	 		'name'      => 'lead_source_description'

        ));
		$fieldset->addField('referred_by', 'text', array(
            'label'     => Mage::helper('csmarketplace')->__('Referred_by'),
	 		'value'      =>  $csmarketplace_row->getReferred_by(),
	 		'name'      => 'referred_by'

        ));


		 $fieldset->addField('have_china_sup', 'select', array(
            'label'     => Mage::helper('csmarketplace')->__('Have China Sup?'),
	 		'class'     => 'required-entry',
	 		'value'      =>  $csmarketplace_row->getHave_china_sup(),
	 		'required'  => true,
	 		'name'      => 'have_china_sup',
			'options'   => array('1'=>'Yes','0'=>'No'),

        )); 
	/* 	$fieldset->addField('have_china_sup', 'checkbox', array(
			'label'     => Mage::helper('csmarketplace')->__('Have China Sup?'),
			'onclick'   => 'this.value = this.checked ? 1 : 0;',
			'name'      => 'have_china_sup',
		)); */
		
	 	
	 	 
      if ( Mage::getSingleton('adminhtml/session')->getCsMarketplaceData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCsMarketplaceData());
          Mage::getSingleton('adminhtml/session')->setCsMarketplaceData(null);
      } elseif ( Mage::registry('csmarketplace_data') ) {
          $form->setValues(Mage::registry('csmarketplace_data')->getData());
      }
      return parent::_prepareForm();
  }
   public function getStatus()
  {	
	$status=array('new'=>'new');
  
  	return $status;
  }
 
}