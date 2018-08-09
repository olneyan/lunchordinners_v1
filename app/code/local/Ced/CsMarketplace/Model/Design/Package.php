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
class Ced_CsMarketplace_Model_Design_Package extends Mage_Core_Model_Design_Package
{
	/**
	 * @var Mage_Core_Model_Design_Fallback
	 */
	protected $_fallback = null;
	const XML_PATH_CED_REWRITES = 'global/ced/rewrites';
	
	/**
     * Use this one to get existing file name with fallback to default
     *
     * $params['_type'] is required
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getFilename($file, array $params)
    {
        Varien_Profiler::start(__METHOD__);        
        $this->updateParamDefaults($params);
        
    	$module = Mage::app()->getRequest()->getRequestedRouteName();
        $controller = Mage::app()->getRequest()->getRequestedControllerName();
        $action= Mage::app()->getRequest()->getRequestedActionName();
        $exceptionblocks='';
        $exceptionblocks = Mage::app()->getConfig()->getNode(self::XML_PATH_CED_REWRITES."/".$module."/".$controller."/".$action);
        if(strlen($exceptionblocks)==0){
          	$action="all";
          	$exceptionblocks = Mage::app()->getConfig()->getNode(self::XML_PATH_CED_REWRITES."/".$module."/".$controller."/".$action);
        }
        if(strlen($exceptionblocks)>0){
		      $exceptionblocks = explode(",",$exceptionblocks);
		      if(count($exceptionblocks)>0 
		      		&& $params['_area']=="adminhtml" 
		      		&& ($params['_package']!=="default" || $params['_theme']!=="default")
		    	){
		      		$params['_package']='default';
		      		if(Mage::helper('core')->isModuleEnabled('Ced_CsVendorPanel'))
		      			$params['_theme']='ced';
		      		else 
		      			$params['_theme']='default';
		    	}
        }
   		if(version_compare(Mage::getVersion(), '1.8.1.0', '<=') || (method_exists('Mage','getEdition') && Mage::getEdition() == "Enterprise")) {
    		$result = $this->_fallback($file, $params, array(
    				array(),
    				array('_theme' => $this->getFallbackTheme()),
    				array('_theme' => self::DEFAULT_THEME),
    		));
    	}
        else{
        	$result = $this->_fallback(
        			$file,
        			$params,
        			$this->_fallback->getFallbackScheme(
        					$params['_area'],
        					$params['_package'],
        					$params['_theme']
        			)
        	);
        }
        Varien_Profiler::stop(__METHOD__);
        return $result;
    }
}
