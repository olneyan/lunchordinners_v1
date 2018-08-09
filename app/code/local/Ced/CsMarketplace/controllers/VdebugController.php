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
class Ced_CsMarketplace_VdebugController extends Mage_Core_Controller_Front_Action {
	/**
     * Action predispatch
     * Check that vendpr is eligible for viewing content
     */
    public function preDispatch()
    {
       parent::preDispatch();
       $action = $this->getRequest()->getActionName();

	   if(!Mage::helper('csmarketplace')->isVendorDebugEnabled() && $action!="noRoute"){
			 $this->_forward('noRoute');
			return;
		}
	}
	
	
	/**
     * Vendor Debug 
     */
	public function indexAction() {
		
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle(Mage::helper('csmarketplace')->__('CsMarketplace Vendor Debug'));
		$this->renderLayout();	
	}
	
	/**
     * Download Processed Datalog file
     */
	public function downloadProceesedLogAction(){

		$filename = Mage::getStoreConfig('ced_vlogs/general/process_file');
		$logPath = Mage::getBaseDir("log");
		$file = $logPath.DS.$filename;
		
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		}else {
			echo "No File Exist!";
			exit;
		}
	}
	
	/**
     * Download Exception log file 
     */
	public function downloadExceptionLogAction(){

		$filename = Mage::getStoreConfig('ced_vlogs/general/exception_file');
		
		$logPath = Mage::getBaseDir("log");
		$file = $logPath.DS.$filename;
		
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		}else {
			echo "No File Exist!";
			exit;
		}
		
	}
	
	

	
	/**
     * Capture screenshot
     */
	public function captureScreenShotAction(){
		
		$data = $this->getRequest()->getParam('data');
		$file = md5(uniqid()) . '.png';
		
		// remove "data:image/png;base64,"
		$uri =  substr($data,strpos($data,",")+1);
		
		
		$vdebug = Mage::getBaseDir('var').DS."vdebug".DS;
		// save to file
		file_put_contents($vdebug.$file, base64_decode($uri));
		
		// return the filename
		echo $file; exit;
	
	
	}
	
	/**
     * Send ReportBug
     */
	public function sendreportBugAction(){
		$data = $this->getRequest()->getParams();
		$path = Mage::getBaseDir('var') . DS ."vdebug" ;
			$attachments = array();	 
			if(isset($data['attachment'])){
				$attachments[] = $path. DS . $data['attachment'];
			}
			
			if (isset($_FILES['attachment']) && $_FILES['attachment']['name'][0] != '') {
			$i = 0;
			foreach($_FILES['attachment']['name'] as $file){
				if(!isset($_FILES['attachment']['name'][$i]) || $_FILES['attachment']['name'][$i]=="")
					continue;
				try {
						$fileName      = $file;
						
						$fileExt        = strtolower(substr(strrchr($fileName, ".") ,1));
						$fileNamewoe    = rtrim($fileName, $fileExt);
						$fileName       = preg_replace('/\s+', '', $fileNamewoe) . time() .$i. '.' . $fileExt;
					$uploads =	array(
								 'name' => $_FILES['attachment']['name'][$i],
								 'type' => $_FILES['attachment']['type'][$i],
								 'tmp_name' => $_FILES['attachment']['tmp_name'][$i],
								 'error' => $_FILES['attachment']['error'][$i],
								 'size' => $_FILES['attachment']['size'][$i]
							);
						$uploader = new Mage_Core_Model_File_Uploader($uploads);
		
						$uploader->setAllowedExtensions(array('doc', 'docx','pdf', 'jpg', 'png', 'zip'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);
						
						if(!is_dir($path)){
							mkdir($path, 0777, true);
						}
						$uploader->save($path . DS, $fileName );
						$attachments[] = $path . DS. $fileName ;
					} catch (Exception $e) {
								Mage::getSingleton('customer/session')->addError($e->getMessage());
								//echo "error : ".$e->getMessage();
						$error = true;
					}
					$i++;
				}
			}
		Mage::helper('csmarketplace/vdebugmail')->sendVdebugReportEmail($data, $attachments);
		Mage::getSingleton('core/session')->addSuccess("Debug report has been sent.");

		
		$this->_redirectReferer();
	}
	
	
	/**
	 * Set referer url for redirect in responce
	 *
	 * @param   string $defaultUrl
	 * @return  Mage_Core_Controller_Varien_Action
	 */
	protected function _redirectReferer($defaultUrl=null)
	{
	
		$refererUrl = $this->_getRefererUrl();
		if (empty($refererUrl)) {
			$refererUrl = empty($defaultUrl) ? Mage::getBaseUrl() : $defaultUrl;
		}
	
		$this->getResponse()->setRedirect($refererUrl);
		return $this;
	}
	
	/**
	 * Identify referer url via all accepted methods (HTTP_REFERER, regular or base64-encoded request param)
	 *
	 * @return string
	 */
	protected function _getRefererUrl()
	{
		$refererUrl = $this->getRequest()->getServer('HTTP_REFERER');
		if ($url = $this->getRequest()->getParam(self::PARAM_NAME_REFERER_URL)) {
			$refererUrl = $url;
		}
		if ($url = $this->getRequest()->getParam(self::PARAM_NAME_BASE64_URL)) {
			$refererUrl = Mage::helper('core')->urlDecode($url);
		}
		if ($url = $this->getRequest()->getParam(self::PARAM_NAME_URL_ENCODED)) {
			$refererUrl = Mage::helper('core')->urlDecode($url);
		}
	
		if (!$this->_isUrlInternal($refererUrl)) {
			$refererUrl = Mage::app()->getStore()->getBaseUrl();
		}
		return $refererUrl;
	}
}
