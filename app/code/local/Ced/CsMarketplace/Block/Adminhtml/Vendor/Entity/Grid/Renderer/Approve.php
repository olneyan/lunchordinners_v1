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

class Ced_CsMarketplace_Block_Adminhtml_Vendor_Entity_Grid_Renderer_Approve extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
 
	/**
	 * Render approval link in each vendor row
	 * @param Varien_Object $row
	 * @return String
	 */
	public function render(Varien_Object $row) {
		$html = '';
		if($row->getEntityId()!='' && $row->getStatus() != Ced_CsMarketplace_Model_Vendor::VENDOR_APPROVED_STATUS) {
			$order = Mage::getModel('sales/order')->loadByIncrementId($row->getOrderId()); 	
			$url =  $this->getUrl('*/*/massStatus', array('vendor_id' => $row->getEntityId(), 'status'=>Ced_CsMarketplace_Model_Vendor::VENDOR_APPROVED_STATUS, 'inline'=>1));
			$html .= '<a href="javascript:void(0);" onclick="deleteConfirm(\''.$this->__('Are you sure you want to Approve?').'\', \''. $url . '\');" >'.Mage::helper('csmarketplace')->__('Approve').'</a>';  
		} 
				
		if($row->getEntityId()!='' && $row->getStatus() != Ced_CsMarketplace_Model_Vendor::VENDOR_DISAPPROVED_STATUS) {
			if(strlen($html) > 0) $html .= ' | ';
			$order = Mage::getModel('sales/order')->loadByIncrementId($row->getOrderId());
			$url =  $this->getUrl('*/*/massStatus', array('vendor_id' => $row->getEntityId(), 'status'=>Ced_CsMarketplace_Model_Vendor::VENDOR_DISAPPROVED_STATUS, 'inline'=>1));
			$html .= '<a href="javascript:void(0);" onclick="deleteConfirm(\''.$this->__('Are you sure you want to Disapprove?').'\', \''. $url . '\');" >'.Mage::helper('csmarketplace')->__('Disapprove')."</a>";
		}
		
		return $html;
	}
}