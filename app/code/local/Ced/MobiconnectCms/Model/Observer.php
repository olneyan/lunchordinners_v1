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
  * @package   Ced_MobiconnectCms
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_MobiconnectCms_Model_Observer {
	public function cmsPage($observer) {
		$enable = ( int ) Mage::getStoreConfig ( 'mobiconnectcms/cms/activation' );
		$eventData = $data = $observer->getEvent ()->getProductInfo ();
		if ($enable) {
			
			$data = $eventData->getProductData ();
			$cmsContent = Mage::helper ( 'mobiconnectcms' )->showCmsContent ();
			if ($cmsContent != '') {
				$cmsTitle=Mage::helper('mobiconnectcms')->getCmsTitle($cmsContent);
				$data ['data'] ['cms-title'] = $cmsTitle;
				$data['data']['cms-content']=Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_WEB, true ) . 'mobiconnectcms/cms/cmsContent/id/' . $cmsContent;
				$eventData->setProductData ( $data );
			}
			
		}
	}
}
?>