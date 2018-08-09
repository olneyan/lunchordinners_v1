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
class Ced_CsMarketplace_Block_Adminhtml_Vproducts extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{	
		$this->_controller = 'adminhtml_vproducts';
		$this->_blockGroup = 'csmarketplace';
		if(Mage::registry('usePendingProductFilter'))
			$this->_headerText = Mage::helper('csmarketplace')->__('Vendor Pending Products');
		else if(Mage::registry('useApprovedProductFilter'))
			$this->_headerText = Mage::helper('csmarketplace')->__('Vendor Approved Products');
		else
			$this->_headerText = Mage::helper('csmarketplace')->__('Manage Vendor Products');
		//$this->_addButtonLabel = Mage::helper('csmarketplace')->__('Add CsMarketplace');
		parent::__construct();
		$this->removeButton('add');
	}
	
	/**
     * Redefine header css class
     *
     * @return string
     */
    public function getHeaderCssClass() {
        return 'icon-head head-products';
    }
}