<?php  

/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License (AFL 3.0)
  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/afl-3.0.php
  *
  * @category    Ced
  * @package     Ced_CsVendorReview
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

/* @var $installer Ced_CsVendorReview_Model_Entity_Setup */

$installer = $this;
$installer->startSetup();

$installer->run("
		CREATE TABLE IF NOT EXISTS `{$installer->getTable('csvendorreview/review')}` (
			`id` int(11) NOT NULL auto_increment,
			`vendor_id` int(11),
			`vendor_name` text,		
			`customer_id` int(11),
			`customer_name` text,
			`quality` int(3),
			`price` int(3),
			`value` int(3),
			`subject` text,
			`review` text,
			`status` tinyint(1),
			`created_at` datetime NOT NULL default '0000-00-00 00:00:00',
			PRIMARY KEY  (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
"); 

$installer->run("
		CREATE TABLE IF NOT EXISTS `{$installer->getTable('csvendorreview/rating')}` (
			`id` int(5) NOT NULL auto_increment,
			`rating_label` text,
			`rating_code` VARCHAR(255) NOT NULL,
			`sort_order` int(5),
			PRIMARY KEY  (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
		TRUNCATE TABLE `{$installer->getTable('csvendorreview/rating')}`
");

$installer->run("
		insert into `{$installer->getTable('csvendorreview/rating')}` (`rating_label`,`rating_code`,`sort_order`) values ('Quality','quality',1),
		('Price','price',2),('Value','value',3);
");

$installer->endSetup();
