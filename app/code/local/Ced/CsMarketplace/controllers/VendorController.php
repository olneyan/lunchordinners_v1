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
 
class Ced_CsMarketplace_VendorController extends Ced_CsMarketplace_Controller_AbstractController
{
    /**
	 * Action vendor approval page
     *
     * Display vendor status and show a link for send request to admin (formally admin) for approval.
     */
	public function approvalAction() {
		if ($this->_getSession()->isLoggedIn() && Mage::helper('csmarketplace')->authenticate($this->_getSession()->getCustomerId())) {
            $this->_redirect('*/*/');
            return;
        }
		
		if(!Mage::getStoreConfig('ced_csmarketplace/general/enable_registration',Mage::app()->getStore()->getId())) {
		   $this->_redirect('customer/account/');
		   return;
	   }
		if (!$this->_getSession()->authenticate($this)) $this->setFlag('', 'no-dispatch', true);
		$this->_getSession()->unsVendorId();
		$this->_getSession()->unsVendor();
		if($this->_getSession()->isLoggedIn() && Mage::helper('csmarketplace')->authenticate($this->_getSession()->getCustomerId())) {
			$vendor = Mage::getModel('csmarketplace/vendor')->loadByCustomerId($this->_getSession()->getCustomerId());
			if($vendor && $vendor->getId()) {
				$this->_getSession()->setData('vendor_id',$vendor->getId());
				$this->_getSession()->setData('vendor',$vendor);
			}
		}
		Mage::dispatchEvent('ced_csmarketplace_vendor_approval_after', array(
		'session' => $this->_getSession(),
		));
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle(Mage::helper('csmarketplace')->__('Vendor Approval Status'));
		$this->renderLayout();
	}	
	
	/**
     * Action vendor approval post page
     *
     * register customer as a vendor.
     */
	public function approvalPostAction() {
		if(Mage::app()->getRequest()->getParam('is_vendor')==1){
			$venderData = Mage::app()->getRequest()->getParam('vendor');
			$customerData = $this->_getSession()->getCustomer();
			
			try {
				$vendor = Mage::getModel('csmarketplace/vendor')
						   ->setCustomer($customerData)
						   ->register($venderData);
				
			//Code to Generate Member ID For Every Vendor Registration.
				$prefix = Mage::getStoreConfig('ced_csmarketplace/general/prefixmemberid_active');
				$entityTypeId = Mage::getModel('eav/entity')->setType('csmarketplace_vendor')->getTypeId();
				$member_lastid_model = Mage::getModel('eav/entity_store')->loadByEntityStore($entityTypeId, 0);
				$member_lastid = $member_lastid_model->getIncrementLastId();
				$member_lastid++;
    			$mid = $prefix.$member_lastid;
    			$vendor['member_id'] = $mid;
				if(!$vendor->getErrors()) {
					$vendor->save();
					if($vendor->getStatus() == Ced_CsMarketplace_Model_Vendor::VENDOR_NEW_STATUS) {
						
						$this->_getSession()->addSuccess(Mage::helper('csmarketplace')->__('Your vendor application has been Pending.'));
					} else if ($vendor->getStatus() == Ced_CsMarketplace_Model_Vendor::VENDOR_APPROVED_STATUS) {
						
						$this->_getSession()->addSuccess(Mage::helper('csmarketplace')->__('Your vendor application has been Approved.'));
					}
				} elseif ($vendor->getErrors()) {
					
					foreach ($vendor->getErrors() as $error) {
						$this->_getSession()->addError($error);
					}
					$this->_getSession()->setFormData($venderData);
				} else {
					$this->_getSession()->addError(Mage::helper('csmarketplace')->__('Your vendor application has been denied'));
				}
				
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/vendor/approval');
	}
	
	/**
     * Default vendor account page
     */
	public function indexAction() {

		if(!$this->_getSession()->getVendorId()) return;
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('catalog/session');
		
		/* $this->getLayout()->getBlock('content')->append(
		 $this->getLayout()->createBlock('csmarketplace/vendor_dashboard')
		); */
		$this->getLayout()->getBlock('head')->setTitle(Mage::helper('csmarketplace')->__('Vendor Dashboard'));
		$navigationBlock = $this->getLayout()->getBlock('csmarketplace_vendor_navigation');
		if ($navigationBlock) {
			$navigationBlock->setActive('csmarketplace/vendor/index');
		}
		$this->renderLayout();
	}
	
	public function newAction() {
        
		if(!$this->_getSession()->getVendorId()) return;		
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        /* $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('csmarketplace/vendor_dashboard')
        ); */
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('csmarketplace')->__('Vendor Dashboard'));
        $navigationBlock = $this->getLayout()->getBlock('csmarketplace_vendor_navigation');
        if ($navigationBlock) {
        	$navigationBlock->setActive('csmarketplace/vendor/index');
        }
        $this->renderLayout();
    }
	
	/**
	 * Vendor profile page
	 */
	public function profileAction() {
		if(!$this->_getSession()->getVendorId()) return;
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('catalog/session');

		$this->getLayout()->getBlock('content')->append(
			$this->getLayout()->createBlock('csmarketplace/vendor_dashboard')
		);
		$this->getLayout()->getBlock('head')->setTitle(Mage::helper('csmarketplace')->__('Vendor Profile'));
		$navigationBlock = $this->getLayout()->getBlock('csmarketplace_vendor_navigation');
		if ($navigationBlock) {
			$navigationBlock->setActive('csmarketplace/vendor/profileview');
		}
		$this->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);
		$this->renderLayout();
	}
	
	/**
	 * Vendor profile View page
	 */
	public function profileviewAction() {
		if(!$this->_getSession()->getVendorId()) return;
		$this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

		$this->getLayout()->getBlock('head')->setTitle(Mage::helper('csmarketplace')->__('Vendor Profile View'));
        $this->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);
        $this->renderLayout();
	}
	
	public function saveAction() {
		
		if(!$this->_getSession()->getVendorId()) return;
		if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/profile');
        }
		if ($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('csmarketplace/vendor');

			if($id = $this->_getSession()->getVendorId()) {
				$model->load($id);
				if(isset($data['vendor'])) {
					/**
					 * customization for client
					 */
					
					if(isset($data['vendor']['mon_open_time']))
					{
						$openTime = $data['vendor']['mon_open_time'];
						$openTime = implode(",",$openTime);
						$data['vendor']['mon_open_time']=$openTime;
					}
					if(isset($data['vendor']['mon_close_time']))
					{
						$closeTime= $data['vendor']['mon_close_time'];
						$closeTime = implode(",",$closeTime);
						$data['vendor']['mon_close_time']=$closeTime;
					}
					if(isset($data['vendor']['tue_open_time']))
					{
						$openTime = $data['vendor']['tue_open_time'];
						$openTime = implode(",",$openTime);
						$data['vendor']['tue_open_time']=$openTime;
					}
					if(isset($data['vendor']['tue_close_time']))
					{
						$closeTime= $data['vendor']['tue_close_time'];
						$closeTime = implode(",",$closeTime);
						$data['vendor']['tue_close_time']=$closeTime;
					}
					if(isset($data['vendor']['wed_open_time']))
					{
						$openTime = $data['vendor']['wed_open_time'];
						$openTime = implode(",",$openTime);
						$data['vendor']['wed_open_time']=$openTime;
					}
					if(isset($data['vendor']['wed_close_time']))
					{
						$closeTime= $data['vendor']['wed_close_time'];
						$closeTime = implode(",",$closeTime);
						$data['vendor']['wed_close_time']=$closeTime;
					}
					if(isset($data['vendor']['thu_open_time']))
					{
						$openTime = $data['vendor']['thu_open_time'];
						$openTime = implode(",",$openTime);
						$data['vendor']['thu_open_time']=$openTime;
					}
					if(isset($data['vendor']['thu_close_time']))
					{
						$closeTime= $data['vendor']['thu_close_time'];
						$closeTime = implode(",",$closeTime);
						$data['vendor']['thu_close_time']=$closeTime;
					}
					if(isset($data['vendor']['fri_open_time']))
					{
						$openTime = $data['vendor']['fri_open_time'];
						$openTime = implode(",",$openTime);
						$data['vendor']['fri_open_time']=$openTime;
					}
					if(isset($data['vendor']['fri_close_time']))
					{
						$closeTime= $data['vendor']['fri_close_time'];
						$closeTime = implode(",",$closeTime);
						$data['vendor']['fri_close_time']=$closeTime;
					}
					if(isset($data['vendor']['sat_open_time']))
					{
						$openTime = $data['vendor']['sat_open_time'];
						$openTime = implode(",",$openTime);
						$data['vendor']['sat_open_time']=$openTime;
					}
					if(isset($data['vendor']['sat_close_time']))
					{
						$closeTime= $data['vendor']['sat_close_time'];
						$closeTime = implode(",",$closeTime);
						$data['vendor']['sat_close_time']=$closeTime;
					}
					if(isset($data['vendor']['sun_open_time']))
					{
						$openTime = $data['vendor']['sun_open_time'];
						$openTime = implode(",",$openTime);
						$data['vendor']['sun_open_time']=$openTime;
					}
					if(isset($data['vendor']['sun_close_time']))
					{
						$closeTime= $data['vendor']['sun_close_time'];
						$closeTime = implode(",",$closeTime);
						$data['vendor']['sun_close_time']=$closeTime;
					}
					
					$model->addData($data['vendor']);
					
					try {		
						if($model->validate()) {
							$model->extractNonEditableData();
							$model->save();
							$customer=$this->_getSession()->getCustomer();
							
							// If password change was requested then add it to common validation scheme
							if ($this->getRequest()->getParam('change_password')) {
								$currPass   = $this->getRequest()->getPost('current_password');
								$newPass    = $this->getRequest()->getPost('password');
								$confPass   = $this->getRequest()->getPost('confirmation');

								$oldPass = $this->_getSession()->getCustomer()->getPasswordHash();
								if ( Mage::helper('core/string')->strpos($oldPass, ':')) {
									list($_salt, $salt) = explode(':', $oldPass);
								} else {
									$salt = false;
								}
								
								if ($customer->hashPassword($currPass, $salt) == $oldPass) {
									if (strlen($newPass)) {
										/**
										 * Set entered password and its confirmation - they
										 * will be validated later to match each other and be of right length
										 */
										$customer->setPassword($newPass);
										$customer->setPasswordConfirmation($confPass);
										$customer->save();
										/* echo $customer->getId();die; */
									} else {
										$this->_getSession()->addError($this->__('New password field cannot be empty.'));
									}
								} else {
									$this->_getSession()->addError($this->__('Invalid current password'));
								}
							}
						} elseif ($model->getErrors()) {
							foreach ($model->getErrors() as $error) {
								$this->_getSession()->addError($error);
							}
							$this->_getSession()->setFormData($data);
							$this->_redirect('*/*/profile');
							return;
						}
						$this->_getSession()->setVendor($model)->addSuccess(Mage::helper('csmarketplace')->__('The profile information has been saved.'));
						$this->_redirect('*/*/profileview');
						return;
					} catch (Exception $e) {
						//echo "cmsg: ".$e->getMessage();die;
						$this->_getSession()->addError($e->getMessage());
						$this->_redirect('*/*/profile');
						return;
					}
				}
			}
		}
		$this->_getSession()->addError(Mage::helper('csmarketplace')->__('Unable to find vendor to save'));
		$this->_redirect('*/*/profile');
	}

	public function chartAction() {
		if(!$this->_getSession()->getVendorId()) return;
		$json = array();

		$json['order'] = array();
		//$json['qty'] = array();
		//$json['sale'] = array();
		$json['xaxis'] = array();

		$json['order']['label'] = Mage::helper('csmarketplace')->__('Orders');
		//$json['qty']['label'] = Mage::helper('csmarketplace')->__('Qty');
		//$json['sale']['label'] = Mage::helper('csmarketplace')->__('Sales');
		$json['order']['data'] = array();
		//$json['qty']['data'] = array();
		//$json['sale']['data'] = array();

		$range = $this->getRequest()->getParam('range','day');
	
		$reportHelper = Mage::helper('csmarketplace/report');
		$vendor = Mage::getModel('csmarketplace/vendor')->loadByCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
		
		if($vendor && $vendor->getId()) {
			$order = $reportHelper->getChartData($vendor,'order',$range);
			/* print_r($order);die; */
			//$qty = $reportHelper->getChartData($vendor,'qty',$range);
			//$sale = $reportHelper->getChartData($vendor,'sale',$range);
			
			foreach ($order as $key => $value) {
				$json['order']['data'][] = array($key, $value['total']);
			}
			/* foreach ($qty as $key => $value) {
				$json['qty']['data'][] = array($key, $value['total']);
			}
			foreach ($sale as $key => $value) {
				$json['sale']['data'][] = array($key, $value['total']);
			} */

			switch ($range) {
				default:
				case 'day':

					for ($i = 0; $i < 24; $i++) {
						$json['xaxis'][] = array($i, $i);
					}
					break;
				case 'week':
					$date_start = strtotime('-' . date('w') . ' days');

					for ($i = 0; $i < 7; $i++) {
						$date = date('Y-m-d', $date_start + ($i * 86400));

						$json['xaxis'][] = array(date('w', strtotime($date)), date('D', strtotime($date)));
					}
					break;
				case 'month':

					for ($i = 1; $i <= date('t'); $i++) {
						$date = date('Y') . '-' . date('m') . '-' . $i;

						$json['xaxis'][] = array(date('j', strtotime($date)), date('d', strtotime($date)));
					}
					break;
				case 'year':

					for ($i = 1; $i <= 12; $i++) {
						$json['xaxis'][] = array($i, date('M', mktime(0, 0, 0, $i)));
					}
					break;
			}
			
			
		}

		$this->getResponse()->setHeader('Content-type', 'application/json');
		/* $this->getResponse()->setBody(json_encode($json)); */
		echo json_encode($json);die;

	}
	
	public function mapAction() {
		if(!$this->_getSession()->getVendorId()) return;
		$json = array();
		
		$reportHelper = Mage::helper('csmarketplace/report');
		$vendor = Mage::getModel('csmarketplace/vendor')->loadByCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
		
		if ($vendor && $vendor->getId()) {
			$results = $reportHelper->getTotalOrdersByCountry($vendor);
			
			foreach ($results as $country => $result) {
				$json[strtolower($country)] = array(
					'total'  => (string)$result['total'],
					'amount' => (string)Mage::app()->getLocale()
                                        ->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
									    ->toCurrency($result['amount']),
				);
			}
		}
		$this->getResponse()->setHeader('Content-type', 'application/json');
		/* $this->getResponse()->setBody(json_encode($json)); */
		echo json_encode($json);die;
	}
}
