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
$tableVroders = $installer->getTable('csmarketplace/vorders');

// delete group group_code duplicates
$duplicatedGroups = $installer->getConnection()->fetchPairs("
SELECT vendor_id, order_id FROM {$tableVroders} GROUP by order_id HAVING COUNT(vendor_id) > 1
");
/* print_r($duplicatedGroups);die; */
$installer->run("DELETE FROM {$tableVroders} WHERE order_id "
    . $installer->getConnection()->quoteInto('IN (?) ', array_values($duplicatedGroups)));

$installer->run("ALTER TABLE `{$installer->getTable('csmarketplace/vorders')}`
  ADD UNIQUE KEY `IDX_CED_VORDERS_UNIQUE_KEY` ( `vendor_id` , `order_id`)");

$installer->endSetup();

	
	
	