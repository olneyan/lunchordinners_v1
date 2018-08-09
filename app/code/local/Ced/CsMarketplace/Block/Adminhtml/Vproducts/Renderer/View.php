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
 
class Ced_CsMarketplace_Block_Adminhtml_Vproducts_Renderer_View extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

public function render(Varien_Object $row) {
	
       $id=$row->getId();
        
        $html='<a href="#popup" onClick="javascript:openMyPopup(\''.$this->getUrl('adminhtml/catalog_product/edit/popup/1/id/'.$id).'\')" title="'.$this->__("Click to View").'">'.$this->__("View").'</a>';
        return $html;
    }

}
