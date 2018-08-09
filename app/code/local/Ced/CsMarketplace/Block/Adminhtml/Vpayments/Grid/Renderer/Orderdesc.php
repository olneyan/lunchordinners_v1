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
 
class Ced_CsMarketplace_Block_Adminhtml_Vpayments_Grid_Renderer_Orderdesc extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	protected $_frontend = false;
	
	public function __construct($frontend = false) {
		$this->_frontend = $frontend;
		return parent::__construct();
	}
	
	public function render(Varien_Object $row)
	{
		$amountDesc=$row->getAmountDesc();
		$html='';
		if($amountDesc!=''){
			$amountDesc=json_decode($amountDesc,true);
			foreach ($amountDesc as $incrementId=>$baseNetAmount){
					$url = 'javascript:void(0);';
					$target = "";
					$amount = Mage::app()->getLocale()->currency($row->getBaseCurrency())->toCurrency($baseNetAmount);
					$vorder = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
					if ($this->_frontend && $vorder && $vorder->getId()) {
						$url =  Mage::getUrl("csmarketplace/vorders/view/",array('increment_id'=>$incrementId));
						$target = "target='_blank'";
						$html .='<label for="order_id_'.$incrementId.'"><b>Order# </b>'."<a href='". $url . "' ".$target." >".$incrementId."</a>".'</label>, <b>Amount </b>'.$amount.'<br/>';
					}
					else 
						$html .='<label for="order_id_'.$incrementId.'"><b>Order# </b>'.$incrementId.'</label>, <b>Amount </b>'.$amount.'<br/>';
			}
		}
		return $html;	
	}
 
}