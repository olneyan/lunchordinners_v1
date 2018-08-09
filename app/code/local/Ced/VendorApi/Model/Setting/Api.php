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

class Ced_VendorApi_Model_Setting_Api extends Mage_Api_Model_Resource_Abstract
{	
	public function model()
	{
		return Mage::getResourceModel('vendorapi/vapisession');
	}
	
	public function save($vendorId, $section, $groups)
	{
		if($vendorId==null ||  $section==null || $groups==null || empty($groups)){
			$response = array (
						'data' => array (
							'message' => 'Vendor Id Is Empty',
							'success' => false, 
						) 
			);
			return $response;
		}
	//Mage::log($groups,null,'bbb.log');
		if(strlen($section) > 0 && $vendorId && count($groups)>0) {
			$vendor_id = (int)$vendorId;
			try {
				foreach ($groups as $code=>$values) {
					foreach ($values as $name=>$value) {
						if($code=='vpaypal'){ 
							if($values['active']=='1'){
								if($values['paypal_email']==null || $values['paypal_email']==''){
									//Mage::log('sdsdds',null,'wwww.log');
									Mage::throwException("paypal email can't be empty");
								}
							}
						}
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
				$response = array (
						'data' => array (
							'message' => Mage::helper('csmarketplace')->__('The setting information has been saved.'),
							'valerts'=>Mage::getModel('vendorapi/vendor_api')->getVendorAlert($vendorId),
							'success' => true, 
						) 
			);
			return $response;

			}catch (Exception $e) {
				$response = array (
						'data' => array (
							'message' => $e->getMessage(),
							'success' => false, 
						) 
			);
			return $response;
			}
		}
	}
	public function availableMethod($vendorId){
		
		if($vendorId==null){
			$response = array (
						'data' => array (
							'message' => 'Vendor Id Is Empty',
							'success' => false, 
						) 
			);
			return $response;
		}
		$vendor=Mage::getModel('csmarketplace/vendor')->load($vendorId);
		$vpaymethods = array();

		if ($vendor && $vendor->getId()) {
			$methods = $vendor->getPaymentMethods();
			if(count($methods) >0 ) {
				$cnt = 0;
				
				$tcnt=0;
				foreach($methods as $code=>$method) {
					$fields = $method->getFields();
					/* print_r($fields); continue; */
					if (count($fields) > 0) {
						foreach ($fields as $id=>$field) {

							$key = strtolower(Ced_CsMarketplace_Model_Vsettings::PAYMENT_SECTION.'/'.$method->getCode().'/'.$id);
							$value = '';
							$vendor_id=Mage::helper('csmarketplace')->getTableKey('vendor_id');
							$key_tmp=Mage::helper('csmarketplace')->getTableKey('key');
							$setting = Mage::getModel('csmarketplace/vsettings')->loadByField(array($key_tmp,$vendor_id),array($key,(int)$vendor->getId()));
							if($setting) $value = $setting->getValue();
							if($value=='null' || $value==''){
								//Mage::log('ddd',null,'dsf.log');
								$value='';
							}
							$values = isset($field['values'])?$field['values']:'0';
							
							if(isset($field['type'])){
								$type=isset($field['type'])?$field['type']:'text';
							}	
							$vpaymethods['fieldset'][$tcnt][$method->getLabel('label')][]= array(
								'label'=>$method->getLabel($id),
								'value'=>$value,
								'values'=>$values,
								'type'=>$type,
								'name'=>$method->getCode(),
								'tag'=>$id,
								'after_element_html'=>(isset($field['after_element_html']) && $field['after_element_html']) ? strip_tags($field['after_element_html']):''
								);
							
						}
						
					}
					++$tcnt;
				}
				++$cnt;	
			}
		}
		if(count($vpaymethods)>0){
			$data=array(
				'data'=>$vpaymethods		
				);
		}
		
		return $data;
	}
}
?>