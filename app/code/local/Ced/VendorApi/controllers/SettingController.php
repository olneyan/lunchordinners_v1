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
  * @category    Ced
  * @package     VendorApi
  * @author      CedCommerce Core Team <connect@cedcommerce.com>
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://cedcommerce.com/license-agreement.txt
  */
 
class Ced_VendorAPi_SettingController extends Ced_VendorApi_Controller_Abstract
{
	/*settings */
	public function indexAction()
	{
		$vendorId  = $this->getRequest()->getParam('vendor_id');
		$validate=array(
			'vendor_id'=>$vendorId,
			'hash'=>$this->getRequest()->getParam('hashkey')
			);
		$validateRequest=$this->validate($validate);
		$data = $this->getRequest()->getParam('settings_data');
		$section = 'payment';
		$groups = json_decode($data,true);
		$settings = Mage::getModel('vendorapi/setting_api')->save($vendorId,$section,$groups);
		$response=json_encode($settings);
		$this->getResponse()->setBody($response);
	}
	public function availableMethodAction(){
		$vendorId  = $this->getRequest()->getParam('vendor_id');
		$validate=array(
			'vendor_id'=>$vendorId,
			'hash'=>$this->getRequest()->getParam('hashkey')
			);
		$validateRequest=$this->validate($validate);
		$settings = Mage::getModel('vendorapi/setting_api')->availableMethod($vendorId);
		$response=json_encode($settings);
		$this->getResponse()->setBody($response);
	}
}
?>