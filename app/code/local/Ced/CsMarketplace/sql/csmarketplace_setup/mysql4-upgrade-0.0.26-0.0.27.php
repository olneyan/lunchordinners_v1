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
 * @author    CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$collection = Mage::getModel('csmarketplace/vproducts')->getCollection()->addFieldToFilter('check_status', 3);
foreach($collection as $product){
	$product->delete();
}

$installer = $this;
$installer->startSetup();

$adminToSellerInvoiceAttributes = array('public_name'=>10, 'email'=>20, 'contact_number'=> 30, 'company_name'=> 40, 'company_address'=> 50);

if(version_compare(Mage::getVersion(), '1.6', '<=')) {

	$installer->getConnection()->addColumn($installer->getTable('csmarketplace/vpayment'), 'trans_credit_summary', Varien_Db_Ddl_Table::TYPE_VARCHAR."(555) DEFAULT ''");
	$installer->getConnection()->addColumn($this->getTable('csmarketplace/vendor_form'), 'use_in_admin_to_seller_invoice', Varien_Db_Ddl_Table::TYPE_INTEGER.' DEFAULT 0');
	$installer->getConnection()->addColumn($this->getTable('csmarketplace/vendor_form'), 'admin_to_seller_invoice_sortorder', Varien_Db_Ddl_Table::TYPE_INTEGER.' DEFAULT 0');

} else {
	
	$installer->getConnection()
		->addColumn($this->getTable('csmarketplace/vpayment'), 'trans_credit_summary', array(
			'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
			'comment' => 'Transaction Credit Summary',
			'default' => '',
		));
	$installer->getConnection()
		->addColumn($this->getTable('csmarketplace/vendor_form'), 'use_in_admin_to_seller_invoice', array(
			'type'    => Varien_Db_Ddl_Table::TYPE_INTEGER,
			'comment' => 'Use in Admin To Seller Invoice',
			'unsigned'=> true,
			'default' => 0, 
		));
	$installer->getConnection()
		->addColumn($this->getTable('csmarketplace/vendor_form'), 'admin_to_seller_invoice_sortorder', array(
			'type'    => Varien_Db_Ddl_Table::TYPE_INTEGER,
			'comment' => 'Admin To Seller Invoice Sort Order',
			'unsigned'=> true,
			'default' => 0, 
		));
}
$installer->run("ALTER TABLE `".$this->getTable('csmarketplace/vproducts')."` ADD UNIQUE(`product_id`)");

$vendorAttributes = Mage::getModel('csmarketplace/vendor_form')->getCollection();
if(count($vendorAttributes) > 0) {

	foreach($vendorAttributes as $vendorAttribute) {
		$vendorMainAttribute = Mage::getModel('csmarketplace/vendor_attribute')->load($vendorAttribute->getAttributeId());
		$isSaveNeeded = false;
		if(isset($adminToSellerInvoiceAttributes[$vendorAttribute->getAttributeCode()]))
		{
			$vendorAttribute->setData('use_in_admin_to_seller_invoice', 1);
			$vendorAttribute->setData('admin_to_seller_invoice_sortorder', $adminToSellerInvoiceAttributes[$vendorAttribute->getAttributeCode()]);
			$isSaveNeeded = true;
		}
		if($isSaveNeeded){
			$vendorAttribute->save();
			$vendorMainAttribute->save();
			$isSaveNeeded = false;
		}
	}
}

$installer->endSetup();
