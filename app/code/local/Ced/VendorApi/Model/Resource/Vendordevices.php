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


class Ced_VendorApi_Model_Resource_Vendordevices extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        
        $this->_init('vendorapi/vendordevices', 'device_rowid');
    }
    public function getDeviceIds($device_rowids){
	   	$read   = $this->_getReadAdapter();
        $select = $read->select()->from($this->getMainTable(),array('device_id'))->where("device_rowid IN(".implode(',',$device_rowids).")");
        $data= $read->fetchCol($select);
        return $data;
    }
}