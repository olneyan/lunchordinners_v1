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

/* @var $installer Ced_CsMarketplace_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

if(version_compare(Mage::getVersion(), '1.6', '<=')) { 	
	$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER);
	$installer->getConnection()->addColumn($installer->getTable('sales/quote_item'), 'vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER);	
} else {
	$installer->getConnection()
		->addColumn($installer->getTable('sales/order_item'), 'vendor_id', array(
			'type'    => Varien_Db_Ddl_Table::TYPE_INTEGER,
			'comment' => 'Vendor Id',
			'unsigned'  => true
		));
	$installer->getConnection()
		->addColumn($installer->getTable('sales/quote_item'), 'vendor_id', array(
			'type'    => Varien_Db_Ddl_Table::TYPE_INTEGER,
			'comment' => 'Vendor Id',
			'unsigned'  => true
		));	
}
	
/**
 * Create table 'csmarketplace/vendor_sales_order'
 */
$installer->run("
		CREATE TABLE IF NOT EXISTS `{$installer->getTable('csmarketplace/vorders')}` (
		  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
		  `vendor_id` int(11) DEFAULT NULL COMMENT 'Vendor ID',
		  `order_id` int(11) DEFAULT NULL COMMENT 'Order ID',
		  `currency` varchar(10) NOT NULL COMMENT 'Currency',
		  `base_order_total` decimal(12,4) NOT NULL COMMENT 'Base Order Total',
		  `order_total` decimal(12,4) DEFAULT NULL COMMENT 'Order Total',
		  `shop_commission_type_id` text NOT NULL COMMENT 'Shop Commission Type',
		  `shop_commission_rate` decimal(12,4) NOT NULL COMMENT 'Shop Commission Rate',
		  `shop_commission_base_fee` decimal(12,4) NOT NULL COMMENT 'Shop Commission Base Fee',
		  `shop_commission_fee` decimal(12,4) NOT NULL COMMENT 'Shop Commission Fee',
		  `product_qty` float NOT NULL COMMENT 'Product Qty',
		  `order_payment_state` varchar(11) NOT NULL COMMENT 'Order Payment State',
		  `payment_state` varchar(11) NOT NULL COMMENT 'Payment State',
		  `billing_country_code` varchar(100) NOT NULL COMMENT 'Billing Country Code',
		  `shipping_country_code` varchar(100) NOT NULL COMMENT 'Shipping Country Code',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
	"); 
$installer->endSetup();

	
	
	