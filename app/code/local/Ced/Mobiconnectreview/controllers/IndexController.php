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
// require_once Mage::getModuleDir('controllers', 'Ced_Mobiconnect').DS.'CategoryController.php';
class Ced_Mobiconnectreview_IndexController extends Ced_Mobiconnectreview_Controller_Action {
  protected $_reviewsCollection;
  /**
    * Get Product Review
    * @param Data Params() 
    **/
  public function getProductReviewAction() {
    $data = array (
        'product_id' => $this->getRequest()->getParam('prodID'),
        'offset' => $this->getRequest()->getParam('page'),
        'limit' => '5' 
    );
    if ($data['product_id'] == '' || $data['product_id'] == 'NULL') {
      echo 'No Product Id';
      return;
    }
    $review = Mage::getModel ( 'mobiconnectreview/review' )->reviewList ( $data );
    $this->printJsonData ( $review );
  }
  /**
    * Add Product Review
    * @param Data Params() 
    **/
  public function addReviewAction() {
    $detail = $this->getRequest()->getParam('detail');
    $title = $this->getRequest()->getParam('title');
    $nickname = $this->getRequest()->getParam('nickname');
    $postedratings = $this->getRequest()->getParam('ratings');
    $ratings = json_decode($postedratings,true);
    /*foreach ( $customratings as $key => $value ) {
      $id = explode('-', $value);
      $ratings[$id['0']] = $id['1'];
    }*/
    $reviewdetails = array(
        'detail' => $detail,
        'title' => $title,
        'nickname' => $nickname,
        'ratings' => $ratings 
    );
    $details = array(
        'data' => $reviewdetails,
        'prod_id' => $this->getRequest()->getParam('prodID'),
        'customer_id'=>$this->getRequest()->getParam('customer_id',null)
    );
   
    $addreview = Mage::getModel('mobiconnectreview/review')->addReview($details);
    $this->printJsonData($addreview);
  }
  /**
    * Get Product Rating Options
    * @param Data Params() 
    **/
  public function getRatingOptionAction() {
    $rationgOption = Mage::getModel ( 'mobiconnectreview/review' )->ratingOption ();
    $this->printJsonData ( $rationgOption );
  }
}
?>