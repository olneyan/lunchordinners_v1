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
  * @package     Ced_CsVattributeApi
  * @author      CedCommerce Core Team <connect@cedcommerce.com>
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://cedcommerce.com/license-agreement.txt
  */

class Ced_CsVattributeApi_Model_Vendor_Api extends Mage_Api_Model_Resource_Abstract
{

	public function getVendorAttributes() {
		$vendorAttributes = Mage::getModel('csmarketplace/vendor_attribute')
								->setStoreId(Mage::app()->getStore()->getId())
								->getCollection()
								->addFieldToFilter('is_visible',array('gt'=>0))
								->setOrder('sort_order','ASC');
		
		Mage::dispatchEvent('ced_csmarketplace_vendor_edit_attributes_load_after',array('vendorattributes'=>$vendorAttributes));
		
		$vendorAttributes->getSelect()->having('vform.is_visible >= 0');
		
		//echo $vendorAttributes->getSelect();die;
		return $vendorAttributes;
	}

	public function edit($vendorId, $attributes = null)
	{
		
		if($vendorId==null){
			$response = array (
						'data' => array (
							'message' => 'Vendor Id Is Empty',
							'success' => false, 
						) 
			);
			return $response;
		}
		$form = new Varien_Data_Form();
		$vattribute_enabled = FALSE;
        if(Mage::helper('core')->isModuleEnabled('Ced_CsVAttribute')) {
            $vattribute_enabled = TRUE;
        }
		$vendor=Mage::getModel('csmarketplace/vendor')->load($vendorId)->getData();
		
		$vpaymethods = array();

		if ($vendor && $vendor['entity_id']) {
			$vendorformFields = $this->getVendorAttributes();
			if(count($vendorformFields) >0 ) {
				$cnt = 0;
				$tcnt =0;

				foreach($vendorformFields as $attribute){
					
					if (!$attribute || ($attribute->hasIsVisible() && !$attribute->getIsVisible())) {
						continue;
					}
					if ($inputType = $attribute->getFrontend()->getInputType()) {
						if(!isset($model[$attribute->getAttributeCode()]) || (isset($model[$attribute->getAttributeCode()]) && !$model[$attribute->getAttributeCode()])){ $model[$attribute->getAttributeCode()] = $attribute->getDefaultValue();  }
						if($inputType == 'boolean') $inputType = 'select';
						if(in_array($attribute->getAttributeCode(),Ced_CsMarketplace_Model_Form::$VENDOR_FORM_READONLY_ATTRIBUTES)) {
							continue;
						}
						
						$fieldType  =  $inputType;
						
						$rendererClass  = $attribute->getFrontend()->getInputRendererClass();
						if (!empty($rendererClass)) {
							$fieldType  = $inputType . '_' . $attribute->getAttributeCode();
							$form->addType($fieldType, $rendererClass);
						}

	                    $label = ($attribute->getAttributeCode()!='region')?($attribute->getStoreLabel()?$attribute->getStoreLabel():$attribute->getFrontend()->getLabel()):'';
	                    $label = $vattribute_enabled?$label:$_helper->__($label);
						
						$value ='';
						$values ='';
						if(isset($vendor[$attribute->getAttributeCode()]))
							$value = $vendor[$attribute->getAttributeCode()];

						if ($inputType == 'select') {
							$values = $attribute->getSource()->getAllOptions(true,true);
						} else if ($inputType == 'multiselect') {
							$values = $attribute->getSource()->getAllOptions(false,true);
							//$element->setCanBeEmpty(true);
						} /*else if ($inputType == 'date') {
							$element->setImage($this->getSkinUrl('images/calendar.gif'));
							$element->setFormat(Mage::app()->getLocale()->getDateFormatWithLongYear());
						} else if ($inputType == 'multiline') {
							$element->setLineCount($attribute->getMultilineCount());
						}*/
						$url ='';
						if($inputType == 'image'){
							if($attribute->getAttributeCode()=='profile_picture'){
								$url = isset($vendor['profile_picture'])? Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$vendor['profile_picture']:Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
							}
							if($attribute->getAttributeCode()=='company_logo'){
								$url = isset($vendor['company_logo'])? Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$vendor['company_logo']:Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
							}
							if($attribute->getAttributeCode()=='company_banner'){
								$url = isset($vendor['company_banner'])? Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$vendor['company_banner']:Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
							}
						}
						$html = '';
						if($attribute->getAttributeCode()=='region_id'){
							$region_id = Mage::getModel('csmarketplace/vendor')->load($vendorId)->getData('region_id');
							$html = "<script type='text/javascript'>$('region_id').setAttribute('defaultValue', '".$region_id."');</script>";
						}
						if($fieldType == 'time'){
							$label = ucwords(str_replace("_"," ",$attribute->getAttributeCode()));
							$values = "24,60,60";
						}
					    	

						$element['fieldset'][$attribute->getAttributeCode()]= array(
									'label'=>$label,
									'value'=>$value,
									'values'=>$values,
									'type'=>$attribute->getFrontend()->getInputType(),
									'name'=>$attribute->getAttributeCode(),
									'url' => $url,
									'after_element_html'=>$html
								);						
					}								
					$tcnt++;
				}
				$cnt++;
				//print_r( $element );die;
			}
		}
		if(count($element)>0){
			$data=array(
				'data'=>$element		
				);
		}
		
		return $data;

	}

	public function getVendorAttributeInfo() {

		$entityTypeId  = Mage::getModel('csmarketplace/vendor')->getEntityTypeId();
		$setIds = Mage::getResourceModel('eav/entity_attribute_set_collection')
				->setEntityTypeFilter($entityTypeId)->getAllIds();
    
		$groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection');
		if(count($setIds) > 0) {
			$groupCollection->addFieldToFilter('attribute_set_id',array('in'=>$setIds));
		}
		if(version_compare(Mage::getVersion(), '1.6', '<')) {
			$groupCollection->getSelect()->order('main_table.sort_order');
		}
		else{
			$groupCollection->setSortOrder()->load();
		}
		return $groupCollection;
 	}

	public function info($vendorId, $attributes = null)
	{ 
		$vendor = Mage::getModel('csmarketplace/vendor')->load($vendorId);
		$groups = $this->getVendorAttributeInfo();
		$returnData = [];
		foreach($groups as $group){
    		$attributes = $vendor->getAttributes($group->getId(), true);
    		$attrdata = array();
    		foreach ($attributes as $attr){
 				$attribute = Mage::getModel('csmarketplace/vendor_attribute')->setStoreId(0)->load($attr->getid());
 				if(!$attribute->getisVisible()){
 					//continue;
 				} 
 				
 				//if($vendor->getData($attr->getAttributeCode()) && !in_array($attr->getAttributeCode(),Ced_CsMarketplace_Model_Form::$VENDOR_PROFILE_RESTRICTED_ATTRIBUTES)){
					
 					$attrdata[$attr->getAttributeCode()]['label'] = $attr->getFrontendLabel();
				    $attrdata[$attr->getAttributeCode()]['value'] = $vendor->getData($attr->getAttributeCode());
					
	
 			//}
		}
		$returnData[$group->getAttributeGroupName()] = $attrdata;
		
		}
		
		print_r($returnData); die('dd');
	
		$image = Mage::helper('csmarketplace/tool_image');
		if($vendorId==null) {
			$data = array (
					'data' => array (
										'success' => false ,
										'message'=> 'Vendor Id IS Empty'
									) 
							
					
			);
			return $data;
		}
		$vendor = Mage::getModel('csmarketplace/vendor')->load($vendorId);
		$region_id=$vendor->getData('region_id');
		if(isset($region_id) && $region_id!='0' ){
			foreach (Mage::getResourceModel('directory/region_collection')->load() as $region)
			{
				if($region->getData('region_id') == $vendor->getData('region_id'))
					$region_name= $region->getData('default_name');
			}
			$vendor->setData('region',$region_name);
		}
		if($vendor->getData('country_id')!=null)
		$vendor->setData('country_id',Mage::app()->getLocale()->getCountryTranslation($vendor->getData('country_id')));
		$vendor->setData('created_at',Mage::helper('core')->formatDate( $vendor->getData('created_at') , 'medium', true));
		if (!$vendor->getId()) {
			$data = array (
					'data' => array (
											'success' => false ,
											'message'=> 'Vendor Does Not Exist'
									) 
							
					
			);
			return $data;
		}
		$vendor = $vendor->getData();
		
		$vendor['profile_picture']= isset($vendor['profile_picture'])? Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$vendor['profile_picture']:Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

		$vendor['company_logo']= isset($vendor['company_logo'])? Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$vendor['company_logo']:Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

		$vendor['company_banner']= isset($vendor['company_banner'])? Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$vendor['company_banner']:Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		$data = array (
					'data' => array (
											'info'=> $vendor,
											'success' => true ,
									) 
							
					
			);
		
		print_r($data); die("vf");
			return $data;
	}

	
	
	
	/*public function update($vendorId,$vendorData)
	{
		if($vendorId==null) {
			$data = array (
				'data' => array (
					'success' => false ,
					'message'=> 'Vendor Id IS Empty'
				) 		
			);
			return $data;
		}
		$vendorData =  (array)$vendorData;
		$attr = Mage::getModel('eav/entity_attribute')->getCollection()->addFieldToFilter('frontend_label', 'Gender')->getFirstItem();

		//$attribute->getSource()->getAllOptions(true,true)
	   	 $options = $attr->getSource()->getAllOptions(true,true);
	   	 $gender=array();
	   	 foreach ($options as $key => $val) {
	   	 	$gender[$val['label']] = $val['value'];
	   	 }
	   	$_gender = (isset($vendorData['gender']) && $vendorData['gender']) ? $vendorData['gender']: '';
	   	$vendorData['gender'] = $gender[$_gender];
		$vendor = Mage::getModel('csmarketplace/vendor')->load($vendorId);
		if (!$vendor->getId()) {
			$data = array (
				'data' => array (
					'success' => false ,
					'message'=> 'Vendor Does Not Exist'
				) 	
			);
			return $data;
		}
		try{
		
			$vendor->addData($vendorData)->save();
			$customer = Mage::getModel("customer/customer")->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail($vendor->getEmail());
			//Mage::log($vendorData,null,'vendordata.log');
			if (isset($vendorData ['currentpassword'])) {
				//Mage::log('inside chngpass',null,'vendordata.log');
				$currPass = $vendorData ['currentpassword'];
				$newPass = $vendorData ['newpassword'];
				//$confPass = $data ['conform_password'];
				if (empty ( $currPass ) || empty ( $newPass )) {
					$data = array (
						'data' => array (
							'message' => 'Password fields cannot be empty.',
							'success' => false ,
						) 
					);
					return $data;
				}
				
				// $customerpass=Mage::getModel('customer/customer');
				$oldPass = $customer->getPasswordHash ();
				if (strpos ( $oldPass, ':' )) {
					list ( , $salt ) = explode ( ':', $oldPass );
				} else {
					$salt = false;
				}
				if ($customer->hashPassword ( $currPass, $salt ) == $oldPass) {
					
					$customer->setPassword ( $newPass );
					$customer->save ();
					$data = array (
						'data' => array (
							'message' => 'Profile Updated Successfully',
							'valerts' => $this->getVendorAlert($vendorId),
							'profile_complete'=>$this->profileComplete($vendorId),
							'success' => true ,	
						) 
					);
					return $data;
				} else {
					$data = array (
						'data' => array (
							'message' => 'Invalid current password.',
							'success' => false ,		
						) 
					);
					return $data;
				}
			}	
			$data = array (
				'data' => array (
					'success' => true ,
					'valerts' => $this->getVendorAlert($vendorId),
					'profile_complete'=>$this->profileComplete($vendorId),
					'message'=> 'Profile Updated Successfully'
				) 		
			);
			return $data;
		}
		catch(Exception $e){
			$data = array (
					'data' => array (
					'success' => false ,
					'message'=> $e->getMessage()
				) 
			);
			return $data;
		}
	}*/
	
	
}

