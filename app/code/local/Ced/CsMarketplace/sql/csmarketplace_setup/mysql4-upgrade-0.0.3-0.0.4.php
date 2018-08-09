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

	
/**
 * Create table 'csmarketplace/vpayment'
 */
$installer->run("
		CREATE TABLE IF NOT EXISTS `{$installer->getTable('csmarketplace/vpayment')}` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `vendor_id` int(11) DEFAULT NULL,
		  `transaction_id` varchar(200) DEFAULT NULL,
		  `amount_desc` text NOT NULL COMMENT 'Amount Description',
		  `amount` decimal(12,4) DEFAULT NULL,
		  `base_amount` decimal(12,4) NOT NULL COMMENT 'Base Amount',
		  `currency` varchar(10) NOT NULL COMMENT 'Currency',
		  `fee` decimal(12,4) DEFAULT NULL COMMENT 'Fee',
		  `base_fee` decimal(12,4) NOT NULL COMMENT 'Base Fee',
		  `net_amount` decimal(12,4) DEFAULT NULL COMMENT 'Net Amount',
		  `base_net_amount` decimal(12,4) NOT NULL COMMENT 'Base Net Amount',
		  `balance` decimal(12,4) NOT NULL COMMENT 'Balance',
		  `base_balance` decimal(12,4) NOT NULL COMMENT 'Base Balance',
		  `tax` decimal(12,4) DEFAULT NULL COMMENT 'Tax',
		  `base_tax` decimal(12,4) NOT NULL COMMENT 'Base Tax',
		  `notes` text,
		  `transaction_type` int(11) NOT NULL,
		  `payment_method` varchar(300) DEFAULT NULL,
		  `payment_code` varchar(200) DEFAULT NULL,
		  `payment_detail` text NOT NULL COMMENT 'Payment Detail',
		  `status` varchar(10) DEFAULT NULL,
		  `created_at` TIMESTAMP NOT NULL DEFAULT 0,
          `payment_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
		"); 
/* $installer->installVendorForms(); */
$installer->endSetup();

	
	
	