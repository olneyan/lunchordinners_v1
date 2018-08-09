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

class Ced_CsMarketplace_Block_Adminhtml_Vendor_Entity_Grid_Renderer_Disableshop extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
 
	/**
	 * Render approval link in each vendor row
	 * @param Varien_Object $row
	 * @return String
	 */
	public function render(Varien_Object $row) {
		$html = '';
		$url = '';
		$vendor_id=Mage::helper('csmarketplace')->getTableKey('vendor_id');
		$model = Mage::getModel('csmarketplace/vshop')->loadByField(array($vendor_id),array($row->getEntityId()));
		if($model->getId()!='' && $model->getShopDisable() == Ced_CsMarketplace_Model_Vshop::ENABLED){
			$url =  $this->getUrl('*/*/massDisable', array('vendor_id' => $row->getEntityId(), 'shop_disable'=>Ced_CsMarketplace_Model_Vshop::DISABLED, 'inline'=>1));
			$html .= Mage::helper('csmarketplace')->__('Enabled').'&nbsp;'.'<a href="javascript:void(0);" onclick="deleteConfirm(\''.$this->__('Are you sure you want to Disable?').'\', \''. $url . '\');" >'.Mage::helper('csmarketplace')->__('Disable').'</a>';  
		} 			
		else if($model->getId()!='' && $model->getShopDisable() == Ced_CsMarketplace_Model_Vshop::DISABLED) {
			$url =  $this->getUrl('*/*/massDisable', array('vendor_id' => $row->getEntityId(), 'shop_disable'=>Ced_CsMarketplace_Model_Vshop::ENABLED, 'inline'=>1));
			$html .= Mage::helper('csmarketplace')->__('Disabled').'&nbsp;'.'<a href="javascript:void(0);" onclick="deleteConfirm(\''.$this->__('Are you sure you want to Enable?').'\', \''. $url . '\');" >'.Mage::helper('csmarketplace')->__('Enable')."</a>";
		}
		else{
			$url =  $this->getUrl('*/*/massDisable', array('vendor_id' => $row->getEntityId(), 'shop_disable'=>Ced_CsMarketplace_Model_Vshop::DISABLED, 'inline'=>1));
			$html .= Mage::helper('csmarketplace')->__('Enabled').'&nbsp;'.'<a href="javascript:void(0);" onclick="deleteConfirm(\''.$this->__('Are you sure you want to Disable?').'\', \''. $url . '\');" >'.Mage::helper('csmarketplace')->__('Disable').'</a>';
		} 	
		return $html;
	}
}