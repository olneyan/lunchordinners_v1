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
 
class Ced_CsMarketplace_Block_Adminhtml_Vproducts_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

	public function render(Varien_Object $row) {
        $sure="you Sure?";
        if($row->getCheckStatus()==Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS)
        	$html='<a href="'.$this->getUrl('adminhtml/adminhtml_vproducts/changeStatus/status/0/id/' . $row->getId()).'" title="'.$this->__("Click to Disapprove").'" onclick="return confirm(\'Are you sure, You want to disapprove?\')">'.$this->__("Disapprove").'</a>';
        if($row->getCheckStatus()==Ced_CsMarketplace_Model_Vproducts::PENDING_STATUS)
        	$html='<a href="'.$this->getUrl('adminhtml/adminhtml_vproducts/changeStatus/status/1/id/' . $row->getId()).'"  title="'.$this->__("Click to Approve").'" onclick="return confirm(\'Are you sure, You want to approve?\')">'.$this->__("Approve").'</a>
        		 | <a href="'.$this->getUrl('adminhtml/adminhtml_vproducts/changeStatus/status/0/id/' . $row->getId()).'" title="'.$this->__("Click to Disapprove").'" onclick="return confirm(\'Are you sure, You want to disapprove?\')">'.$this->__("Disapprove").'</a>';
        if($row->getCheckStatus()==Ced_CsMarketplace_Model_Vproducts::NOT_APPROVED_STATUS)
        	$html='<a href="'.$this->getUrl('adminhtml/adminhtml_vproducts/changeStatus/status/1/id/' . $row->getId()).'"  title="'.$this->__("Click to Approve").'" onclick="return confirm(\'Are you sure, You want to approve?\')">'.$this->__("Approve").'</a>';
        return $html;
    }

}
?>