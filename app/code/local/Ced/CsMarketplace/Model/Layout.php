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
 * Layout model
 *
 * @category   Ced
 * @package    Ced_CsMarketplace
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Model_Layout extends Mage_Core_Model_Layout
{
	const XML_PATH_CED_REWRITES = 'global/ced/rewrites';
    /**
     * Block Factory
     *
     * @param     string $type
     * @param     string $blockName
     * @param     array $attributes
     * @return    Mage_Core_Block_Abstract
     */
    public function createBlock($type, $name='', array $attributes = array())
    {
        try {
            $block = $this->_getBlockInstance($type, $attributes);
            
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
	            if(count($exceptionblocks)>0){
	            	foreach ($exceptionblocks as $exceptionblock){
	            		if(strpos(get_class($block),$exceptionblock) !== false)
	            			$block->setArea('adminhtml');
	            	}
	            }
            }
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }

        if (empty($name) || '.'===$name{0}) {
            $block->setIsAnonymous(true);
            if (!empty($name)) {
                $block->setAnonSuffix(substr($name, 1));
            }
            $name = 'ANONYMOUS_'.sizeof($this->_blocks);
        } elseif (isset($this->_blocks[$name]) && Mage::getIsDeveloperMode()) {
            //Mage::throwException(Mage::helper('core')->__('Block with name "%s" already exists', $name));
        }

        $block->setType($type);
        $block->setNameInLayout($name);
        $block->addData($attributes);
        $block->setLayout($this);

        $this->_blocks[$name] = $block;
        Mage::dispatchEvent('core_layout_block_create_after', array('block'=>$block));
        return $this->_blocks[$name];
    }
}
