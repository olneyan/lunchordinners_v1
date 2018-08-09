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
 * @category    Ced;
 * @package     Ced_AdvFlatRate 
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com> 
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
$installer->startSetup();

$installer->run("

			DROP TABLE IF EXISTS {$this->getTable('advflatrate/advance')};
			CREATE TABLE {$this->getTable('advflatrate/advance')} (
				  id int(10) unsigned NOT NULL auto_increment,
				  website_id int(11) NOT NULL default '0',
				  vendor_id varchar(10) NOT NULL default 'admin',
				  country_id varchar(4) NOT NULL default '0',
				  region_id int(10) NOT NULL default '0',
				  city varchar(30) NOT NULL default '',
				  zipcode varchar(10) NOT NULL default '',
				  price decimal(12,4) NOT NULL default '0.0000',
				  PRIMARY KEY(`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
   ");

$installer->endSetup();


