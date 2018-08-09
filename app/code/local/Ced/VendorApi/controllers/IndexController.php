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

class Ced_VendorApi_IndexController extends Ced_VendorApi_Controller_Abstract
{
	/* public function __construct()
	{
		die('etstindf');
	} */
	/*public function indexAction()
	{
		die('here');
	}*/

	/*to check vendor is login*/

	public function loginAction()
	{
		$website = 'base';
		$email = $this->getRequest()->getParam('email');
		$password = $this->getRequest()->getParam('password');
		$login = Mage::getModel('vendorapi/vendor_api')->login($website,$email,$password);
		$response=json_encode($login);
		$this->getResponse()->setBody($response);
		
	}

		/*logout action*/
	 public function logoutAction()
	 {
	 	$vsession = '1cvnk487okuq8tf7cmcjtq5te4';$vendorId = 1;
	 	$logout =  Mage::getModel('vendorapi/vendor_api')->logout($vsession,$vendorId);
	 	print_r($logout);
	 	return $logout;
	 }

	/*create vendor*/ 
	public function createAction()
	{
		$data =  $this->getRequest()->getParam('createaccount');
	//Mage::log($data,null,'vaccont.log');
		$customer_data=json_decode($data,true);
		$createCustomer = Mage::getModel('vendorapi/vendor_api')->create($customer_data);
		$response=json_encode($createCustomer);
		$this->getResponse()->setBody($response);
		//return $createCustomer;
	}

	/*get vendor profile*/
	public function infoAction()
	{
		$vendorId = $this->getRequest()->getParam('vendor_id');
		$validate=array(
			'vendor_id'=>$this->getRequest()->getParam('vendor_id'),
			'hash'=>$this->getRequest()->getParam('hashkey')
			);
		$validateRequest=$this->validate($validate);
		$vendorInfo = Mage::getModel('vendorapi/vendor_api')->info($vendorId);
		$response=json_encode($vendorInfo);
		$this->getResponse()->setBody($response);
	}
	 /*get all vendors */
	public function itemAction()
	{
		try{
		$data=$this->getRequest()->getParams();
		$data['page']='';
		$data['fgroup']='';
		//echo get_class(Mage::getModel('vendorapi/vendor_api')); die("gfk");
		$allVendor = Mage::getModel('vendorapi/vendor_api')->items($data);
		$response=json_encode($allVendor);
		$this->getResponse()->setBody($response);
		}
		catch(Exception $e)
		{
			die($e);
		}
		
	}
	public function forgotPasswordAction() {
		$data = array (
				 'email'=>$this->getRequest()->getParam('email'),
		);
	
		$customer = Mage::getModel ( 'vendorapi/vendor_api' )->forgotPassword ( $data );
		$response=json_encode($customer);
		$this->getResponse()->setBody($response);
	}
	/*update vendor*/
	public function updateAction()
	{
		try{
			$vendorData = $this->getRequest()->getParams();
			$data = array();

			if (!function_exists('getallheaders')) { 
		        foreach($_SERVER as $key=>$value) { 
		            if (substr($key,0,5)=="HTTP_") { 
		                $key=str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5))))); 
		                $head[$key]=$value; 
		            } else { 
		                $head[$key]=$value; 
		        	} 
	            } 
        	} 
        	 /*Source Github Darkheir*/
        	else { 
				$head = getallheaders ();
			} 
			foreach ( $head as $key => $val ) {
				if ($key == "Authorization") {
					$vendorId = $val;
				}
			}	
			if(count($vendorData)>0){
				$vendorId=$vendorData['vendor_id'];
			}
			//Mage::log('******************Files*********************',null,'newpro.log');
			//Mage::log($_FILES,null,'newpro.log');
			//Mage::log('***************Header************************',null,'newpro.log');
			//Mage::log($head,null,'newpro.log');
			//Mage::log('*******************Vendor Id********************',null,'newpro.log');
			//Mage::log($vendorId,null,'newpro.log');
			
			if (isset ( $_FILES ['company_banner'] ['name'] ) && $_FILES ['company_banner'] ['name'] != '') {
				
				$imgName = $_FILES ['company_banner'] ['name'];
				$imgName = str_replace ( ' ', '_', $imgName );
				$path = Mage::getBaseDir('media') . DS .'ced' . DS . 'csmaketplace' . DS . 'vendor' . DS;
				$uploader = new Varien_File_Uploader ( 'company_banner' );
				$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
				
				//$uploader->addValidateCallback('size', $this, 'validateMaxSize');
				//die;
				$uploader->setAllowRenameFiles(false);
				$uploader->setFilesDispersion(false);
				$uploader->save ( $path, $imgName );
				$vendorData['company_banner']='ced/csmaketplace/vendor/'.$imgName;
			}
			if (isset ( $_FILES ['company_logo'] ['name'] ) && $_FILES ['company_logo'] ['name'] != '') {
				
				$imgName = $_FILES ['company_logo'] ['name'];
				$imgName = str_replace ( ' ', '_', $imgName );
				$path = Mage::getBaseDir('media') . DS .'ced' . DS . 'csmaketplace' . DS . 'vendor' . DS;
				$uploader = new Varien_File_Uploader ( 'company_logo' );
				$uploader->setAllowedExtensions ( array (
						'jpg',
						'JPG',
						'jpeg',
						'gif',
						'GIF',
						'png',
						'PNG' 
				) );
				
				//$uploader->addValidateCallback('size', $this, 'validateMaxSize');
				//die;
				$uploader->setAllowRenameFiles ( true );
				$uploader->setFilesDispersion ( false );
				$uploader->save ( $path, $imgName );
				$vendorData['company_logo']='ced/csmaketplace/vendor/'.$imgName;
			}
			if (isset ( $_FILES ['profile_picture'] ['name'] ) && $_FILES ['profile_picture'] ['name'] != '') {
				
				$imgName = $_FILES ['profile_picture'] ['name'];
				$imgName = str_replace ( ' ', '_', $imgName );
				$path = Mage::getBaseDir('media') . DS .'ced' . DS . 'csmaketplace' . DS . 'vendor' . DS;
				$uploader = new Varien_File_Uploader ( 'profile_picture' );
				$uploader->setAllowedExtensions ( array (
						'jpg',
						'JPG',
						'jpeg',
						'gif',
						'GIF',
						'png',
						'PNG' 
				) );
				$uploader->setAllowRenameFiles ( true );
				$uploader->setFilesDispersion ( false );
				$uploader->save ( $path, $imgName );
				$vendorData['profile_picture']='ced/csmaketplace/vendor/'.$imgName;
			}
		}
		catch(Exception $e){
			$data = array (
				'data' => array (
					'success' => false,
					'message'=> $e->getMessage(),
				) 		
			);
			echo json_encode($data);
			exit();
		}
		$updateVendor = Mage::getModel('vendorapi/vendor_api')->update($vendorId,$vendorData);
		$response=json_encode($updateVendor);
		$this->getResponse()->setBody($response);

	}

	/*delete vendor */
	public function deleteAction()
	{
		$vendorId = $this->getRequest()->getParam('vendor_id');
		$deleteVendor = Mage::getModel('vendorapi/vendor_api')->delete($vendorId);
		return $deleteVendor;
	}

	/*get links for vendor*/
	public function linkAction()
	{
		$vendorId = $this->getRequest()->getParam('vendor_id');
		$validate=array(
			'vendor_id'=>$this->getRequest()->getParam('vendor_id'),
			'hash'=>$this->getRequest()->getParam('hashkey')
			);
		$validateRequest=$this->validate($validate); 
		$vendorLinks = Mage::getModel('vendorapi/vendor_api')->getVendorLinks($vendorId,false);
		$response=json_encode($vendorLinks);
		$this->getResponse()->setBody($response);
	}
	/*get dashboard info for vendor*/
	public function dashboardAction()
	{
		$vendorId = $this->getRequest()->getParam('vendor_id'); 
		$validate=array(
			'vendor_id'=>$this->getRequest()->getParam('vendor_id'),
			'hash'=>$this->getRequest()->getParam('hashkey')
			);
		$validateRequest=$this->validate($validate);
		$range = $this->getRequest()->getParam('range',array('0'=>'day','2'=>'week','3'=>'month','4'=>'year'));
		$dashboard = Mage::getModel('vendorapi/vendordashboard')->getInfo($vendorId,$range);
		$response=json_encode($dashboard);
		$this->getResponse()->setBody($response);
	}
	/*get notification info for vendor*/
	public function vendornotificationAction(){
		$vendorId =$this->getRequest()->getParam('vendor_id');
		$validate=array(
			'vendor_id'=>$this->getRequest()->getParam('vendor_id'),
			'hash'=>$this->getRequest()->getParam('hashkey')
			);
		$validateRequest=$this->validate($validate);
		$dashboard = Mage::getModel('vendorapi/vendor_api')->getVendorAlert($vendorId);
		$response=json_encode($dashboard);
		$this->getResponse()->setBody($response); 
	}
	/*save vendor device id*/
	public function vendordeviceAction(){
		if($this->getRequest()->isPost()){
    		//$enable= Mage::getStoreConfig('mobinotification/mobinotify/activation');
    		$data=$this->getRequest()->getParams();
    		//Mage::log($data,null,'gcm.log');
    		if(isset($data['Token']) && $data['Token']!=''){
    			$model = Mage::getModel('vendorapi/vendordevices');
		    	$model->setDeviceId($data['Token']);
		    	$model->setVendorId($data['vendor_id']);
		    	$model->save();

		    	$this->getResponse()->setBody(json_encode(array('token_id'=>$model->getData('device_rowid'),'success'=>true)));
		    	return;
			}else{
				$this->getResponse()->setBody(json_encode(array('success'=>false)));
				return;
			}
    	}
	}
	public function isRegistrationEnabledAction()
	{
		$isEnabled = Mage::getStoreConfig('ced_csmarketplace/general/enable_registration');
		$response = array(
			'is_enabled'=>$isEnabled
			);
		$response=json_encode($response);
		$this->getResponse()->setBody($response); 
	}
	public function getCountryAction() {
		$country_code=$this->getRequest()->getParam('country_code','');
		$getCountry = Mage::getModel ( 'vendorapi/vendordashboard' )->getCountry($country_code);
		$response=json_encode($getCountry);
		$this->getResponse()->setBody($response); 
	}
	public function vendorAttributeAction(){
		$vendorId=$this->getRequest()->getParam('customer_id');
		$enabled = '1';
		if (!$enabled){
           // Code To Update Field Value In Vendor form Attribute.
            $collection = Mage::getModel('csmarketplace/vendor_form')->getCollection()->addFieldtoFilter('attribute_code','shop_url');
            foreach ($collection as $value){
                $value->setData('use_in_registration',0);
                $value->save();
            }
            $collection1 = Mage::getModel('csmarketplace/vendor_form')->getCollection()->addFieldtoFilter('attribute_code','public_name');
            foreach ($collection1 as $value1){
            $value1->setData('use_in_registration',0);
            $value1->save();
            }
        }
		else{
			// Code To Update Field Value In Vendor form Attribute.
			$collection = Mage::getModel('csmarketplace/vendor_form')->getCollection()->addFieldtoFilter('attribute_code','shop_url');
			foreach ($collection as $value){
				$value->setData('use_in_registration',1);
				$value->save();
			}
			$collection1 = Mage::getModel('csmarketplace/vendor_form')->getCollection()->addFieldtoFilter('attribute_code','public_name');
			foreach ($collection1 as $value1){
				$value1->setData('use_in_registration',1);
				$value1->save();
			}
		}
        $vendorformFields = $this->getRegistrationAttributes();
        $vformAttribute=array();
        $vendor = Mage::getModel('csmarketplace/vendor')->load($vendorId);
        $model = $vendorId?$vendor->getData():array();
		$id = $vendorId;
		$tcnt=0;
		foreach($vendorformFields as $attribute) {
			$ascn = 0;
			if (!$attribute || ($attribute->hasUseInRegistration() && !$attribute->getUseInRegistration())) {
				continue;
			}
			if ($inputType = $attribute->getFrontend()->getInputType()) {
				if(!isset($model[$attribute->getAttributeCode()]) || (isset($model[$attribute->getAttributeCode()]) && !$model[$attribute->getAttributeCode()])){ $model[$attribute->getAttributeCode()] = $attribute->getDefaultValue();  }
				if($inputType == 'boolean') $inputType = 'select';
				if(in_array($attribute->getAttributeCode(),Ced_CsMarketplace_Model_Form::$VENDOR_REGISTRATION_RESTRICTED_ATTRIBUTES)) {
					continue;
				}
				
				$fieldType  =  $inputType;
			
				if ($inputType == 'select') {
					$element->setValues($attribute->getSource()->getAllOptions(true,true));
				} else if ($inputType == 'multiselect') {
					$values=$attribute->getSource()->getAllOptions(false,true);
					$element->setCanBeEmpty(true);
				} else if ($inputType == 'multiline') {
					$values=$attribute->getMultilineCount();
				}
				$vformAttribute['fieldset'][$tcnt][$attribute->getFrontend()->getLabel()][]=array(
							'label'=>$attribute->getStoreLabel()?$attribute->getStoreLabel():$attribute->getFrontend()->getLabel(),
							'value'=>$model[$attribute->getAttributeCode()],
							'values'=>$values,
							'placeholder' => $attribute->getStoreLabel()?$attribute->getStoreLabel():$attribute->getFrontend()->getLabel(),
							'name'=>$attribute->getAttributeCode(),
							'required'  => $attribute->getIsRequired(),
							'type'=>$fieldType,
							);
			}
			++$tcnt;
		}
		if(count($vformAttribute)>0){
			$data=array(
				'data'=>$vformAttribute		
				);

			$response=json_encode($vformAttribute);
			$this->getResponse()->setBody($response); 
		}
	}
	public function getRegistrationAttributes($storeId = null){
		if($storeId == null) $storeId = Mage::app()->getStore()->getId();
		$attributes =  Mage::getModel('csmarketplace/vendor_attribute')
							->setStoreId($storeId)
							->getCollection()
							->addFieldToFilter('use_in_registration',array('gt'=>0))
							->setOrder('position_in_registration','ASC');
		Mage::dispatchEvent('ced_csmarketplace_registration_attributes_load_after',array('attributes'=>$attributes));
		return $attributes;
	}

	public function approvalPostAction(){
		$data =  $this->getRequest()->getParam('approveaccount');
		$customerId	  =  $this->getRequest()->getParam('customer_id');
		$customer_data=json_decode($data,true);
		$createVendor = Mage::getModel('vendorapi/vendor_api')->approvalPost($customer_data,$customerId);
		$response=json_encode($createVendor);
		$this->getResponse()->setBody($response);
	}
	public function getmoduleListAction(){
		$modules = Mage::getConfig()->getNode('modules')->children();
		$i=1;
		$activemodules=array();
		foreach ($modules as $moduleName => $moduleSettings) {
			 if($moduleName=='Ced_VUPSShippingApi' || 
			 	$moduleName=='Ced_VSocialLoginApi' || 
			 	$moduleName=='Ced_VReviewApi' || 
			 	$moduleName=='Ced_VProductAttributeApi' || 
			 	$moduleName=='Ced_VendorApi' || 
			 	$moduleName=='Ced_VProductApi' || 
			 	$moduleName=='Ced_VOrderApi' 
			 	 ){
			 		if($moduleSettings->is('active')){

			 			$activemodules[$moduleName]=$moduleName;
			 		}
			 }
			 
		}

		if(count($activemodules)>0){
			$data = array (
						'data' => array (
								'modules' => $activemodules, 
								'success'=>true
						) 
				);
		}
		else{
			$data = array (
						'data' => array (
								'modules' => array('no_modules'=>'no_modules'),
								'success'=>false
						) 
				);
		}
		$response=json_encode($data);
		$this->getResponse()->setBody($response);
	}
}
