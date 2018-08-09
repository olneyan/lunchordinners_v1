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
 
class Ced_CsMarketplace_Model_Core_Store extends Mage_Core_Model_Store
{

    /**
     * Retrieve store configuration data
     *
     * @param   string $path
     * @param   string $scope
     * @return  string|null
     */
    public function getConfig($path)
    {
		$origPath = $path;
		
		$path = $this->preparePath($path);
        if (isset($this->_configCache[$path])) {
            return $this->_configCache[$path];
        }

        $config = Mage::getConfig();
		
		/* Check Vendor wise/group wise/default */
        $fullPath = 'stores/'.$this->getCode().'/'.$path;
        $data = $config->getNode($fullPath);
        if (!$data && !Mage::isInstalled()) {
            $data = $config->getNode('default/' . $path);
        }
		
		/* Check Group Wise */
		if (!$data || $origPath == $path) {
			$path = $this->preparePath($origPath,null,2);
			
			$fullPath = 'stores/'.$this->getCode().'/'.$path;
			$data = $config->getNode($fullPath);
			if (!$data && !Mage::isInstalled()) {
				$data = $config->getNode('default/' . $path);
			}
			/* if(preg_match('/ced_/i',$path)) {
				$id = 0;
				if(Mage::registry('current_order_vendor')) $id = Mage::registry('current_order_vendor'); 
				Mage::log($id.'=='.$path."--val: ".print_r($data, true),null,'cscommission_path.log');
			} */
			/* $path = $origPath; */
        }
		
		/* Check Default Value*/
        if (!$data) {
			$fullPath = 'stores/'.$this->getCode().'/'.$origPath;
			$data = $config->getNode($fullPath);
			if (!$data && !Mage::isInstalled()) {
				$data = $config->getNode('default/' . $origPath);
			}
			$path = $origPath;
        }
		
		if (!$data) {
            return null;
        }
        
        if(strpos($path,'ced_') !== false && !Mage::app()->getStore()->isAdmin()){
        	$result = new Varien_Object();
        	Mage::dispatchEvent('ced_csgroup_config_data_change_after',array('result'=>$result,'path'=>$path,'groupdata'=>$this->_processConfigValue($fullPath, $path, $data)));
        	if($result->getResult()){
        		return $result->getResult();
        	}
        }
		
        return $this->_processConfigValue($fullPath, $path, $data);
    }

   

    /**
     * Set config value for CURRENT model
     * This value don't save in config
     *
     * @param string $path
     * @param mixed $value
     * @return Mage_Core_Model_Store
     */
    public function setConfig($path, $value)
    {	
		$path = $this->preparePath($path);
        if (isset($this->_configCache[$path])) {
            $this->_configCache[$path] = $value;
        }
        $fullPath = 'stores/'.$this->getCode().'/'.$path;
        Mage::getConfig()->setNode($fullPath, $value);

        return $this;
    }
	
	public function preparePath($path, $group = null,$case = 1) {
		if(!preg_match('/ced_/i',$path) || preg_match('/'.preg_quote('ced_csgroup/general/activation','/').'/i',$path)) return $path;
		
		if($group == null) {
			switch($case) {
				case 1: 
					if(Mage::helper('core')->isModuleEnabled('Ced_CsCommission')) {
						if(Mage::registry('current_order_vendor')) {
							$vendor = Mage::registry('current_order_vendor');
							if(is_numeric(Mage::registry('current_order_vendor')))
								$vendor = Mage::getModel('csmarketplace/vendor')->load(Mage::registry('current_order_vendor'));
							if($vendor && is_object($vendor) && $vendor->getId()) {
								return $vendor->getId().'/'.$path;
							}
						} elseif(!Mage::app()->getStore()->isAdmin() && Mage::getSingleton('customer/session')->getVendorId() && strlen(Mage::getSingleton('customer/session')->getVendor()->getGroup()) > 0 ) {
							return Mage::getSingleton('customer/session')->getVendor()->getId().'/'.$path;
						}/*  else if(Mage::app()->getStore()->isAdmin()) {
							$groupData = Mage::app()->getRequest()->getPost();
							$gcode = isset($groupData['group_code']) && strlen($groupData['group_code']) > 0 ? $groupData['group_code']:(Mage::app()->getRequest()->getParam('gcode',false)?Mage::app()->getRequest()->getParam('gcode'):'');
							if(strlen($gcode) > 0) {
								return $gcode.'/'.$path;
							}
						} */
					}
					return $path;
					break;
				case 2 :
					if(Mage::helper('core')->isModuleEnabled('Ced_CsGroup') && Mage::getStoreConfig('ced_csgroup/general/activation',Mage::app()->getStore()->getId())) {
						if(Mage::registry('current_order_vendor')) {
							$vendor = Mage::registry('current_order_vendor');
							if(is_numeric(Mage::registry('current_order_vendor')))
								$vendor = Mage::getModel('csmarketplace/vendor')->load(Mage::registry('current_order_vendor'));
							if($vendor && is_object($vendor) && $vendor->getId()) {
								return $vendor->getGroup().'/'.$path;
							}
						} elseif(!Mage::app()->getStore()->isAdmin() && Mage::getSingleton('customer/session')->getVendorId() && strlen(Mage::getSingleton('customer/session')->getVendor()->getGroup()) > 0 ) {
							return Mage::getSingleton('customer/session')->getVendor()->getGroup().'/'.$path;
						} elseif(Mage::app()->getStore()->isAdmin()) {
							$groupData = Mage::app()->getRequest()->getPost();
							$gcode = isset($groupData['group_code']) && strlen($groupData['group_code']) > 0 ? $groupData['group_code']:(Mage::app()->getRequest()->getParam('gcode',false)?Mage::app()->getRequest()->getParam('gcode'):'');
							if(strlen($gcode) > 0) {
								return $gcode.'/'.$path;
							}
						}
					}
					return $path;
					break;
				default: 
					return $path;
					break;
				
			}
		} else {
			return $path;
		}
		
	}
	
	public function getCollection()
    {
        $collection = parent::getCollection();
		if(Mage::getConfig()->getModuleConfig('Ced_CsSeoSuite')->is('active', 'true') && defined('Ced_CsSeoSuite_Helper_Data::VENDOR_PANEL_STORE_VIEW_CODE')) {
			$collection->addFieldToFilter('code',array('nlike'=>Ced_CsSeoSuite_Helper_Data::VENDOR_PANEL_STORE_VIEW_CODE.'%'));
		}
		return $collection;
    }
}
