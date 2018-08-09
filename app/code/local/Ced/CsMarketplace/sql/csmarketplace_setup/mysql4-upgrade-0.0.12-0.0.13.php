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

$installer = $this;
$installer->startSetup();
if(version_compare(Mage::getVersion(), '1.6', '<=')) {
	$installer->getConnection()->addColumn($installer->getTable('csmarketplace/vorders'), 'vorders_mode', Varien_Db_Ddl_Table::TYPE_INTEGER);
	$installer->getConnection()->addColumn($installer->getTable('csmarketplace/vorders'), 'shipping_amount', Varien_Db_Ddl_Table::TYPE_DOUBLE);
	$installer->getConnection()->addColumn($installer->getTable('csmarketplace/vorders'), 'base_shipping_amount', Varien_Db_Ddl_Table::TYPE_DOUBLE);
	$installer->getConnection()->addColumn($installer->getTable('csmarketplace/vorders'), 'shipping_paid', Varien_Db_Ddl_Table::TYPE_DOUBLE);
	$installer->getConnection()->addColumn($installer->getTable('csmarketplace/vorders'), 'shipping_refunded', Varien_Db_Ddl_Table::TYPE_DOUBLE);
	$installer->getConnection()->addColumn($installer->getTable('csmarketplace/vorders'), 'method', Varien_Db_Ddl_Table::TYPE_VARCHAR.'(1000)');
	$installer->getConnection()->addColumn($installer->getTable('csmarketplace/vorders'), 'method_title', Varien_Db_Ddl_Table::TYPE_VARCHAR.'(1000)');
	$installer->getConnection()->addColumn($installer->getTable('csmarketplace/vorders'), 'carrier', Varien_Db_Ddl_Table::TYPE_VARCHAR.'(1000)');
	$installer->getConnection()->addColumn($installer->getTable('csmarketplace/vorders'), 'carrier_title', Varien_Db_Ddl_Table::TYPE_VARCHAR.'(1000)');
	$installer->getConnection()->addColumn($installer->getTable('csmarketplace/vorders'), 'code', Varien_Db_Ddl_Table::TYPE_VARCHAR.'(1000)');
	$installer->getConnection()->addColumn($installer->getTable('csmarketplace/vorders'), 'shipping_description', Varien_Db_Ddl_Table::TYPE_VARCHAR.'(1000)');
} else {
	$installer->getConnection()
	->addColumn($installer->getTable('csmarketplace/vorders'),
			'shipping_amount',
			array(
					'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
					'nullable' => true,
					'default' => 0,
					'comment' => 'Shipping Amount'
			)
	);
	$installer->getConnection()
	->addColumn($installer->getTable('csmarketplace/vorders'),
			'base_shipping_amount',
			array(
					'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
					'nullable' => true,
					'default' => 0,
					'comment' => 'Base Shipping Amount'
			)
	);
	$installer->getConnection()
	->addColumn($installer->getTable('csmarketplace/vorders'),
			'shipping_paid',
			array(
					'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
					'nullable' => true,
					'default' => 0,
					'comment' => 'Shipping Paid'
			)
	);
	$installer->getConnection()
	->addColumn($installer->getTable('csmarketplace/vorders'),
			'shipping_refunded',
			array(
					'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
					'nullable' => true,
					'default' => 0,
					'comment' => 'Shipping Refunded'
			)
	);
	$installer->getConnection()

	->addColumn($installer->getTable('csmarketplace/vorders'),
			'method',
			array(
					'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'Shipping Method'
			)
	);
	$installer->getConnection()

	->addColumn($installer->getTable('csmarketplace/vorders'),
			'method_title',
			array(
					'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'Shipping Method Title'
			)
	);
	$installer->getConnection()
	->addColumn($installer->getTable('csmarketplace/vorders'),
			'carrier',
			array(
					'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'Shipping Carrier'
			)
	);
	$installer->getConnection()
	->addColumn($installer->getTable('csmarketplace/vorders'),
			'carrier_title',
			array(
					'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'Shipping Carier Title'
			)
	);
	$installer->getConnection()
	->addColumn($installer->getTable('csmarketplace/vorders'),
			'code',
			array(
					'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'Shipping Code'
			)
	);
	$installer->getConnection()
	->addColumn($installer->getTable('csmarketplace/vorders'),
			'shipping_description',
			array(
					'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
					'nullable' => true,
					'default' => null,
					'comment' => 'Shipping Description'
			)
	);
	
	$installer->getConnection()
	->addColumn($installer->getTable('csmarketplace/vorders'), 'vorders_mode', array(
			'type'    => Varien_Db_Ddl_Table::TYPE_INTEGER,
			'comment' => 'Vorder Mode',
			'default' => '0',
			'unsigned'  => true
	));
}

$installer->run("
		CREATE TABLE IF NOT EXISTS `{$installer->getTable('csmarketplace/vshop')}` (
		`id` int(11) NOT NULL auto_increment,
		`vendor_id` int(11),
		`shop_disable` int(11),
		PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
	
