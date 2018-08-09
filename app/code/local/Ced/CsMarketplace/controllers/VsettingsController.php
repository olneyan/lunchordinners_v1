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
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */  
 
class Ced_CsMarketplace_VsettingsController extends Ced_CsMarketplace_Controller_AbstractController
{
	/**
     * Default vendor account page
     */
	public function indexAction()
    {
        if(!$this->_getSession()->getVendorId()) return;
		$this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Vendor')." ".$this->__('Settings'));
        $this->renderLayout();
    }
	
	/**
	 * Save settings
	 */
	public function saveAction() {
		if(!$this->_getSession()->getVendorId()) return;
		$section = $this->getRequest()->getParam('section','');
		$groups = $this->getRequest()->getPost('groups',array());
		//print_r($groups);die;
		if(strlen($section) > 0 && $this->_getSession()->getData('vendor_id') && count($groups)>0) {
			$vendor_id = (int)$this->_getSession()->getData('vendor_id');
			try {
				foreach ($groups as $code=>$values) {
					foreach ($values as $name=>$value) {
						$serialized = 0;
						$key = strtolower($section.'/'.$code.'/'.$name);
						if (is_array($value)){  $value = serialize($value); $serialized = 1; }
						/* print_r(Mage::getModel('csmarketplace/vsettings')->loadByField('key',$key)->getData());die;*/
						$key_tmp=Mage::helper('csmarketplace')->getTableKey('key');
						$vendor_id_tmp=Mage::helper('csmarketplace')->getTableKey('vendor_id');
						$setting = Mage::getModel('csmarketplace/vsettings')->loadByField(array($key_tmp,$vendor_id_tmp),array($key,$vendor_id));
						if ($setting && $setting->getId()) {
							$setting->setVendorId($vendor_id)
									->setGroup($section)
									->setKey($key)
									->setValue($value)
									->setSerialized($serialized)
									->save();
						} else {
							$setting = Mage::getModel('csmarketplace/vsettings');
							$setting->setVendorId($vendor_id)
								->setGroup($section)
								->setKey($key)
								->setValue($value)
								->setSerialized($serialized)
								->save();
						}
					}
				}
				$this->_getSession()->addSuccess($this->__('The setting information has been saved.'));
				$this->_redirect('*/*');
				return;
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				$this->_redirect('*/*');
				return;
			}
		}
		$this->_redirect('*/*');
	}
	
}
