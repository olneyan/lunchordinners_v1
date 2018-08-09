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
class Ced_MobiconnectAdvCart_Helper_Data extends Mage_Core_Helper_Abstract {

	public function getSuggestionLimit(){
		$limit = Mage::getStoreConfig ( "mobiconnectadvcart/advancecart/resultnbmr" );
		return $limit;
	}
	public function isEnabled($moduleName){
		$storeid=Mage::app()->getStore()->getStoreId();
		if($moduleName=='Ced_Mobiconnectcheckout'){
			return Mage::getStoreConfig('mobiconnectcheckout/mobicheckout/activation',$storeid);
		}
		if($moduleName=='Ced_Mobiconnect'){
			return Mage::getStoreConfig('mobiconnect/general/enable',$storeid);
		}

		if($moduleName=='Ced_Mobiconnectdeals'){
			return Mage::getStoreConfig('mobiconnectdeals/mobideals/activation',$storeid);
		}

		if($moduleName=='Ced_MobiconnectAdvCart'){
			return Mage::getStoreConfig('mobiconnectadvcart/advancecart/activation',$storeid);
		}

		if($moduleName=='Ced_MobiconnectCms'){
			return Mage::getStoreConfig('mobiconnectcms/cms/activation',$storeid);
		}

		if($moduleName=='Ced_Mobiconnectstore'){
			return Mage::getStoreConfig('mobiconnectstore/mobistore/activation',$storeid);
		}

		if($moduleName=='Ced_Mobiconnectreview'){
			return Mage::getStoreConfig('mobiconnectreview/mobireview/activation',$storeid);
		}

		if($moduleName=='Ced_MobiconnectSocialLogin'){
			return Mage::getStoreConfig('mobiconnectsociallogin/sociallogin/activation',$storeid);
		}

		if($moduleName=='Ced_Mobinotification'){
			return Mage::getStoreConfig('mobinotification/mobinotify/activation',$storeid);
		}

		if($moduleName=='Ced_MobiconnectWishlist'){
			return Mage::getStoreConfig('mobiconnectwishlist/wishlist/activation',$storeid);
		}
	}
	
}
?>