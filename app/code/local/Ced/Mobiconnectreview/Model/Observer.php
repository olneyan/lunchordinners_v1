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
  * @package   Ced_Mobiconnectreview
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobiconnectreview_Model_Observer {
	public function productReview($observer) {
		$enable = ( int ) Mage::getStoreConfig ( 'mobiconnectreview/mobireview/activation' );
		if ($enable) {
			$eventData = $data = $observer->getEvent ()->getProductInfo ();
			if (! ($eventData->getIsCategory ())) {
				$data = $eventData->getProductData ();
				$avg = $this->getProductReview ( $eventData->getProductId () );
				$data ['data'] ['review'] = array (
						$avg 
				);
				$eventData->setProductData ( $data );
			} else {
				$avg = $this->getProductReview ( $eventData->getProductId () );
				$eventData->setAvg ( $avg );
			}
		}
	}
	public function getProductReview($productId) {
		$reviews = Mage::getModel ( 'review/review' )->getResourceCollection ()->addStoreFilter ( Mage::app ()->getStore ()->getId () )->addEntityFilter ( 'product', $productId )->addStatusFilter ( Mage_Review_Model_Review::STATUS_APPROVED )->setDateOrder ()->addRateVotes ();
		/**
		 * Getting average of ratings/reviews
		 */
		$avg = 0;
		$ratings = array ();
		if (count ( $reviews ) > 0) {
			foreach ( $reviews->getItems () as $review ) {
				foreach ( $review->getRatingVotes () as $vote ) {
					$ratings [] = ($vote->getPercent ()) / 20;
				}
			}
			
			$avg = array_sum ( $ratings ) / count ( $ratings );
			$avg = ($avg);
		}
		
		return $avg;
	}
}
?>