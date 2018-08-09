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
 * @package     Ced_Groupgift
 * @author 		CedCommerce Core Team <Ced_MagentoCoreTeam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Groupgift block for the registry index
 */

class Ced_Csmarketplace_Block_Cart extends Mage_Core_Block_Template
{
	protected $_helper;
	protected $_options;
	protected $allowed_guest;	
	
	/**
     * function construct
     *
     * For initialization of class variables
     */
	
	
	
	public function getProduct()
	{
		$params = $this->getRequest()->getParams();
		$productId = $params['product'];
		
		return(Mage::getModel('catalog/product')->load($productId));
	}
}
