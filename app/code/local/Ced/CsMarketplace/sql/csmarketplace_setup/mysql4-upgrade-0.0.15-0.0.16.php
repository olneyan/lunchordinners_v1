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

if(version_compare(Mage::getVersion(), '1.6', '<=')) { 

	$installer->getConnection()->addColumn($this->getTable('csmarketplace/vendor_form'), 'use_in_registration', Varien_Db_Ddl_Table::TYPE_INTEGER.' DEFAULT 0');
	$installer->getConnection()->addColumn($this->getTable('csmarketplace/vendor_form'), 'position_in_registration', Varien_Db_Ddl_Table::TYPE_INTEGER.' DEFAULT 0');
	$installer->getConnection()->addColumn($this->getTable('csmarketplace/vendor_form'), 'use_in_left_profile', Varien_Db_Ddl_Table::TYPE_INTEGER.' DEFAULT 0');
	$installer->getConnection()->addColumn($this->getTable('csmarketplace/vendor_form'), 'position_in_left_profile', Varien_Db_Ddl_Table::TYPE_INTEGER.' DEFAULT 0');
	$installer->getConnection()->addColumn($this->getTable('csmarketplace/vendor_form'), 'fontawesome_class_for_left_profile', Varien_Db_Ddl_Table::TYPE_VARCHAR."(200) DEFAULT 'fa-circle-thin'");

} else {
	
	$installer->getConnection()
		->addColumn($this->getTable('csmarketplace/vendor_form'), 'use_in_registration', array(
			'type'    => Varien_Db_Ddl_Table::TYPE_INTEGER,
			'comment' => 'Use in Registration Form',
			'unsigned'=> true,
			'default' => 0, 
		));
	$installer->getConnection()
		->addColumn($this->getTable('csmarketplace/vendor_form'), 'position_in_registration', array(
			'type'    => Varien_Db_Ddl_Table::TYPE_INTEGER,
			'comment' => 'Position in Registration Form',
			'unsigned'=> true,
			'default' => 0, 
		));
		
	$installer->getConnection()
		->addColumn($this->getTable('csmarketplace/vendor_form'), 'use_in_left_profile', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_INTEGER,
				'comment' => 'Use in Left Profile',
				'unsigned'=> true,
				'default' => 0,
		));
	
	$installer->getConnection()
		->addColumn($this->getTable('csmarketplace/vendor_form'), 'position_in_left_profile', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_INTEGER,
				'comment' => 'Position in Left Profile',
				'unsigned'=> true,
				'length'  => 50,
				'default' => 0,
		));
	
	$installer->getConnection()
		->addColumn($this->getTable('csmarketplace/vendor_form'), 'fontawesome_class_for_left_profile', array(
				'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
				'comment' => 'Fontawesome class for Left Profile',
				'length'  => 200,
				'unsigned'=> true,
				'default' => 'fa fa-circle-thin',
		));
}
$installer->endSetup();