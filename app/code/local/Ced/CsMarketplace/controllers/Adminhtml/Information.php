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
class Ced_CsMarketplace_Block_Adminhtml_Vendor_Entity_Edit_Tab_Information extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm(){ 	
		$form = new Varien_Data_Form(); 
		$this->setForm($form);
		$model = Mage::registry('vendor_data')->getData();
		$vendor_id = $this->getRequest()->getParam('vendor_id',0);
		
		$group = $this->getGroup();
		$attributeCollection = $this->getGroupAttributes();
		
		$fieldset = $form->addFieldset('group_'.$group->getId(), array('legend'=>Mage::helper('csmarketplace')->__($group->getAttributeGroupName())));    
		
		foreach($attributeCollection as $attribute){
			$ascn = 0;
			if (!$attribute || ($attribute->hasIsVisible() && !$attribute->getIsVisible())) {
				continue;
			}
			if (/* ($attribute->getAttributeCode()=="email" || */ $attribute->getAttributeCode()=="website_id") {
				continue;
			}
			
			if ($inputType = $attribute->getFrontend()->getInputType()) {
				if($vendor_id && $attribute->getAttributeCode()=="created_at") {
					$inputType = 'label';
				} elseif (!$vendor_id && $attribute->getAttributeCode()=="created_at") {
					continue;
				}
				if(!isset($model[$attribute->getAttributeCode()]) || (isset($model[$attribute->getAttributeCode()]) && !$model[$attribute->getAttributeCode()])){ $model[$attribute->getAttributeCode()] = $attribute->getDefaultValue();  }
				 
				$showNewStatus = false;
				if($inputType == 'boolean') $inputType = 'select';
				if($attribute->getAttributeCode() == 'customer_id' && $vendor_id) {
					$options = $attribute->getSource()->toOptionArray($model[$attribute->getAttributeCode()]);
					if(count($options)) {
						$ascn = isset($options[0]['label'])?$options[0]['label']:0;
					}
				}
				if($attribute->getAttributeCode() == 'status' && !$vendor_id) {
					$showNewStatus = true;	
				}
				
				
				$fieldType      = $inputType;
				
				//echo get_class($attribute->getFrontend());die;
				$rendererClass  = $attribute->getFrontend()->getInputRendererClass();
				if (!empty($rendererClass)) {
					$fieldType  = $inputType . '_' . $attribute->getAttributeCode();
					$form->addType($fieldType, $rendererClass);
				}			
				
				$element = $fieldset->addField($attribute->getAttributeCode(), $fieldType,
					array(
						'name'      => "vendor[".$attribute->getAttributeCode()."]",
						'label'     => $attribute->getStoreLabel()?$attribute->getStoreLabel():$attribute->getFrontend()->getLabel(),
						'class'     => $attribute->getFrontend()->getClass(),
						'required'  => $attribute->getIsRequired(),
						'note'      => $ascn && $attribute->getAttributeCode() == 'customer_id' && $vendor_id?'':$attribute->getNote(),
						$ascn && ($attribute->getAttributeCode() == 'customer_id') && $vendor_id?'disabled':'' => $ascn && ($attribute->getAttributeCode() == 'customer_id') && $vendor_id?true:'',
						$ascn && ($attribute->getAttributeCode() == 'customer_id') && $vendor_id?'readonly':'' => $ascn && ($attribute->getAttributeCode() == 'customer_id') && $vendor_id?true:'',
						$ascn && ($attribute->getAttributeCode() == 'customer_id') && $vendor_id?'style':'' => $ascn && ($attribute->getAttributeCode() == 'customer_id') && $vendor_id?'display: none;':'',
						'value'    => $model[$attribute->getAttributeCode()],
					)
				)
				->setEntityAttribute($attribute);
				if ($attribute->getAttributeCode() == 'shop_url') {
					$element->setAfterElementHtml('<style type="text/css">
														span.ced-csmarketplace-availability-failed{
															background : url("'.Mage::getBaseUrl('skin').'frontend/base/default/images/ced/csmarketplace/error_msg_icon.gif");
															display: block;
															height: 17px;
															width: 16px;
															margin: 6px;
														}
														span.ced-csmarketplace-availability-passed {
															background : url("'.Mage::getBaseUrl('skin').'frontend/base/default/images/ced/csmarketplace/success_msg_icon.gif");
															display: block;
															height: 17px;
															width: 16px;
															margin: 6px;
														}
													</style>
													</td><td><span id="ced-csmarketplace-availability">&nbsp;</span>'
												  );
				} elseif($ascn && $attribute->getAttributeCode() == 'customer_id' && $vendor_id) {
					$element->setAfterElementHtml('<a target="_blank" href="'.Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit',array('id'=>$model[$attribute->getAttributeCode()], '_secure'=>true)).'" title="'.$ascn.'">'.$ascn.'</a>');
				}
				else if($attribute->getAttributeCode() == 'customer_id') {
					$element->setAfterElementHtml('<a target="_blank" href="'.Mage::helper('adminhtml')->getUrl('adminhtml/customer/new').'" title="Create New Customer">Create New Customer</a>');
				}
				else {
					$element->setAfterElementHtml('');
				}
				if ($inputType == 'select') {
					//print_r($attribute->getSource()->getAllOptions(true,false));die;
					
					$element->setValues($attribute->getSource()->getAllOptions(true,$showNewStatus));
				} else if ($inputType == 'multiselect') {
					$element->setValues($attribute->getSource()->getAllOptions(false,$showNewStatus));
					$element->setCanBeEmpty(true);
				} else if ($inputType == 'date') {
					$element->setImage($this->getSkinUrl('images/grid-cal.gif'));
					$element->setFormat(Mage::app()->getLocale()->getDateFormatWithLongYear());
				} else if ($inputType == 'multiline') {
					$element->setLineCount($attribute->getMultilineCount());
				}
				if($element->getExtType() == 'multifile') {
					try {
						$fieldRenderer = Mage::getBlockSingleton('csvattribute/adminhtml_vendor_attribute_renderer_multifile');
						$fieldRenderer->setForm($form);
						$fieldRenderer->setConfigData($model[$attribute->getAttributeCode()]);
						$element->setRenderer($fieldRenderer);
					} catch(Exception $e){}
				}
				if($element->getExtType() == 'multiimage') {
					try {
						$fieldRenderer = Mage::getBlockSingleton('csvattribute/adminhtml_vendor_attribute_renderer_multiimage');
						$fieldRenderer->setForm($form);
						$fieldRenderer->setConfigData($model[$attribute->getAttributeCode()]);
						$element->setRenderer($fieldRenderer);
					} catch(Exception $e){}
				}
			}
		}
		$element->setAfterElementHtml('<script type="text/javascript"> window.onload = function() { ced_csmarketplace = new ced_csmarketplace("'.$this->getUrl("*/*/checkAvailability",array("_secure"=>true,'id'=>$vendor_id)).'","edit_form"); } </script>');
		
		
		
		
		$html="<script>new RegionUpdater('country_id', 'region', 'region_id',".Mage::helper('directory')->getRegionJson().", undefined, 'postcode');
				window.onload = function() {
				  	var reg= document.getElementById('region').value;
					var reg_id= document.getElementById('region_id').value;
					if(reg){
						document.getElementById('region_id').parentNode.parentNode.style.display='none'; 
						document.getElementById('region').parentNode.parentNode.style.display=''; 
					}else if(reg_id){
						document.getElementById('region').parentNode.parentNode.style.display='none';
						document.getElementById('region_id').parentNode.parentNode.style.display='';  
					}
				};
				 country_id.onchange = function()
			    {
			       	var country_id= document.getElementById('country_id').value;
				
					var url= '".$this->getUrl('adminhtml/adminhtml_vendor/country/cid/".country_id."')."';
        			
 					new Ajax.Request(url, {
			            method: 'get',
			            onComplete: function(stateform) {
			            	if(stateform.responseText==true){
								document.getElementById('region').parentNode.parentNode.style.display='none';
								document.getElementById('region_id').parentNode.parentNode.style.display='';
							}else{
								document.getElementById('region_id').parentNode.parentNode.style.display='none'; 
								document.getElementById('region').parentNode.parentNode.style.display=''; 
							}
			            }
			        });	 
	
				
					
			    }
			</script>";
		
		
		
		$element->setAfterElementHtml($html);
		
		/* if( Mage::getSingleton('adminhtml/session')->getVendorData()){
			$form->setValues(Mage::getSingleton('adminhtml/session')->getVendorData());
			Mage::getSingleton('adminhtml/session')->setVendorData(null);
		}elseif( Mage::registry('vendor_data')){
			$form->setValues(Mage::registry('vendor_data')->getData());
		} */
		/* print_r($model); */
		return parent::_prepareForm();
	}
}