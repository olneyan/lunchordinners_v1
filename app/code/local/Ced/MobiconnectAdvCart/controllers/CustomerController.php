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
/**
* Override Controller of Mobiconnect for advance product system 
**/

class Ced_MobiconnectAdvCart_CustomerController extends Ced_Mobiconnect_Controller_Action {

	public function downloadAction(){
    	$data = array(
    			'customer'=>$this->getRequest()->getParam('customer_id'),
    			'hash'=>$this->getRequest()->getParam('hashkey'),
    	);
    	$validateRequest=$this->validate($data);
    	$order = Mage::getModel ('mobiconnectadvcart/customer_order')->getDownloadLink($data);
    	$this->printJsonData ( $order );
    
    }

}