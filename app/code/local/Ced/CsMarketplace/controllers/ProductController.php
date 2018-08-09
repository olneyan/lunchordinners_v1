<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced;
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_CsMarketplace_ProductController extends Mage_Core_Controller_Front_Action {
	
 public function viewAction()
    {
    	$productId  = (int) $this->getRequest()->getParam('product');
    	Mage::helper('catalog/product')->initProduct($productId, $this);
		$this->loadLayout();
        $this->renderLayout();
    }
    
    public function removeAction()
    {
	   try{
	
		$cart = Mage::getSingleton('checkout/cart'); 
		$quoteItems = Mage::getSingleton('checkout/session')
		                  ->getQuote()
		                  ->getItemsCollection();
		 
		foreach( $quoteItems as $item ){
		    $cart->removeItem( $item->getId() );    
		}
		 $cart->save();
		 $id = $this->getRequest()->getParam('id');
		$product = Mage::getModel('catalog/product')->load($id);
		if($product->getHasOptions())
     	{
     		echo 'yes';
	    }
	    else{
	    	echo 'no';
	    } 
	    
	   }
    	catch(Exception $e)
    	{
    		echo $e->getMessage();
    	}
    }
}
