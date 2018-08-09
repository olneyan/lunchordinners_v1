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
class Ced_MobiconnectWishlist_Model_Customer_Wishlist extends Mage_Core_Model_Abstract {
	
	public function addToWishlist($data){
		//if (Mage::getSingleton('customer/session')->isLoggedIn()) {
		$product = Mage::getModel('catalog/product')->load($data['prodID']);
		$wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($data['customer'],true);/*Need to change here for session*/
		
		if (!$product->getId() || !$product->isVisibleInCatalog()) {
			$data = array (
					'data' => array (
							'message' => 'Cannot Specify Product',
							'status' => 'false'
					)
			);
			return $data;
				
		}
			
		try {
			//$requestParams = $this->getRequest()->getParams();
				
			$buyRequest = new Varien_Object($requestParams=array());
			
			$result = $wishlist->addNewItem($product, $buyRequest);
			
			/*if (is_string($result)) {
			 Mage::throwException($result);
			}*/
			$item_id=$result->getWishlistItemId();
			$wishlist->save();
				
			Mage::dispatchEvent(
			'wishlist_add_product',
				array(
					'wishlist' => $wishlist,
					'product' => $product,
					'item' => $result
				)
			);
				
				
				
			Mage::helper('wishlist')->calculate();
				
			$data = array (
			
			 		'message' => 'The product has been added to the wislist',
			 		'wishlist-item-id'=>$item_id,
			 		'status' => 'true'
			
			);
			return $data;
				
		} catch (Mage_Core_Exception $e) {
		
			$data = array (
					
							'message' =>  $e->getMessage(),
							'status' => 'false'
					
			);
			return $data;
		}
		catch (Exception $e) {
		
			$data = array (
					
							'message' =>  $e->getMessage(),
							'status' => 'false'
					
			);
			return $data;
		}
			
		//}
		/*else{
		$data = array (
				'data' => array (
						'wishlist' => 'Please Login First',
						'status' => 'error'
				)
		);
		return $data;
		}*/
		}
	public function customerWishlist($data){

		$baseCurrency = Mage::app ()->getStore ()->getBaseCurrencyCode ();
		$currency_symbol = Mage::app ()->getLocale ()->currency ( $baseCurrency )->getSymbol ();
		//$product = Mage::getModel('catalog/product')->load($data['prodID']);
		$wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($data['customer'], true);/*Need to change here for session*/
		$wishListItemCollection = $wishlist->getItemCollection();
		
		if (count($wishListItemCollection)) {
			$arrProductIds = array();
		
			foreach ($wishListItemCollection as $item) {
				
				$arrProductId = $item->getProductId();
				$product=Mage::getModel('catalog/product')->load($item->getProductId());
				$special_price=($product->getSpecialPrice ()!=null?$currency_symbol .$product->getSpecialPrice ():'no_special');
				$wishlistProduct[] = array (
						'product_id' =>$arrProductId,
						'product_name' => $product->getName (),
						//'type' => $product->getTypeId (),
						'qty'=>$item->getQty(),
						'wishlist_id'=>$item->getWishlistId(),
						'wishlist_item_id'=>$item->getWishlistItemId(),
						'special_price' => $special_price,
						'regular_price' => $currency_symbol . Mage::app ()->getStore ()->convertPrice ( $product->getPrice (), false ),
						// 'stock_status' => $product->isSaleable(),
						'product_image' => Mage::helper ( 'catalog/image' )->init ( $product, 'small_image' )->constrainOnly ( TRUE )->keepAspectRatio ( TRUE )->keepFrame ( FALSE )->resize ( 600, 600 )->__toString ()
				)
				;
			}
			$data = array (
					'data' => array (
							'products' => $wishlistProduct,
							'item_count'=>count($wishlistProduct),
							'status' => 'true'
					)
			);

			return $data;
		}
		else{
			$data = array (
					'data' => array (
							'wishlist' => 'NOWISHLIST',
							'status' => 'false'
					)
			);
			return $data;
		}
		
	}
	public function removeItem($data)
	{
		$wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($data['customer'], true);
		$id = $data['itemId'];
		$item = Mage::getModel('wishlist/item')->load($id);
		
		if ($item->getWishlistId() == $wishlist->getId()) {
			try {
				$item->delete();
				$wishlist->save();
				Mage::helper('wishlist')->calculate();
				$data = array (
						
								'message' => 'The item was successfully removed',
								'status' => 'true'
						
				);

				return $data;
			} catch (Mage_Core_Exception $e) {
				$data = array (
						
								'message' => $e->getMessage(),
								'status' => 'false'
						
				);
				return $data;
			} catch(Exception $e) {
				$data = array (
						
								'message' => $e->getMessage(),
								'status' => 'false'
						
				);
				return $data;
			}
		} else {
			$data = array (
					
							'message' => 'Specified item does not exist in wishlist.',
							'status' => 'false'
					
			);
			return $data;
		}
		
	
	}
	public function clearWishlist($data){

		$wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($data['customer'], true);
		$items = $wishlist->getItemCollection();
		
		try {
			foreach ($items as $item) {
				$item->delete();
			}
			$wishlist->save();
			Mage::helper('wishlist')->calculate();
			$data = array (
					
							'message' => 'Your wishlist was cleared successfully',
							'status' => 'true'
					
			);
			return $data;
		} catch (Mage_Core_Exception $e) {
			
			$data = array (
					
							'message' => $e->getMessage(),
							'status' => 'false'
					
			);
			return $data;
		} catch(Exception $e) {
			$data = array (
					
							'message' => $e->getMessage(),
							'status' => 'false'
					
			);
			return $data;
		}
		
		
		
	}
	public function updateWishlist($data){
		
		$wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($data['customer']);
		
		$items = $wishlist->getItemCollection();
		
		try {
			foreach ($items as $item) {
				if($item->getId()==$data['item_id'])
				 $item->setQty($data['item_qty']);
			}
			Mage::helper('wishlist')->calculate();
			$wishlist->save();

			$data = array (
					
							'message' => 'Wishlist Successfully Updated',
							'status' => 'true'
					
			);
			
			//Mage::helper('wishlist')->calculate();
			return $data;
		} catch (Mage_Core_Exception $e) {
			
			$data = array (
					
							'message' => $e->getMessage(),
							'status' => 'false'
					
			);
			return $data;
		} catch(Exception $e) {
			$data = array (
					
							'message' => $e->getMessage(),
							'status' => 'false'
					
			);
			return $data;
		}
		
		
		
	}
	
}
	
