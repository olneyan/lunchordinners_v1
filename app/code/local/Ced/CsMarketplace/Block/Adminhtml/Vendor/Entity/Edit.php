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
 * @category    Ced;
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_CsMarketplace_Block_Adminhtml_Vendor_Entity_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        
                 
        $this->_objectId = 'vendor_id';
        $this->_blockGroup = 'csmarketplace';
        $this->_controller = 'adminhtml_vendor_entity';
        
		parent::__construct();
		
        $this->_updateButton('save', 'label', Mage::helper('csmarketplace')->__('Save Vendor'));
        $this->_updateButton('delete', 'label', Mage::helper('csmarketplace')->__('Delete Vendor'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('csmarketplace')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        
        if( Mage::registry('vendor_data') && Mage::registry('vendor_data')->getId() ) {
	        $vendorId = Mage::registry('vendor_data')->getId();
	        $url = '';
	        $button = '';
	        $class = '';
			$vendor_id=Mage::helper('csmarketplace')->getTableKey('vendor_id');
	        $model = Mage::getModel('csmarketplace/vshop')->loadByField(array($vendor_id),array($vendorId));
	        
	        if($model->getId()!='' && $model->getShopDisable() == Ced_CsMarketplace_Model_Vshop::ENABLED){
	        	$url =  $this->getUrl('*/*/massDisable', array('vendor_id' => $vendorId, 'shop_disable'=>Ced_CsMarketplace_Model_Vshop::DISABLED, 'inline'=>1));
	        	$url = "if(confirm('".$this->__('Are you sure you want to Disable?')."'))setLocation('".$url."')";
	        	$button = Mage::helper('csmarketplace')->__('Disable Vendor Shop');
	        	$class='delete';
	        }
	        else if($model->getId()!='' && $model->getShopDisable() == Ced_CsMarketplace_Model_Vshop::DISABLED) {
	        	$url =  $this->getUrl('*/*/massDisable', array('vendor_id' => $vendorId, 'shop_disable'=>Ced_CsMarketplace_Model_Vshop::ENABLED, 'inline'=>1));
	        	$url = "if(confirm('".$this->__('Are you sure you want to Enable?')."')) setLocation('".$url."')";
	        	$button = Mage::helper('csmarketplace')->__('Enable Vendor Shop');
	        	$class='save';
	        }
	        else{
	        	$url =  $this->getUrl('*/*/massDisable', array('vendor_id' => $vendorId, 'shop_disable'=>Ced_CsMarketplace_Model_Vshop::DISABLED, 'inline'=>1));
	        	$url = "if(confirm('".$this->__('Are you sure you want to Disable?')."')) setLocation('".$url."')";
	        	$button = Mage::helper('csmarketplace')->__('Disable Vendor Shop');
	        	$class='delete';
	        }
	        
	        $this->_addButton('shop_disable', array(
	        		'label'     => $button,
	        		'onclick'   => $url,
	        		'class'     => $class,
	        ), -100);
        }

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('vendor_data') && Mage::registry('vendor_data')->getId() ) {
            return Mage::helper('csmarketplace')->__('Edit Store "%s" ', $this->htmlEscape(Mage::registry('vendor_data')->getPublicName()));
        } else {
            return Mage::helper('csmarketplace')->__('Add Vendor');
        }
    }
}