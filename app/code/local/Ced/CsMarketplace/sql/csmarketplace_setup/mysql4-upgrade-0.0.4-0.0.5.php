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
	$installer->getConnection()->addColumn($installer->getTable('csmarketplace/vendor'), 'website_id', Varien_Db_Ddl_Table::TYPE_INTEGER);
} else {
	$installer->getConnection()
		->addColumn($installer->getTable('csmarketplace/vendor'), 'website_id', array(
			'type'    => Varien_Db_Ddl_Table::TYPE_INTEGER,
			'comment' => 'Website ID',
			'unsigned'=> true,
			'default' => 0, 
		));
}	
$installer->updateVendorAttributes();

$installer->endSetup();

	
	
	