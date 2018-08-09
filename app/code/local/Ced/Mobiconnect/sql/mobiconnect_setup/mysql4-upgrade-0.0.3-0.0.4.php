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
		-- DROP TABLE IF EXISTS {$this->getTable('mobiconnect_customerhash')};
		CREATE TABLE {$this->getTable('mobiconnect_customerhash')} (
		`id` int(11) NOT NULL auto_increment,
		`customer_id` int(11),
		`secure_hash` varchar(255),
		`expiry_date` timestamp,
		 PRIMARY KEY  (`id`)
);
");
$installer->run("
		
");
?>