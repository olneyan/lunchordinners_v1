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
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */ 
 
class Ced_CsMarketplace_Block_Adminhtml_Vproducts_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'csmarketplace';
        $this->_controller = 'adminhtml_vproducts';//actual location of block files
        
        $this->_updateButton('save', 'label', Mage::helper('csmarketplace')->__('Save CsMarketplace'));
        $this->_updateButton('delete', 'label', Mage::helper('csmarketplace')->__('Delete CsMarketplace'));
		if($this->getRequest()->getParam('id'))
		{
			$onclick="setLocation('".Mage::helper('adminhtml')->getUrl('adminhtml/adminhtml_vproducts/sendinvitation',array('id',$this->getRequest()->getParam('id')))."')";
			$this->_addButton('sendinvitation', array(
				'label'     => Mage::helper('adminhtml')->__('Send Invitation'),
				'onclick'   => $onclick,
				'class'     => 'save',
			), -100); 
		}
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('csmarketplace_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'csmarketplace_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'csmarketplace_content');
                }
            }
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/new/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( $this->getRequest()->getParam('id')) {
            return Mage::helper('csmarketplace')->__('Edit CsMarketplace Lead');
        } else {
            return Mage::helper('csmarketplace')->__('Add CsMarketplace');
        }
    }
	public function getStores()
	{	$array=array();
		foreach (Mage::app()->getWebsites() as $website) {
		foreach ($website->getGroups() as $group) {
					$stores = $group->getStores();
					foreach ($stores as $store) {
						$array[$website->getId()][$store->getId()]=$store->getName();
					}
			}
		}
		return json_encode($array,true);
	}
}