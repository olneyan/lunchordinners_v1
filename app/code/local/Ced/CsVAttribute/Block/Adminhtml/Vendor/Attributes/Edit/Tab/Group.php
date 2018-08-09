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
class Ced_CsVAttribute_Block_Adminhtml_Vendor_Attributes_Edit_Tab_Group extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Preparing global layout
	 *
	 * @return Mage_Core_Block_Abstract
	 */
	protected function _prepareLayout()
    {
        $this->setChild('csmarketplace_add_new_group_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Add New'),
                    'onclick'   => "addGroup();",
                    'class'     => 'save',
                    ))
                );
        return parent::_prepareLayout();
    }
	
    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
	protected function _prepareForm(){ 	
		$form = new Varien_Data_Form(); 
		$this->setForm($form);
		$vendor = Mage::getModel('csmarketplace/vendor');
		$entityTypeId  = $vendor->getEntityTypeId();
		$setIds = Mage::getResourceModel('eav/entity_attribute_set_collection')->setEntityTypeFilter($entityTypeId)->getAllIds();
		$setId = isset($setIds[0])?$setIds[0]:0;
	
		$options = $this->getGroupOptions($setId,true);
		$attribute = Mage::registry('entity_attribute');
		$groupName = '';
		//$installer = new Ced_CsVAttribute_Model_Resource_Setup();
		//$attributeGroupObject = new Varien_Object($installer->getAttributeGroup($entityTypeId ,$attributeSetProductsId,"Prices"));
		foreach($this->getGroupOptions($setId,false) as $id=>$label) {
			$attributes = Mage::getModel('eav/entity_attribute')->getCollection()->setAttributeGroupFilter($id)->getAllIds();
			if(in_array($attribute->getId(),$attributes)){
				$groupName = $label;
				break;
			}
		}

		$fieldset = $form->addFieldset('group_fieldset', array('legend'=>Mage::helper('csmarketplace')->__('Group')));    
		
		
		$element = $fieldset->addField('group_select', 'select',
					array(
						'name'      => "group",
						'label'     => Mage::helper('csmarketplace')->__('Group'),
						'required'  => true,
						'values'    => $options,
						'after_element_html' => $this->getChildHtml('csmarketplace_add_new_group_button'),
						)
					);
		if(strlen($groupName)) {
			$element->setValue($groupName);
		}
		/* if( Mage::getSingleton('adminhtml/session')->getEntityAttribute()){
			$form->setValues(Mage::getSingleton('adminhtml/session')->getEntityAttribute());
			Mage::getSingleton('adminhtml/session')->setEntityAttribute(null);
		}elseif( Mage::registry('entity_attribute')){
			$form->setValues(Mage::registry('entity_attribute')->getData());
		} */
		return parent::_prepareForm();
	}
	
	/**
	 * Retrieve group options.
	 *
	 * @return array
	 */
	protected function getGroupOptions($setId,$flag = false) {
		$groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
					->setAttributeSetFilter($setId);
		if(version_compare(Mage::getVersion(), '1.6', '<')) {
			$groupCollection->getSelect()->order('main_table.sort_order');
		}
		else{
			$groupCollection->setSortOrder()
			->load();
		}
		$options = array();
		if($flag) {
			foreach ($groupCollection as $group) {
				$options[$group->getAttributeGroupName()] = $this->__($group->getAttributeGroupName());		
			}
		} else {
			foreach ($groupCollection as $group) {
				$options[$group->getId()] = $group->getAttributeGroupName();		
			}
		}
		return 	$options;
	}	
}