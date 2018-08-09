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
class Ced_CsMarketplace_Block_Adminhtml_Vpayments_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	protected $_availableMethods = null;
	
    public function __construct()
    {
		$amount = $this->getRequest()->getPost('total',0);
        parent::__construct();
                 
        $this->_objectId = 'paymentid';
        $this->_blockGroup = 'csmarketplace';
        $this->_controller = 'adminhtml_vpayments';
        $url = Mage::helper('core/http')->getHttpReferer() && preg_match('/\/index\//i',Mage::helper('core/http')->getHttpReferer()) ? Mage::helper('core/http')->getHttpReferer()  : Mage::helper('adminhtml')->getUrl('*/adminhtml_vendororder/index');
		$this->_updateButton('back', 'onclick', "setLocation('".$url."')");
		if($amount) {
			$this->_updateButton('save', 'label', Mage::helper('csmarketplace')->__('Pay').' '.Mage::helper('csmarketplace/acl')->getDefaultPaymentTypeLabel());
		} else {
			$this->_removeButton('save');
			$this->_addButton('saveandcontinue', array(
				'label'     => Mage::helper('csmarketplace')->__('Continue'),
				'onclick'   => 'saveAndContinueEdit()',
				'class'     =>  count($this->availableMethods()) == 0?'save disabled':'save',
				count($this->availableMethods()) == 0?'disabled':''=> count($this->availableMethods()) == 0?true:'',
			), -100);
			
			 $this->_formScripts[] = " function saveAndContinueEdit(){
											editForm.submit($('edit_form').action+'back/edit/'+csaction);
									 }";
		}
      
    }
    
    public function availableMethods() {
    	if($this->_availableMethods == null) {
    		$vendorId = $this->getRequest()->getParam('vendor_id',0);
    		$this->_availableMethods = Mage::getModel('csmarketplace/vendor')->getPaymentMethodsArray($vendorId);
    	}
    	return $this->_availableMethods;
    }

    public function getHeaderText()
    {
		$params = $this->getRequest()->getParams();
		$type = isset($params['type']) && in_array(trim($params['type']),array_keys(Ced_CsMarketplace_Model_Vpayment::getStates()))?trim($params['type']):Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_CREDIT;
		if($type == Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_DEBIT) {
			return Mage::helper('csmarketplace')->__("Debit Amount");
		} else {
			return Mage::helper('csmarketplace')->__("Credit Amount");
		}
    }


     public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }


}