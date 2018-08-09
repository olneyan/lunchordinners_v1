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
 * @package     Ced_CsVAttribute
 * @author    CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
$installer->startSetup();
if(version_compare(Mage::getVersion(), '1.6', '<=')) { 

	$installer->getConnection()->addColumn($installer->getTable('csmarketplace/vpayment'), 'recovered', Varien_Db_Ddl_Table::TYPE_VARCHAR."(2) DEFAULT '0'");

} else {
	
	
	$installer->getConnection()
		->addColumn($this->getTable('csmarketplace/vpayment'), 'recovered', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
				'comment' => 'Recovered',
				'length'  => 2,
				'unsigned'=> true,
				'default' => '0',
		));
}


$installer->run("UPDATE {$installer->getTable('csmarketplace/vpayment')} SET `currency`=`base_currency`,`recovered`='1' WHERE `currency`='0.0000' ");

$installer->endSetup();