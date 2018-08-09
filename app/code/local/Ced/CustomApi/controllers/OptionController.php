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
  * @package   Ced_CustomApi
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_CustomApi_OptionController extends Ced_Mobiconnect_Controller_Action {
	
	public function viewAction() {
		
		$params = $this->getRequest()->getParams();
		$productId = $params['product_id'];
		echo $productId; die("kfj");
	}
		
	}
   
		