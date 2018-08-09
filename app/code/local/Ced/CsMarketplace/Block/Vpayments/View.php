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
 * CsMarketplace Payments View block
 *
 * @category   Ced
 * @package    Ced_CsMarketplace
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Block_Vpayments_View extends Ced_CsMarketplace_Block_Vendor_Abstract
{
	/**
	 * Get Details of the payment
	 *
	 */
	
	public function getVpayment(){
		$payment = Mage::registry('current_vpayment');
		return $payment;
	}
	
	/**
     * @return Ced_CsMarketplace_Block_Adminhtml_Vpayments_Details_Form
     */
    protected function _prepareForm()
    {
        list($model,$fieldsets) = $this->loadFields();
        $form = new Varien_Data_Form();
		//print_r($fieldsets);die;
        foreach ($fieldsets as $key => $data) {
            $fieldset = $form->addFieldset($key, array('legend' => $data['legend']));
			foreach ($data['fields'] as $id => $info) {
				if($info['type']=='link'){
					$fieldset->addField($id, $info['type'], array(
						'name'  => $id,
						'label' => $info['label'],
						'title' => $info['label'],
						'href' => $info['href'],
						'value' => isset($info['value']) ? $info['value'] : $model->getData($id),
						'text'  => isset($info['text']) ? $info['text'] : $model->getData($id),
						'after_element_html' => isset($info['after_element_html']) ? $info['after_element_html'] : '',
					));
				}else{
					$fieldset->addField($id, $info['type'], array(
						'name'  => $id,
						'label' => $info['label'],
						'title' => $info['label'],
						'value' => isset($info['value']) ? $info['value'] : $model->getData($id),
						'text'  => isset($info['text']) ? $info['text'] : $model->getData($id),
						'after_element_html' => isset($info['after_element_html']) ? $info['after_element_html'] : '',
						
					));
				}
            }
        }
        $this->setForm($form);
        return $this;
    }
	
	protected function loadFields() {
		$model = $this->getVpayment();
		$renderOrderDesc = $this->getLayout()->createBlock('csmarketplace/adminhtml_vpayments_grid_renderer_orderdesc');
		$renderName = new Ced_CsMarketplace_Block_Adminhtml_Vorders_Grid_Renderer_Vendorname(true);
        if ($model->getBaseCurrency() != $model->getCurrency()) {
			$fieldsets = array(
				'beneficiary_details' => array(
					'fields' => array(
						'vendor_id' => array('label'=>Mage::helper('csmarketplace')->__('Vendor Name'),'text' => $renderName->render($model), 'type' => 'note'),
						'payment_code' => array('label' => Mage::helper('csmarketplace')->__('Payment Method'),'type'	=> 'label', 'value' => $model->getData('payment_code')),
						'payment_detail' => array('label' => Mage::helper('csmarketplace')->__('Beneficiary Details'), 'type' => 'note', 'text' => $model->getData('payment_detail')),
					),
					'legend' => Mage::helper('csmarketplace')->__('Beneficiary Details')
				),

				'order_details' => array(
					'fields' => array(
						'amount_desc' => array(
							'label' => Mage::helper('csmarketplace')->__('Order Details'),
							'text' => $renderOrderDesc->render($model),
							'type'	=> 'note',
						),                   
					),
					'legend' => Mage::helper('csmarketplace')->__('Order Details')
				),

				'payment_details' => array(
					'fields' => array(
						'transaction_id' => array('label' => Mage::helper('csmarketplace')->__('Transaction ID#'),'type'	=> 'label', 'value' => $model->getData('transaction_id')),
						'created_at' => array(
							'label' => Mage::helper('csmarketplace')->__('Transaction Date'),
							'value' => $model->getData('created_at'),
							'type'	=> 'label',
						),
						'payment_method' => array(
							'label' => Mage::helper('csmarketplace')->__('Transaction Mode'),
							'value' => Mage::helper('csmarketplace/acl')->getDefaultPaymentTypeLabel($model->getData('payment_method')),
							'type'	=> 'label',
						),
						'transaction_type' => array(
							'label' => Mage::helper('csmarketplace')->__('Transaction Type'),
							'value' => ($model->getData('transaction_type') == 0)?Mage::helper('csmarketplace')->__('Credit Type'):Mage::helper('csmarketplace')->__('Debit Type'),
							'type'	=> 'label',
						),
						'amount' => array(
							'label' => Mage::helper('csmarketplace')->__('Amount'),
							'value' => Mage::app()->getLocale()->currency($model->getCurrency())->toCurrency($model->getData('amount')),
							'type'	=> 'label',
						),
						'base_amount' => array(
							'label' => '&nbsp;',
							'value' => '['.Mage::app()->getLocale()->currency($model->getBaseCurrency())->toCurrency($model->getData('base_amount')).']',
							'type'	=> 'label',
						),
						'fee' => array(
							'label' => Mage::helper('csmarketplace')->__('Adjustment Amount'),
							'value' => Mage::app()->getLocale()->currency($model->getCurrency())->toCurrency($model->getData('fee')),
							'type'	=> 'label',
						),
						'base_fee' => array(
							'label' => '&nbsp;',
							'value' =>  '['.Mage::app()->getLocale()->currency($model->getBaseCurrency())->toCurrency($model->getData('base_fee')).']',
							'type'	=> 'label',
						),
						'net_amount' => array(
							'label' => Mage::helper('csmarketplace')->__('Net Amount'),
							'value' => Mage::app()->getLocale()->currency($model->getCurrency())->toCurrency($model->getData('net_amount')),
							'type'	=> 'label',
						),
						'base_net_amount' => array(
							'label' => '&nbsp;',
							'value' => '['.Mage::app()->getLocale()->currency($model->getBaseCurrency())->toCurrency($model->getData('base_net_amount')).']',
							'type'	=> 'label',
						),
						'notes' => array(
							'label' => Mage::helper('csmarketplace')->__('Notes'),
							'value' => $model->getData('notes'),
							'type'	=> 'label',
						),
					),
					'legend' => Mage::helper('csmarketplace')->__('Transaction Details')
				),
			);
		} else {
			$fieldsets = array(
				'beneficiary_details' => array(
					'fields' => array(
						'vendor_id' => array('label'=>Mage::helper('csmarketplace')->__('Vendor Name'),'text' => $renderName->render($model), 'type' => 'note'),
						'payment_code' => array('label' => Mage::helper('csmarketplace')->__('Payment Method'),'type'	=> 'label', 'value' => $model->getData('payment_code')),
						'payment_detail' => array('label' => Mage::helper('csmarketplace')->__('Beneficiary Details'), 'type' => 'note', 'text' => $model->getData('payment_detail')),
					),
					'legend' => Mage::helper('csmarketplace')->__('Beneficiary Details')
				),

				'order_details' => array(
					'fields' => array(
						'amount_desc' => array(
							'label' => Mage::helper('csmarketplace')->__('Order Details'),
							'text' => $renderOrderDesc->render($model),
							'type'	=> 'note',
						),                   
					),
					'legend' => Mage::helper('csmarketplace')->__('Order Details')
				),

				'payment_details' => array(
					'fields' => array(
						'transaction_id' => array('label' => Mage::helper('csmarketplace')->__('Transaction ID#'),'type'	=> 'label', 'value' => $model->getData('transaction_id')),
						'created_at' => array(
							'label' => Mage::helper('csmarketplace')->__('Transaction Date'),
							'value' => $model->getData('created_at'),
							'type'	=> 'label',
						),
						'payment_method' => array(
							'label' => Mage::helper('csmarketplace')->__('Transaction Mode'),
							'value' => Mage::helper('csmarketplace/acl')->getDefaultPaymentTypeLabel($model->getData('payment_method')),
							'type'	=> 'label',
						),
						'transaction_type' => array(
							'label' => Mage::helper('csmarketplace')->__('Transaction Type'),
							'value' => ($model->getData('transaction_type') == 0)?Mage::helper('csmarketplace')->__('Credit Type'):Mage::helper('csmarketplace')->__('Debit Type'),
							'type'	=> 'label',
						),
						'amount' => array(
							'label' => Mage::helper('csmarketplace')->__('Amount'),
							'value' => Mage::app()->getLocale()->currency($model->getCurrency())->toCurrency($model->getData('amount')),
							'type'	=> 'label',
						),
						'fee' => array(
							'label' => Mage::helper('csmarketplace')->__('Adjustment Amount'),
							'value' =>  Mage::app()->getLocale()->currency($model->getCurrency())->toCurrency($model->getData('fee')),
							'type'	=> 'label',
						),
						'net_amount' => array(
							'label' => Mage::helper('csmarketplace')->__('Net Amount'),
							'value' =>  Mage::app()->getLocale()->currency($model->getCurrency())->toCurrency($model->getData('net_amount')),
							'type'	=> 'label',
						),
						'notes' => array(
							'label' => Mage::helper('csmarketplace')->__('Notes'),
							'value' => $model->getData('notes'),
							'type'	=> 'label',
						),
					),
					'legend' => Mage::helper('csmarketplace')->__('Transaction Details')
				),
			);
		}
		
		return array($model,$fieldsets);
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
            $this->getLayout()->createBlock('csmarketplace/vpayments_view_element')
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
	
	/**
	 * back Link url
	 *   
	 */
	public function getBackUrl()
	{
		return $this->getUrl('*/*/index',array('_secure'=>true,'_nosid'=>true));
	}
	
	
}
