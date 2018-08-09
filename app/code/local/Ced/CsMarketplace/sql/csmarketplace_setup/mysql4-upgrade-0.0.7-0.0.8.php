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
$vorderTableName = 'csmarketplace/vorders';
$vpaymentTableName = 'csmarketplace/vpayment';
if(version_compare(Mage::getVersion(), '1.6', '<=')) {
	$installer->getConnection()->addColumn($installer->getTable($vorderTableName), 'base_currency', Varien_Db_Ddl_Table::TYPE_VARCHAR.'(20)');
	$installer->getConnection()->addColumn($installer->getTable($vorderTableName), 'base_to_global_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL.'(12,4)');
	$installer->getConnection()->addColumn($installer->getTable($vpaymentTableName), 'base_currency', Varien_Db_Ddl_Table::TYPE_VARCHAR.'(20)');
	$installer->getConnection()->addColumn($installer->getTable($vpaymentTableName), 'base_to_global_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL.'(12,4)');
} else {
	$installer->getConnection()
	->addColumn($installer->getTable($vorderTableName), 'base_currency', array(
			'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
			'comment' => 'Base Currency',
			'unsigned'  => true,
	));
	$installer->getConnection()
	->addColumn($installer->getTable($vorderTableName), 'base_to_global_rate', array(
			'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
			'comment' => 'Base to Global Rate',
			'unsigned'  => true,
			'length'=>'12,4'
	));
	$installer->getConnection()
	->addColumn($installer->getTable($vpaymentTableName), 'base_currency', array(
			'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
			'comment' => 'Base Currency',
			'unsigned'  => true
	));
	$installer->getConnection()
	->addColumn($installer->getTable($vpaymentTableName), 'base_to_global_rate', array(
			'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
			'comment' => 'Base to Global Rate',
			'unsigned'  => true,
			'length'=>'12,4'
	));
}	
$installer->endSetup();

	
	
	