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
  * @package     Ced_CsVendorReview
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
  
class Ced_CsVendorReview_Block_Vshops_List extends Ced_CsMarketplace_Block_Vshops_List
{
	
	/**
     * Retrieve Review & Rating Html
     *
     * @return string
     */
    public function getReviewsSummaryHtml($vendor)
    {
		if(Mage::getStoreConfig('ced_csmarketplace/vendorreview/activation')){
		
			$review_data = Mage::getModel('csvendorreview/review')->getCollection()
								->addFieldToFilter('vendor_id',$vendor->getId())
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
				$width = ceil($rating_sum/$count);
					return '<div class="rating-box">
							<div class="rating" style="width:'.$width.'%;"></div>
					</div>';
			}
			else{
				return '';
			}
		}
		else{
			return '';
		}
    }	     
}
