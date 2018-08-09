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

class Ced_VendorApi_Model_Resource_Vapisession extends Mage_Core_Model_Resource_Db_Abstract
{
	public function _construct()
	{
		$this->_init('vendorapi/vapisession', 'id');
	}
	
	public function recordVendorSession($vid, $sid, $uname)
	{
		$readAdapter    = $this->_getReadAdapter();
		$writeAdapter   = $this->_getWriteAdapter();
	/* 	$select = $readAdapter->select()
		->from($this->getTable('vendorapi/vapisession'), 'vendor_id')
		->where('vendor_id = ?', $vid)
		->where('vendor_username = ?', $uname); */
		$loginDate = Mage::getModel('core/date')->date('Y-m-d H:i:s');
		
		$writeAdapter->insert(
				$this->getTable('vendorapi/vapisession'),
				array(
						'vendor_id' => $vid,
						'vendor_username' => $uname,
						'sid' => $sid,
						'logdate' => $loginDate
				)
		);
		return $this;
	}
	
	public function updateVendorSession($vid, $sid)
	{
		$readAdapter    = $this->_getReadAdapter();
		$writeAdapter   = $this->_getWriteAdapter();
		
		$select = $readAdapter->select()
		->from($this->getTable('vendorapi/vapisession'), 'vendor_id')
		->where('vendor_id = ?', $vid)
		->where('sid = ?', $sid);
		
		if ($row = $readAdapter->fetchRow($select)) {
			$where = $readAdapter->quoteInto('vendor_id = ?', $vid) . ' AND '. $readAdapter->quoteInto('sid = ?', $sid);
			$loginDate = Mage::getModel('core/date')->date('Y-m-d H:i:s');
			$writeAdapter->update(
					$this->getTable('vendorapi/vapisession'),
					array ('logdate' => $loginDate),$where
					);
			return $row;
		}
	}
	
	public function isVendorLoggedIn($vid, $sid)
	{
		$readAdapter    = $this->_getReadAdapter();
		$writeAdapter   = $this->_getWriteAdapter();
		$select = $readAdapter->select()
		->from($this->getTable('vendorapi/vapisession'))
		->where('vendor_id = ?', $vid)
		->where('sid = ?', $sid);
		$row = $readAdapter->fetchRow($select);
		if ($row && $this->dateTimeDiff($row['logdate'])) {
			$this->updateVendorSession($vid, $sid);
			return true;
		}
		return false;
	}
	
	public function dateTimeDiff($datetime1)
	{	
		$old_time = new DateTime($datetime1);
		$year = 0;
		$month = 0;
		$date=0;
		$hour=1;
		$min=0;
		$sec=0;
		$add_time_string = 'P'.$year.'Y'.$month.'M'.$date.'DT'.$hour.'H'.$min.'M'.$sec.'S';
		
		$old_time->add(new DateInterval($add_time_string));
		$old_time = $old_time->format('Y-m-d H:i:s');
		
		
		$now = Mage::getModel('core/date')->date('Y-m-d H:i:s');
			
		$datetime1 = new DateTime($old_time);
		$datetime2 = new DateTime($now);
		
		$diff = ($datetime1->diff($datetime2));
	
		if($diff->format("%R") == '+') {
			//return 'Date2 is Greater Then Date1';
			return false;
		}
		elseif($diff->format("%R") == '-') {
			//return 'Date1 is Greater Then Date2';
			return true;
		}
	}
	
	public function deleteVendorSession($vid)
	{
		try {
			$table = $this->getTable('vendorapi/vapisession');
			//$where = $this->_getWriteAdapter()->quoteInto('vendor_id = ?', $vid) . ' AND '. $readAdapter->quoteInto('sid = ?', $sid);
			$where = $this->_getWriteAdapter()->quoteInto('vendor_id = ?', $vid);
			return $this->_getWriteAdapter()->delete($table,$where);
		}
		catch (Exception $e) {
			echo $e->getMessage();
			die;
		}
	}
}
