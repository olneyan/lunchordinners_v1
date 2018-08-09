<?php

/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License (AFL 3.0)
  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/afl-3.0.php
  *
  * @category    Ced
  * @package     Ced_CsVAttribute
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
$resource = Mage::getSingleton('core/resource');
$readConnection = $resource->getConnection('core_read');  
    /**
     * Get the table name
     */
$tableName = $resource->getTableName('csmarketplace/vendor_form');

$saleafield=$readConnection->describeTable($tableName);
$attribute=array();
foreach($saleafield as $key=>$val) {
	$attribute[]=$key;
}
if(!in_array('use_in_invoice',$attribute)) {
$installer = $this;
$installer->startSetup();
$installer->run("ALTER TABLE {$installer->getTable('ced_csmarketplace_vendor_form_attribute')} ADD ( `use_in_invoice` int)");
$installer->endSetup();	 
}
?>
