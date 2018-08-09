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
 
class Ced_CsMarketplace_Block_Adminhtml_Vpayments_Edit_Tab_Addorder extends Mage_Adminhtml_Block_Template
{
	protected $_availableMethods = null;
	
	public function __construct(){
		parent::__construct();
		$this->setTemplate('csmarketplace/vpayments/edit/tab/addorder.phtml');
	}
	/**
     * Round price
     *
     * @param mixed $price
     * @return double
     */
    public function roundPrice($price)
    {
        return Mage::getModel('core/store')->roundPrice($price);
    }
	
	public function availableMethods() {
		if($this->_availableMethods == null) {
			$vendorId = $this->getRequest()->getParam('vendor_id',0);
			$this->_availableMethods = Mage::getModel('csmarketplace/vendor')->getPaymentMethodsArray($vendorId);
		}
		return $this->_availableMethods;
	}
	
	protected function _prepareLayout()
    {
        $this->setChild('csmarketplace_continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Continue'),
                    'onclick'   => "setSettings('".$this->getContinueUrl()."','vendor_id')",
                    'class'     => 'save'
                    ))
                );
        return parent::_prepareLayout();
    }
		
	public function getContinueUrl()
    {
        return $this->getUrl('*/*/*', array(
            '_current'  => true,
			'_secure'	=> true,
            'vendor_id' => '{{vendor_id}}',	
        ));
    }
	
	public function getButtonsHtml()
	{
		$addButtonData = array(
				'label' => Mage::helper('sales')->__('Add/Remove Amount(s) for Payment'),
				'onclick' => "this.parentNode.style.display = 'none'; document.getElementById('order-search').style.display = ''",
				'class' => 'add',
		);
		return $this->getLayout()->createBlock('adminhtml/widget_button')->setData($addButtonData)->toHtml();
	}
	
	protected function noticeBlock() {
		if(count($this->availableMethods()) == 0) {
			return '<div>
							<ul class="messages">
							    <li class="notice-msg">
							        <ul>
							            <li>'.Mage::helper("csmarketplace")->__("Can't continue with payment,because vendor did not specify payment method(s).").'</li>
							        </ul>
							    </li>
							</ul>
						</div>';
		}
		return '';
		
	}
	
	public function getSearchFormHtml() { 
		$form = new Varien_Data_Form();
		$fieldset = $form->addFieldset('form_fields', array('legend'=>Mage::helper('csmarketplace')->__('Beneficiary  Information')));
		$vendorId = $this->getRequest()->getParam('vendor_id',0);
		$message = Mage::helper('csmarketplace')->__("Are you sure to change the vendor Because it will change the Selected Amount(s) for Payment section.");
		$params = $this->getRequest()->getParams();
		$type = isset($params['type']) && in_array($params['type'],array_keys(Ced_CsMarketplace_Model_Vpayment::getStates()))?$params['type']:Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_CREDIT;
		
		$id = $fieldset->addField('vendor_id', 'select', array(
							  'label'     => Mage::helper('csmarketplace')->__('Beneficiary Vendor'),
							  'class'     => 'required-entry',
							  'required'  => true,
							  'name'      => 'vendor_id',
							  'script'	  => 'var cs_ok = 0;',
							  'onchange'  => "document.getElementById('order-items').innerHTML=''; document.getElementById('order-search').innerHTML=''; setLocation('".Mage::helper('adminhtml')->getUrl('*/*/*',array('type'=>$type))."vendor_id/'+this.value);",
							  'value'	  => $vendorId,
							  'values' => Mage::getModel('csmarketplace/vendor')->getCollection()->toOptionArray(),
							  'after_element_html' => '<small>Vendor selection will change the <b>"Selected Amount(s) for Payment"</b> section.</small>',
							));

								
		$params = $this->getRequest()->getParams();
		
		$type = isset($params['type']) && in_array(trim($params['type']),array_keys(Ced_CsMarketplace_Model_Vpayment::getStates()))?trim($params['type']):Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_CREDIT;

		$relationIds = isset($params['order_ids'])? explode(',',trim($params['order_ids'])):array();
		$collection = Mage::getModel('csmarketplace/vorders')
							->getCollection()
							->addFieldToFilter('vendor_id',array('eq'=>$vendorId));
		if($type == Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_DEBIT) {
			$collection->addFieldToFilter('order_payment_state',array('eq'=>Mage_Sales_Model_Order_Invoice::STATE_PAID))
					   ->addFieldToFilter('payment_state',array('eq'=>Ced_CsMarketplace_Model_Vorders::STATE_REFUND));
		} else{
			$collection->addFieldToFilter('order_payment_state',array('eq'=>Mage_Sales_Model_Order_Invoice::STATE_PAID))
					   ->addFieldToFilter('payment_state',array('eq'=>Ced_CsMarketplace_Model_Vorders::STATE_OPEN));
		}

		$collection = $collection->addFieldToFilter('id',array('in'=>$relationIds)); 

		$renderer = new Ced_CsMarketplace_Block_Adminhtml_Vorders_Grid_Renderer_Orderid();
		$collection->getSelect()->columns(array('net_vendor_earn' => new Zend_Db_Expr('(order_total - shop_commission_fee)')));
		$html="";		
		$html.='<div class="entry-edit">
					<div class="entry-edit-head">
						<div id="csmarketplace_add_more" style="float: right;">'.$this->getButtonsHtml().'</div>
						<h4 class="icon-head head-cart">'.Mage::helper("csmarketplace")->__("Selected Amount(s) for Payment").'</h4>
					</div>
					<div class="grid" id="order-items_grid">
						<table cellspacing="0" class="data order-tables">
		 
							<col width="100" />
							<col width="40" />
							<col width="100" />
							<col width="80" />
							<thead>
								<tr class="headings">
									<th class="no-link">'.Mage::helper("csmarketplace")->__("Order ID").'</th>
									<th class="no-link">'.Mage::helper("csmarketplace")->__("Grand Total") .'</th>
									<th class="no-link">'.Mage::helper("csmarketplace")->__("Commission Fee").'</th>
									<th class="no-link">'.Mage::helper("csmarketplace")->__("Vendor Payment").'</th>
								</tr>
							</thead>
							<tbody>';
		$amount = 0.00;
		$class = '';
		foreach ($collection as $item)
		{
			$class = ($class == 'odd')? 'even':'odd';
			$html .= '<tr class="'.$class.'"';
			$html.='>';

			$html .= '<td>'.$renderer->render($item).'</td>';
			$html .= '<td>'.Mage::app()->getLocale()
										->currency($item->getCurrency())
										->toCurrency($item->getOrderTotal()).'</td>';
			$html .= '<td>'.Mage::app()->getLocale()
										->currency($item->getCurrency())
										->toCurrency($item->getShopCommissionFee()).'</td>';
			$html .= '<td>'.Mage::app()->getLocale()
										->currency($item->getCurrency())
										->toCurrency($item->getNetVendorEarn());
					
			$amount += $item->getNetVendorEarn();
			$html .= '<input id="csmarketplace_vendor_orders_'.$item->getId().'" type="hidden" value="'.$this->roundPrice($item->getNetVendorEarn()).'" name="orders['.$item->getOrderId().']"/>';
					
			$html .= '</td>';
			$html .= '</tr>';
			
		} 
		 
		$html.=       ' </tbody></table>
					   </div>
		</div>';						

		$fieldset->addField('csmarketplace_vendor_total', 'text', array(
							  'label'     => Mage::helper('csmarketplace')->__('Total Amount'),
							  'class'     => 'required-entry validate-greater-than-zero',
							  'required'  => true,
							  'name'      => 'total',
							  'value'	  => $this->roundPrice($amount),
							  'readonly'  => 'readonly',
							  'after_element_html' => '<b>['.Mage::app()->getBaseCurrencyCode().']</b><small><i> Readonly field</i>.</small>',
							  ));
		return array($this->noticeBlock().$form->toHtml(),$html);
	}
	
	public function getAddOrderBlock_Old() {
		$relationIds=$this->getRequest()->getParam('order_ids',array());
		$vendorId = $this->getRequest()->getParam('vendor_id',0);
		$params = $this->getRequest()->getParams();
		$type = isset($params['type']) && in_array($params['type'],array_keys(Ced_CsMarketplace_Model_Vpayment::getStates()))?$params['type']:Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_CREDIT;
		$collection = Mage::getModel('csmarketplace/vorders')
							->getCollection()
							->addFieldToFilter('vendor_id',array('eq'=>$vendorId));
		if($type == Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_DEBIT) {
			$collection->addFieldToFilter('order_payment_state',array('eq'=>Mage_Sales_Model_Order_Invoice::STATE_PAID))
					   ->addFieldToFilter('payment_state',array('eq'=>Ced_CsMarketplace_Model_Vorders::STATE_REFUND));
		} else {
			$collection->addFieldToFilter('order_payment_state',array('eq'=>Mage_Sales_Model_Order_Invoice::STATE_PAID))
					   ->addFieldToFilter('payment_state',array('eq'=>Ced_CsMarketplace_Model_Vorders::STATE_OPEN));
		}
		$collection = $collection->addFieldToFilter('id',array('in'=>$relationIds)); 
	
		$renderer = new Ced_CsMarketplace_Block_Adminhtml_Vorders_Grid_Renderer_Orderid();
		$collection->getSelect()->columns(array('net_vendor_earn' => new Zend_Db_Expr('(order_total - shop_commission_fee)')));
		$html="";
		$html.='<table cellspacing="0" class="data order-tables">
         
            				<col width="100" />
            				<col width="40" />
            				<col width="100" />
            				<col width="80" />
            				<thead>
				                <tr class="headings">
    								<th class="no-link">'.Mage::helper("csmarketplace")->__("Order ID").'</th>
				                    <th class="no-link">'.Mage::helper("csmarketplace")->__("Grand Total") .'</th>
				                  	<th class="no-link">'.Mage::helper("csmarketplace")->__("Commission Fee").'</th>
				                    <th class="no-link">'.Mage::helper("csmarketplace")->__("Vendor Payment").'</th>
				                </tr>
            				</thead>
    						<tbody>';
		$amount = 0.00;
		$class = '';
		foreach ($collection as $item) {
			$class = ($class == 'odd')? 'even':'odd';
			
			$html .= '<tr class="'.$class.'"';
			$html.='>';

			$html .= '<td>'.$renderer->render($item).'</td>';
			$html .= '<td>'.Mage::app()->getLocale()
                                        ->currency($item->getCurrency())
									    ->toCurrency($item->getOrderTotal()).'</td>';
			$html .= '<td>'.Mage::app()->getLocale()
                                        ->currency($item->getCurrency())
									    ->toCurrency($item->getShopCommissionFee()).'</td>';
			$html .= '<td>'.Mage::app()->getLocale()
                                        ->currency($item->getCurrency())
									    ->toCurrency($item->getNetVendorEarn());

			$amount += $item->getNetVendorEarn();
			$html .= '<input id="csmarketplace_vendor_orders_'.$item->getId().'" type="hidden" value="'.$this->roundPrice($item->getNetVendorEarn()).'" name="orders['.$item->getOrderId().']"/>';
					
			$html .= '</td>';
			$html .= '</tr>';
			
		} 
		$html.= '<input type="hidden" id="csmarketplace_fetched_total" value="'.$this->roundPrice($amount).'"/></tbody></table>';
		return $html;
	}
	
	public function getAddOrderBlock() {
		$relationIds=$this->getRequest()->getParam('order_ids',array());
		$array = [];
		foreach ($relationIds as $ids)
		{
			$incrementId =Mage::getModel('csmarketplace/vorders')->load($ids)->getOrderId();
			$order =  Mage::getModel("sales/order")->loadByIncrementId($incrementId);
			$orderId = $order->getId();
			$payment_method = $order->getPayment()->getMethodInstance()->getCode();
			$array[$ids] = $payment_method;
		}
		//print_r($array); die("dfif");
		$vendorId = $this->getRequest()->getParam('vendor_id',0);
		$params = $this->getRequest()->getParams();
		$type = isset($params['type']) && in_array($params['type'],array_keys(Ced_CsMarketplace_Model_Vpayment::getStates()))?$params['type']:Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_CREDIT;
		$collection = Mage::getModel('csmarketplace/vorders')
		->getCollection()
		->addFieldToFilter('vendor_id',array('eq'=>$vendorId));
		if($type == Ced_CsMarketplace_Model_Vpayment::TRANSACTION_TYPE_DEBIT) {
			$collection->addFieldToFilter('order_payment_state',array('eq'=>Mage_Sales_Model_Order_Invoice::STATE_PAID))
			->addFieldToFilter('payment_state',array('eq'=>Ced_CsMarketplace_Model_Vorders::STATE_REFUND));
		} else {
			$collection->addFieldToFilter('order_payment_state',array('eq'=>Mage_Sales_Model_Order_Invoice::STATE_PAID))
			->addFieldToFilter('payment_state',array('eq'=>Ced_CsMarketplace_Model_Vorders::STATE_OPEN));
		}
		$collection = $collection->addFieldToFilter('id',array('in'=>$relationIds));
		$currency = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
		$renderer = new Ced_CsMarketplace_Block_Adminhtml_Vorders_Grid_Renderer_Orderid();
		$collection->getSelect()->columns(array('net_vendor_earn' => new Zend_Db_Expr('(order_total - shop_commission_fee)')));
		$html="";
		$html.='<table cellspacing="0" class="data order-tables">
     
            				<col width="100" />
            				<col width="40" />
            				<col width="100" />
            				<col width="80" />
            				<thead>
				                <tr class="headings">
    								<th class="no-link">'.Mage::helper("csmarketplace")->__("Order ID").'</th>
				                    <th class="no-link">'.Mage::helper("csmarketplace")->__("Grand Total") .'</th>
				                  	<th class="no-link">'.Mage::helper("csmarketplace")->__("Commission Fee").'</th>
				                    <th class="no-link">'.Mage::helper("csmarketplace")->__("Vendor Payment").'</th>
				                </tr>
            				</thead>
    						<tbody>';
		$amount = 0.00;
		$class = '';
		foreach ($collection as $item) {
			//print_r($collection->getData()); die("g");
			$class = ($class == 'odd')? 'even':'odd';
				
			$html .= '<tr class="'.$class.'"';
			$html.='>';
	
			$html .= '<td>'.$renderer->render($item).'</td>';
			$html .= '<td>'.Mage::app()->getLocale()
			->currency($item->getCurrency())
			->toCurrency($item->getOrderTotal()).'</td>';
			$html .= '<td>'.Mage::app()->getLocale()
			->currency($item->getCurrency())
			->toCurrency($item->getShopCommissionFee()).'</td>';
			foreach($array as $k=>$val)
			{
				//print_r($array); die("kfv");
				if($k == $item->getId())
				{
					if($val =="checkmo" || $val =="cashondelivery" )
					{
						$earn = -$item->getShopCommissionFee();
					}
						
					else{
						$earn = $item->getNetVendorEarn();
					}
				}
	
	
			}
			//echo $earn; die("gkjb");
			//echo $item->getOrderId();echo "<br>"; $earn;die("lkj");
			$html .= '<td>'.$currency.$earn;
	
			$amount += $earn;
			$html .= '<input id="csmarketplace_vendor_orders_'.$item->getId().'" type="hidden" value="'.$this->roundPrice($earn).'" name="orders['.$item->getOrderId().']"/>';
						
			$html .= '</td>';
					$html .= '</tr>';
								
		}
		$html.= '<input type="hidden" id="csmarketplace_fetched_total" value="'.$this->roundPrice($amount).'"/></tbody></table>';
		return $html;
	}
}