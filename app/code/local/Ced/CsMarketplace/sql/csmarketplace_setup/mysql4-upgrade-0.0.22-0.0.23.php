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
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;
$installer->startSetup();
$vpaymentTableName = 'csmarketplace/vpayment';
if(version_compare(Mage::getVersion(), '1.6', '<=')) { 
	$installer->getConnection()->modifyColumn($this->getTable($vpaymentTableName), 'currency', Varien_Db_Ddl_Table::TYPE_VARCHAR.'(20)');
	
} else {
	
	
	$installer->getConnection()
		->modifyColumn($this->getTable($vpaymentTableName), 'currency', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
				'comment' => 'Currency',
				'unsigned'  => true,
				'length'=>'12,4'
		));
}

$installer->endSetup();