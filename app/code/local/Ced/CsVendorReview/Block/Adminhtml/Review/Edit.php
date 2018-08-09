<?php 

/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License (AFL 3.0)
  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/afl-3.0.php
  *
  * @category    Ced
  * @package     Ced_CsVendorReview
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

/**
 * Rating Item admin form container
 */
 

class Ced_CsVendorReview_Block_Adminhtml_Review_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
	 * Initialize Constructor
	 */
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'csvendorreview';
        $this->_controller = 'adminhtml_review';//actual location of block files       
        $this->_updateButton('save', 'label', Mage::helper('csvendorreview')->__('Save Review Item'));
    //    $this->_updateButton('delete', 'label', Mage::helper('csvendorreview')->__('Delete Rating Item'));

    }
	
	/**
	 * @return Header Text (String)
	 */
    public function getHeaderText()
    {
        return Mage::helper('csvendorreview')->__('Edit Review Item');
    }
	
	/**
	 * Prepare array for the all stores of all websites
	 * @return Json
	 */
	public function getStores()
	{
		$array=array();
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