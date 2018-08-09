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

/* Update the input of created_at attribute from label to date */
$attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode('csmarketplace_vendor', 'created_at');
$installer->updateAttribute('csmarketplace_vendor',$attributeModel->getId(),'frontend_input','date');

$installer->run("
		CREATE TABLE IF NOT EXISTS `{$installer->getTable('csmarketplace/vproducts')}` (
			`id` int(11) NOT NULL auto_increment,
			`vendor_id` int(11),
			`product_id` int(11),
			`type` text,
			`price` float,
			`special_price` float,
			`name` text,
			`description` text,
			`short_description` text,
			`sku` text,
			`weight` decimal(12,4),
			`check_status` tinyint(1),
			`qty` int(11),
			`is_in_stock` boolean,
			PRIMARY KEY  (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
"); 
$installer->endSetup();
