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
//$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->run("
	
-- DROP TABLE IF EXISTS {$this->getTable('mobiconnectadvcart/categorybanner')};
		CREATE TABLE {$this->getTable('mobiconnectadvcart/categorybanner')} (
  `id` int(11) NOT NULL auto_increment,
  `banner_title` varchar(255) NOT NULL DEFAULT '',
  `banner_image` varchar(255),
		`store` varchar(255) ,
		`banner_status` varchar(255),
		`updated_at` timestamp,
		`created_at` timestamp,
		`priority` int(11),
		`show_on` varchar(55),
		 PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();
?>