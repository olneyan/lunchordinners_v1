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
 
class Ced_CsMarketplace_Block_Adminhtml_Extensions_Details extends Mage_Adminhtml_Block_Abstract
{
	protected function _toHtml() {
		$modules = Mage::helper('csmarketplace')->getCedCommerceExtensions();
		$params = array();
		$args = '';
		foreach ($modules as $moduleName=>$releaseVersion)
		{
			$m = strtolower($moduleName); if(!preg_match('/ced/i',$m)){ return $this; }  $h = Mage::getStoreConfig(Ced_CsMarketplace_Block_Extensions::HASH_PATH_PREFIX.$m.'_hash'); for($i=1;$i<=(int)Mage::getStoreConfig(Ced_CsMarketplace_Block_Extensions::HASH_PATH_PREFIX.$m.'_level');$i++){$h = base64_decode($h);}$h = json_decode($h,true); 
			if(is_array($h) && isset($h['domain']) && isset($h['module_name']) && isset($h['license']) && $h['module_name'] == $m && $h['license'] == Mage::getStoreConfig(Ced_CsMarketplace_Block_Extensions::HASH_PATH_PREFIX.$m)){}else{ 
				$args .= $m.'|';
			}	
		}
		$args = trim($args,'|');
		if(strlen($args)>0) $params['modules'] = $args;
		$html = '<input id="cedpop" name="cedpop" style="display: none; height:0; width:0;" />';
		$height = 140; 

		if(strlen($args) > 56) {
			$remainingLength = strlen($args) - 56;
			$height += ((((int)($remainingLength / 80)) + 1) * 20);
		}
		return $html.$this->_getScriptHtml($params,$height);
	}
	
	protected function _getScriptHtml($params = array(),$height = 140) {
		$url = $this->getUrl('*/*/cedpop');
		if(count($params) > 0) $url = Mage::helper('csmarketplace')->addParams($url,$params,false);
		$script = '<script type="text/javascript">';
		$script .= 'new Ajax.Request("'.$url.'", {
							method: "get",
							asynchronous: false,
							onSuccess: function(transport) {
								try {
									content = transport.responseText;
									elementId = "cedpop";
									this.overlayShowEffectOptions = Windows.overlayShowEffectOptions;
									this.overlayHideEffectOptions = Windows.overlayHideEffectOptions;
									Windows.overlayShowEffectOptions = {duration:0};
									Windows.overlayHideEffectOptions = {duration:0};
									
									Dialog.confirm(content, {
										draggable:true,
										resizable:true,
										closable:true,
										className:"magento",
										windowClassName:"popup-window",
										title:"License Violation",
										width:500,
										height:'.$height.',
										zIndex:1000,
										recenterAuto:false,
										hideEffect:Element.hide,
										showEffect:Element.show,
										id:"cedpop-editor",
										buttonClass:"form-button",
										okLabel:"Activate",
										ok: function(dialogWindow){ dialogWindow.close(); setLocation("'.$this->getUrl('adminhtml/system_config/edit/section/cedcore/').'"); },
										cancel:  function(dialogWindow){ dialogWindow.close() },
										onClose:  function(dialogWindow){ dialogWindow.close() },
										firedElementId: elementId
									});
								} catch(e) {
									alert(e.message);
								}
							},
					});';
		$script .= '</script>';

		return $script;
	}
}