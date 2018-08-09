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
class Ced_Mobiconnect_Model_Banner extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct ();
		$this->_init ( 'mobiconnect/banner' );
	}
	static public function getWebsite() {
		$options = array ();
		$options [] = array (
				'value' => 0,
				'label' => Mage::helper ( 'core' )->__ ( 'All' ) 
		);
		$collection = Mage::helper ( 'mobiconnect' )->getWebsites ();
		foreach ( $collection as $item ) {
			$options [] = array (
					'value' => $item->getId (),
					'label' => $item->getName () 
			);
		}
		return $options;
	}
	public function getStatus() {
		$status = array (
				'Enabled' => 'yes',
				'Disabled' => 'no' 
		);
		return $status;
	}
	public function getLayoutType(){
		$options=array(
			'default'=>'Default'
			);
		$storeId = Mage::app()->getStore()->getStoreId();
		$enable=Mage::getStoreConfig('mobiconnectadvcart/advancecart/activation',$storeId);
		if($enable){
			$options=array(
			'default'=>'Default',
			'simple'=>'Simple',
			'ultimate'=>'Ultimate',
			'lite'=>'Lite',
			'core'=>'core'
			);
		}
		return $options;
	}
}
?>
