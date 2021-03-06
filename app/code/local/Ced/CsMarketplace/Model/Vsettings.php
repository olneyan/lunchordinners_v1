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

/**
 * Vendor Settings model
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Model_Vsettings extends Ced_CsMarketplace_Model_Abstract
{
	const PAYMENT_SECTION = 'payment';
	protected $_eventPrefix = 'csmarketplace_vsettings';
	
	protected function _construct()
	{
		$this->_init('csmarketplace/vsettings');
	}

	public function getValue() {
		if ($this->getId() && $this->getSerialized()) {
			return unserialize($this->getData('value'));
		}
		return $this->getData('value');
	}
}