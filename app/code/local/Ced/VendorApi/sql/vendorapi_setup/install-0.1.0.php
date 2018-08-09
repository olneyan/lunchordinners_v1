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
  * @category    Ced
  * @package     VendorApi
  * @author      CedCommerce Core Team <connect@cedcommerce.com>
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://cedcommerce.com/license-agreement.txt
  */
 
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()->newTable($installer->getTable('vendorapi/vapisession'))
			->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER,11,array(
	        'identity'  => true,
	        'unsigned'  => true,
	        'nullable'  => false,
	        'primary'   => true,
	        ))
	        ->addColumn('vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER,11, array(
	        	'nullable'  => true,
	        ))
	        ->addColumn('vendor_username', Varien_Db_Ddl_Table::TYPE_VARCHAR, 200, array(
	        	'nullable'  => true,
	        ))
       	    ->addColumn('sid', Varien_Db_Ddl_Table::TYPE_VARCHAR, 200, array(
        		'nullable'  => true,
       	    ))
       	    ->addColumn('logdate', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
       	    	'nullable'  => true,
        	));  
$installer->getConnection()->createTable($table);  

$installer->run("
		-- DROP TABLE IF EXISTS {$this->getTable('vendor_api_vendorhash')};
		CREATE TABLE {$this->getTable('vendor_api_vendorhash')} (
		`id` int(11) NOT NULL auto_increment,
		`customer_id` int(11),
		`vendor_id` int(11),
		`secure_hash` varchar(255),
		`expiry_date` timestamp,
		 PRIMARY KEY  (`id`)
);
"); 

$installer->run("CREATE TABLE IF NOT EXISTS {$this->getTable('vendorapi/vendordevices')} (
  `device_rowid` int(11) NOT NULL AUTO_INCREMENT,
  `device_id` text NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `email` text NOT NULL,
  `extra` text NOT NULL,
PRIMARY KEY  (`device_rowid`)
) ;

    ");
$installer->endSetup();
?>