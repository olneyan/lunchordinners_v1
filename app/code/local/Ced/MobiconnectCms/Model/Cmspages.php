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
class Ced_MobiconnectCms_Model_CmsPages extends Mage_Catalog_Model_Config {
	public function getCmsPages($storeid) {
		$store_id = array (
				'0' => '0' 
		);
		$store_id [] = $storeid;
		// var_dump($store_id);die;
		foreach ( $store_id as $value ) {
			$cmsblock = Mage::getModel ( 'mobiconnectcms/cmsblock' )->getCollection ()->addFieldToFilter ( 'status', '1' )->addFieldToFilter ( 'store', array (
					'finset' => $value 
			) )->getData ();
			foreach ( $cmsblock as $key => $value ) {
				
				$blockinfo [] = array (
						'page-title' => $value ['title'],
						'page-id' => $value ['id'],
						'page-url'=>Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_WEB, true ) . 'mobiconnectcms/cms/cmsContent/id/' . $value ['id'],
				);
			}
		}
		if (count ( $blockinfo ) > 0) {
			$data = array (
					'data' => array (
							'cmsblocks' => $blockinfo,
							'status' => 'success' 
					) 
			);
		} else {
			$data = array (
					'data' => array (
							'message' => 'No CMS Block Created Yet',
							'status' => 'false' 
					) 
			);
		}
		return $data;
	}
}
	