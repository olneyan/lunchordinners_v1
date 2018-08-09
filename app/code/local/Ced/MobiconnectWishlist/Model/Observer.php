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
  * @package   Ced_MobiconnectWishlist
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_MobiconnectWishlist_Model_Observer {
	public function WishlistProduct($observer) {
		$enable = ( int ) Mage::getStoreConfig ( 'mobiconnectwishlist/wishlist/activation' );
		if ($enable) {
			$eventData =$observer->getEvent ()->getProductInfo ();
			$data = $eventData->getProductData ();
			$productId = $eventData->getProductId ();
			$customerid = $eventData->getCustomerId ();
			// $data['data']['review']=array($avg);
			//
			/* Check product is in wishlist or not */
			if ($customerid != 'NULL' && $customerid != '') {
				
				$wishlist = Mage::getModel ( 'wishlist/wishlist' )->loadByCustomer ( $customerid ); /* Need to change here for session */
				$wishlistId = $wishlist->getData ( 'wishlist_id' );
				
				// $wishListItemCollection = $wishlist->getItemCollection();
				$item = Mage::getModel ( 'wishlist/item' )->getCollection ()->addFieldToFilter ( 'wishlist_id', $wishlistId )->addFieldToFilter ( 'product_id', $productId );
				
				$item = $item->getData ();
				
				if (count ( $item ) > 0) {
					// $arrProductIds = array();
					
					/*
					 * foreach ($wishListItemCollection as $item) { $arrProductIds[] = $item->getProductId(); }
					 */
					if ($eventData->getIsCategory ()) {
						$eventData->setWishlist('1');
					}
					$data ['data'] ['Inwishlist'] = array (
							'IN' 
					);
					$data ['data'] ['item_id'] = array (
							$item ['0'] ['wishlist_item_id'] 
					);
				} else {
					if ($eventData->getIsCategory ()) {
						$eventData->setWishlist('0');
					}
					$data ['data'] ['Inwishlist'] = array (
							'OUT' 
					);
				}
			} else {
				if ($eventData->getIsCategory ()) {
						$eventData->setWishlist('0');
					}
				$data ['data'] ['Inwishlist'] = array (
						'OUT' 
				);
			}
			/* End */
			$eventData->setProductData ( $data );
		}
	}
}
?>