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
class Ced_CsVAttribute_Block_Adminhtml_Vendor_Attribute_Renderer_Multiimage extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
	protected $_defaultRenderer;
    protected $_actionRenderer;
    protected $_priceTypeRenderer;

	protected function _getUploadRenderer()
    {
        if (!$this->_actionRenderer) {
            $this->_actionRenderer = $this->getLayout()->createBlock(
                'csvattribute/adminhtml_vendor_attribute_renderer_upload', '',
                array('is_render_to_js_template' => true)
            );
            $this->_actionRenderer->setExtraParams('style="width:178px"');
        }
        return $this->_actionRenderer;
    }

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn('title', array(
            'label' => Mage::helper('catalog')->__('Title'),
            'style' => 'width: 123px;',
        ));

        $this->addColumn('multiimage', array(
            'label' => Mage::helper('cscommission')->__('Upload'),
			'renderer' => $this->_getUploadRenderer(),
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('cscommission')->__('Add New');
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getProductTypesRenderer()->calcOptionHash($row->getData('types')),
            'selected="selected"'
        );
		$row->setData(
            'option_extra_attr_' . $this->_getCalculationMethodRenderer()->calcOptionHash($row->getData('method')),
            'selected="selected"'
        );
        //Zend_Debug::dump($row, '_prepareArrayRow', true);
    }

	 /**
     * Get the grid and scripts contents
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html = parent::_getElementHtml($element);
		$html .= '<input type="hidden" name="product_dummy" id="'.$element->getHtmlId().'" />';
        return $html;
    }
}