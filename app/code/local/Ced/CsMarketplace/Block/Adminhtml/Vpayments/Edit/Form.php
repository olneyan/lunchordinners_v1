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
class Ced_CsMarketplace_Block_Adminhtml_Vpayments_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
	  $back = $this->getRequest()->getParam('back','');
	  $amount = $this->getRequest()->getPost('total',0);
	  $params = $this->getRequest()->getParams();
	  $type = isset($params['type']) && in_array($params['type'],array_keys(Ced_CsMarketplace_Model_Vpayment::getStates()))?$params['type']:Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_CREDIT;
		
	  if($back == 'edit' && $amount) {
		 $form = new Varien_Data_Form(array(
										  'id' => 'edit_form',
										  'action' => $this->getUrl('*/*/save', array('payment_method'=>Mage::helper('csmarketplace/acl')->getDefaultPaymentType(),'type'=>$type)),
										  'method' => 'post',
										  'enctype' => 'multipart/form-data'
									   )
		  );
	  } else {
		  $form = new Varien_Data_Form(array(
										  'id' => 'edit_form',
										  'action' => $this->getUrl('*/*/*', array('vendor_id'=>$this->getRequest()->getParam('vendor_id'),'type'=>$type)),
										  'method' => 'post',
										  'enctype' => 'multipart/form-data'
									   )
		  );
	  }

      $form->setUseContainer(true);
      $this->setForm($form);
	  
      return parent::_prepareForm();
  }
}