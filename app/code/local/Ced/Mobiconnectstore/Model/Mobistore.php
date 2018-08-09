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
  * @package   Ced_Mobiconnectstore
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobiconnectstore_Model_Mobistore extends Mage_Core_Model_Abstract {
	/**
	*Get Store 
	**/
	public function getStore(){
		$stores = Mage::app()->getStores();
		$array=array();
		$allowspecific = Mage::getStoreConfig('mobiconnectstore/mobistore/allowspecific'); 
        if($allowspecific){
            $specificstore = Mage::getStoreConfig('mobiconnectstore/mobistore/specificcountry'); 
            $specificstore=explode(',',$specificstore);
				foreach ($stores as $store)
				{	
					if(in_array(trim($store->getId()),$specificstore))
					{
						$array[]=$store->getData();
					}	
				}
			}else{
				foreach ($stores as $store)
				{	
					$array[]=$store->getData();
				}
			}	
		return json_encode(array('store_data'=> $array));
    }
    /**
	*Set Store 
	**/
	public function setStore($store_id){
		Mage::app()->setCurrentStore($store_id);
		if($store_id==Mage::app()->getStore()->getId()){
			return json_encode(array('success'=> true,'locale_code'=>Mage::getStoreConfig('general/locale/code', $store_id)));
		}else{
			return json_encode(array('success'=> false,'message'=> 'Store not set Successfully'));
		}
		
    }  
}
?>
