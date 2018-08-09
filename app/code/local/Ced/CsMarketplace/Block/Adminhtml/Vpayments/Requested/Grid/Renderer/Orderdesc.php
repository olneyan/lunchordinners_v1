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
 
class Ced_CsMarketplace_Block_Adminhtml_Vpayments_Requested_Grid_Renderer_Orderdesc extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	protected $_frontend = false;
	
	public function __construct($frontend = false) {
		$this->_frontend = $frontend;
		return parent::__construct();
	}
	
	public function render(Varien_Object $row)
	{
		$orderIds=$row->getOrderIds();
		$html='';
		if($orderIds!=''){
			$orderIds=explode(',',$orderIds);
			foreach ($orderIds as $orderId){
					$url = 'javascript:void(0);';
					$target = "";
					$amount = Mage::app()->getLocale()->currency($row->getBaseCurrency())->toCurrency($baseNetAmount);
					$vorder = Mage::getModel('csmarketplace/vorders')->load($orderId);
					$html .='<label for="order_id_'.$vorder->getOrderId().'"><b>Order# </b>'.$vorder->getOrderId().'</label><br/>';
			}
		}
		return $html;	
	}
 
}