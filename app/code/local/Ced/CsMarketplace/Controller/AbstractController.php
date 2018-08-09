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
 
class Ced_CsMarketplace_Controller_AbstractController extends Mage_Core_Controller_Front_Action
{

	public static $openActions = array(
            'create',
            'login',
            'logoutsuccess',
            'forgotpassword',
            'forgotpasswordpost',
            'resetpassword',
            'resetpasswordpost',
            'confirm',
            'confirmation',
			'approval',
			'checkAvailability',
			'denied',
			'noRoute'
        );
	public $_allowedResource = true;
	/**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
	/**
     * Action predispatch
     * Check that vendpr is eligible for viewing content
     */
    public function preDispatch()
    {

       parent::preDispatch();
	   if (!$this->getRequest()->isDispatched()) {
            return;
       }
	
	  if(!Mage::getStoreConfig('ced_csmarketplace/general/enable_registration',Mage::app()->getStore()->getId()) && ($this->getRequest()->getActionName() == 'create')) {
		   $this->_redirect('csmarketplace/account/login/');
		   return;
	   }
        
       Mage::dispatchEvent('ced_csmarketplace_predispatch_action', array(
       'session' => $this->_getSession(),
       ));
       
        $action = $this->getRequest()->getActionName();
        self::$openActions = array(
            'create',
            'login',
            'logoutsuccess',
            'forgotpassword',
            'forgotpasswordpost',
            'resetpassword',
            'resetpasswordpost',
            'confirm',
            'confirmation',
			'approval',
			'checkAvailability',
			'denied',
			'noRoute'
        );
        $pattern = '/^(' . implode('|', self::$openActions) . ')/i';
        if (!preg_match($pattern, $action)) {
            if (!$this->authenticate($this)) {	
            	 
                $this->setFlag('', 'no-dispatch', true);
            
            } elseif(!Mage::helper('csmarketplace/acl')->isEnabled()) {
            	$this->_getSession()->addError(Mage::helper('csmarketplace')->__('Marketplace Is Disabled'));
				$this->_redirect('customer/account/login/');
				return;
			} elseif ($this->authenticate($this) && !Mage::helper('csmarketplace')->authenticate($this->_getSession()->getCustomerId())) {
				$this->_getSession()->unsVendorId();
				$this->_getSession()->unsVendor();
				$this->_redirect('*/vendor/approval');
				return;
			}
        } else {
            $this->_getSession()->setNoReferer(true);
        }
	}
	
	/**
	 * Authenticate controller action by login customer
	 *
	 * @param   Mage_Core_Controller_Varien_Action $action
	 * @param   bool $loginUrl
	 * @return  bool
	 */
	public function authenticate(Mage_Core_Controller_Varien_Action $action, $loginUrl = null)
	{
		if (!$this->_getSession()->isLoggedIn()) {
            if($action->getRequest()->isAjax()) {
				$this->_getSession()->setBeforeVendorAuthUrl(Mage::getUrl('*/vendor/', array('_current'=>true,'_secure'=>true,'_nosid'=>true)));
			} else {
				$oAuthUrl = Mage::getUrl('*/*/*', array('_current'=>true,'_secure'=>true,'_nosid'=>true));
            	$this->_getSession()->setBeforeVendorAuthUrl($oAuthUrl);
            }
			if (is_null($loginUrl)) {
				if(Mage::getConfig()->getModuleConfig('Ced_CsVendorPanel')->is('active', 'true'))
				{
					$url='csmarketplace/account/login';
				}else{
					$url='customer/account/login';
				}
                $loginUrl = Mage::getUrl($url, array('_secure' => Mage::app()->getFrontController()->getRequest()->isSecure(),'_nosid'=>true));
            }
            if($action->getRequest()->isAjax()){
            	$ajaxResponse=array();
            	$ajaxResponse['ajaxExpired']=true;
            	$ajaxResponse['ajaxRedirect']=$loginUrl;
            	$action->getResponse()->setBody(json_encode($ajaxResponse));
            	return;
            }
            $action->getResponse()->setRedirect($loginUrl);
            return false;
        }
		
		if($this->_getSession()->isLoggedIn() && Mage::helper('csmarketplace')->authenticate($this->_getSession()->getCustomerId())) {
			$vendor = Ced_CsMarketplace_Helper_Data::$ced_vendor;
			if($vendor && $vendor->getId()) {
				$this->_getSession()->setData('vendor_id',$vendor->getId());
				$this->_getSession()->setData('vendor',$vendor);
				Mage::dispatchEvent('ced_csmarketplace_vendor_authenticate_after', array(
					'session' => $this->_getSession(),
				));
			}
		}
		Mage::dispatchEvent('ced_csmarketplace_vendor_acl_check', array(
					'current' => $this,
					'action'  => $action,
				));
        return $this->_allowedResource;
	}
	
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
		if (!$this->authenticate($this)) $this->setFlag('', 'no-dispatch', true);
		$this->_getSession()->unsVendorId();
		$this->_getSession()->unsVendor();
		if($this->_getSession()->isLoggedIn() && Mage::helper('csmarketplace')->authenticate($this->_getSession()->getCustomerId())) {
			$vendor = Mage::getModel('csmarketplace/vendor')->loadByCustomerId($this->_getSession()->getCustomerId());
			if($vendor && $vendor->getId()) {
				$this->_getSession()->setData('vendor_id',$vendor->getId());
				$this->_getSession()->setData('vendor',$vendor);
			}
		}
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle(Mage::helper('csmarketplace')->__('CsMarketplace Vendor Shops'));
		$this->renderLayout();
	}
	
	/**
     * Action vendor shop url availability
     */
	public function checkAvailabilityAction() {
		if(Mage::app()->getRequest()->getParam('is_vendor')==1){
			$venderData = Mage::app()->getRequest()->getParam('vendor',array());
			$json = Mage::getModel('csmarketplace/vendor')->checkAvailability($venderData);
			$this->getResponse()->setHeader('Content-type', 'application/json');
			$this->getResponse()->setBody(json_encode($json));
		}
		return;
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
     * Action postdispatch
     *
     * Remove No-referer flag from vendor-customer session after each action
     */
    public function postDispatch()
    {
        parent::postDispatch();
        $this->_getSession()->unsNoReferer(false);
    }
	
	public function deniedAction() {
		$this->_forward('noRoute');
	}
	
	protected function _redirect($path, $arguments = array())
	{
		if(Mage::app()->getStore()->isCurrentlySecure()){
			$arguments['_secure'] = true;
		}
		if(version_compare(Mage::getVersion(), '1.6', '<=')) {
			return parent::_redirect($path, $arguments);
		} else {
			return $this->setRedirectWithCookieCheck($path, $arguments);
		}
	}
}