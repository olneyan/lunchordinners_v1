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
  * @package   Ced_MobiconnectAdvCart
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
$installer = $this;
$installer->startSetup();
$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('mobiconnectadv_homelayout')};
		 CREATE TABLE {$this->getTable('mobiconnectadv_homelayout')} (
		`id` int(11) NOT NULL auto_increment,
		`widget_title` varchar(255),
		`banner_image` varchar(255),
		`layout_type` varchar(255),
		`website` varchar(255) ,
		`store` varchar(255) ,
		`status` varchar(255),
		`updated_at` timestamp,
		`created_at` timestamp,
		`priority` int(11),
		 PRIMARY KEY  (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->run("
		
");
?>