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

class Ced_VReviewApi_Model_Review extends Mage_Core_Model_Abstract {
  public function ratingOption() {
      $ratingCollection =  Mage::getModel('csvendorreview/rating')->getCollection();
      $rating_opt = Mage::getModel('csvendorreview/options')->getRatingOption();
      if ($ratingCollection && $ratingCollection->getSize ()) {
        foreach ( $ratingCollection as $_rating ) {
          // echo $_rating->getRatingCode().'<hr>';
          $options = array ();
          foreach ( $rating_opt as $key => $_option ) {
            if($key > 0){ 
              $options [] = array (
                  'value' => $key,
                  'label' => $_option
              );
            }
          }
          $ratings [] = array (
              'rating_name' => $_rating->getRatingCode(),
              'rating_code' => $_rating->getRatingCode (),
              'option' => $options ,
              'label'=>$_rating->getRatingLabel()
          );
        }
        
        $data = array (
            'data' => array (
                'rating-option' => $ratings,
                'status' => 'success' 
            ) 
        );
        return $data;
      } else {
        $data = array (
            'data' => array (
                'message' => 'No rating option available',
                'status' => 'success' 
            ) 
        );
      }
    }
    public function saveReview($details){
      if(!empty($details)){
        $vendor = Mage::getModel('csmarketplace/vendor')->load($details['vendor_id']);
        $details['vendor_name'] = $vendor->getName();
        if(Mage::getStoreConfig('ced_csmarketplace/vendorreview/vendorapprval')){
          $msg = 'Your review has been submited for approval';
          $details['status'] = 0;
        }
        else{
          $msg = 'Your review has been submitted successfully';
          $details['status'] = 1;
        }
        try{
          $details['created_at'] = date("Y-m-d H:i:s");
          $review = Mage::getModel('csvendorreview/review');
          $review->addData($details);
          $review->save();
          $data = array (
            'data' => array (
                      'success' => true ,
                      'message'=> $msg  
            ) 
          );
          return $data;
        }
        catch(Exception $e){
          $data = array (
            'data' => array (
                      'success' => true ,
                      'message'=> $e->getMessage(),  
            ) 
          );
          return $data;
        }
      }
      else{
        $data = array (
            'data' => array (
                      'success' => false ,
                      'message'=> 'Data is Empty',  
            ) 
          );
          return $data;
      }
    }
  public function reviewList($data) {
    $offset = $data ['offset'];
    $limit = $data ['limit'];
    $curr_page = 1;
    
    if ($offset != 'null' && $offset != '') {
      
      $curr_page = $offset;
    }
    $offset = ($curr_page - 1) * $limit;
    
    $reviewsCollection = Mage::getModel('csvendorreview/review')->getCollection()
          ->addFieldToFilter('vendor_id', $data ['vendor_id'])
          ->addFieldToFilter('status',1)
          ->setOrder('created_at', 'desc');
    $reviewsCollection->getSelect ()->limit ( $limit, $offset );
   // $reviewsCollection->addRateVotes ();
    $_items = $reviewsCollection->getItems ();

    $newCollection = Mage::getModel('csvendorreview/review')->getCollection()
          ->addFieldToFilter('vendor_id',$data ['vendor_id'])
          ->addFieldToFilter('status',1)
          ->setOrder('created_at', 'desc');
    $count = $newCollection->count ();
    $productReview = array ();
    
    foreach ( $_items as $_review ) {
      $proReview='';
      $votes = array ();
      $_votes = Mage::getModel('csvendorreview/rating')->getCollection();
      
      foreach ( $_votes as $_vote ) {
        $code = strtolower ( $_vote->getRatingCode () );
        $proReview += $_review[$_vote->getRatingCode()]/20;
        $votes [] = array (
            'rating-label'=>$_vote->getRatingLabel(),
            'rating-code' => $_vote->getRatingCode (),
            'rating-value-' . $code => $_review[$_vote->getRatingCode()],
        )
        ;
      }
      
      $productReview [] = array (
          //'vote' => $votes,
          'v_review'=> round($proReview/count($votes),1),
          'review-id' => $_review->getId (),
          'review-by' => $_review->getCustomerName (),
          'posted_on' => $this->formatDate($_review->getCreatedAt(), 'long'),
          'review-title' => $_review->getSubject(),
          'review-description' =>$_review->getReview()
      )
      ;
    }
    
    if (count ( $productReview ) > 0) {
      $data = array (
          'data' => array (
              'productreview' => $productReview,
              'success' => true,
              'review-count' => $count 
          )
           
      );
      return $data;
    } else {
      $data = array (
          'data' => array (
              'message' => 'Be The First One To Review',
              'status' => 'no_review' ,
              'success'=>false
          )
           
      );
      return $data;
    }
  }
  public function formatDate($date = null, $format = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, $showTime = false)
    { 
        return Mage::helper('core')->formatDate($date, $format, $showTime);
    }
}