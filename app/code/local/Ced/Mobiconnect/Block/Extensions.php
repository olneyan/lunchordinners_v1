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
 class Ced_Mobiconnect_Block_Extensions extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
	protected $_dummyElement;
	protected $_fieldRenderer;
	protected $_values;
	protected $_licenseUrl;
	const LICENSE_USE_HTTPS_PATH = 'web/secure/use_in_adminhtml';
	const LICENSE_VALIDATION_URL_PATH = 'system/license/license_url';
	const HASH_PATH_PREFIX = 'cedcore/extensions/extension_';
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
		$header = $html = $footer = $script = '';
		$header = $this->_getHeaderHtml($element);
		$modules = Mage::helper('mobiconnect')->getCedCommerceExtensions(false,true);
		//print_r($modules);die;
		$field = $element->addField('extensions_heading', 'note', array(
            'name'  => 'extensions_heading',
            'label' => '<a href="javascript:;"><b>Extension Name (version)</b></a>',
            'text' => '<a href="javascript:;"><b>License Information</b></a>',
		))->setRenderer($this->_getFieldRenderer());
		$html.= $field->toHtml();
        foreach ($modules as $moduleName=>$releaseVersion) {
        	
			$moduleProductName = isset($releaseVersion['parent_product_name']) ? $releaseVersion['parent_product_name'] : '';
			$releaseVersion = isset($releaseVersion['release_version']) ? $releaseVersion['release_version'] : trim($releaseVersion);
			$html.= $this->_getFieldHtml($element, $moduleName,$releaseVersion,$moduleProductName);
        	
		}
		if (strlen($html) == 0) {
			$html = '<p>'.$this->__('No records found.').'</p>';
		}
        $footer .= $this->_getFooterHtml($element);
		
		$script .= $this->_getScriptHtml();
		
        return $header. $html . $footer . $script;
    }
	
    protected function _getFieldRenderer()
    {
    	if (empty($this->_fieldRenderer)) {
    		$this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
    	}
    	return $this->_fieldRenderer;
    }
	
	protected function _getDummyElement()
    {
        if (empty($this->_dummyElement)) {
            $this->_dummyElement = new Varien_Object(array('show_in_default'=>1, 'show_in_website'=>1));
        }
        return $this->_dummyElement;
    }
	
	protected function _getFieldHtml($fieldset, $moduleName,$currentVersion = '0.0.1',$moduleProductName = '')
    {

		$configData = $this->getConfigData();
        $path = self::HASH_PATH_PREFIX.strtolower($moduleName);
        if (isset($configData[$path])) {
            $data = $configData[$path];
            $inherit = false;
        } else {
            $data = (string)$this->getForm()->getConfigRoot()->descend($path);
            $inherit = true;
        }
 
        $e = $this->_getDummyElement();

		$allExtensions  = unserialize(Mage::app()->loadCache('all_extensions_by_cedcommerce'));
        $name    	    = strlen($moduleProductName) > 0 ? $moduleProductName : $moduleName;
		$releaseVersion = $name.'-'.$currentVersion;
		$warning = '';
		if ($allExtensions && isset($allExtensions[$moduleName])) {
			$url     = $allExtensions[$moduleName]['url'];
            $warning = isset($allExtensions[$moduleName]['warning'])?$allExtensions[$moduleName]['warning']:'';
			
			if(strlen($warning) == 0) {
				$releaseVersion = $allExtensions[$moduleName]['release_version'];
				$releaseVersion = '<a href="'.$url.'" target="_blank" title="Upgarde Available('.$releaseVersion.')">'.$name.'-'.$currentVersion.'</a>';
			} else {
				$releaseVersion = '<div class="notification-global"><strong class="label">'.$warning.'</strong></div>';
			}
		}
		$buttonHtml = '<div style="float: right;"><img src="'.$this->getSkinUrl('images/success_msg_icon.gif').'"><strong class="label">&nbsp;</strong></div>';
		$type = 'label';
		$title = 'License Number';
		if(strlen($data) == 0) {
			$title = Mage::helper('mobiconnect')->__('Enter the valid license after that you have to click on Save Config button.');
			/* $buttonHtml = $this->getLayout()->createBlock('adminhtml/widget_button')
							->setData(array(
								'label'     => Mage::helper('csmarketplace')->__('Validate'),
								'title'		=> Mage::helper('csmarketplace')->__('Enter the valid license and click on Validate button after that you have to click on Save Config button.'),
								'onclick'   => '',
								'class'     => '',
								'type'      => 'button',
								'id'        => 'button_'.strtolower($moduleName),
								'style'		=> 'float:right',
							))
							->toHtml(); */
			$buttonHtml = '<div style="clear: both; height:0; width:0; ">&nbsp;</div>';
			$buttonHtml .= '<p class="note"><span>Please fill the valid license number in above field. If you don\'t have license number please <a href="http://cedcommerce.com/licensing?product_name='.strtolower($moduleName).'" target="_blank">Get a license number from CedCommerce.com</a></span></p>';
			$type = 'text';
		}
		
		if($moduleName && strtolower($moduleName) == 'ced_csvendorpanel'){
			$type = 'label';
			$path = self::HASH_PATH_PREFIX.'ced_csmarketplace';
			if (isset($configData[$path])) {
				$data = $configData[$path];
				$inherit = false;
			} else {
				$data = (string)$this->getForm()->getConfigRoot()->descend($path);
				$inherit = true;
			}
			if(!$data) {
				$data = 'n/a';
			} else {
				$buttonHtml = '<div style="float: right;"><img src="'.$this->getSkinUrl('images/success_msg_icon.gif').'"><strong class="label">&nbsp;</strong></div>';
			}
		}
		
		$field = $fieldset->addField(strtolower($moduleName), $type,//this is the type of the element (can be text, textarea, select, multiselect, ...)
			array(
				'name'          => 'groups[extensions][fields][extension_'.strtolower($moduleName).'][value]',//this is groups[group name][fields][field name][value]
				'label'         => $name.' ('.$currentVersion.')',//this is the label of the element
				'value'         => $data,//this is the current value
				'title'			=> $title,
				'inherit'       => $inherit,
				'class'			=>'validate-cedcommerce-license',
				'style'		=> 'float:left;',
				'can_use_default_value' => $this->getForm()->canUseDefaultValue($e),//sets if it can be changed on the default level
				'can_use_website_value' => $this->getForm()->canUseWebsiteValue($e),//sets if can be changed on website level
				'after_element_html' => $buttonHtml,
			))->setRenderer($this->_getFieldRenderer());
		
		/* $field = $fieldset->addField(strtolower($moduleName), 'note', array(
            'name'  => 'csmarketplace',
            'label' => '<span style="text-align: center;">'.$name.'-'.$currentVersion.'</span>',
            'text' => '<span style="text-align: center;">'.$releaseVersion.'</span>',
		))->setRenderer($this->_getFieldRenderer()); */
		
		return $field->toHtml();
    }
	
	/**
     * Retrieve local license url
     *
     * @return string
     */
    public function getLicenseUrl()
    {
        if (is_null($this->_licenseUrl)) {
			$secure = false;
			if(Mage::getStoreConfigFlag(self::LICENSE_USE_HTTPS_PATH)) {
				$secure = true;
			}
			$this->_licenseUrl = $this->getUrl(Mage::getStoreConfig(self::LICENSE_VALIDATION_URL_PATH),array('_secure'=>$secure));
        }
        return $this->_licenseUrl;
    }
	
	protected function _getScriptHtml() {
		$script = '<script type="text/javascript">';
		$script .= 'Validation.add("validate-cedcommerce-license", "Please enter a valid License Number.", function(v,licenseElement) {';     
 
		$script .= 'var url = "'.$this->getLicenseUrl().'";';
		$script .= 'var formId = configForm.formId;';
	
		$script .= 'var ok = false;';
		$script .= 'var licenseParam = licenseElement.id + "=" + licenseElement.value;';
		$script .= 'new Ajax.Request(url, {
							method: "post",
							asynchronous: false,
							onSuccess: function(transport) {
								var response = transport.responseText.evalJSON();
								validateTrueEmailMsg = response.message;
								if (response.success == 0) {									
									Validation.get("validate-cedcommerce-license-"+licenseElement.id).error = validateTrueEmailMsg;
									alert(validateTrueEmailMsg);
									ok = false;
								} else {
									ok = true; 
								}
							},
							parameters: licenseParam,
					});
					return ok';
		$script .= '})';
		$script .= '</script>';

		return $script;
	}
}