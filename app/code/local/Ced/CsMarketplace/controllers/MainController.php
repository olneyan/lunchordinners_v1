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
final class Ced_CsMarketplace_MainController extends Mage_Core_Controller_Front_Action 
{
	public function checkAction() {
		$data = $this->getRequest()->getParams();
		$json = array('success'=>0,'module_name'=>'','module_license'=>'');
		if($data && isset($data['module_name'])) {			
			$json['module_name'] = strtolower($data['module_name']);
			$json['module_license'] = Mage::getStoreConfig(Ced_CsMarketplace_Block_Extensions::HASH_PATH_PREFIX.strtolower($data['module_name']));
			if(strlen($json['module_license']) > 0) $json['success'] = 1;
			$this->getResponse()->setHeader('Content-type', 'application/json');
			echo json_encode($json);die;
		} else {
			$this->_forward('noroute');
		}
	}
	
	public function infoAction() {
		$headers=getallheaders();
		$signature = isset($headers['HTTP_X_CEDCOMMERCE_AUTHENTICATION'])?$headers['HTTP_X_CEDCOMMERCE_AUTHENTICATION']:'';
		
		if(strlen($signature) > 0 && $signature == '4ec6aa57fd9a8fc7473c8def05e79bd2') {	
			$json = array('success'=>0,'information'=>Mage::helper('csmarketplace')->getEnvironmentInformation(),'installed_modules'=>Mage::helper('csmarketplace')->getCedCommerceExtensions(false,true));
			$json['success'] = 1;
			$this->getResponse()->setHeader('HTTP/1.1 200 OK');
			$this->getResponse()->setHeader('HTTP_X_CEDCOMMERCE_AUTHENTICATION',$signature);
			$this->getResponse()->setHeader('Content-type', 'application/json');
			echo json_encode($json);die;
		} else {
			$this->_forward('noroute');
		}
	}
}
