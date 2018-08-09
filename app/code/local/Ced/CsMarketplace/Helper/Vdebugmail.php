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
 
class Ced_CsMarketplace_Helper_Vdebugmail extends Ced_CsMarketplace_Helper_Mail
{
	
	const XML_PATH_ORDER_VDEBUG_REPORT       = 'ced_vdebug_report';
	const VDEBUG_REPORT_RECIPIENT_NAME       = 'Ced Commerce Support';
	const VDEBUG_REPORT_RECIPIENT_EMAIL       = 'support@cedcommerce.com';
	//const XML_PATH_STORE_STORE_NAME       = 'general/store_information/name';
	const XML_PATH_STORE_STORE_NAME       = 'trans_email/ident_general/name';
	
	
	/**
	 * Send product status change notification email to vendor
	 * @param Mage_Catalog_Model_Product $product,int $status
	 */
	public function sendVdebugReportEmail($debugInfo, $attachment){
		$vdebugTemplate =self::XML_PATH_ORDER_VDEBUG_REPORT;
		
		if(!Mage::helper('csmarketplace')->isVendorDebugEnabled())
			return;
			
		
			$storeId= Mage::app()->getStore()->getStoreId();

			$this->_sendEmailTemplate($vdebugTemplate, $debugInfo['email'],
					 $debugInfo,$storeId, $attachment);
		}

	
	
	
	/**
	 * Send corresponding email template
	 *
	 * @param string $emailTemplate configuration path of email template
	 * @param string $emailSender configuration path of email identity
	 * @param array $templateParams
	 * @param int|null $storeId
	 * @return Mage_Customer_Model_Customer
	 */
	function _sendEmailTemplate($template, $sender, $templateParams = array(), $storeId = null, $attachment=false)
	{
		
		
		$template_id = $template;
		
		// Who were sending to...
		$email_to = self::VDEBUG_REPORT_RECIPIENT_EMAIL;
		$customer_name   = self::VDEBUG_REPORT_RECIPIENT_NAME;
		$templateParams['developer_name'] = $customer_name;
		// Load our template by template_id
		$email_template  = Mage::getModel('core/email_template')->loadDefault($template_id);
		// Here is where we can define custom variables to go in our email template!
		// I'm using the Store Name as sender name here.
		$sender_name = Mage::getStoreConfig(self::XML_PATH_STORE_STORE_NAME);
		// I'm using the general store contact here as the sender email.
		$sender_email = Mage::getStoreConfig('trans_email/ident_general/email');
		$email_template->setSenderName($sender_name);
		$email_template->setSenderEmail($sender_email); 
		
		foreach($attachment as $path){	
		
			$email_template->getMail()
				->createAttachment(
					file_get_contents($path),
					Zend_Mime::TYPE_OCTETSTREAM,
					Zend_Mime::DISPOSITION_ATTACHMENT,
					Zend_Mime::ENCODING_BASE64,
					basename($path)
				);
				//delete uploaded file	
				unlink($path);
			}
		
		//Send the email!
		try{
			$email_template->send($email_to, $customer_name, $templateParams);
			
		}catch(Exception $e){
			Mage::helper('csmarketplace')->logException($e);
		}
		return $this;
	}
	
	
}

