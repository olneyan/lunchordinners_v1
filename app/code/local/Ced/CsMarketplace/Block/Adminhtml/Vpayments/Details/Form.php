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

/**
 * Transaction Detail View
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 */
class Ced_CsMarketplace_Block_Adminhtml_Vpayments_Details_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return Ced_CsMarketplace_Block_Adminhtml_Vpayments_Details_Form
     */
    protected function _prepareForm()
    {
        list($model,$fieldsets) = $this->loadFields();
        $form = new Varien_Data_Form();
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
        return parent::_prepareForm();
    }
	
	protected function loadFields() {
		$model = Mage::registry('csmarketplace_current_transaction');
		$renderOrderDesc = $this->getLayout()->createBlock('csmarketplace/adminhtml_vpayments_grid_renderer_orderdesc');
		$renderName = new Ced_CsMarketplace_Block_Adminhtml_Vorders_Grid_Renderer_Vendorname();
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
						'base_amount' => array(
							'label' => Mage::helper('csmarketplace')->__('Amount'),
							'value' => Mage::app()->getLocale()->currency($model->getBaseCurrency())->toCurrency($model->getData('base_amount')),
							'type'	=> 'label',
						),
						'amount' => array(
							'label' => '',
							'value' => '['.Mage::app()->getLocale()->currency($model->getCurrency())->toCurrency($model->getData('amount')).']',
							'type'	=> 'label',
						),
						'base_fee' => array(
							'label' => Mage::helper('csmarketplace')->__('Adjustment Amount'),
							'value' =>  Mage::app()->getLocale()->currency($model->getBaseCurrency())->toCurrency($model->getData('base_fee')),
							'type'	=> 'label',
						),
						'fee' => array(
							'label' => '',
							'value' => '['.Mage::app()->getLocale()->currency($model->getCurrency())->toCurrency($model->getData('fee')).']',
							'type'	=> 'label',
						),
						'base_net_amount' => array(
							'label' => Mage::helper('csmarketplace')->__('Net Amount'),
							'value' =>  Mage::app()->getLocale()->currency($model->getBaseCurrency())->toCurrency($model->getData('base_net_amount')),
							'type'	=> 'label',
						),
						'net_amount' => array(
							'label' => '',
							'value' => '['.Mage::app()->getLocale()->currency($model->getCurrency())->toCurrency($model->getData('net_amount')).']',
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
						'base_amount' => array(
							'label' => Mage::helper('csmarketplace')->__('Amount'),
							'value' => Mage::app()->getLocale()->currency($model->getBaseCurrency())->toCurrency($model->getData('base_amount')),
							'type'	=> 'label',
						),
						'base_fee' => array(
							'label' => Mage::helper('csmarketplace')->__('Adjustment Amount'),
							'value' =>  Mage::app()->getLocale()->currency($model->getBaseCurrency())->toCurrency($model->getData('base_fee')),
							'type'	=> 'label',
						),
						'base_net_amount' => array(
							'label' => Mage::helper('csmarketplace')->__('Net Amount'),
							'value' =>  Mage::app()->getLocale()->currency($model->getBaseCurrency())->toCurrency($model->getData('base_net_amount')),
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
}