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
  * @package   Ced_MobiconnectAdvCart
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_MobiconnectAdvCart_Model_Customer_Order extends Mage_Core_Model_Abstract {

	public function formatDate($date = null, $format = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, $showTime = false){
        return $this->helper('core')->formatDate($date, $format, $showTime);
    }
    public function helper($name){
        if ($this->getLayout()) {
            return $this->getLayout()->helper($name);
        }
        return Mage::helper($name);
    }
	public function getDownloadLink($data){
   
		try{
	  	$purchased = Mage::getResourceModel('downloadable/link_purchased_collection')
	   	->addFieldToFilter('customer_id',$data['customer'])
	   	->addOrder('created_at', 'desc');
	   	$this->setPurchased($purchased);
	   	$purchasedIds = array();
	   	foreach ($purchased as $_item) {
	   		$purchasedIds[] = $_item->getId();
	   	}
	   	//var_dump($purchasedIds);die;
	   	if (empty($purchasedIds)) {
	   		$purchasedIds = array(null);
	   	}
	   	$purchasedItems = Mage::getResourceModel('downloadable/link_purchased_item_collection')
	   	->addFieldToFilter('purchased_id', array('in' => $purchasedIds))
	   	->addFieldToFilter('status',
	   			array(
	   					'nin' => array(
	   							Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING_PAYMENT,
	   							Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PAYMENT_REVIEW
	   					)
	   			)
	   	)
	   	->setOrder('item_id', 'desc');
	   	 
	   	if(count($purchasedItems)>0){
	   		$downloadable=array();
	   		foreach ($purchasedItems as $_item)
	   		{
	   			$filename=explode('/',$_item->getLinkFile());
	   			$filename=$filename[count($filename)-1];
	   			$model=Mage::getModel('downloadable/link_purchased')->load($_item->getData('purchased_id'));
	   
	   			if ($_item->getNumberOfDownloadsBought()) {
	   				$downloads = $_item->getNumberOfDownloadsBought() - $_item->getNumberOfDownloadsUsed();
	   			}
	   			else{
	   				$downloads= Mage::helper('downloadable')->__('Unlimited');
	   			}
	   			$downloadlink= Mage::getUrl('mobiconnectadvcart/download/link', array('id' => $_item->getLinkHash(), '_secure' => true));
	   
	   			$downloadable[]=array(
	   					'order_id'=>$model->getOrderIncrementId(),
	   					'date'=>$this->formatDate($model->getCreatedAt()),
	   					'title'=>$model->getProductName(),
	   					'download_url'=>$downloadlink,
	   					'file_name'=>$filename,
	   					'link_title'=>$_item->getLinkTitle(),
	   					'status'=>Mage::helper('downloadable')->__(ucfirst($_item->getStatus())),
	   					'remaining_dowload'=>$downloads
	   
	   			);
	   
	   		}
	   
	   		$data=array(
	   				'data'=>array(
	   						'downloadable-product'=>$downloadable,
	              'item_count'=>count($downloadable),
	   						'status'=>'true'
	   				)
	   		);
	   		return $data;
	   
	   	}
	   	else{
	   		$data=array(
	   				'data'=>array(
	   						'message'=>'You have not purchased any downloadable products yet.',
	   						'status'=>'false'
	   
	   				)
	   		);
	   		return $data;
	   	}
	   }catch ( Exception $e ) {
			$message = $e->getMessage ();
			$data = array (
					'data' => array(
								'message' => $message,
								'status' => 'exception' 
						)			
					);
			return $data;
		}
   }
}