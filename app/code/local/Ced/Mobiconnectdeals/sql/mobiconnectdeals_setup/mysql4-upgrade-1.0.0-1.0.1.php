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
  * @package   Ced_Mobiconnectdeals
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS  {$this->getTable('mobiconnectdeals/group')} (
`group_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `group_image_name` text NOT NULL,
  `content` text NOT NULL,
  `group_status` smallint(6) NOT NULL DEFAULT '0',
  `timer_status` smallint(6) NOT NULL DEFAULT '0',
  `view_all_status` smallint(6) NOT NULL DEFAULT '0',
  `is_static` smallint(6) NOT NULL DEFAULT '0',
  `deal_link`  text NOT NULL,
  `created_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `group_start_date` datetime NOT NULL,
  `group_end_date` datetime NOT NULL,
  PRIMARY KEY  (`group_id`)
);");
$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('mobiconnectdeals/deals')}(
`id` int(11) NOT NULL AUTO_INCREMENT,
  `deal_title` text NOT NULL,
  `deal_type` int(11) NOT NULL,
  `offer_text` text NOT NULL,
  `static_link` text NOT NULL,
  `category_link` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` text NOT NULL,
  `product_link` text NOT NULL,
  `show_in_slider` int(11) NOT NULL,
  `deal_image_name` text NOT NULL,
  `deal_group` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);");

$installer->endSetup(); 