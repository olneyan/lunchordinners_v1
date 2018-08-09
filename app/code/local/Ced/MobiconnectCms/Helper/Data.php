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
class Ced_MobiconnectCms_Helper_Data extends Mage_Core_Helper_Abstract {
	public function showCmsContent() {
		$id = Mage::getStoreConfig ( "mobiconnect/general/cmscontent" );
		return $id;
	}
	public function getCmsTitle($id){

		$cmsblock = Mage::getModel ( 'mobiconnectcms/cmsblock' )->getCollection ()->addFieldToFilter ( 'status', '1' )->addFieldToFilter ('id',$id )->getData ();
		if($cmsblock){
		foreach ($cmsblock as $key => $value) {
			$title=$value['title'];
			break;
		}
		return $title;
	}
	}
}
?>
