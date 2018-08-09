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
 
class Ced_CsMarketplace_Adminhtml_VendorController extends Ced_CsMarketplace_Controller_Adminhtml_AbstractController
{
	
	protected function _initVendor($idFieldName = 'vendor_id')
    {
        $this->_title($this->__('CsMarketplace'))->_title($this->__('Manage Vendors'));

        $vendorId = (int) $this->getRequest()->getParam($idFieldName);
        $vendor = Mage::getModel('csmarketplace/vendor');

        if ($vendorId) {
            $vendor->load($vendorId);
        }

        Mage::register('current_vendor', $vendor);
        return $this;
    }
	public function countryAction(){
		$regionCollection = Mage::getModel('directory/region')->getCollection()->addCountryFilter($this->getRequest()->getParam('cid'));
		//print_r($regionCollection->getData());die;
		if($regionCollection->getData()!=null){
			echo 'true';
		}else{
			echo 'false';
		}
	}
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('csmarketplace/vendor/entity')
			->_addBreadcrumb(Mage::helper('csmarketplace')->__('Vendor'), Mage::helper('csmarketplace')->__('Vendor'));
		return $this;
	}
	
	public function indexAction() {
        $this->_initAction();
		$this->getLayout()->getBlock('head')->setTitle(Mage::helper('csmarketplace')->__('Manage Vendors'));
		$this->renderLayout();
	}
	
	public function gridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
	
	public function vproductsgridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
	
	public function vordersgridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
	
	public function vpaymentsgridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
	
	public function editAction() {
		$vendor_id     = $this->getRequest()->getParam('vendor_id');
		$model  = Mage::getModel('csmarketplace/vendor')->load($vendor_id);

		if ($model->getId() || $vendor_id == 0) {
			Mage::register('vendor_data', $model);		
			$this->loadLayout();				
			$this->_setActiveMenu('csmarketplace/vendor');
			/* 
				$this->_addContent($this->getLayout()->createBlock('csmarketplace/adminhtml_vendor_entity_edit','vendor_edit_form_edit'))
					 ->_addLeft($this->getLayout()->createBlock('csmarketplace/adminhtml_vendor_entity_edit_tabs','vendor_edit_form_tabs'));
			*/
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('csmarketplace')->__('Vendor does not exist'));
			$this->_redirect('*/*/');
		}
	}
	
	public function newAction() {
		$this->_forward('edit');
	}
 
public function saveAction() {
		
		if ($data = $this->getRequest()->getPost()) {
			Mage::app()->cleanCache();
			$model = Mage::getModel('csmarketplace/vendor');
			//print_r($data); die("fvkg");
			
			/**
			 * Customization for client
			 * 
			 */
			if(isset($data['vendor']['open_time']))
			{
				$openTime = $data['vendor']['open_time'];
				$openTime = implode(",",$openTime);
				$data['vendor']['open_time']=$openTime;
			}
			if(isset($data['vendor']['close_time']))
			{
				$closeTime= $data['vendor']['close_time'];
				$closeTime = implode(",",$closeTime);
				$data['vendor']['close_time']=$closeTime;
			}
			
			
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
			
		/* 	if(isset($data['vendor']['delivery_time']))
			{
				$deliveryTime = $data['vendor']['delivery_time'];
				$deliveryTime = implode(",",$deliveryTime);
				$data['vendor']['delivery_time']=$deliveryTime;
			} */
			
			
			if(!$id = $this->getRequest()->getParam('vendor_id')) {
			if(!isset($data['vendor']['customer_id']))
			{
				$websiteId = Mage::app()->getWebsite()->getId();
				
				$store = Mage::app()->getStore();
				
				$customer = Mage::getModel("customer/customer");
				$customer->setWebsiteId($data['website_id']);
				//->setStore($store)
				$name = explode(' ',$data['vendor']['name']);
				if(count($name)>=2)
				{
					$customer->setFirstname($name[0]);
				    $customer->setLastname($name[1]);
				}
				else{
					$customer->setFirstname($name[0]);
				    $customer->setLastname($name[0]);
				}
				
				$customer->setEmail($data['vendor']['email']);
				$customer->setPassword($data['password']);
				$customer->setCountryId($data['vendor']['country_id']);
				$customer->setPostcode($data['vendor']['zip_code']);
				$customer->setCity($data['vendor']['city']);
				$customer->setTelephone($data['vendor']['contact_number']);
				
				try{
					$customer->save();
				}
				catch (Exception $e) {
					Zend_Debug::dump($e->getMessage());
				}
				//die("gvvj");
			}
			
			$customerid = $customer->getId();
			$data['vendor']['customer_id'] = $customerid;
			}
			
			if($id = $this->getRequest()->getParam('vendor_id')) {
				$model->load($id);
				if($model && $model->getId()) {
					$custometId = (int)$model->getCustomerId();
					if(isset($data['vendor']['customer_id'])) unset($data['vendor']['customer_id']);
				}
			}
			$customer = Mage::getModel('customer/customer')->load($customerid);
			if ($customer && $customer->getId()) {
				$data['vendor']['email'] = $customer->getEmail();
			}

			if (!$this->getRequest()->getParam('vendor_id')){
				//Code to Generate Member ID For Every Vendor Registration.
                $prefix = Mage::getStoreConfig('ced_csmarketplace/general/prefixmemberid_active',Mage::app()->getStore()->getId());

                $entityTypeId = Mage::getModel('eav/entity')->setType('csmarketplace_vendor')->getTypeId();

                $eavEntityStore = Mage::getModel('eav/entity_store')->load($id, 'field_name');


                $mid = $prefix.'100000';
                $m_id = Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToSelect('*');
                $m_id->setOrder('entity_id', 'DESC');
                $m_id->getSelect()->limit(1);
                foreach ($m_id as $value) {
                    $id = $value->getMemberId();
                }
                if ($id) {
                    $mid = $id;
                    $regexp = "/([A-Z]+)([0-9]+)/";
                    preg_match($regexp, $mid, $matches);
                    $num = $matches[2]+1;
                    $mid = $prefix.$num;
                }
                $model['member_id'] = $mid;
			}
			
			
			$vendorData = array_merge(Mage::helper('csmarketplace/acl')->getDefultAclValues(),$data['vendor']);
			$model->addData($vendorData);
			
			$model['created_at'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
			try {
				if($model->validate()) {
					$model->save();
					$model->savePaymentMethods($this->getRequest()->getParam('groups',array()));
				} elseif ($model->getErrors()) { 
					
					foreach ($model->getErrors() as $error) {
						Mage::getSingleton('adminhtml/session')->addError($error);
					}
					Mage::getSingleton('adminhtml/session')->setFormData($data);
					$this->_redirect('*/*/edit', array('vendor_id' => $model->getId()));
					return;
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('csmarketplace')->__('Vendor is successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('vendor_id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('vendor_id' => $this->getRequest()->getParam('vendor_id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('csmarketplace')->__('Unable to find vendor to save'));
        $this->_redirect('*/*/');
	}
	
	/**
     * Delete vendor action
     */
    public function deleteAction()
    {
        $this->_initVendor();
        $vendor = Mage::registry('current_vendor');
        if ($vendor->getId()) {
            try {
                $vendor->load($vendor->getId());
                Mage::getModel('csmarketplace/vproducts')->deleteVendorProducts($vendor->getId());
                Mage::helper('csmarketplace/mail')->sendAccountEmail(Ced_CsMarketplace_Model_Vendor::VENDOR_DELETED_STATUS,'',$vendor);
                $vendor->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('csmarketplace')->__('The vendor has been deleted.'));
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }
	
	/**
     * Delete vendor(s) action
     *
     */
	public function massDeleteAction()
    {
        $vendorIds = $this->getRequest()->getParam('vendor_id');
        if (!is_array($vendorIds)) {
            $this->_getSession()->addError(Mage::helper('csmarketplace')->__('Please select vendor(s).'));
        } else {
            if (!empty($vendorIds)) {
                try {
                    foreach ($vendorIds as $vendorId) {
                        $vendor = Mage::getModel('csmarketplace/vendor')->load($vendorId);
                        Mage::dispatchEvent('csmarketplace_controller_adminhtml_vendor_delete', array('vendor' => $vendor));
                        Mage::getModel('csmarketplace/vproducts')->deleteVendorProducts($vendorId);
                        Mage::helper('csmarketplace/mail')->sendAccountEmail(Ced_CsMarketplace_Model_Vendor::VENDOR_DELETED_STATUS,'',$vendor);
                        $vendor->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($vendorIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }
	
	/**
     * Update vendor(s) status action
     *
     */
	public function massStatusAction()
    {
		$inline = $this->getRequest()->getParam('inline',0);
		$vendorIds = $this->getRequest()->getParam('vendor_id');
		$status    = $this->getRequest()->getParam('status','');
		if($inline) {
			$vendorIds = array($vendorIds);
		}
		if(!is_array($vendorIds)) {
           $this->_getSession()->addError($this->__('Please select vendor(s)'));
        } else {
            try {
				$model = $this->_validateMassStatus($vendorIds, $status);
				$model->saveMassAttribute($vendorIds,array('code'=>'status', 'value' => $status));
				$this->_getSession()->addSuccess(
					$this->__('Total of %d record(s) have been updated.', count($vendorIds))
				);
				
            }catch (Mage_Core_Model_Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			} catch (Mage_Core_Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			} catch (Exception $e) {
				$this->_getSession()->addException($e, $this->__($e->getMessage().' An error occurred while updating the vendor(s) status.'));
			}
        }
		
		$this->_redirect('*/*/index',array('_secure'=>true));

    }
    
    /**
     * Disable vendor(s) Shop action
     *
     */
    public function massDisableAction()
    {
    	$inline = $this->getRequest()->getParam('inline',0);
    	$vendorIds = $this->getRequest()->getParam('vendor_id');
    	$shop_disable   = $this->getRequest()->getParam('shop_disable','');
    	if($inline) {
    		$vendorIds = array($vendorIds);
    	}
    	if(!is_array($vendorIds)) {
    		$this->_getSession()->addError($this->__('Please select vendor(s)'));
    	} else {
    		try {
    			$model = Mage::getModel('csmarketplace/vshop');
    			$change = $model->saveShopStatus($vendorIds,$shop_disable);
    			$this->_getSession()->addSuccess(
    					$this->__('Total of %d shop(s) have been updated.',$change)
    			);
    
    		}catch (Mage_Core_Model_Exception $e) {
    			$this->_getSession()->addError($e->getMessage());
    		} catch (Mage_Core_Exception $e) {
    			$this->_getSession()->addError($e->getMessage());
    		} catch (Exception $e) {
    			$this->_getSession()->addException($e, $this->__($e->getMessage().' An error occurred while updating the vendor(s) status.'));
    		}
    	}
    
    	$this->_redirect('*/*/index',array('_secure'=>true));
    
    }
	
	/**
     * Validate batch of vendors before theirs status will be set
     *
     * @throws Mage_Core_Exception
     * @param  array $vendorIds
     * @param  String $status
     * @return Ced_CsMarketplace_Model_Vendor
     */
    public function _validateMassStatus(array $vendorIds, $status)
    {
	    if (Mage::getStoreConfig('ced_csmarketplace/general/shopurl_active')){
	            $model = Mage::getModel('csmarketplace/vendor');
	            if ($status == Ced_CsMarketplace_Model_Vendor::VENDOR_APPROVED_STATUS) {
	                if (!$model->validateMassAttribute('shop_url',$vendorIds)) {
	                    throw new Mage_Core_Exception(
	                        $this->__('Some of the processed vendors have no Shop URL value defined. Please fill it prior to performing operations on these vendors.')
	                    );
	                }
	             }
	        return $model;
	        }
	        else{
	            $model = Mage::getModel('csmarketplace/vendor');
	            return $model;
	        }
    }
	
	public function exportCsvAction() {
        $fileName   = 'Ced_CsMarketplace_Vendors'.time().'.csv';
        $content    = $this->getLayout()->createBlock('csmarketplace/adminhtml_vendor_entity_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName   = 'Ced_CsMarketplace_Vendors'.time().'.xml';
        $content    = $this->getLayout()->createBlock('csmarketplace/adminhtml_vendor_entity_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }
    
    public function rebuildWebsitesAction(){
    	try {
    		Mage::helper('csmarketplace')->rebuildWebsites();
    		$this->_getSession()->addSuccess($this->__('Vendor Website Sharing has been changed.'));
    	} catch (Exception $e) {
    		$this->_getSession()->addError($this->__('Unable to Process Vendor Website Sharing.'));
    	}
    	$this->_redirectReferer();
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

}