<?php
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category  Ced
  * @package   Ced_Mobinotification
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
$installer = $this;

$installer->startSetup();

$installer->run("
  CREATE TABLE IF NOT EXISTS `{$this->getTable('mobinotification/notification')}` (
`id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `message` text NOT NULL,
  `status` tinyint(1) NOT NULL,
  `image` text NOT NULL,
  `type` tinyint(4) NOT NULL,
  `related_link` int(11) NOT NULL,
  `extra` text NOT NULL,
  `linked_product` int(11) NOT NULL,
  `linked_category` int(11) NOT NULL,
  `linked_page` text NOT NULL,
  `shedule` datetime NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY  (`id`)) ;");
$installer->run("
  CREATE TABLE IF NOT EXISTS `{$this->getTable('mobinotification/mobidevices')}` (
`device_rowid` int(11) NOT NULL AUTO_INCREMENT,
  `device_id` text NOT NULL,
  `email` text NOT NULL,
  `extra` text NOT NULL,
PRIMARY KEY  (`device_rowid`)) ;");
$installer->endSetup(); 
