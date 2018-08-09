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
class Ced_CsMarketplace_Block_Adminhtml_Vendor_Entity_Edit_Tab_Payment_Methods extends Mage_Adminhtml_Block_Widget_Form
{
	/**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
		$vendor = Mage::registry('vendor_data');
		if ($vendor) {
			$methods = $vendor->getPaymentMethods();
			$form = new Varien_Data_Form();
			if(count($methods) >0 ) {
				$cnt = 1;
				foreach($methods as $code=>$method) {
					$fields = $method->getFields();
					/* print_r($fields); continue; */
					if (count($fields) > 0) {
						$fieldset = $form->addFieldset('csmarketplace_'.$code, array('legend'=>$method->getLabel('label')));
						foreach ($fields as $id=>$field) {
							$key = strtolower(Ced_CsMarketplace_Model_Vsettings::PAYMENT_SECTION.'/'.$method->getCode().'/'.$id);
							$value = '';
							if((int)$vendor->getId()){
								$key_tmp=Mage::helper('csmarketplace')->getTableKey('key');
								$vendor_id=Mage::helper('csmarketplace')->getTableKey('vendor_id');
								$setting = Mage::getModel('csmarketplace/vsettings')->loadByField(array($key_tmp,$vendor_id),array($key,(int)$vendor->getId()));
								if($setting) $value = $setting->getValue();
							}
							$fieldset->addField($method->getCode().$method->getCodeSeparator().$id, 'label', array(
									'label'     												=> $method->getLabel($id),
									'value'      												=>  isset($field['values'])?$this->getLabelByValue($value,$field['values']):$value,
									'name'      												=> 'groups['.$method->getCode().']['.$id.']',
									isset($field['class'])?'class':''   						=> isset($field['class'])?$field['class']:'',
									isset($field['required'])?'required':''    					=> isset($field['required'])?$field['required']:'',
									isset($field['onchange'])?'onchange':''    					=> isset($field['onchange'])?$field['onchange']:'',
									isset($field['onclick'])?'onclick':''    					=> isset($field['onclick'])?$field['onclick']:'',
									isset($field['href'])?'href':''								=> isset($field['href'])?$field['href']:'',
									isset($field['target'])?'target':''							=> isset($field['target'])?$field['target']:'',
									isset($field['values'])? 'values': '' 						=> isset($field['values'])? $field['values']: '',
									//isset($field['after_element_html'])? 'after_element_html':''=> isset($field['after_element_html'])? '<div><small>'.$field['after_element_html'].'</small></div>': '',
							));
						}
						$cnt++;
					}	
				}
			}
			$this->setForm($form);
		}
		return $this;
    }
	
	/**
	 * retrieve label from value
	 * @param array
	 * @return string
	 */
	protected function getLabelByValue($value = '', $values = array()) {
		foreach($values as $option) {
			if(isset($option['value']) && $option['value'] == $value && $option['label']){
				return $option['label'];
				break;
			}
		}
		return $value;
	}
}