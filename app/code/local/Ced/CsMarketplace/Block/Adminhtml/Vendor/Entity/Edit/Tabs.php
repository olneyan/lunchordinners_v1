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
class Ced_CsMarketplace_Block_Adminhtml_Vendor_Entity_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('vendor_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('csmarketplace')->__('Vendor Information'));
  }
  
  protected function _beforeToHtml()
  {
	$activeTab = true;
	$vendor = Mage::getModel('csmarketplace/vendor');
	$entityTypeId  = $vendor->getEntityTypeId();
	$setIds = Mage::getResourceModel('eav/entity_attribute_set_collection')->setEntityTypeFilter($entityTypeId)->getAllIds();
	$groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection');
	if(count($setIds) > 0) {
		$groupCollection->addFieldToFilter('attribute_set_id',array('in'=>$setIds));
	}
	if(version_compare(Mage::getVersion(), '1.6', '<')) {
		$groupCollection->getSelect()->order('main_table.sort_order');
	}
	else{
		$groupCollection->setSortOrder()
					->load();
	}
	foreach ($groupCollection as $group) {
		$attributes = $vendor->getAttributes($group->getId(), true);
		if (count($attributes)==0) {
			continue;
		}
		$this->addTab('group_'.$group->getId(), array(
			'label'     => Mage::helper('csmarketplace')->__($group->getAttributeGroupName()),
			'content'   => $this->getLayout()->createBlock($this->getAttributeTabBlock(),
				'csmarketplace.adminhtml.vendor.entity.edit.tab.attributes.'.$group->getId())->setGroup($group)
					->setGroupAttributes($attributes)
					->toHtml(),
			$activeTab?'active':'' => $activeTab?$activeTab:''
		));
		$activeTab = false;
	}
	
	if($vendor_id = $this->getRequest()->getParam('vendor_id',0)) {
		$this->addTab('payment_details', array(
			'label'     => Mage::helper('csmarketplace')->__('Payment Details'),
			'content'   => $this->getLayout()->createBlock('csmarketplace/adminhtml_vendor_entity_edit_tab_payment_methods')->toHtml(),
		));
		$this->addTab('vproducts', array(
	          'label'     => Mage::helper('csmarketplace')->__('Vendor Products'),
	          'title'     => Mage::helper('csmarketplace')->__('Vendor Products'),
		  	  'content'   => $this->getLayout()->createBlock('csmarketplace/adminhtml_vendor_entity_edit_tab_vproducts')->toHtml(),
	      ));
		  $this->addTab('vorders', array(
	          'label'     => Mage::helper('csmarketplace')->__('Vendor Orders'),
	          'title'     => Mage::helper('csmarketplace')->__('Vendor Orders'),
		  	  'content'   => $this->getLayout()->createBlock('csmarketplace/adminhtml_vendor_entity_edit_tab_vorders')->toHtml(),
	      ));
		$this->addTab('vpayments', array(
	          'label'     => Mage::helper('csmarketplace')->__('Vendor Transactions'),
	          'title'     => Mage::helper('csmarketplace')->__('Vendor Transactions'),
		  	  'content'   => $this->getLayout()->createBlock('csmarketplace/adminhtml_vendor_entity_edit_tab_vpayments')->toHtml(),
	      ));
	}
	
	/**
	 * Dispatch Event for CsAssign to Assign Product Tab
	 **/
	Mage::dispatchEvent('csmarketplace_adminhtml_vendor_entity_edit_tabs', array(
	'tabs'  => $this
	));
    return parent::_beforeToHtml();
  }
  
  /**
     * Getting attribute block name for tabs
     *
     * @return string
     */
    public function getAttributeTabBlock()
    {
        return 'csmarketplace/adminhtml_vendor_entity_edit_tab_information';
    }
}