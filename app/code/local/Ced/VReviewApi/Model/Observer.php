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
  * @package     VReviewApi
  * @author      CedCommerce Core Team <connect@cedcommerce.com>
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://cedcommerce.com/license-agreement.txt
  */

class Ced_VReviewApi_Model_Observer {
	public function vendorReview($observer) {

		$enable = '1';
		if ($enable) {
			$eventData =$observer->getEvent ()->getVendorInfo ();
			$response = $eventData->getResponse();
			$isVendorListing = $eventData->getIsVendorList();
			if($isVendorListing){
				$review = $this->getVendorRating ($response->getEntityId());
				$response->setVendorReview($review);
				$response->setVendorReviewCount($this->getReviewCount($response->getEntityId()));
			}
			else{
				$review = $this->getVendorRating ($eventData->getVendorId());
				$response['data']['vendor_review'] = $review;
				$response['data']['review_count'] = $this->getReviewCount($eventData->getVendorId());
				$eventData->setResponse($response);
			}
		}
	}
	public function getVendorRating($vendorId){
		$review_data = Mage::getModel('csvendorreview/review')->getCollection()
							->addFieldToFilter('vendor_id',$vendorId)
							->addFieldToFilter('status',1);
		
		$rating = Mage::getModel('csvendorreview/rating')->getCollection()
								->addFieldToSelect('rating_code');
		$count = 0;
		$rating_sum = 0;
		foreach($review_data as $key => $value){
			foreach($rating as $k => $val){
					
				if($value[$val['rating_code']] > 0){
					$rating_sum += $value[$val['rating_code']];
					$count++;
				}
			}
		}

		if($count > 0 && $rating_sum > 0){
			$width = ($rating_sum/$count)/20;
			return round($width, 1);
		}
		else{
			return false;
		}
	}
	public function getReviewCount($vendorId){
		$review_data = Mage::getModel('csvendorreview/review')->getCollection()
							->addFieldToFilter('vendor_id',$vendorId)
							->addFieldToFilter('status',1);
		return count($review_data->getData());
	}
}
?>