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
 * @package     Ced_AdvFlatRate
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ced_AdvFlatRate_Model_Carrier_Advancerate extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
    {

    	protected $_code = 'advflatrate';
    	protected $_isFixed = true;
    	protected $_default_condition_name = 'vendor_id';
    	
    	
    	/**
    	 * Collect and get rates
    	 *
    	 * @param Mage_Shipping_Model_Rate_Request $request
    	 */
    	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    	{	
    	if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active')) {
			return false;
		}
		
	   //print_r($this->getRequest()->getParams());
		
	   $referUrl = Mage::app()->getRequest()->getServer('HTTP_REFERER');
	  // $data = "extradata/";
           if ($indx = strpos($referUrl, "/extradata/")) {
               $uri = substr($referUrl, $indx + 1);

                if(!empty($uri))
                {
                     $uri = explode('/',$uri);

                 $uri1 = urldecode($uri[1]);

                 $split = explode("?",$uri1);

                 $uri1 = $split[0];

                 $uri1 = json_decode($uri1,true);

                 if(count($uri1))
                 {
                     if(Mage::getSingleton('core/session')->getCartParams())
                     {
                             Mage::getSingleton('core/session')->unsCartParams();
                     }
                     Mage::getSingleton('core/session')->setCartParams($uri1);
                 }
                }
           }
	 
		$params = Mage::getSingleton('core/session')->getCartParams();
	 
		//print_r($params); 
		//echo $referUrl; die("rfo");
    		$result = Mage::getModel('shipping/rate_result');
    		
    		$cart = Mage::getSingleton('checkout/session')->getQuote();

    		$vid =[];$price=0;
    		foreach ($cart->getAllVisibleItems() as $item) 
    		{
                //print_r($item->getProductId());
    			$price = $price + $item->getPrice()*$item->getQty();
            	$productId = $item->getProductId();
                $vproduct = Mage::getModel('csmarketplace/vproducts')->getCollection()->addFieldToFilter('product_id',$productId)->getFirstItem();
            	$vid[] = $vproduct->getVendorId();
            }
            $vid =array_unique($vid);
            

    		if ($vid[0]) {
    			
    			$vendor = Mage::getModel('csmarketplace/vendor')->load($vid[0])->toArray();
    			//print_r($vendor); die("dfkg");
    			$freeShipAmount = $vendor['min_freeship'];
    			//echo $freeShipAmount; die("hjk");
    			
    			$method = Mage::getModel('shipping/rate_result_method');
    			//$currentUrl = Mage::getSingleton('core/session')->getReferUrl();
    			//$pos = strpos($currentUrl, 'pickup');
    			
    				
  
                    if($params['order_for'] == 'delivery' && $params['time'] == 'now')
    				{
    					$method->setCarrier('advflatrate');
    					$method->setCarrierTitle($this->getConfigData('title'));
    				
    					$method->setMethod('advflatrate');
    					$method->setMethodTitle('('.$vendor['public_name'].')'.' delivery in: '.$vendor['delivery_time']);
    				
    					$method->setPrice($vendor['delivery_fee']);
    					$method->setCost($vendor['delivery_fee']);
    				}
    				
    				if($params['order_for'] == 'pickup' && $params['time'] == 'now')
    				{
    					$method->setCarrier('advflatrate');
    					$method->setCarrierTitle("Pickup Charges");
    				
    					$method->setMethod('advflatrate');
    					$method->setMethodTitle($vendor['public_name']);
    				
    					$method->setPrice(0);
    					$method->setCost(0);
    				}
    				if($params['order_for'] == 'pickup' && $params['time'] == 'later')
    				{
    					$method->setCarrier('advflatrate');
    					$method->setCarrierTitle("Pickup Charges");
    				
    					$method->setMethod('advflatrate');
    					$method->setMethodTitle('('.$vendor['public_name'].')'." Pickup Time -".$params['delivery_date']." at ".$params['delivery_time']);
    				
    					$method->setPrice(0);
    					$method->setCost(0);
    				}
    				if($params['order_for'] == 'delivery' && $params['time'] == 'later')
    				{
    					$method->setCarrier('advflatrate');
    					$method->setCarrierTitle($this->getConfigData('title'));
    				
    					$method->setMethod('advflatrate');
    					$method->setMethodTitle('('.$vendor['public_name'].')'." Delivery Time -".$params['delivery_date']." at ".$params['delivery_time']);
    				
    					$method->setPrice($vendor['delivery_fee']);
    					$method->setCost($vendor['delivery_fee']);
    				}
    			
    	        //print_r($method->getData());
                //die();   
    			$result->append($method);
    		} 
    	
    		return $result;
    	}
    	
    	
    	
    	
    	/**
    	 * Get allowed shipping methods
    	 *
    	 * @return array
    	 */
    	 public function getAllowedMethods()
    	{
    		return array('advflatrate'=>$this->getConfigData('name'));
    	}
    }