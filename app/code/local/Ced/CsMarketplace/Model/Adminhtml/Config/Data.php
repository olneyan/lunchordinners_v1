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

class Ced_CsMarketplace_Model_Adminhtml_Config_Data extends Mage_Adminhtml_Model_Config_Data
{
    /**
     * Save config section
     * Require set: section, website, store and groups
     *
     * @return Mage_Adminhtml_Model_Config_Data
     */
    public function save()
    {	
        $is_csgroup = Mage::app()->getRequest()->getParam('is_csgroup',false);
		if(!$is_csgroup) return parent::save();
     
		$this->_validate();
        $this->_getScope();

        $section = $this->getSection();
        $website = $this->getWebsite();
        $store   = $this->getStore();
        $groups  = $this->getGroups();
        $scope   = $this->getScope();
        $scopeId = $this->getScopeId();

        if (empty($groups)) {
            return $this;
        }

        $sections = Mage::getModel('adminhtml/config')->getSections();
        /* @var $sections Mage_Core_Model_Config_Element */

        $oldConfig = $this->_getConfig(true);

        $deleteTransaction = Mage::getModel('core/resource_transaction');
        /* @var $deleteTransaction Mage_Core_Model_Resource_Transaction */
        $saveTransaction = Mage::getModel('core/resource_transaction');
        /* @var $saveTransaction Mage_Core_Model_Resource_Transaction */

        // Extends for old config data
        $oldConfigAdditionalGroups = array();

        foreach ($groups as $group => $groupData) {

            /**
             * Map field names if they were cloned
             */
            $groupConfig = $sections->descend($section.'/groups/'.$group);

            if ($clonedFields = !empty($groupConfig->clone_fields)) {
                if ($groupConfig->clone_model) {
                    $cloneModel = Mage::getModel((string)$groupConfig->clone_model);
                } else {
                    Mage::throwException('Config form fieldset clone model required to be able to clone fields');
                }
                $mappedFields = array();
                $fieldsConfig = $sections->descend($section.'/groups/'.$group.'/fields');

                if ($fieldsConfig->hasChildren()) {
                    foreach ($fieldsConfig->children() as $field => $node) {
                        foreach ($cloneModel->getPrefixes() as $prefix) {
                            $mappedFields[$prefix['field'].(string)$field] = (string)$field;
                        }
                    }
                }
            }
            // set value for group field entry by fieldname
            // use extra memory
            $fieldsetData = array();
            foreach ($groupData['fields'] as $field => $fieldData) {
                $fieldsetData[$field] = (is_array($fieldData) && isset($fieldData['value']))
                    ? $fieldData['value'] : null;
            }

            foreach ($groupData['fields'] as $field => $fieldData) {

                /**
                 * Get field backend model
                 */
                $backendClass = $sections->descend($section.'/groups/'.$group.'/fields/'.$field.'/backend_model');
                if (!$backendClass && $clonedFields && isset($mappedFields[$field])) {
                    $backendClass = $sections->descend($section.'/groups/'.$group.'/fields/'.$mappedFields[$field].'/backend_model');
                }
                if (!$backendClass) {
                    $backendClass = 'core/config_data';
                }

                $dataObject = Mage::getModel($backendClass);
                if (!$dataObject instanceof Mage_Core_Model_Config_Data) {
                    Mage::throwException('Invalid config field backend model: '.$backendClass);
                }
                /* @var $dataObject Mage_Core_Model_Config_Data */

                $fieldConfig = $sections->descend($section.'/groups/'.$group.'/fields/'.$field);
                if (!$fieldConfig && $clonedFields && isset($mappedFields[$field])) {
                    $fieldConfig = $sections->descend($section.'/groups/'.$group.'/fields/'.$mappedFields[$field]);
                }

                $dataObject
                    ->setField($field)
                    ->setGroups($groups)
                    ->setGroupId($group)
                    ->setStoreCode($store)
                    ->setWebsiteCode($website)
                    ->setScope($scope)
                    ->setScopeId($scopeId)
                    ->setFieldConfig($fieldConfig)
                    ->setFieldsetData($fieldsetData)
                ;
			 
                if (!isset($fieldData['value'])) {
                    $fieldData['value'] = null;
                }

                /*if (is_array($fieldData['value'])) {
                    $fieldData['value'] = join(',', $fieldData['value']);
                }*/

                $path = $section.'/'.$group.'/'.$field;

                /**
                 * Look for custom defined field path
                 */
                if (is_object($fieldConfig)) {
                    $configPath = (string)$fieldConfig->config_path;
                    if (!empty($configPath) && strrpos($configPath, '/') > 0) {
                        // Extend old data with specified section group
                        $groupPath = substr($configPath, 0, strrpos($configPath, '/'));
                        if (!isset($oldConfigAdditionalGroups[$groupPath])) {
                            $oldConfig = $this->extendConfig($groupPath, true, $oldConfig);
                            $oldConfigAdditionalGroups[$groupPath] = true;
                        }
                        $path = $configPath;
                    }
                }
				
				$oldPath = $path;
				
				switch($is_csgroup) {
					case 1: 
						if(Mage::helper('core')->isModuleEnabled('Ced_CsGroup')) {
							$groupData = Mage::app()->getRequest()->getPost();
							$gcode = isset($groupData['group_code']) && strlen($groupData['group_code']) > 0 ? $groupData['group_code']:(Mage::app()->getRequest()->getParam('gcode',false)?Mage::app()->getRequest()->getParam('gcode'):'');
							if(strlen($gcode) > 0){
								$path = $gcode.'/'.$path;
							} 
						} 
						break;
					case 2 : 
						if(Mage::helper('core')->isModuleEnabled('Ced_CsCommission')) {
							$vendorId =  Mage::app()->getRequest()->getParam('vendor_id',0);
							$vendor = Mage::getModel('csmarketplace/vendor')->load($vendorId);
							if($vendor && $vendor->getId()) {
								$path = $vendor->getId().'/'.$path;
							}
						}
						break;
					default : return $this; break;
				}
				if($path == $oldPath) {
					return $this;
				}
                $inherit = !empty($fieldData['inherit']);
				/* echo $path;
				echo $fieldData['value'];die; */
                $dataObject->setPath($path)
                    ->setValue($fieldData['value']);
			
                if (isset($oldConfig[$path])) {

					$dataObject->setConfigId($oldConfig[$path]['config_id']);

                    /**
                     * Delete config data if inherit
                     */
                    if (!$inherit) {
                        $saveTransaction->addObject($dataObject);
                    } else {
                        $deleteTransaction->addObject($dataObject);
                    }
                } elseif (isset($oldConfig[$oldPath])) {
                    $dataObject->setConfigId($oldConfig[$oldPath]['config_id']);

                    /**
                     * Delete config data if inherit
                     */
                    if (!$inherit) {
                        $saveTransaction->addObject($dataObject);
                    }
                    else {
                        $deleteTransaction->addObject($dataObject);
                    }
                }
                elseif (!$inherit) {
                    $dataObject->unsConfigId();
                    $saveTransaction->addObject($dataObject);
                }
            }

        }
		/* print_r($dataObject->getData());die; */
        $deleteTransaction->delete();
		/* print_r($dataObject->getData());die; */
        $saveTransaction->save();
		

        return $this;
    }

    /**
     * Load config data for section
     *
     * @return array
     */
    public function load()
    {
		$is_csgroup = Mage::app()->getRequest()->getParam('is_csgroup',false);
		if(!$is_csgroup) return parent::load();
        $this->_validate();
        $this->_getScope();

        return $this->_getConfig(false);
    }

    /**
     * Extend config data with additional config data by specified path
     *
     * @param string $path Config path prefix
     * @param bool $full Simple config structure or not
     * @param array $oldConfig Config data to extend
     * @return array
     */
    public function extendConfig($path, $full = true, $oldConfig = array())
    {	$is_csgroup = Mage::app()->getRequest()->getParam('is_csgroup',false);
		if(!$is_csgroup) return parent::extendConfig($path, $full, $oldConfig);
        $extended = $this->_getPathConfig($path, $full);
        if (is_array($oldConfig) && !empty($oldConfig)) {
            return $oldConfig + $extended;
        }
        return $extended;
    }

    /**
     * Validate isset required parametrs
     *
     */
    protected function _validate()
    {
		$is_csgroup = Mage::app()->getRequest()->getParam('is_csgroup',false);
		if(!$is_csgroup) return parent::_validate();
        if (is_null($this->getSection())) {
            $this->setSection('');
        }
        if (is_null($this->getWebsite())) {
            $this->setWebsite('');
        }
        if (is_null($this->getStore())) {
            $this->setStore('');
        }
    }

    /**
     * Get scope name and scopeId
     *
     */
    protected function _getScope()
    {
		$is_csgroup = Mage::app()->getRequest()->getParam('is_csgroup',false);
		if(!$is_csgroup) return parent::_getScope();
		
        if ($this->getStore()) {
            $scope   = 'stores';
            $scopeId = (int)Mage::getConfig()->getNode('stores/' . $this->getStore() . '/system/store/id');
        } elseif ($this->getWebsite()) {
            $scope   = 'websites';
            $scopeId = (int)Mage::getConfig()->getNode('websites/' . $this->getWebsite() . '/system/website/id');
        } else {
            $scope   = 'default';
            $scopeId = 0;
        }
        $this->setScope($scope);
        $this->setScopeId($scopeId);
    }

    /**
     * Return formatted config data for current section
     *
     * @param bool $full Simple config structure or not
     * @return array
     */
    protected function _getConfig($full = true)
    {
		$is_csgroup = Mage::app()->getRequest()->getParam('is_csgroup',false);
		if(!$is_csgroup) return parent::_getConfig($full);
		
        return $this->_getPathConfig($this->getSection(), $full);
    }

    /**
     * Return formatted config data for specified path prefix
     *
     * @param string $path Config path prefix
     * @param bool $full Simple config structure or not
     * @return array
     */
    protected function _getPathConfig($path, $full = true)
    {
		$is_csgroup = Mage::app()->getRequest()->getParam('is_csgroup',false);
		if(!$is_csgroup) return parent::_getPathConfig($path, $full);
			switch($is_csgroup) {
				case 1: 
					$groupData = Mage::app()->getRequest()->getPost();
					$gcode = isset($groupData['group_code']) && strlen($groupData['group_code']) > 0 ? $groupData['group_code']:(Mage::app()->getRequest()->getParam('gcode',false)?Mage::app()->getRequest()->getParam('gcode'):'');
					if(strlen($gcode) > 0){
						$path = $gcode.'/'.$path;
					} 
					break;
				case 2 : 
					$vendorId =  Mage::app()->getRequest()->getParam('vendor_id',0);
					$vendor = Mage::getModel('csmarketplace/vendor')->load($vendorId);
					if($vendor && $vendor->getId()) {
						$path = $vendor->getId().'/'.$path;
					}
			}
        $configDataCollection = Mage::getModel('core/config_data')
            ->getCollection()
            ->addScopeFilter($this->getScope(), $this->getScopeId(), $path);

        $config = array();
        foreach ($configDataCollection as $data) {
            if ($full) {
                $config[$data->getPath()] = array(
                    'path'      => $data->getPath(),
                    'value'     => $data->getValue(),
                    'config_id' => $data->getConfigId()
                );
            }
            else {
                $config[$data->getPath()] = $data->getValue();
            }
        }
        return $config;
    }
}
