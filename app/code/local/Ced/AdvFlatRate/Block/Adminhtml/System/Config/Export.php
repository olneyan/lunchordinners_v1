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
 * @package     Ced_AdvFlatRate 
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com> 
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Export CSV button for Advance Flat Rate
 */
 
class Ced_AdvFlatRate_Block_Adminhtml_System_Config_Export extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected  function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
		$this->setElement($element);
        $buttonBlock = $this->getLayout()->createBlock('adminhtml/widget_button');

        $params = array(
            'website' => $buttonBlock->getRequest()->getParam('website')
        );

        $data = array(
            'label'     => Mage::helper('adminhtml')->__('Export CSV'),
            'onclick'   => 'setLocation(\''.Mage::helper('adminhtml')->getUrl("*/*/exportRatesCsv", $params).'advancerate.csv\' )',
            'class'     => '',
        );

        $html = $buttonBlock->setData($data)->toHtml();

        return $html;
    }
}
