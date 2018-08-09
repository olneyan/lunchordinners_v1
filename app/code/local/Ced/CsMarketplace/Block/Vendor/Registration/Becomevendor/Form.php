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

/**
 * Dashboard CsMarketplace Approval
 *
 * @category   Ced
 * @package    Ced_CsMarketplace
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */

class Ced_CsMarketplace_Block_Vendor_Registration_Becomevendor_Form extends Ced_CsMarketplace_Block_Vendor_Abstract
{
	/**
	 * Get collection of Vendor Attributes 
	 * @return Mage_Eav_Model_Entity_Attribute
	 */
	public function getRegistrationAttributes($storeId = null){
		if($storeId == null) $storeId = Mage::app()->getStore()->getId();
		$attributes =  Mage::getModel('csmarketplace/vendor_attribute')
							->setStoreId($storeId)
							->getCollection()
							->addFieldToFilter('use_in_registration',array('gt'=>0))
							->setOrder('position_in_registration','ASC');
		Mage::dispatchEvent('ced_csmarketplace_registration_attributes_load_after',array('attributes'=>$attributes));
		return $attributes;
	}
	
	 /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changin layout
     *
     * @return Ced_CsMarketplace_Block_Vendor_Abstract
     */
	protected function _prepareLayout() {
        Varien_Data_Form::setElementRenderer(
            $this->getLayout()->createBlock('csmarketplace/widget_form_renderer_element')
        );
        Varien_Data_Form::setFieldsetRenderer(
            $this->getLayout()->createBlock('csmarketplace/widget_form_renderer_fieldset')
        );
        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock('csmarketplace/widget_form_renderer_fieldset_element')
        );

        return parent::_prepareLayout();
    }
	
	/**
     * Get form object
     *
     * @return Varien_Data_Form
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * Get form object
     *
     * @deprecated deprecated since version 1.2
     * @see getForm()
     * @return Varien_Data_Form
     */
    public function getFormObject()
    {
        return $this->getForm();
    }

    /**
     * Get form HTML
     *
     * @return string
     */
    public function getFormHtml()
    {
        if (is_object($this->getForm())) {
            return $this->getForm()->getHtml();
        }
        return '';
    }

    /**
     * Set form object
     *
     * @param Varien_Data_Form $form
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    public function setForm(Varien_Data_Form $form)
    {
        $this->_form = $form;
        $this->_form->setParent($this);
        $this->_form->setBaseUrl(Mage::getBaseUrl());
        return $this;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $vendorformFields = $this->getRegistrationAttributes();
		$form = new Varien_Data_Form();

		$form->setAction($this->getUrl('*/*/save',array('_secure'=>true)))
			->setId('form-validate')
			->setMethod('POST')
			->setEnctype('multipart/form-data')
			->setUseContainer(false);
		$model = $this->getVendorId()?$this->getVendor()->getData():array();
		$id = $this->getVendorId();
		foreach($vendorformFields as $attribute) {
				$ascn = 0;
				
				if (!$attribute || ($attribute->hasUseInRegistration() && !$attribute->getUseInRegistration())) {
					continue;
				}
				if ($inputType = $attribute->getFrontend()->getInputType()) {
					if(!isset($model[$attribute->getAttributeCode()]) || (isset($model[$attribute->getAttributeCode()]) && !$model[$attribute->getAttributeCode()])){ $model[$attribute->getAttributeCode()] = $attribute->getDefaultValue();  }
					if($inputType == 'boolean') $inputType = 'select';
					if(in_array($attribute->getAttributeCode(),Ced_CsMarketplace_Model_Form::$VENDOR_REGISTRATION_RESTRICTED_ATTRIBUTES)) {
						continue;
					}
					
					$fieldType  =  $inputType;
					
					$rendererClass  = $attribute->getFrontend()->getInputRendererClass();
					if (!empty($rendererClass)) {
						$fieldType  = $inputType . '_' . $attribute->getAttributeCode();
						$form->addType($fieldType, $rendererClass);
					}
					$afterHtmlShopUrl = '<div id="advice-validate-shopurl-ced-shop-url-field" class="validation-advice" style="display:none;">Shop Url is not available.</div>
										<span class="note"><small style="font-size: 10px;">'.Mage::helper('csmarketplace')->__('Please enter your Shop URL Key. For example "my-shop-url".').'</small></span>
										<div style="clear:both"></div>
										<span style="float:left;" id="ced-csmarketplace-availability" >&nbsp;</span>
										<span style="float:left;" id="ced-csmarketplace-availability-suggestion" >&nbsp;</span>
										<div style="clear:both"></div>';
					$element = $form->addField('ced-'.str_replace('_','-',$attribute->getAttributeCode()).'-field', $fieldType,
						array(
							'container_id' => 'ced-'.str_replace('_','-',$attribute->getAttributeCode()),
							'name'      => "vendor[".$attribute->getAttributeCode()."]",
							'label'     => $attribute->getStoreLabel()?$attribute->getStoreLabel():$attribute->getFrontend()->getLabel(),
							'class'     => 'form-control '.$attribute->getFrontend()->getClass(),
							'required'  => $attribute->getIsRequired(),
							'placeholder' => $attribute->getStoreLabel()?$attribute->getStoreLabel():$attribute->getFrontend()->getLabel(),
							'value'  => $model[$attribute->getAttributeCode()],
							'after_element_html' => $attribute->getAttributeCode() == 'shop_url'?$afterHtmlShopUrl:'',
						)
					)
					->setEntityAttribute($attribute);	

					if ($inputType == 'select') {
						$element->setValues($attribute->getSource()->getAllOptions(true,true));
					} else if ($inputType == 'multiselect') {
						$element->setValues($attribute->getSource()->getAllOptions(false,true));
						$element->setCanBeEmpty(true);
					} else if ($inputType == 'date') {
						$element->setImage($this->getSkinUrl('images/calendar.gif'));
						$element->setFormat(Mage::app()->getLocale()->getDateFormatWithLongYear());
					} else if ($inputType == 'multiline') {
						$element->setLineCount($attribute->getMultilineCount());
					}
				}
			}
			$this->setForm($form);
			return $this;
    }

    /**
     * This method is called before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _beforeToHtml()
    {
        $this->_prepareForm();
        $this->_initFormValues();
        return parent::_beforeToHtml();
    }

    /**
     * Initialize form fields values
     * Method will be called after prepareForm and can be used for field values initialization
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _initFormValues()
    {
        return $this;
    }

    /**
     * Set Fieldset to Form
     *
     * @param array $attributes attributes that are to be added
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param array $exclude attributes that should be skipped
     */
    protected function _setFieldset($attributes, $fieldset, $exclude=array())
    {
        $this->_addElementTypes($fieldset);
        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            if (!$attribute || ($attribute->hasIsVisible() && !$attribute->getIsVisible())) {
                continue;
            }
            if ( ($inputType = $attribute->getFrontend()->getInputType())
                 && !in_array($attribute->getAttributeCode(), $exclude)
                 && ('media_image' != $inputType)
                 ) {

                $fieldType      = $inputType;
                $rendererClass  = $attribute->getFrontend()->getInputRendererClass();
                if (!empty($rendererClass)) {
                    $fieldType  = $inputType . '_' . $attribute->getAttributeCode();
                    $fieldset->addType($fieldType, $rendererClass);
                }

                $element = $fieldset->addField($attribute->getAttributeCode(), $fieldType,
                    array(
                        'name'      => $attribute->getAttributeCode(),
                        'label'     => $attribute->getFrontend()->getLabel(),
                        'class'     => $attribute->getFrontend()->getClass(),
                        'required'  => $attribute->getIsRequired(),
                        'note'      => $attribute->getNote(),
                    )
                )
                ->setEntityAttribute($attribute);

                $element->setAfterElementHtml($this->_getAdditionalElementHtml($element));

                if ($inputType == 'select') {
                    $element->setValues($attribute->getSource()->getAllOptions(true, true));
                } else if ($inputType == 'multiselect') {
                    $element->setValues($attribute->getSource()->getAllOptions(false, true));
                    $element->setCanBeEmpty(true);
                } else if ($inputType == 'date') {
                    $element->setImage($this->getSkinUrl('images/calendar.gif'));
                    $element->setFormat(Mage::app()->getLocale()->getDateFormatWithLongYear());
                } else if ($inputType == 'datetime') {
                    $element->setImage($this->getSkinUrl('images/calendar.gif'));
                    $element->setTime(true);
                    $element->setStyle('width:50%;');
                    $element->setFormat(
                        Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
                    );
                } else if ($inputType == 'multiline') {
                    $element->setLineCount($attribute->getMultilineCount());
                }
            }
        }
    }

    /**
     * Add new element type
     *
     * @param Varien_Data_Form_Abstract $baseElement
     */
    protected function _addElementTypes(Varien_Data_Form_Abstract $baseElement)
    {
        $types = $this->_getAdditionalElementTypes();
        foreach ($types as $code => $className) {
            $baseElement->addType($code, $className);
        }
    }

    /**
     * Retrieve predefined additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array();
    }

    /**
     * Enter description here...
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getAdditionalElementHtml($element)
    {
        return '';
    }
}
