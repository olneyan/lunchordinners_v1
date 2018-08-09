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

class Ced_VendorApi_Model_Vendor_Api extends Mage_Api_Model_Resource_Abstract
{
	protected $_customerSession = null;
	protected $_links = null;
	protected $_urls = null;
	protected $_vendor= null;
	const ID_PATH_SEPARATOR = ':';
	
	public function model()
	{
		return Mage::getResourceModel('vendorapi/vapisession');
	}
	
	public function login( $website, $email, $password )
    {
    	if($website==null || $email==null || $password==null) {
    		$data = array (
					'data' => array (
							'customer' => array (
									array (
											'success' => false ,
											'message'=> 'Email Or Password Is Empty'
									) 
							) 
					) 
			);
			return $data;
    	}
        // determine store to login to
        $store = $this->_getStore($website);
 
        // get customer session object
        $session = $this->_getCustomerSession();
 		try{
        // authenticate customer
	        $authenticated = $session->login($email, $password);
	        
	        if($authenticated && Mage::helper('csmarketplace')->authenticate($session->getCustomerId())) {
	        	$vendor = Mage::getModel('csmarketplace/vendor')->loadByCustomerId($session->getCustomerId());
	        	
	        	if($vendor && $vendor->getId()) {
	        		$this->_customerSession->setData('vendor_id',$vendor->getId());
	        		$this->_customerSession->setData('vendor',$vendor);
	        		
	        		$this->model()->deleteVendorSession($vendor->getId());//clear all vendor session
	        		$this->model()->recordVendorSession($vendor->getId(), $session->getEncryptedSessionId(), $email);//record vendor session

	    			$customerId = $session->getCustomer ()->getId ();
					$secureHash = new Varien_Object ();
					$secureHash->setCustomId ( $customerId );
					$secureHash->setVendorId ($vendor->getId());
					$helper = Mage::helper('csmarketplace/tool_image');
					$img=$helper->init($vendor->getData('profile_picture'));
					$vcp=$this->getVendorLinks($vendor->getId(),true);
					$dats = Mage::dispatchEvent ( 'vlogin_hash_event', array (
							'cid' => $secureHash 
					) );
					$hash = $secureHash->getHash ();
					$data = array (
							'data' => array (
									'customer' => array (
											array (
													'customer_id' => $customerId,
													'vendor_id'=>$vendor->getId(),
													'vendor_name'=>$vendor->getName(),
													'profile_complete'=>$this->profileComplete($vendor->getId()),
													'valerts'=>$this->getVendorAlert($vendor->getId()),
													'vendor_link'=>$vcp,
													'profile_picture'=>$img->__toString (),
													'hashkey' => $hash,
													'success' => true 
											) 
									) 
							) 
					);
					return $data;
	        	}
	        }
	        else{
	        	$vendor = Mage::getModel('csmarketplace/vendor')->loadByCustomerId($session->getCustomerId());
	        	if($vendor && $vendor->getId()){
	        		$msg = Mage::helper('vendorapi')->__('Your vendor account is under admin review.');
	        		if($vendorId->getStatus=='disapproved')
	        			$msg = Mage::helper('vendorapi')->__('Your vendor account has been Disapproved.');
	        		$data = array (
									'data' => array (
											'customer' => array (
													array (
															'message' => $msg,
															'success' => false 
													) 
											) 
									) 
							);
							return $data;
				}
				else{
					$data = array (
									'data' => array (
											'customer' => array (
													array (
															'message' => 'create account',
															'is_vendor'=>$session->getCustomerId(),
															'success' => false 
													) 
											) 
									) 
							);
							return $data;
				}
	        }
	    }
	     catch ( Mage_Core_Exception $e ) {
					switch ($e->getCode ()) {
						case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED :
							// $value = $this->_getHelper('customer')->getEmailConfirmationUrl($login['username']);
							$message = Mage::helper ( 'vendorapi' )->__ ( 'This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value );
							$data = array (
									'data' => array (
											'customer' => array (
													array (
															'message' => $message,
															'success' => false 
													) 
											) 
									) 
							);
							return $data;
							break;
						case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD :
							$message = $e->getMessage ();
							$data = array (
									'data' => array (
											'customer' => array (
													array (
															'message' => $message,
															'success' => false
													) 
											) 
									) 
							);
							return $data;
							break;
						default :
							$message = $e->getMessage ();
							$data = array (
									'data' => array (
											'customer' => array (
													array (
															'message' => $message,
															'success' => false
													) 
											) 
									) 
							);
							return $data;
					}
				}
    }
 	public function forgotPassword($data) {
		$email = $data['email'];
		// echo $email;
		if ($email) {
			if (! Zend_Validate::is ( $email, 'EmailAddress' )) {
				$data = array (
						'data' => array (
								'customer' => array (
										array (
												'message' => 'Invalid Email Address',
												'success' => false
										) 
								) 
						) 
				);
				return $data;
			}
			$customer = Mage::getModel ( 'customer/customer' )->setWebsiteId ( Mage::app ()->getStore ()->getWebsiteId () )->loadByEmail ( $email );
			
			if ($customer->getId ()) {
				try {
					$newResetPasswordLinkToken =  Mage::helper('customer')->generateResetPasswordLinkToken();
                    $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                    $customer->sendPasswordResetConfirmationEmail();
					$data = array (
							'data' => array (
									'customer' => array (
											array (
													'message' => 'If there is an account associated with this '.$email.' you will receive an email with a link to reset your password.',
													'success' => true  
											) 
									) 
							) 
					);
					return $data;
				} catch ( Mage_Core_Exception $e ) {
					
					$data = array (
							'data' => array (
									'customer' => array (
											array (
													'message' => $e->getMessage (),
													'success' => false  
											) 
									) 
							) 
					);
					return $data;
				} catch ( Exception $e ) {
					$data = array (
							'data' => array (
									'customer' => array (
											array (
													'message' => $e->getMessage (),
													'success' => false
											) 
									) 
							) 
					);
					return $data;
				}
			} else {
				$data = array (
						'data' => array (
								'customer' => array (
										array (
												'message' => 'This email address was not found in our records.',
												'success' => false 
										) 
								) 
						) 
				);
				return $data;
			}
		} else {
			$data = array (
					'data' => array (
							'customer' => array (
									array (
											'message' => 'Customer email not specified.',
											'success' => false  
									) 
							) 
					) 
			);
			return $data;
		}
	}
    public function logout($vsession,$vendorId)
    {
    	if($vsession==null || $vendorId==null) {
    		$this->_fault('data_invalid');
    		return false;
    	} 
    	   	
    	if($this->model()->isVendorLoggedIn($vendorId, $vsession)){
    		return $this->model()->deleteVendorSession($vendorId);
    	}
    	return false;
    }
	
	public function create($customerData,$isJustConfirmed = false)
	{	
		if($customerData==null) { 
			$data = array (
					'data' => array (
							'customer' => array (
									array (
											'success' => false ,
											'message'=> 'Customer Data IS Empty'
									) 
							) 
					) 
			);
			return $data;
		}
		
		if(is_object($customerData))
		{
			$customerData = json_decode(json_encode($customerData), true);
		}

		$customer = $this->createCustomer($customerData);
		
		try {
			$vendorData = array();
		//	var_dump(json_encode($customerData['vendor']));die;
			foreach($customerData['vendor'] as $value)
			{
				$vendorData[$value['key']] =  $value['value'];
			}
			
			$vendor = Mage::getModel('csmarketplace/vendor')
						   ->setCustomer($customer)
						   ->register($vendorData);
			$vendorExist=$this->isVendorExist($vendor);
			if($vendorExist){
				$data = array (
					'data' => array (
							'customer' => array (
									array (
							
											'success' => false ,
											'message'=> 'Vendor with email id already exist'
									) 
							) 
					) 
			);
			return $data;
			}
				if(!$vendor->getErrors())
					$vendor->save();

				elseif($vendor->getErrors()) { 
					$messge=$vendor->getErrors();
					Mage::throwException($messge['0']);
				}
			if($vendor->getStatus() == Ced_CsMarketplace_Model_Vendor::VENDOR_APPROVED_STATUS) {
				if ($customer->isConfirmationRequired ()) {
						/**
					 *
					 * @var $app Mage_Core_Model_App
					 */
					$app = Mage::app ();
					/**
					 *
					 * @var $store Mage_Core_Model_Store
					 */
					$store = $app->getStore ();
					$customer->sendNewAccountEmail ( 'confirmation', $session->getBeforeAuthUrl (), $store->getId () );
					$message = Mage::helper ( 'mobiconnect' )->__ ( 'Account confirmation is required. Please, check your email for the confirmation link.' );
					$data = array (
						'data' => array (
								'customer' => array (
										array (
												'isConfirmationRequired'=>'YES',
												'success' => true,
												'message'=> $message
										) 
								) 
						) 
					);
					return $data;
				}
				else{
					$customerId = $customer->getId ();
					$vendorId = $vendor->getId ();
					$secureHash = new Varien_Object ();
					$secureHash->setCustomId ( $customerId );
					$secureHash->setVendorId ( $vendorId );
					$vcp=$this->getVendorLinks($vendorId,true);
					$dats = Mage::dispatchEvent ( 'vlogin_hash_event', array (
							'cid' => $secureHash 
						));
					$hash = $secureHash->getHash();
					$helper = Mage::helper('csmarketplace/tool_image');
					$img=$helper->init($vendor->getData('profile_picture'));
					$customer->sendNewAccountEmail ( $isJustConfirmed ? 'confirmed' : 'registered', '', Mage::app ()->getStore ()->getId () );
					$message = Mage::helper ( 'vendorapi' )->__ ( "Thank you for registering with " . Mage::app ()->getStore ()->getName () . " store" );
					$data = array (
						'data' => array (
								'customer' => array (
										array (
												'vendor_id' =>$vendor->getId(),
												'customer_id'=>$customer->getId(),
												'isConfirmationRequired'=>'NO',
												'vendor_name'=>$vendor->getName(),
												'vendor_link'=>$vcp,
												'profile_complete'=>$this->profileComplete($vendor->getId()),
												'valerts'=>$this->getVendorAlert($vendor->getId()),
												'profile_picture'=>$img->__toString (),
												'hashkey' => $hash,
												'success' => true,
												'message'=> $message
										) 
								) 
						) 
					);
					return $data;
				}
			}
			$customer->sendNewAccountEmail ( $isJustConfirmed ? 'confirmed' : 'registered', '', Mage::app ()->getStore ()->getId () );
			$message = Mage::helper ( 'vendorapi' )->__ ( "Thank you for registering with " . Mage::app ()->getStore ()->getName () . " store" );
			$data = array (
					'data' => array (
							'customer' => array (
									array (
											'isConfirmationRequired'=>'YES',
											'success' => true,
											'message'=> $message
									) 
							) 
					) 
				);
			return $data;
		} catch (Mage_Core_Exception $e) {
			$data = array (
					'data' => array (
							'customer' => array (
									array (
							
											'success' => false ,
											'message'=> $e->getMessage()
									) 
							) 
					) 
			);
			return $data;
		}
		catch ( Exception $e ) {
			$data = array (
					'data' => array (
							'customer' => array (
									array (
											'success' => false ,
											'message'=> $e->getMessage()
									) 
							) 
					) 
			);
			return $data;
		}
	}
	public function isVendorExist($vendor){

		
		$vendor=Mage::getModel('csmarketplace/vendor')->setStoreId(Mage::app()->getStore()->getId())->loadByAttribute('email',$vendor['email']);
		
		if(is_object($vendor))
			return true;
		return false;
	}
	public function createCustomer($customerData)
	{
		//var_dump($customerData);die;
		$website_id = Mage::app()->getWebsite()->getId(); 
		$isCustomerExists = $this->IscustomerExists($customerData['email'], $website_id);
		if($isCustomerExists && $isCustomerExists->getId())
			return $isCustomerExists;
		
		
		try 
		{
			$customer = Mage::getModel('customer/customer')
			->setData($customerData)
			->save();
		} catch (Mage_Core_Exception $e) {
			echo $e->getMessage();
		}
		return $customer;
	}
	
	function IscustomerExists($email, $websiteId = null){
		$customer = Mage::getModel('customer/customer');
	
		if ($websiteId) {
			$customer->setWebsiteId($websiteId);
		}
		$customer->loadByEmail($email);
		if ($customer->getId()) {
			return $customer;
		}
		return false;
	}
	
	public function info($vendorId, $attributes = null)
	{
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
			return $data;
	}
	
	public function items($data)
	{
		
		if(isset($data['sellersearch'])){
			$search = $data['sellersearch'];
			$data['search'] = json_decode($search,true);
		}

		$limit ='5';
		$offset = $data['page'];
		$curr_page = '1';
		
		if ($offset != 'null' && $offset != '') {
			$curr_page = $offset;
		}

		$offset = ($curr_page - 1) * $limit;
		$helper = Mage::helper('csmarketplace/tool_image');

		$img = Mage::getStoreConfig("ced_vshops/general/vshoppage_banner",Mage::app()->getStore()->getId())?"ced/csmarketplace/".Mage::getStoreConfig("ced_vshops/general/vshoppage_banner",Mage::app()->getStore()->getId()):'';

		//echo $img;die;
		$collection = Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('status','approved');


		if( isset($data['search']) && count($data['search'])>0){
			if(isset($data['search']['country']) && $data['search']['country']!='')
			{
				
				$collection->addAttributeToFilter('country_id',$data['search']['country']);
			}
			if(isset($data['search']['zip']) && $data['search']['zip']!='')
			{
				$collection->addAttributeToFilter('zip_code',$data['search']['zip']);
			}
			if(isset($data['search']['state']) && $data['search']['state']!='')
			{ 
				$collection->addAttributeToFilter('region',$data['search']['state']);
			}
			if(isset($data['search']['region_id']) && $data['search']['region_id']!='')
			{
				$collection->addAttributeToFilter('region_id',$region_id);
			}
			
			if(isset($data['search']['vendorname']) && $data['search']['vendorname']!='')
			{
				$collection->addAttributeToFilter('public_name',array('like'=>'%'.$data['search']['vendorname'].'%'));
			}
			if(isset($data['search']['estimate_city']) && $data['search']['estimate_city']!=''){
				$collection->addAttributeToFilter('city',$data['search']['estimate_city']);
			}
		}

		$collection->getSelect ()->limit ($limit,$offset);
		/*if (is_array($filters)) {
			try {
				foreach ($filters as $field => $value) {
					$collection->addFieldToFilter($field, $value);
				}
			} catch (Mage_Core_Exception $e) {
				$this->_fault('filters_invalid', $e->getMessage());
			}
		}*/
		$vendors = array();
		$count = 0;
		foreach ($collection as $vendor) { 
			$changeData = new Varien_Object ();
			$changeData->setResponse ($vendor);
			$changeData->setIsVendorList('1');
			Mage::dispatchEvent ( 'get_vendor_review', array (
							'vendor_info' => $changeData 
					) );
			$vendors[] = $vendor->toArray();

			$url = (isset($vendors[$count]['company_logo']) && $vendors[$count]['company_logo'])? $vendors[$count]['company_logo']:'';
			$newurl= ($url=='')?Mage::getBaseUrl():$helper->init($url)->__toString ();
			$vendors[$count]['company_logo']= $newurl;

			$banner_url = (isset($vendors[$count]['company_banner']) && $vendors[$count]['company_banner']) ? $vendors[$count]['company_banner']:'';
			$vendors[$count]['company_banner']=($banner_url=='')?Mage::getBaseUrl():$helper->init($banner_url)->__toString ();

			$profile_url = (isset($vendors[$count]['profile_picture']) && $vendors[$count]['profile_picture']) ? $vendors[$count]['profile_picture']:'';

			$vendors[$count]['profile_picture']=($profile_url=='')? Mage::getBaseUrl():$helper->init($profile_url)->__toString ();

			//$newurl='';
			++$count;	
		}
		$response = array();

		if(count($vendors)){
			$response['data']['sellers']=$vendors;
			$response['data']['banner_url']=($img=='')?Mage::getBaseUrl():$helper->init($img,'csbanner')->__toString ();
			$response['data']['success']=true;
		}
		else{
			$response['data']['sellers']=$vendors;
			$response['data']['banner_url']=($img=='')?Mage::getBaseUrl():$helper->init($img,'csbanner')->__toString ();
			$response['data']['message']='Sorry!! No vendor available.';
			$response['data']['success']=false;
		}
		
		return $response;
	}
	
	public function update($vendorId,$vendorData)
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
	}
	public function delete($vendorId)
	{
		if($vendorId==null) {
			$this->_fault('data_invalid');
			return false;
		}
		$vendor = Mage::getModel('csmarketplace/vendor')->load($vendorId);
		if($vendor && $vendor->getId())
		{
			try {
				Mage::getModel('csmarketplace/vproducts')->deleteVendorProducts($vendor->getId());
				$vendor->delete();
			} catch (Exception $e){
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				}
		}		
		elseif (!$vendor->getId()) {
			$this->_fault('not_exists');
			return false;
		}
		return true;
	}
	
	protected function _getStore( $code = null )
	{
		// get customer session
		$session = $this->_getCustomerSession();
	
		// if website code not supplied, check for selected store in register or selected website in session
		if ( null === $code ) {
			// try to get selected store from register
			$store = Mage::registry('current_store');
			if ( $store ) {
				return $store;
			}
			 
			// try to get selected website code from session
			$code = $session->getCurrentWebsiteCode();
			if ( !$code ) {
				// if no store in register or website code in session, throw an exception
				Mage::throwException(Mage::helper('mymodule')->__('No Store set'));
			}
		}
	
		// load website from code
		/** @var Mage_Core_Model_Website $website */
		$website = Mage::getModel('core/website')
		->load($code, 'code');
		if ( !$website->getId() ) {
			// if unknown website, throw an exception
			Mage::throwException(Mage::helper('mymodule')->__('Invalid Store') . $code);
		}
		 
		// get the default store of the website
		$store = $website->getDefaultStore();
		 
		// register the current store
		Mage::app()->setCurrentStore($store);
		Mage::register('current_store', $store, true);
		 
		// set the current website code in the session
		$session->setCurrentWebsiteCode($website->getCode());
		 
		// return store object
		return $store;
	}
	
	protected function _getCustomerSession()
	{
		if ( !$this->_customerSession ) {
			$this->_customerSession = Mage::getSingleton('customer/session');
		}
		return $this->_customerSession;
	}
	
	public function getVendorLinks($vendorId,$vsession,$link = false, $p_name='') {
		
		if($vendorId==null) {
			$data = array (
					'data' => array (
						'success' => false ,
						'message'=> 'Vendor Id Is Empty'		
					) 
			);
			return $data;
		}
		
		if(count($this->_links) == 0) {
			Mage::app()->cleanCache();
			
			$appEmulation = Mage::getSingleton('core/app_emulation');
			$initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation('default','frontend');
			
			Mage::getDesign()->setPackageName('ced');
			Mage::getDesign()->setTheme('default');
			
			Mage::app()->cleanCache();
			Mage::app()->getLayout()->getUpdate()->addHandle(array('default','csmarketplace_vendor'));
			Mage::app()->getLayout()->getUpdate()->load();
			Mage::app()->getLayout()->generateXml();
			Mage::app()->getLayout()->generateBlocks();
			$linkBlock = Mage::app()->getLayout()->getBlock('csmarketplace_vendor_navigation');
			if(is_object($linkBlock)) {
				$this->_links = Mage::app()->getLayout()->getBlock('csmarketplace_vendor_navigation')->getLinks();
			}
				
			/* print_r($this->_links);die; */
	
			$appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
		}
		if($link) {
			foreach($link as $name=>$tmp_link) {
				
				//$this->_urls[$p_name]['children'][$name] = $tmp_link->getData();
				//$p_name[$name] = $tmp_link->getData();
				$t=$tmp_link->getData();
				$this->_urls[]=$t['label'];
				if(count($tmp_link->getChildren()) > 0) {
					$this->getVendorLinks($vendorId,false,$tmp_link->getChildren(),$name);
				}
				else
					unset($this->_urls[$p_name]['children'][$name]['children']);
			}
		} elseif(count($this->_links) > 0) {
			$csmarketplaceHelper = Mage::helper('csmarketplace');
				foreach($this->_links as $name=>$tmp_link) {
				
					//$this->_urls[$name]=$tmp_link->getData();
					$t=$tmp_link->getData();
					$this->_urls[]=$t['label'];
					if(count($tmp_link->getChildren()) > 0) {
						$this->getVendorLinks($vendorId,false,$tmp_link->getChildren(),$name);
						//$this->getVendorLinks($vendorId, $vsession, $tmp_link->getChildren(),$this->_urls[$name]['children']);
					}
					else 
						unset($this->_urls[$name]['children']);
			}
		}
		if($vsession){
			return $this->_urls;
		}
		$vendor = Mage::getModel('csmarketplace/vendor')->load($vendorId);
		$helper = Mage::helper('csmarketplace/tool_image');
		$img=$helper->init($vendor->getData('profile_picture'));
		$data=array(
			'data'=>array(
			'vendor_navigation'=>$this->_urls,
			'profile_complete'=>$this->profileComplete($vendorId),
			'valerts'=>$this->getVendorAlert($vendorId),
			'vendor_name'=>$vendor->getName(),
			'profile_picture'=>$img->__toString (),
			'success' => true ,
			)
			);
		return $data;
	}
	public function getVendorAlert($vendorId){
		if($vendorId==null) {
			$data = array (
					'data' => array (
							'vendor' => array (
									array (
											'success' => false ,
											'message'=> 'Vendor Id Is Empty'
									) 
							) 
					) 
			);
			return $data;
		}
		if(Mage::helper('core')->isModuleEnabled('Ced_CsMultiShipping')){
			$helper = Mage::helper ( 'csmultishipping' );
			$vendorMethods=Mage::helper('csmultishipping')->getVendorMethods($vendorId);
			$vendorAddress=Mage::helper('csmultishipping')->getVendorAddress($vendorId);
		}
		$this->_vendor = Mage::getModel('csmarketplace/vendor');
		$vendor_info = $this->_vendor->load($vendorId);
		$vendorPaymentMethod = count($this->_vendor->getPaymentMethodsArray($vendorId,false));	
		$vendorAlert = array();
		if(!$vendor_info->getData('company_logo')){
			$vendorAlert[] = array(
				'tag'=>'Add Company Logo',
				'link_to'=>'Vendor Profile'
				);
		}
		if(!$vendor_info->getData('profile_picture')){
			$vendorAlert[] = array(
					'tag'=>'Add Profile Picture',
					'link_to'=>'Vendor Profile'
					);
		}
		if(!$vendor_info->getData('company_banner')){
			$vendorAlert[] = array(
					'tag'=>'Add Company Banner',
					'link_to'=>'Vendor Profile'
					);
		}

		if(!Mage::helper('csmarketplace')->isShopEnabled($vendor_info)){
			$vendorAlert[] = array(
					'tag'=>'Your Shop is disabled By Admin',
					'link_to'=>'Vendor Profile'
					);
		}
		if(Mage::helper('core')->isModuleEnabled('Ced_CsMultiShipping')){
			if(count($vendorMethods)==0){
				$vendorAlert[] = array(
						'tag'=>'Setup Shipping Methods Details',
						'link_to'=>'Shipping Method'
						);
			}
			if(!Mage::helper('csmultishipping')->validateAddress($vendorAddress)){
				$vendorAlert[] = array(
						'tag'=>'Complete Shipping Origin Setting',
						'link_to'=>'Shipping Origin'
						);
			}
		}
		$isFirst = !count(Mage::getModel('csmarketplace/vproducts')->getVendorProducts('',$vendorId));
		if($isFirst) {
			$vendorAlert[] = array(
					'tag'=>'Add Your First Product',
					'link_to'=>'Add New Product'
					);
		}
		if(!$vendorPaymentMethod) {
			$vendorAlert[] = array(
					'tag'=>'Add your Payment Details',
					'link_to'=>'Payment Setting'
					);
		}
		$action = Mage::app()->getRequest()->getActionName();
		if($action=='vendornotification'){
			$data = array(
					'vendorAlert'=>$vendorAlert,
					'alert_count'=>count($vendorAlert),
					'profile_count'=>'1',
					'profile_complete'=>$this->profileComplete($vendorId),
				);
			return $data;
		}
		return count($vendorAlert);
	}
	public function profileComplete($vendorId){
		if($vendorId==null) {
			$data = array (
					'data' => array (
							'vendor' => array (
									array (
											'success' => false ,
											'message'=> 'Vendor Id Is Empty'
									) 
							) 
					) 
			);
			return $data;
		}
		$this->_vendor = Mage::getModel('csmarketplace/vendor');
		$vendor_info = $this->_vendor->load($vendorId);
		$this->_totalattr = 0;
		$this->_savedattr = 0;
 		$entityTypeId  = $this->_vendor->getEntityTypeId();
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
		$total = 0;
		$saved = 0;
		foreach($groupCollection as $group){
			$attributes = $this->_vendor->getAttributes($group->getId(), true);
			if (count($attributes)==0) {
				continue;
			}
			
			foreach ($attributes as $attr){
				$attribute = Mage::getModel('csmarketplace/vendor_attribute')->setStoreId(0)->load($attr->getid());
				if(!$attribute->getisVisible()) continue;
				$total += 1;
				if($vendor_info->getData($attr->getAttributeCode())){
					$saved++;
				}
			}
		}
		$this->_totalattr = $total;
		$this->_savedattr = $saved;	
		$percent = round(($this->_savedattr * 100)/$this->_totalattr);
		$percent = round($percent, 2);
		return $percent;
	}
	public function approvalPost($customerData,$customerId){
		$customer = Mage::getModel('customer/customer')->load($customerId);
		try {
			$vendorData = array();
			foreach($customerData['vendor'] as $value)
			{
				$vendorData[$value['key']] =  $value['value'];
			}
			$vendor = Mage::getModel('csmarketplace/vendor')
						   ->setCustomer($customer)
						   ->register($vendorData);
			$vendorExist=$this->isVendorExist($vendor);
			if($vendorExist){
				$data = array (
					'data' => array (
							'customer' => array (
									array (
							
											'success' => false ,
											'message'=> 'Vendor with email id already exist'
									) 
							) 
					) 
			);
			return $data;
			}
			if(!$vendor->getErrors()) {
				$vendor->save();
				$name = explode(' ',$vendor->getName());
				$first_name = $name[0];
				$last_name = $name[1];
				if($vendor->getStatus() == Ced_CsMarketplace_Model_Vendor::VENDOR_NEW_STATUS) {
					$message = Mage::helper ( 'vendorapi' )->__ ( "Your vendor application has been Pending.");
					$data = array (
							'data' => array (
									'customer' => array (
											array (
													'isConfirmationRequired'=>'YES',
													'success' => true,
													'first_name'=>$first_name,
													'last_name'=>$last_name,
													'message'=> $message
											) 
									) 
							) 
						);
					return $data;
				} else if ($vendor->getStatus() == Ced_CsMarketplace_Model_Vendor::VENDOR_APPROVED_STATUS) {
					$vendorId = $vendor->getId ();
					$secureHash = new Varien_Object ();
					$secureHash->setCustomId ( $customerId );
					$secureHash->setVendorId ( $vendorId );
					$vcp=$this->getVendorLinks($vendorId,true);
					$dats = Mage::dispatchEvent ( 'vlogin_hash_event', array (
							'cid' => $secureHash 
						));
					$hash = $secureHash->getHash();
					$helper = Mage::helper('csmarketplace/tool_image');
					$img=$helper->init($vendor->getData('profile_picture'));
					$message = Mage::helper ( 'vendorapi' )->__ ( "Your vendor application has been Approved." );
					$data = array (
						'data' => array (
								'customer' => array (
										array (
												'vendor_id' =>$vendor->getId(),
												'customer_id'=>$customer->getId(),
												'isConfirmationRequired'=>'NO',
												'vendor_name'=>$vendor->getName(),
												'vendor_link'=>$vcp,
												'profile_complete'=>$this->profileComplete($vendor->getId()),
												'valerts'=>$this->getVendorAlert($vendor->getId()),
												'profile_picture'=>$img->__toString (),
												'hashkey' => $hash,
												'success' => true,
												'message'=> $message
										) 
								) 
						) 
					);
					return $data;
				}
			} elseif ($vendor->getErrors()) {
				$messge=$vendor->getErrors();
				Mage::throwException($messge['0']);
			} else {
				$message = Mage::helper ( 'vendorapi' )->__ ( "Your vendor application has been denied.");
					$data = array (
							'data' => array (
									'customer' => array (
											array (
													'isConfirmationRequired'=>'YES',
													'success' => true,
													'message'=> $message
											) 
									) 
							) 
						);
				return $data;
			}
			
		} catch (Exception $e) {
				$data = array (
					'data' => array (
							'customer' => array (
									array (
							
											'success' => false ,
											'message'=> $e->getMessage()
									) 
							) 
					) 
			);
			return $data;
		}
	}
}
?>
