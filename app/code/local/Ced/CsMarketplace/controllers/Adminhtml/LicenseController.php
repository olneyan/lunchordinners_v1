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
final class Ced_CsMarketplace_Adminhtml_LicenseController extends Mage_Adminhtml_Controller_Action 
{
	protected $_licenseActivateUrl = null;
	
	const LICENSE_ACTIVATION_URL_PATH = 'system/license/activate_url';
	
	public function indexAction() {
		$postData = $this->getRequest()->getPost();
		$json = array('success'=>0,'message'=>Mage::helper('csmarketplace')->__('There is an Error Occurred.'));
		if($postData){
			foreach($postData as $moduleName=>$licensekey){
				if(preg_match('/ced_/i',$moduleName)) {
					if(strlen($licensekey) ==0) {
						$json = array('success'=>1,'message'=>'');
						$this->getResponse()->setHeader('Content-type', 'application/json');
						echo json_encode($json);die;
					}
					unset($postData[$moduleName]);
					$postData['module_name'] = $moduleName;
					$allModules = Mage::app()->getConfig()->getNode(Ced_CsMarketplace_Model_Feed::XML_PATH_INSTALLATED_MODULES);
					$allModules = json_decode(json_encode($allModules),true);
					$postData['module_version'] = isset($allModules[$moduleName]['release_version'])?$allModules[$moduleName]['release_version']:'';
					$postData['module_license'] = $licensekey;
					break;
				}
			}
			$response = $this->validateAndActivateLicense($postData);
			if ($response && isset($response['hash']) && isset($response['level'])) {
				$config = new Mage_Core_Model_Config();
				$json = array('success'=>0,'message'=>Mage::helper('csmarketplace')->__('There is an Error Occurred.'));
				$valid = $response['hash'];
				try {

					for($i = 1;$i<=$response['level'];$i++){
						$valid = base64_decode($valid);
					}
					$valid = json_decode($valid,true);

					if(is_array($valid) && 
						isset($valid['domain']) && 
						isset($valid['module_name']) && 
						isset($valid['license']) &&
						$valid['module_name'] == $postData['module_name'] &&
						$valid['license'] == $postData['module_license']						
					)
					{
						$path = Ced_CsMarketplace_Block_Extensions::HASH_PATH_PREFIX.strtolower($postData['module_name']).'_hash';
						$config->saveConfig($path, $response['hash'], 'default', 0);
						$path = Ced_CsMarketplace_Block_Extensions::HASH_PATH_PREFIX.strtolower($postData['module_name']).'_level';
						$config->saveConfig($path, $response['level'], 'default', 0);
						$json['success'] = 1;
						$json['message'] = Mage::helper('csmarketplace')->__('Module Activated successfully.');
					} else {
						$json['success'] = 0;
						$json['message'] = isset($response['error']['code']) && isset($response['error']['msg']) ? 'Error ('.$response['error']['code'].'): '.$response['error']['msg'] : Mage::helper('csmarketplace')->__('Invalid License Key.');
					}
				} catch (Exception $e) {
					$json['success'] = 0;
					$json['message'] = $e->getMessage();
				}
			}
		}
		$this->getResponse()->setHeader('Content-type', 'application/json');
		echo json_encode($json);die;
	}
	
	/**
     * Retrieve local license url
     *
     * @return string
     */
    private function getLicenseActivateUrl()
    {
        if (is_null($this->_licenseActivateUrl)) {
            $this->_licenseActivateUrl = (Mage::getStoreConfigFlag(Ced_CsMarketplace_Block_Extensions::LICENSE_USE_HTTPS_PATH) ? 'https://' : 'http://')
                . Mage::getStoreConfig(self::LICENSE_ACTIVATION_URL_PATH);
        }
        return $this->_licenseActivateUrl;
    }
	
	 /**
     * Retrieve feed data as XML element
     *
     * @return SimpleXMLElement
     */
    private function validateAndActivateLicense($urlParams = array())
    {
		$result = false;

		$body = '';
		if(isset($urlParams['form_key'])) unset($urlParams['form_key']);
		$urlParams = array_merge(Mage::helper('csmarketplace')->getEnvironmentInformation(),$urlParams);
		
		if (is_array($urlParams) && count($urlParams) > 0) {
			
			if(isset($urlParams['installed_extensions_by_cedcommerce'])) unset($urlParams['installed_extensions_by_cedcommerce']);
			$body = Mage::helper('csmarketplace')->addParams('',$urlParams);
			$body = trim($body,'?');
		}

		try {
			$ch = curl_init();					
			curl_setopt($ch, CURLOPT_URL,$this->getLicenseActivateUrl());	
			curl_setopt($ch, CURLOPT_POST, 1);					
			curl_setopt($ch, CURLOPT_POSTFIELDS, $body);					
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 					
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
			$result = curl_exec($ch);	
			$info = curl_getinfo($ch);	
			curl_close ($ch);
			if(isset($info['http_code']) && $info['http_code']!=200) return false;
			$result = json_decode($result,true);
        } catch (Exception $e) {
			return false;
        }

        return $result;
    }

    public function checkupdateAction() {
    	$urlParams = Mage::helper('csmarketplace')->getEnvironmentInformation();

		if($this->fetchUpdate($urlParams)) {
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('csmarketplace')->__('Updates Fetched Successfully.'));
		}
		else {
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('csmarketplace')->__('No Updates Found.'));
		}
		$this->_redirect('adminhtml/system_config/edit',array('section'=>'cedcore'));
    }


    public function fetchUpdate($params)
	{
		$feedData = array();
		$feed = array();
		$installedModules = Mage::helper('csmarketplace')->getCedCommerceExtensions();
		$plateform = isset($params['plateform'])? strtolower($params['plateform']):'';	
		$url = $this->getFeedUrl($plateform);
		$module_name['module_notification'] = array_keys($installedModules);
		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);    // get the url contents
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, urldecode(http_build_query($module_name)));
			$data = curl_exec($ch); // execute curl request
			if(curl_error($ch))
			{
				echo 'error:' . curl_error($ch);die;
			}
			
			curl_close($ch);
			
			if ($data === false) {
				return false;
			}
			
			if(trim($data)=='')
				return false;

			$feedXml = new SimpleXMLElement((string)$data);
			if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
				
				foreach ($feedXml->channel->item as $item) {

					if(!isset($installedModules[(string)$item->module]) || version_compare($installedModules[(string)$item->module], trim((string)$item->release_version), '<' ) === false) {
						continue;
					}
					
					if(Mage::helper('csmarketplace')->isAllowedFeedType($item)) {
						if(strlen(trim($item->module)) > 0) {
							if(isset($feedData[trim((string)$item->module)]) && isset($feedData[trim((string)$item->module)]['release_version']) && strlen((string)$item->release_version) > 0 && version_compare($feedData[trim((string)$item->module)]['release_version'], trim((string)$item->release_version), '>')===true) {
								continue;
							}
							$feedData[trim((string)$item->module)] = array(
									'severity'          => (int)$item->severity,
									'date_added'        => $this->getDate((string)$item->pubDate),
									'title'             => (string)$item->title,
									'description'       => (string)$item->description,
									'url'               => (string)$item->link,
									'module'            => (string)$item->module,
									'release_version'   => (string)$item->release_version,
									'update_type'       => (string)$item->update_type,
							);
							if(strlen((string)$item->warning) > 0) {
								$feedData[trim((string)$item->module)]['warning'] = (string)$item->warning;
							}
								
							if(strlen((string)$item->product_url) > 0) {
								$feedData[trim((string)$item->module)]['url'] = (string)$item->product_url;
							}
								
						}
							
						$feed[] = array(
								'severity'          => (int)$item->severity,
								'date_added'        => $this->getDate((string)$item->pubDate),
								'title'             => (string)$item->title,
								'description'       => (string)$item->description,
								'url'               => (string)$item->link
						);
					}
				}
				if ($feed) {
	                Mage::getModel('adminnotification/inbox')->parse(array_reverse($feed));
	            }
				if($feedData) {
					Mage::app()->saveCache(serialize($feedData), 'all_extensions_by_cedcommerce');
				}
				
				if(count($feedData))
					return true;
				else
					return false;
			}		
		} catch (\Exception $e) {
            return false;
        }
	}

	/**
	 * Retrieve feed url
	 *
	 * @return string
	 */
	public function getFeedUrl($plateform){
		$url = "http://cedcommerce.com/blog/notifications/feed/";
		$plateformUrls= array(
				'wordpress'=>'wordpress-notifications',
				'woocommerce'=>'wordpress-notifications',
		);
		if($plateform!=''){
			if(isset($plateformUrls[$plateform]))
				$url = "http://cedcommerce.com/blog/".$plateformUrls[$plateform]."/feed/";
		}
		return $url;
	}
	
	/**
	 * Retrieve DB date from RSS date
	 *
	 * @param  string $rssDate
	 * @return string YYYY-MM-DD YY:HH:SS
	 */
	public function getDate($rssDate)
	{
		return gmdate('Y-m-d H:i:s', strtotime($rssDate));
	}

}
