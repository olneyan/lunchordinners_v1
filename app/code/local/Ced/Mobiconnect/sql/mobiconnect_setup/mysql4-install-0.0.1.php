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
  * @package   Ced_Mobiconnect
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
$installer = $this;
$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->run("
		-- DROP TABLE IF EXISTS {$this->getTable('mobiconnect_banner')};
		CREATE TABLE {$this->getTable('mobiconnect_banner')} (
		`id` int(11) NOT NULL auto_increment,
		`title` varchar(255),
		`description` text,
		`image` varchar(255) ,
		`status` varchar(20) ,
		`store` varchar(20) ,
		`show_in` varchar(20) ,
		`choose_website` varchar(255) ,
		 PRIMARY KEY  (`id`)
);

-- DROP TABLE IF EXISTS {$this->getTable('mobiconnect_widget')};
		 CREATE TABLE {$this->getTable('mobiconnect_widget')} (
		`id` int(11) NOT NULL auto_increment,
		`widget_title` varchar(255),
		`banner_image` varchar(255),
		`type` varchar(255),
		`website` varchar(255) ,
		`store` varchar(255) ,
		`status` varchar(255),
		`updated_at` timestamp,
		`created_at` timestamp,
		 PRIMARY KEY  (`id`)
);
");
$installer->run("
		
");
?>