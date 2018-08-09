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
class Ced_Mobiconnect_Adminhtml_CedpopupController extends Mage_Adminhtml_Controller_Action
{
	public function cedpopAction() {
		
		if (!Mage::getSingleton('admin/session')->isLoggedIn()) {
			$this->_redirect('*/index/login');
			return;
		}
		$this->loadLayout(array('c_e_d_c_o_m_m_e_r_c_e_2'));
		$this->renderLayout();
	}
	
}