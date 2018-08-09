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
class Ced_Mobiconnectreview_Model_Review extends Mage_Core_Model_Abstract {
  public function reviewList($data) {
    $offset = $data ['offset'];
    
    $limit = $data ['limit'];
    $curr_page = 1;
    
    if ($offset != 'null' && $offset != '') {
      
      $curr_page = $offset;
    }
    $offset = ($curr_page - 1) * $limit;
    
    $reviewsCollection = Mage::getModel ( 'review/review' )->getCollection ()->addStoreFilter ( Mage::app ()->getStore ()->getId () )->addStatusFilter ( Mage_Review_Model_Review::STATUS_APPROVED )->addEntityFilter ( 'product', $data ['product_id'] )->setDateOrder ();
    $reviewsCollection->getSelect ()->limit ( $limit, $offset );
    $reviewsCollection->addRateVotes ();
    $_items = $reviewsCollection->getItems ();
    // var_dump($reviewsCollection);die;
    
    $newCollection = Mage::getModel ( 'review/review' )->getCollection ()->addStoreFilter ( Mage::app ()->getStore ()->getId () )->addStatusFilter ( Mage_Review_Model_Review::STATUS_APPROVED )->addEntityFilter ( 'product', $data ['product_id'] )->setDateOrder ();
    $count = $newCollection->count ();
    $productReview = array ();
    
    foreach ( $_items as $_review ) {
      $votes = array ();
      
      $_votes = $_review->getRatingVotes ();
      
      foreach ( $_votes as $_vote ) {
        $code = strtolower ( $_vote->getRatingCode () );
        $votes [] = array (
            'rating-code' => $_vote->getRatingCode (),
            'rating-value-' . $code => $_vote->getPercent () / 20 
        )
        ;
      }
      
      $productReview [] = array (
          'vote' => $votes,
          'review-id' => $_review->getId (),
          'review-by' => $_review->getNickname (),
          'posted_on' => $_review->getCreatedAt (),
          'review-title' => $_review->getTitle (),
          'review-description' => nl2br ( $_review->getDetail () ) 
      )
      ;
    }
    $guestcanreview = Mage::getStoreConfig ( 'catalog/review/allow_guest', Mage::app ()->getStore ()->getId () );
    $avg = $this->getAvgProductReview ( $data ['product_id'] );
    $product = Mage::getModel ( 'catalog/product' )->load ( $data ['product_id'] );
    if (count ( $productReview ) > 0) {
      $data = array (
          'data' => array (
              'reviewed-product' => $product->getName (),
              'productreview' => $productReview,
              'overall-review' => $avg,
              'status' => 'success',
              'review-count' => $count,
              'guest-can-review' => $guestcanreview 
          )
           
      );
      return $data;
    } else {
      $data = array (
          'data' => array (
              'reviewed-product' => $product->getName (),
              'message' => 'Be The First One To Review',
              'guest-can-review' => $guestcanreview,
              'status' => 'no_review' 
          )
           
      );
      return $data;
    }
  }
  public function ratingOption() {
    $ratingCollection = Mage::getModel('rating/rating')->getResourceCollection()->addEntityFilter('product')->setPositionOrder()->addRatingPerStoreName(Mage::app()->getStore()->getId())->setStoreFilter(Mage::app()->getStore()->getId())->load()->addOptionToItems();
    // return $ratingCollection;
    if ($ratingCollection && $ratingCollection->getSize ()) {
      foreach ( $ratingCollection as $_rating ) {
        // echo $_rating->getRatingCode().'<hr>';
        $options = array();
        foreach ( $_rating->getOptions () as $_option ) {
          $options [] = array(
              'value' => $_option->getId () 
          );
        }
        $ratings [] = array(
            'rating-id' => $_rating->getRatingId (),
            'rating-code' => $_rating->getRatingCode (),
            'option' => $options 
        );
      }
      
      $data = array(
          'data' => array(
              'rating-option' => $ratings,
              'status' => 'success' 
          ) 
      );
      return $data;
    } else {
      $data = array(
          'data' => array(
              'message' => 'No rating option available available',
              'status' => 'success' 
          ) 
      );
      return $data;
    }
  }
  /**
   *
   * @param  Review Data
   * @return array
   */
  public function addReview($details) 
  {

    $data = $details['data'];
    $rating = $details['data']['ratings'];

    $review = Mage::getModel('review/review')->setData($data);
    $validate = $review->validate();
    if ($validate === true) {

      try {
        $review->setEntityId($review->getEntityIdByCode(Mage_Review_Model_Review::ENTITY_PRODUCT_CODE))->setEntityPkValue($details['prod_id'])->setStatusId(Mage_Review_Model_Review::STATUS_PENDING)->setCustomerId($details['customer_id'])->setStoreId (Mage::app()->getStore()->getId())->setStores(array(
            Mage::app()->getStore()->getId()))->save();
        
        foreach ( $rating as $ratingId => $optionId ) {
          Mage::getModel('rating/rating')->setRatingId($ratingId)->setReviewId($review->getId())->setCustomerId($details['customer_id'])->addOptionVote($optionId, $details['prod_id']);
        }
        
        $review->aggregate();
        $data = array(
            'data' => array(
                'message' => 'Your review has been accepted for moderation.',
                'status' => 'success' 
            ) 
        );
        return $data;
      }catch(Exception $e){
        //Mage::log($e->getMessage(),'1','mobiconnectapp.log',TRUE);
        $data = array(
            'data' => array(
                'message' => 'Unable to post the review.',
                'status' => 'exception' 
            ) 
        );
        return $data;
      }
    }else {
      $message = array();
      if (is_array($validate)) {
        foreach ($validate as $errorMessage) {
          $message[]=$errorMessage;
        }
        
        $data = array(
            'data' => array(
                'message' => implode(',',$message),
                'status' => 'exception' 
            ) 
        );
        return $data;
      } else { 
        $data = array(
            'data' => array(
                'message' => 'Unable to post the review.',
                'status' => 'exception' 
            ) 
        );
        return $data;
      }
    }
  }
  
  /**
   *
   * @param  $productId         
   * @return int
   */
    public function getAvgProductReview($productId) 
    {
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
            if(count($ratings)>0){
                $avg = array_sum ( $ratings ) / count ( $ratings );
                $avg =  $avg ;
            }
            
        }
        return $avg;
    }
}
?>
