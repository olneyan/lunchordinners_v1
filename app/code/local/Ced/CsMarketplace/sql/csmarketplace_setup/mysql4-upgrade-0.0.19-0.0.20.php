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
 * @package     Ced_CsVAttribute
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;
$installer->startSetup();
$vorderTableName = 'csmarketplace/vorders';
$vpaymentTableName = 'csmarketplace/vpayment';
if(version_compare(Mage::getVersion(), '1.6', '<=')) { 

	$installer->getConnection()->modifyColumn($this->getTable($vorderTableName), 'base_order_total', Varien_Db_Ddl_Table::TYPE_DECIMAL.'(12,4)');
	$installer->getConnection()->modifyColumn($this->getTable($vorderTableName), 'order_total', Varien_Db_Ddl_Table::TYPE_DECIMAL.'(12,4)');
	$installer->getConnection()->modifyColumn($this->getTable($vorderTableName), 'shop_commission_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL.'(12,4)');
	$installer->getConnection()->modifyColumn($this->getTable($vorderTableName), 'shop_commission_base_fee', Varien_Db_Ddl_Table::TYPE_DECIMAL.'(12,4)');
	$installer->getConnection()->modifyColumn($this->getTable($vorderTableName), 'shop_commission_fee', Varien_Db_Ddl_Table::TYPE_DECIMAL.'(12,4)');
	$installer->getConnection()->modifyColumn($this->getTable($vorderTableName), 'base_to_global_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL.'(12,4)');
	
	$installer->getConnection()->modifyColumn($this->getTable($vpaymentTableName), 'amount', Varien_Db_Ddl_Table::TYPE_DECIMAL.'(12,4)');
	$installer->getConnection()->modifyColumn($this->getTable($vpaymentTableName), 'base_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL.'(12,4)');
	/* $installer->getConnection()->modifyColumn($this->getTable($vpaymentTableName), 'currency', Varien_Db_Ddl_Table::TYPE_DECIMAL.'(12,4)'); */
	$installer->getConnection()->modifyColumn($this->getTable($vpaymentTableName), 'fee', Varien_Db_Ddl_Table::TYPE_DECIMAL.'(12,4)');
	$installer->getConnection()->modifyColumn($this->getTable($vpaymentTableName), 'base_fee', Varien_Db_Ddl_Table::TYPE_DECIMAL.'(12,4)');
	$installer->getConnection()->modifyColumn($this->getTable($vpaymentTableName), 'net_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL.'(12,4)');
	$installer->getConnection()->modifyColumn($this->getTable($vpaymentTableName), 'base_net_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL.'(12,4)');
	$installer->getConnection()->modifyColumn($this->getTable($vpaymentTableName), 'balance', Varien_Db_Ddl_Table::TYPE_DECIMAL.'(12,4)');
	$installer->getConnection()->modifyColumn($this->getTable($vpaymentTableName), 'base_balance', Varien_Db_Ddl_Table::TYPE_DECIMAL.'(12,4)');
	$installer->getConnection()->modifyColumn($this->getTable($vpaymentTableName), 'tax', Varien_Db_Ddl_Table::TYPE_DECIMAL.'(12,4)');
	$installer->getConnection()->modifyColumn($this->getTable($vpaymentTableName), 'base_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL.'(12,4)');
	$installer->getConnection()->modifyColumn($this->getTable($vpaymentTableName), 'base_to_global_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL.'(12,4)');
	
} else {
	
	
	$installer->getConnection()
		->modifyColumn($this->getTable($vorderTableName), 'base_order_total', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
				'comment' => 'Base Order Total',
				'unsigned'  => true,
				'length'=>'12,4'
		));
		$installer->getConnection()
		->modifyColumn($this->getTable($vorderTableName), 'order_total', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
				'comment' => 'Order Total',
				'unsigned'  => true,
				'length'=>'12,4'
		));
		$installer->getConnection()
		->modifyColumn($this->getTable($vorderTableName), 'shop_commission_rate', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
				'comment' => 'Shop Commission Rate',
				'unsigned'  => true,
				'length'=>'12,4'
		));
		$installer->getConnection()
		->modifyColumn($this->getTable($vorderTableName), 'shop_commission_base_fee', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
				'comment' => 'Shop Commission Base Fee',
				'unsigned'  => true,
				'length'=>'12,4'
		));
		$installer->getConnection()
		->modifyColumn($this->getTable($vorderTableName), 'shop_commission_fee', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
				'comment' => 'Shop Commission Fee',
				'unsigned'  => true,
				'length'=>'12,4'
		));
		$installer->getConnection()
		->modifyColumn($this->getTable($vorderTableName), 'base_to_global_rate', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
				'comment' => 'Base To Global Rate',
				'unsigned'  => true,
				'length'=>'12,4'
		));
		
		
	$installer->getConnection()
		->modifyColumn($this->getTable($vpaymentTableName), 'amount', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
				'comment' => 'Amount',
				'unsigned'  => true,
				'length'=>'12,4'
		));
		$installer->getConnection()
		->modifyColumn($this->getTable($vpaymentTableName), 'base_amount', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
				'comment' => 'Base Amount',
				'unsigned'  => true,
				'length'=>'12,4'
		));$installer->getConnection()
		->modifyColumn($this->getTable($vpaymentTableName), 'currency', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
				'comment' => 'Currency',
				'unsigned'  => true,
				'length'=>'12,4'
		));$installer->getConnection()
		->modifyColumn($this->getTable($vpaymentTableName), 'fee', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
				'comment' => 'Fee',
				'unsigned'  => true,
				'length'=>'12,4'
		));$installer->getConnection()
		->modifyColumn($this->getTable($vpaymentTableName), 'base_fee', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
				'comment' => 'Base Fee',
				'unsigned'  => true,
				'length'=>'12,4'
		));$installer->getConnection()
		->modifyColumn($this->getTable($vpaymentTableName), 'net_amount', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
				'comment' => 'Net Amount',
				'unsigned'  => true,
				'length'=>'12,4'
		));$installer->getConnection()
		->modifyColumn($this->getTable($vpaymentTableName), 'base_net_amount', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
				'comment' => 'Base Net Amount',
				'unsigned'  => true,
				'length'=>'12,4'
		));$installer->getConnection()
		->modifyColumn($this->getTable($vpaymentTableName), 'balance', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
				'comment' => 'Balance',
				'unsigned'  => true,
				'length'=>'12,4'
		));$installer->getConnection()
		->modifyColumn($this->getTable($vpaymentTableName), 'base_balance', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
				'comment' => 'Base Balance',
				'unsigned'  => true,
				'length'=>'12,4'
		));$installer->getConnection()
		->modifyColumn($this->getTable($vpaymentTableName), 'tax', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
				'comment' => 'Tax',
				'unsigned'  => true,
				'length'=>'12,4'
		));$installer->getConnection()
		->modifyColumn($this->getTable($vpaymentTableName), 'base_tax', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
				'comment' => 'Base Tax',
				'unsigned'  => true,
				'length'=>'12,4'
		));
		$installer->getConnection()
		->modifyColumn($this->getTable($vpaymentTableName), 'base_to_global_rate', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
				'comment' => 'Base To Global Rate',
				'unsigned'  => true,
				'length'=>'12,4'
		));
}

$installer->endSetup();