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

class Ced_VReviewApi_IndexController extends Ced_VendorApi_Controller_Abstract
{
	public function getVendorReviewAction() {
	    $data = array (
	        'vendor_id' => $this->getRequest ()->getParam ( 'vendor_id' ),
	        'offset' => $this->getRequest ()->getParam ( 'page' ),
	        'limit' => '5' 
	    );
	    $review = Mage::getModel ( 'vreviewapi/review' )->reviewList($data);
	    $response=json_encode($review);
		$this->getResponse()->setBody($response);
	  }

	public function getRatingOptionAction(){
		$rationgOption = Mage::getModel ( 'vreviewapi/review' )->ratingOption ();
	    $response=json_encode($rationgOption);
		$this->getResponse()->setBody($response);
	}
	public function addReviewAction() {
		$details = $this->getRequest()->getParams();
		//Mage::log($details,null,'vendorreview.log');
		$addreview = Mage::getModel ( 'vreviewapi/review' )->saveReview ( $details );
		$response=json_encode($addreview);
		$this->getResponse()->setBody($response);
	}
}
