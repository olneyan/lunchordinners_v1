<?php
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category  Ced
  * @package   Ced_MobiconnectAdvCart
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_MobiconnectAdvCart_Block_Adminhtml_Widget_Edit_Tab_Renderer_Sku extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row) {
		$checked = '';
		if (in_array ( $row->getId (), $this->_getSelectedProducts () ))
			$checked = 'checked';
		$html = '<input type="radio" ' . $checked . ' name="selected" value="' . $row->getId () . '" class="checkbox" onclick="selectProduct(this)">';
		return sprintf ( '%s', $html );
	}
	protected function _getSelectedProducts() {
		$products = $this->getRequest ()->getPost ( 'selected', array () );
		return $products;
	}
}

