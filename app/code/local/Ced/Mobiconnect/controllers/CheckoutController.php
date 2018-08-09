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
  * @package   Ced_Mobiconnect
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobiconnect_CheckoutController extends Ced_Mobiconnect_Controller_Action {
	
    /**
    * Add to cart
    * @param Post Data Params() 
    **/
	function addtocartAction(){
        $data=$this->getRequest()->getParams();
        if(isset($data['super_attribute'])){
                $data['super_attribute']=json_decode($data['super_attribute'],true);
            }
        if(isset($data['bundle_option'])){
                $data['bundle_option']=json_decode($data['bundle_option'],true);
            }
        if(isset($data['bundle_option_qty'])){
                $data['bundle_option_qty']=json_decode($data['bundle_option_qty'],true);
            }        
        $valid=Mage::getModel('mobiconnect/checkoutmobi')->validate($data);
        if($valid)
        {
            $response=Mage::getModel('mobiconnect/checkoutmobi')->addtocart($data);
            //Mage::log($response,null,'addtos.log');
            $this->getResponse()->setBody($response);
        }
        else{
            $this->getResponse()->setBody(json_encode(array('error'=>'true','message'=>'Order Id not Found.')));
        }
	}
    /**
    * View to cart
    * @param Post Data Params() 
    **/
	function viewcartAction(){
		$data=$this->getRequest()->getParams();
        $valid=Mage::getModel('mobiconnect/checkoutmobi')->validate($data);
        if($valid)
        {
        	 $response=Mage::getModel('mobiconnect/checkoutmobi')->viewcart($data);
            $this->getResponse()->setBody($response);
        }
        else{
            $this->getResponse()->setBody(json_encode(array('error'=>'true','message'=>'Order Id not Found.')));
        }
	}
	/**
	* Delete item from cart 
    *@param item_id
	**/
	function deletecartAction()
	{ 
		$data=$this->getRequest()->getParams();
        $valid=Mage::getModel('mobiconnect/checkoutmobi')->validate($data);
        if($valid)
        {
            $response=Mage::getModel('mobiconnect/checkoutmobi')->deletecart($data);
            $this->getResponse()->setBody($response);
        }
        else{
            $this->getResponse()->setBody(json_encode(array('error'=>'true','message'=>'Order Id not Found.')));
        }
	}

     /**
     * Initialize coupon
     *
     * @return null
     */
    public function applycouponAction()
    {
        $data=$this->getRequest()->getParams();
        $valid=Mage::getModel('mobiconnect/checkoutmobi')->validate($data);
        if($valid)
        {die("vfk");
            $response=Mage::getModel('mobiconnect/checkoutmobi')->applycoupon($data);
            $this->getResponse()->setBody($response);
        }
        else{
            $this->getResponse()->setBody(json_encode(array('error'=>'true','message'=>'Order Id not Found.')));
        }
    }

    /**
    * For saving address at checkout 
    **/

    function savebillingshipingAction(){
        $data=$this->getRequest()->getParams();
        $valid=Mage::getModel('mobiconnect/checkoutmobi')->validate($data);
        if($valid)
        {
            $response=Mage::getModel('mobiconnect/checkoutmobi')->savebillingshiping($data);
            $this->getResponse()->setBody($response);
        }
        else{
            $this->getResponse()->setBody(json_encode(array('error'=>'true','message'=>'Order Id not Found.')));
        }
    }
    /**
    * save shipping and  payment
    **/
    public function saveshippingpayamentAction(){
        $data=$this->getRequest()->getParams();
        $valid=Mage::getModel('mobiconnect/checkoutmobi')->validate($data);
        if($valid)
        {
            $response=Mage::getModel('mobiconnect/checkoutmobi')->saveshippingpayament($data);
            $this->getResponse()->setBody($response);
        }
        else{
            $this->getResponse()->setBody(json_encode(array('error'=>'true','message'=>'Order Id not Found.')));
        }
    } 
    public function saveorderAction(){
        $data=$this->getRequest()->getParams();
        $valid=Mage::getModel('mobiconnect/checkoutmobi')->validate($data);
        if($valid)
        {
            $response=Mage::getModel('mobiconnect/checkoutmobi')->saveorder($data);
            $this->getResponse()->setBody($response);
        }
        else{
            $this->getResponse()->setBody(json_encode(array('error'=>'true','message'=>'Order Id not Found.')));
        }
    } 
    /**
    * get shipping and  payment
    **/
    public function getsaveshippingpayamentAction(){
        $data=$this->getRequest()->getParams();
        $valid=Mage::getModel('mobiconnect/checkoutmobi')->validate($data);
        if($valid)
        {
            $response=Mage::getModel('mobiconnect/checkoutmobi')->getsaveshippingpayament($data);
            $this->getResponse()->setBody($response);
        }
        else{
            $this->getResponse()->setBody(json_encode(array('success'=>'false','message'=>'No Method(s) Found.')));
        }
    } 
	
    function getorderdataAction(){
        $increment_id=$this->getRequest()->getParam('id');
        if($increment_id){
            $order = Mage::getModel('sales/order')->loadByIncrementId($increment_id);
            $this->getResponse()->setBody(json_encode($order->getData()));
        }else{
            $this->getResponse()->setBody(json_encode(array('error'=>"Order Id not Found.")));
        }
         
    }
    function getcartcountAction(){
      $data=$this->getRequest()->getParams();
        $valid=Mage::getModel('mobiconnect/checkoutmobi')->validate($data);
        if($valid)
        {
            $response=Mage::getModel('mobiconnect/checkoutmobi')->getcartcount($data);
            $this->getResponse()->setBody($response);
        }
        else{
            $this->getResponse()->setBody(json_encode(array('error'=>'true','message'=>'Order Id not Found.')));
        }
    }
    /**
     * Get country dropdown
     **/
    function getcountrydropdownAction(){
    	$response=Mage::getModel('mobiconnect/checkoutmobi')->getCountryDropdown();
    	$this->getResponse()->setBody(json_encode($response));
    }

    public function emptyshoppingcartAction()
    {
        $data = $this->getRequest()->getParams();
        $valid = Mage::getModel('mobiconnect/checkoutmobi')->validate($data);
        if($valid)
        {
            $response = Mage::getModel('mobiconnect/checkoutmobi')->emptyShoppingCart($data);
            $this->getResponse()->setBody($response);

        } else {
            $this->getResponse()->setBody(json_encode(array('error'=>'true','message'=>'Order Id not Found.')));
        }
    }

    /* for update qty */

    function updateqtyAction()
    {
        $data = $this->getRequest()->getParams();
        $valid = Mage::getModel('mobiconnect/checkoutmobi')->validate($data);
        if($valid)
        {
            $response = Mage::getModel('mobiconnect/checkoutmobi')->updateQty($data);
            $this->getResponse()->setBody($response);
        }
        else{
            $this->getResponse()->setBody(json_encode(array('error'=>'true','message'=>'Order Id not Found.')));
        }
    }
}


