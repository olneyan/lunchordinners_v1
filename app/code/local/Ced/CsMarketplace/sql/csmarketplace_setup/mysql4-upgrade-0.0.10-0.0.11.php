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
if(version_compare(Mage::getVersion(), '1.6', '<=')) {
	$installer->getConnection()->addColumn($installer->getTable($vorderTableName), 'items_commission', Varien_Db_Ddl_Table::TYPE_VARCHAR.'(1000)');
} else {
	$installer->getConnection()
	->addColumn($installer->getTable($vorderTableName), 'items_commission', array(
			'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
			'comment' => 'Items Commission',
			'unsigned'  => true,
	));
}	
$installer->endSetup();

	
	
	