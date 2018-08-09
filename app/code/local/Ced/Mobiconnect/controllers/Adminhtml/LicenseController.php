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
  * @category  Ced
  * @package   Ced_Mobiconnect
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
final class Ced_Mobiconnect_Adminhtml_LicenseController extends Mage_Adminhtml_Controller_Action 
{
	protected $_licenseActivateUrl = null;
	
	const LICENSE_ACTIVATION_URL_PATH = 'system/license/activate_url';
	
	public function indexAction() {
		
		$postData = $this->getRequest()->getPost();
		$json = array('success'=>0,'message'=>Mage::helper('mobiconnect')->__('There is an Error Occurred.'));
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
					$allModules = Mage::app()->getConfig()->getNode(Ced_Mobiconnect_Model_Feed::XML_PATH_INSTALLATED_MODULES);
					$allModules = json_decode(json_encode($allModules),true);
					$postData['module_version'] = isset($allModules[$moduleName]['release_version'])?$allModules[$moduleName]['release_version']:'';
					$postData['module_license'] = $licensekey;
					break;
				}
			}
			$response = $this->validateAndActivateLicense($postData);
			/* print_r($response);die('dfgdf'); */
			if ($response && isset($response['hash']) && isset($response['level'])) {
				$config = new Mage_Core_Model_Config();
				$json = array('success'=>0,'message'=>Mage::helper('mobiconnect')->__('There is an Error Occurred.'));
				$valid = $response['hash'];
				try {
					/* echo $response['level'].'  ';
					echo $response['hash'];
					
					die; */
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
						$path = Ced_Mobiconnect_Block_Extensions::HASH_PATH_PREFIX.strtolower($postData['module_name']).'_hash';
						$config->saveConfig($path, $response['hash'], 'default', 0);
						$path = Ced_Mobiconnect_Block_Extensions::HASH_PATH_PREFIX.strtolower($postData['module_name']).'_level';
						$config->saveConfig($path, $response['level'], 'default', 0);
						$json['success'] = 1;
						$json['message'] = Mage::helper('mobiconnect')->__('Module Activated successfully.');
					} else {
						$json['success'] = 0;
						$json['message'] = isset($response['error']['code']) && isset($response['error']['msg']) ? 'Error ('.$response['error']['code'].'): '.$response['error']['msg'] : Mage::helper('ced_sociallogin')->__('Invalid License Key.');
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
            $this->_licenseActivateUrl = (Mage::getStoreConfigFlag( Ced_Mobiconnect_Block_Extensions::LICENSE_USE_HTTPS_PATH) ? 'https://' : 'http://')
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
		$urlParams = array_merge(Mage::helper('mobiconnect')->getEnvironmentInformation(),$urlParams);
		
		if (is_array($urlParams) && count($urlParams) > 0) {
			
			if(isset($urlParams['installed_extensions_by_cedcommerce'])) unset($urlParams['installed_extensions_by_cedcommerce']);
			$body = Mage::helper('mobiconnect')->addParams('',$urlParams);
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
}