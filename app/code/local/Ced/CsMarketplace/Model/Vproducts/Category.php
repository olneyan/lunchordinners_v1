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

class Ced_CsMarketplace_Model_Vproducts_Category extends Mage_Catalog_Model_Category
{
    /**
     * Initialize resource mode
     *
     */
    protected function _construct()
    {
    	if(strpos(Mage::helper('core/url')->getCurrentUrl(),"csmarketplace/vproducts/") !== false||strpos(Mage::helper('core/url')->getCurrentUrl(),"csproduct/vproducts/") !== false){
            $this->_init('catalog/category');
    	}
    	else
    		parent::_construct();
    }
}
