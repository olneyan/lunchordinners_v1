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
  * @category  Ced
  * @package   Ced_Mobiconnectdeals
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobiconnectdeals_Model_Mysql4_Deals extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        
        $this->_init('mobiconnectdeals/deals', 'id');
    }
    function getdealsdata($dealId=''){
    	$read   = $this->_getReadAdapter();
        $select = $read->select()->from($this->getMainTable());
        if($dealId)
        $select->where("(id = '{$dealId}')");
    	$dealdata=$read->fetchRow($select);
    	print_r($dealdata);die;
        return  json_encode($read->fetchRow($select));
    }
}