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
  * @package   Ced_MobiconnectCms
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_MobiconnectCms_Block_Adminhtml_Cmsblock_Edit_Tab_Cmsblock extends Mage_Adminhtml_Block_Widget_Form{
	/**
	 * prepare tab form's information
	 *
	 * @return form
	 */
	protected function _prepareForm() {
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('mobiconnect_form', array('legend' => Mage::helper('mobiconnect')->__('CMS Block')));
		$wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
		
		$wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array('tab_id' => 'form_section'));
		$wysiwygConfig["files_browser_window_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index');
		$wysiwygConfig["directives_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive');
		$wysiwygConfig["directives_url_quoted"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive');
		$wysiwygConfig["widget_window_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/widget/index');
		$wysiwygConfig["files_browser_window_width"] = (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_width');
		$wysiwygConfig["files_browser_window_height"] = (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_height');
		$plugins = $wysiwygConfig->getData("plugins");
		$plugins[0]["options"]["url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/system_variable/wysiwygPlugin');
		$plugins[0]["options"]["onclick"]["subject"] = "MagentovariablePlugin.loadChooser('".Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/system_variable/wysiwygPlugin')."', '{{html_id}}');";
		$plugins = $wysiwygConfig->setData("plugins",$plugins);

		
		$object = Mage::getModel('mobiconnectcms/cmsblock')->load($this->getRequest()->getParam('id'));
		$imgPath=Mage::getBaseUrl('media')."Mobiconnect/cms/".$object['image'];
		/*$fieldset->addField('website', 'select', array(
				'label' => Mage::helper('mobiconnect')->__('In website'),
				'name' => 'website',
				'values' => Mage::getSingleton('mobiconnect/banner')->getWebsite(),
		));*/
		$fieldset->addField('title','text',array(
			'label'=>Mage::helper('mobiconnectcms')->__('Title'),
			'name'=>'title',
			'class' => 'required-entry',
            'required' => true,
		));
		$fieldset->addField('identifier','text',array(
			'label'=>Mage::helper('mobiconnectcms')->__('Identifier'),
			'name'=>'identifier',
			'class' => 'required-entry',
            'required' => true,
		));
		if (! Mage::app ()->isSingleStoreMode ()) {
	      	$fieldset->addField ( 'store', 'multiselect', array (
	          'name' => 'store[]',
	          'label' => Mage::helper ( 'mobiconnectcms' )->__ ( 'Store View' ),
	          'title' => Mage::helper ( 'mobiconnectcms' )->__ ( 'Store View' ),
	          'class' => 'required-entry',
           	  'required' => true,
	          'values' => Mage::getSingleton ( 'adminhtml/system_store' )->getStoreValuesForForm ( false, true ) 
	      ) );
	    } else {
	      	$fieldset->addField ( 'store', 'hidden', array (
	          'name' => 'store[]',
	          'value' => Mage::app ()->getStore ( true )->getId () 
	      ) );
	    }
		if($object['image'])
		{
			$fieldset->addField('image', 'label', array(
					'label' => Mage::helper('mobiconnectcms')->__('CMS Image'),
					'name'  =>'image',
					'after_element_html' => '<img src="'.$imgPath .'" height="100" width="100" />',
			));
		}
			$fieldset->addField('fileimage','image',array(
				'label'=>Mage::helper('mobiconnectcms')->__('Browse Image'),
				'name' =>'fileimage',
			));
		
		 $fieldset->addField('cms_content', 'editor', array(
            'name' => 'cms_content',
            'class' => 'required-entry',
            'required' => true,
			'config'	=> $wysiwygConfig,
            'label' => Mage::helper('mobiconnectcms')->__('Content'),
            'title' => Mage::helper('mobiconnectcms')->__('Content'),
			'style'		=> 'width: 600px;',
        ));
		$fieldset->addField('status','select',array(
			'label'=>Mage::helper('mobiconnectcms')->__('Status'),
			'name'=>'status',
			'class' => 'required-entry',
            'required' => true,
		    'values' => array(
				array('value' =>1, 'label' => Mage::helper('mobiconnectcms')->__('Yes')),
                array('value' =>0, 'label' => Mage::helper('mobiconnectcms')->__('No')),                
            ) 	
		));	
		
		if ( $data=Mage::registry('cms_data') ){	
			$form->setValues(Mage::registry('cms_data')->getData());
		}
		return parent::_prepareForm();
	}
	 protected function _prepareLayout()
    {
    	$return = parent::_prepareLayout();
    	if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
    		$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
    	}
    	return $return;
    }
}
