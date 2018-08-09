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
class Ced_CsMarketplace_Block_Html_Pager extends Mage_Page_Block_Html_Pager
{

    protected function _construct()
    {  
        parent::_construct();
        $this->setData('show_amounts', true);
        $this->setData('use_container', true);
        if(Mage::helper('core')->isModuleEnabled('Ced_CsVendorPanel'))
        	$this->setTemplate('csmarketplace/html/pager.phtml');
        else
        	$this->setTemplate('page/html/pager.phtml');
    }
    
    public function getLimitUrl($limit)
    {
    	return $this->getPagerUrl(array($this->getLimitVarName()=>$limit));
    }
    
    public function getPagerUrl($params=array())
    {
    	$urlParams = array();
    	$urlParams['_current']  = true;
    	$urlParams['_escape']   = true;
    	$urlParams['_use_rewrite']   = true;
    	if(Mage::app()->getStore()->isCurrentlySecure()){
    		$urlParams['_secure']   = true;
    	}
    	$urlParams['_query']    = $params;
    	return $this->getUrl('*/*/*', $urlParams);
    }
}
