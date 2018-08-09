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
  * @package     VendorApi
  * @author      CedCommerce Core Team <connect@cedcommerce.com>
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://cedcommerce.com/license-agreement.txt
  */

class Ced_CsVattributeApi_ProfileController extends Ced_VendorApi_Controller_Abstract
{
  /* vendor profile edit action*/
  public function editAction()
  { 
  	
    $vendorId = $this->getRequest()->getParam('vendor_id');
    $validate=array(
      'vendor_id'=>$this->getRequest()->getParam('vendor_id'),
      'hash'=>$this->getRequest()->getParam('hashkey')
      );
    //$validateRequest=$this->validate($validate);
    $vendorInfo = Mage::getModel('csvattributeapi/vendor_api')->edit($vendorId);
    $response=json_encode($vendorInfo);
    $this->getResponse()->setBody($response);
  }
}