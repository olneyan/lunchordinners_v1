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
class Ced_Mobiconnectdeals_Model_Mysql4_Group extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('mobiconnectdeals/group', 'group_id');
    }
    function getdealgroupdata($groupId=''){
    	$read   = $this->_getReadAdapter();
        $select = $read->select()->from($this->getMainTable());
        $deal_groups=$read->fetchAll($select);
        $currentTime =Mage::getModel('core/date')->date('Y-m-d H:i:s');
        $select->where("group_end_date >= '{$currentTime}' AND group_start_date <= '{$currentTime}' AND group_status='1'");
        $deal_groups=$read->fetchAll($select);
        $result=array();
        foreach ($deal_groups as $key => $deal_group) {
            $deals=$deal_group['content'];
            $start_date =$deal_group['group_start_date'];
            $end_date = $deal_group['group_end_date'];
            $currentTime =Mage::getModel('core/date')->date('Y-m-d H:i:s');
            if (strtotime($start_date) < strtotime($end_date)) {
                if(strtotime($currentTime) > strtotime($start_date)){ 
                    if(strtotime($currentTime) <= strtotime($end_date)){
                        $diff = abs(strtotime($end_date) - strtotime($currentTime));
                        $deal_group['deal_duration']= $diff*1000;
                    }else{
                       $deal_group['deal_duration']=0;
                    }
                }else{
                     $deal_group['deal_duration']=0;
                }
            } else {
                if(strtotime($currentTime) > strtotime($end_date)){ 
                    if(strtotime($currentTime) <= strtotime($start_date)){
                        $diff = abs(strtotime($start_date) - strtotime($currentTime)); 
                        $deal_group['deal_duration']= $diff;
                     }else{
                       $deal_group['deal_duration']=0;
                     }
                }else{
                     $deal_group['deal_duration']=0;
                }
            }   
   
            $deals=explode(',',$deals);
            if(count($deals)!=0){
                $darray=array();
                foreach ($deals as $key => $value) {
                    $deal=Mage::getModel('mobiconnectdeals/deals')->load($value)->getData();
                    if(isset($deal['id']) && $deal['id']!=0){
                    if(isset($deal['deal_image_name']) && $deal['deal_image_name']!='' )
                    $deal['deal_image_name']=Mage::getBaseUrl('media').$deal['deal_image_name'];  
                       
                      if(isset($deal['deal_type']) && $deal['deal_type']>0){
                            switch ($deal['deal_type']) {
                                    case '1':
                                        $deal['relative_link']=$deal['product_link'];
                                        break;
                                    case '2':
                                        $deal['relative_link']=$deal['category_link'];
                                        break;
                                    case '3':
                                       $deal['relative_link']=$deal['static_link'];
                                        break;    
                                    default:
                                        break;
                                }
                        }
                        $darray[]=$deal;  
                    }
                }
            $deal_group['group_image_name']=Mage::getBaseUrl('media').$deal_group['group_image_name'];
            $deal_group['content']=$darray;         
            $result['success']=false;
            $result['data']['deal_products'][]= $deal_group;
            $result['data']['status'][]=$deal_group['group_status'];
            }
        }
        return json_encode($result);
    }
}
