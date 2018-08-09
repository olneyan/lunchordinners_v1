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
 * @author    CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
$installer->startSetup();

$entityTypeId = Mage::getModel('eav/entity')->setType('csmarketplace_vendor')->getTypeId();
$eavEntityStore = Mage::getModel('eav/entity_store');
$eavEntityStore->setData('entity_type_id',$entityTypeId);
$eavEntityStore->setData('store_id',0);
$eavEntityStore->setData('increment_prefix',1);
$eavEntityStore->setData('increment_last_id',100000);
$eavEntityStore->save();

$vendor_collection = Mage::getModel('csmarketplace/vendor')->getCollection();
$prefix = Mage::getStoreConfig('ced_csmarketplace/general/prefixmemberid_active');
$member_lastid = $eavEntityStore->getIncrementLastId();

foreach($vendor_collection as $value){
	$vendor = Mage::getModel('csmarketplace/vendor')->load($value->getId());	
	if($vendor->getId()){
		$member_lastid++;
		$vendor->setMemberId($prefix.$member_lastid);
		$vendor->save();
	}
}
$eavEntityStore->setIncrementLastId($member_lastid)->save();

$installer->endSetup();
