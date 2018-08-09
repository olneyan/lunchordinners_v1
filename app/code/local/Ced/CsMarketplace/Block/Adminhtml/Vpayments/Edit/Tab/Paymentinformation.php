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
class Ced_CsMarketplace_Block_Adminhtml_Vpayments_Edit_Tab_Paymentinformation extends Mage_Adminhtml_Block_Widget_Form
{	
	 protected function _prepareForm()
    {
		$params = $this->getRequest()->getParams();
		$type = isset($params['type']) && in_array($params['type'],array_keys(Ced_CsMarketplace_Model_Vpayment::getStates()))?$params['type']:Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_CREDIT;
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('form_fields',        
		       array('legend'=>Mage::helper('csmarketplace')->__('Transaction Information')));
        $vendorId = $this->getRequest()->getParam('vendor_id',0);
		$base_amount = $this->getRequest()->getPost('total',0);
		$amountDesc = $this->getRequest()->getPost('orders');
		
		$vendor = Mage::getModel('csmarketplace/vendor')->getCollection()->toOptionArray($vendorId);
		$ascn = isset($vendor[$vendorId])?$vendor[$vendorId]:'';
		$fieldset->addField('vendor_id', 'hidden', array(
		  'name'      => 'vendor_id',
		  'value' 	  => $vendorId,
		));
		$fieldset->addField('amount_desc', 'hidden', array(
		  'name'      => 'amount_desc',
		  'value' 	  => json_encode($amountDesc),
		));
		$fieldset->addField('currency', 'hidden', array(
				'name'      => 'currency',
				'value' 	  => Mage::app()->getBaseCurrencyCode(),
		));
		$fieldset->addField('vendor_name', 'label', array(
		  'label' => Mage::helper('csmarketplace')->__('Vendor'), 
		  'after_element_html' => '<a target="_blank" href="'.Mage::helper('adminhtml')->getUrl('adminhtml/adminhtml_vendor/edit/',array('vendor_id'=>$vendorId, '_secure'=>true)).'" title="'.$ascn.'">'.$ascn.'</a>',
		));
		$fieldset->addField('base_amount', 'text', array(
			  'label'     => Mage::helper('csmarketplace')->__('Amount'),
			  'class'     => 'required-entry validate-greater-than-zero',
			  'required'  => true,
			  'name'      => 'base_amount',
			  'value'	  => $base_amount,
			  'readonly'  => 'readonly',
			  'after_element_html' => '<b>['.Mage::app()->getBaseCurrencyCode().']</b><small><i>'.$this->__('Readonly field').'</i>.</small>',
		  ));
		
		$fieldset->addField('payment_code', 'select', array(
		  'label'     => Mage::helper('csmarketplace')->__('Payment Method'),
		  'class'     => 'required-entry',
		  'required'  => true,
		  'onchange'  => !$type?'vpayment.changePaymentDatail(this)':'vpayment.changePaymentToOther(this)',
		  'name'      => 'payment_code',
		  'values' => Mage::getModel('csmarketplace/vendor')->getPaymentMethodsArray($vendorId),
		  'after_element_html' => '<small id="beneficiary-payment-detail">'.$this->__('Select Payment Method').'</small><script type="text/javascript"> var vpayment = new ced_csmarketplace("'.Mage::helper('adminhtml')->getUrl('*/*/getdetail',array('vendor_id'=>$vendorId)).'","");</script>',
		));
			
		$fieldset->addField('payment_code_other', 'text', array(
		  'label'     => '',
		  'style'	  => 'display: none;',
		  'disbaled'  => 'true',
		  'name'      => 'payment_code',
		));
		
		$fieldset->addField('base_fee', 'text', array(
		  'label'     => Mage::helper('csmarketplace')->__('Adjustment Amount'),
		  'class'     => 'validate-number',
		  'required'  => false,
		  'name'      => 'base_fee',
		  'after_element_html' => '<b>['.Mage::app()->getBaseCurrencyCode().']</b><small>'.$this->__('Enter adjustment amount in +/- (if any)').'</small>',
		));
		
		
		$fieldset->addField('transaction_id', 'text', array(
		  'label'     => Mage::helper('csmarketplace')->__('Transaction Id'),
		  'class'     => 'required-entry',
		  'required'  => true,
		  'name'      => 'transaction_id',
		  'after_element_html' => '<small>'.$this->__('Enter transaction id').'</small>',
		));
		
		
		$fieldset->addField('textarea', 'textarea', array(
		  'label'     => Mage::helper('csmarketplace')->__('Notes'),
		  'required'  => false,
		  'name'      => 'notes',
		));

		if($type == Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_CREDIT){
			$fieldset->addField('amount_description', 'label', array(
			  'label'     => Mage::helper('csmarketplace')->__('Amount Description'),
			  'required'  => true,
			  'name'      => 'amount_description',
			  'after_element_html' => $this->getAmountDescriptionHtml()
			));
		}

        return parent::_prepareForm();
		
		}
		
	public function getContinueUrl()
    {
        return $this->getUrl('*/*/*', array(
            '_current'  => true,
			'_secure'	=> true,
            'vendor_id'       => '{{vendor_id}}'
        ));
    }

    /**
     * Get form HTML
     *
     * @return string
     */
    public function getAmountDescriptionHtml()
    {
    	$html='';
        $object = new Varien_Object();
        $object->setBlocks(array());
        Mage::dispatchEvent('transaction_amountdescription_edit_prepare_blocks',array('container'=>$object));
        $html = $this->prepareAmountDescriptionHtml($object);
        $object->setHtml($html);
        Mage::dispatchEvent('transaction_amountdescription_edit_render_before',array('container'=>$object));
        return $object->getHtml();
    }

    public function prepareAmountDescriptionHtml($object){
    	$blocks = $object->getBlocks();
    	$html = '';
    	foreach($blocks as $block)
    	{
    		$html.= $this->getLayout()->createBlock($block)->getHtml();  		
    	}
    	return $html;
    }
}