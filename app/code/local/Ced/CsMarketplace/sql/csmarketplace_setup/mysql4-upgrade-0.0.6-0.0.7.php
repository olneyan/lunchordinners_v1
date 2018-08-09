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
	$types = array(
					'datetime',
					'decimal',
					'int',
					'text',
					'varchar'
				);

	$baseTableName = 'csmarketplace/vendor';

	foreach($types as $type) {
		$eavTableName = $baseTableName ."_". $type;
		$installer->run("ALTER TABLE `".$installer->getTable($baseTableName)."_".$type."` ADD UNIQUE `IDX_CSMARKETPLACE_CED_".strtoupper($type)."_UNIQUE_KEY` ( `attribute_id` , `entity_id` , `entity_type_id` , `store_id` )");
	}
	
	$installer->getConnection()->addColumn($installer->getTable('csmarketplace/vproducts'), 'website_ids', 'text');
	
} else {
	$types = array(
					'datetime',
					'decimal',
					'int',
					'text',
					'varchar',
					'char'
				);
	$baseTableName = 'csmarketplace/vendor';
	foreach($types as $type) {
		 $eavTableName = array($baseTableName, $type);
		 $installer->run("ALTER TABLE `".$installer->getTable($eavTableName)."` ADD UNIQUE `IDX_CSMARKETPLACE_CED_".strtoupper($type)."_UNIQUE_KEY` ( `attribute_id` , `entity_id` , `entity_type_id` , `store_id` )");
	}

	$installer->getConnection()
		->addColumn($installer->getTable('csmarketplace/vproducts'), 'website_ids', array(
			'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
			'comment' => 'Website Ids',
			'unsigned'  => true
		));
} 
$installer->endSetup();

	
	
	