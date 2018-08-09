<?php

require_once 'Mage/Checkout/controllers/CartController.php';

class Ced_CsMarketplace_CartController extends Mage_Checkout_CartController {

    public function laterorderAction() {
        $params = $this->getRequest()->getParams();
        $indexs = $params['index'];
        $vandorid = $params['vendor_id'];


        $vendor = Mage::getModel('csmarketplace/vendor')->load($vandorid)->toArray();
        
         $days = Mage::getSingleton('core/date')->date('w');

         if($days == 1){
            $index['0'] = 1;
            $index['1'] = 2;
            $index['2'] = 3;
            $index['3'] = 4;
            $index['4'] = 5;
            $index['5'] = 6;
            $index['6'] = 0;
         }elseif ($days == 2) {
            $index['0'] = 2;
            $index['1'] = 3;
            $index['2'] = 4;
            $index['3'] = 5;
            $index['4'] = 6;
            $index['5'] = 0;
            $index['6'] = 1;
         }
         elseif ($days == 3) {
            $index['0'] = 3;
            $index['1'] = 4;
            $index['2'] = 5;
            $index['3'] = 6;
            $index['4'] = 0;
            $index['5'] = 1;
            $index['6'] = 2;
         }
         elseif ($days == 4) {
            $index['0'] = 4;
            $index['1'] = 5;
            $index['2'] = 6;
            $index['3'] = 0;
            $index['4'] = 1;
            $index['5'] = 2;
            $index['6'] = 3;
         }
         elseif ($days == 5) {
            $index['0'] = 5;
            $index['1'] = 6;
            $index['2'] = 0;
            $index['3'] = 1;
            $index['4'] = 2;
            $index['5'] = 3;
            $index['6'] = 4;
         }
         elseif ($days == 6) {
            $index['0'] = 6;
            $index['1'] = 0;
            $index['2'] = 1;
            $index['3'] = 2;
            $index['4'] = 3;
            $index['5'] = 4;
            $index['6'] = 5;
         }
         elseif ($days == 0) {
            $index['0'] = 0;
            $index['1'] = 1;
            $index['2'] = 2;
            $index['3'] = 3;
            $index['4'] = 4;
            $index['5'] = 5;
            $index['6'] = 6;
         }


         $indexvalue =  $indexs ;
         $day = $index[$indexvalue];
         //if($index)

         $offset=4*60*60; //converting 4 hours to seconds.
         $dateFormat="H:i"; //set the date format
         $timeNdate=gmdate($dateFormat, time()-$offset);
         //echo "<br/>"; //get GMT date - 4
         //echo strtotime($timeNdate);
         //echo $time = date('h:i', strtotime("now"));
        //print_r($vendor);
         if ($day == 0) {
            $openTime = $vendor ['sun_open_time'];
            $openTime = explode(',', $openTime);
            $openTime = implode(':', $openTime);
           // $openTime = date('h:i', strtotime($openTime));
            $closeTime = $vendor ['sun_close_time'];
            $closeTime = explode(',', $closeTime);
            $closeTime = implode(':', $closeTime);
            //$closeTime = date("H:i", strtotime($closeTime));
        } elseif ($day == 1) {
            $openTime = $vendor ['mon_open_time'];
            $openTime = explode(',', $openTime);
            $openTime = implode(':', $openTime);
           // $openTime = date("H:i", strtotime($openTime));

            $closeTime = $vendor ['mon_close_time'];
            $closeTime = explode(',', $closeTime);
            $closeTime = implode(':', $closeTime);
           // $closeTime = date("H:i", strtotime($closeTime));
        } elseif ($day == 2) {

            $openTime = $vendor ['tue_open_time'];
            $openTime = explode(',', $openTime);
            $openTime = implode(':', $openTime);
            //$openTime = date("H:i", strtotime($openTime));

            $closeTime = $vendor ['tue_close_time'];
            $closeTime = explode(',', $closeTime);
            $closeTime = implode(':', $closeTime);
            //$closeTime = date("H:i", strtotime($closeTime));
        } elseif ($day == 3) {
            $openTime = $vendor ['wed_open_time'];
            $openTime = explode(',', $openTime);
            $openTime = implode(':', $openTime);
            //$openTime = date("H:i", strtotime($openTime));

            $closeTime = $vendor ['wed_close_time'];
            $closeTime = explode(',', $closeTime);
            $closeTime = implode(':', $closeTime);
            //$closeTime = date("H:i", strtotime($closeTime));
        } elseif ($day == 4) {
            $openTime = $vendor ['thu_open_time'];
            $openTime = explode(',', $openTime);
            $openTime = implode(':', $openTime);
            //$openTime = date("H:i", strtotime($openTime));

            $closeTime = $vendor ['thu_close_time'];
            $closeTime = explode(',', $closeTime);
            $closeTime = implode(':', $closeTime);
            //$closeTime = date("H:i", strtotime($closeTime));
        } elseif ($day == 5) {
            $openTime = $vendor ['fri_open_time'];
            $openTime = explode(',', $openTime);
            $openTime = implode(':', $openTime);
            //$openTime = date("H:i", strtotime($openTime));

            $closeTime = $vendor ['fri_close_time'];
            $closeTime = explode(',', $closeTime);
            $closeTime = implode(':', $closeTime);
            //$closeTime = date("H:i", strtotime($closeTime));
        } elseif ($day == 6) {
            $openTime = $vendor ['sat_open_time'];
            $openTime = explode(',', $openTime);
            $openTime = implode(':', $openTime);
            //$openTime = date("H:i", strtotime($openTime));

            $closeTime = $vendor ['sat_close_time'];
            $closeTime = explode(',', $closeTime);
            $closeTime = implode(':', $closeTime);
            //$closeTime = date("H:i", strtotime($closeTime));
        }
        $timingArray = array(
            '00:00' => '00:00',
            '00:15' => '00:15',
            '00:30' => '00:30',
            '00:45' => '00:45',
            '01:00' => '01:00',
            '01:15' => '01:15',
            '01:30' => '01:30',
            '01:45' => '01:45',
            '02:00' => '02:00',
            '02:15' => '02:15',
            '02:30' => '02:30',
            '02:45' => '02:45',
            '03:00' => '03:00',
            '03:15' => '03:15',
            '03:30' => '03:30',
            '03:45' => '03:45',
            '04:00' => '04:00',
            '04:15' => '04:15',
            '04:30' => '04:30',
            '04:45' => '04:45',
            '05:00' => '05:00',
            '05:15' => '05:15',
            '05:30' => '05:30',
            '05:45' => '05:45',
            '06:00' => '06:00',
            '06:15' => '06:15',
            '06:30' => '06:30',
            '06:45' => '06:45',
            '07:00' => '07:00',
            '07:15' => '07:15',
            '07:30' => '07:30',
            '07:45' => '07:45',
            '08:00' => '08:00',
            '08:15' => '08:15',
            '08:30' => '08:30',
            '08:45' => '08:45',
            '09:00' => '09:00',
            '09:15' => '09:15',
            '09:30' => '09:30',
            '09:45' => '09:45',
            '10:00' => '10:00',
            '10:15' => '10:15',
            '10:30' => '10:30',
            '10:45' => '10:45',
            '11:00' => '11:00',
            '11:15' => '11:15',
            '11:30' => '11:30',
            '11:45' => '11:45',
            '12:00' => '12:00',
            '12:15' => '12:15',
            '12:30' => '12:30',
            '12:45' => '12:45',
            '13:00' => '13:00',
            '13:15' => '13:15',
            '13:30' => '13:30',
            '13:45' => '13:45',
            '14:00' => '14:00',
            '14:15' => '14:15',
            '14:30' => '14:30',
            '14:45' => '14:45',
            '15:00' => '15:00',
            '15:15' => '15:15',
            '15:30' => '15:30',
            '15:45' => '15:45',
            '16:00' => '16:00',
            '16:15' => '16:15',
            '16:30' => '16:30',
            '16:45' => '16:45',
            '17:00' => '17:00',
            '17:15' => '17:15',
            '17:30' => '17:30',
            '17:45' => '17:45',
            '18:00' => '18:00',
            '18:15' => '18:15',
            '18:30' => '18:30',
            '18:45' => '18:45',
            '19:00' => '19:00',
            '19:15' => '19:15',
            '19:30' => '19:30',
            '19:45' => '19:45',
            '20:00' => '20:00',
            '20:15' => '20:15',
            '20:30' => '20:30',
            '20:45' => '20:45',
            '21:00' => '21:00',
            '21:15' => '21:15',
            '21:30' => '21:30',
            '21:45' => '21:45',
            '22:00' => '22:00',
            '22:15' => '22:15',
            '22:30' => '22:30',
            '22:45' => '22:45',
            '23:00' => '23:00',
            '23:15' => '23:15',
            '23:30' => '23:30',
            '23:45' => '23:45'
            );
        $options = "<select id='del_time' style='margin-top: 8px;'>";
        $openTime= strtotime($openTime);
        $closeTime = strtotime($closeTime);
        if($index == 0){
            foreach ($timingArray as $val) {
                $now = strtotime($val);

                if ($openTime > $closeTime) {
                    if ($now >= $openTime || $now < $closeTime) {

                    } else {
                        continue;
                    }
                } else if ($now >= $openTime && $now <= $closeTime) {

                } else {
                    continue;
                }
                if(strtotime($timeNdate) < strtotime($val)) {
                    $options .= "<option>". date("h:i a", strtotime($val)) ."</option>";
                }
                

            }
        }
        else {
            foreach ($timingArray as $val) {
                $now = strtotime($val);

                if ($openTime > $closeTime) {
                    if ($now >= $openTime || $now < $closeTime) {

                    } else {
                        continue;
                    }
                } else if ($now >= $openTime && $now <= $closeTime) {

                } else {
                    continue;
                }
                $options .= "<option>". date("h:i a", strtotime($val)) ."</option>";

            }
        }
        //echo $options;
        echo $options . "<select id='del_time'>";

    }
    public function addAction() {

        $cart = $this->_getCart();
        $params = $this->getRequest()->getParams();

        if ($this->getRequest()->isXmlHttpRequest()) {

            $eligible = $this->eligible($params['product']);
            if ($eligible == "success") {
                $response = array();
                try {
                    if (isset($params['qty'])) {
                        $filter = new Zend_Filter_LocalizedToNormalized(
                            array('locale' => Mage::app()->getLocale()->getLocaleCode())
                            );
                        $params['qty'] = $filter->filter($params['qty']);
                    }

                    $product = $this->_initProduct();
                    $related = $this->getRequest()->getParam('related_product');

                    /**
                     * Check product availability
                     */
                    if (!$product) {
                        $response['status'] = 'ERROR';
                        $response['message'] = $this->__('Unable to find Product ID');
                    }

                    $cart->addProduct($product, $params);
                    if (!empty($related)) {
                        $cart->addProductsByIds(explode(',', $related));
                    }

                    $cart->save();

                    $this->_getSession()->setCartWasUpdated(true);

                    /**
                     * @todo remove wishlist observer processAddToCart
                     */
                    Mage::dispatchEvent('checkout_cart_add_product_complete', array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
                        );

                    if (!$cart->getQuote()->getHasError()) {
                        $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
                        $response['status'] = 'SUCCESS';
                        $response['message'] = $message;
                        //New Code Here
                        $this->loadLayout();
                        $toplink = $this->getLayout()->getBlock('top.links')->toHtml();
                        $sidebar_block = $this->getLayout()->getBlock('cart_sidebar');
                        Mage::register('referrer_url', $this->_getRefererUrl());
                        $sidebar = $sidebar_block->toHtml();
                        $response['toplink'] = $toplink;
                        $response['sidebar'] = $sidebar;
                    }
                } catch (Mage_Core_Exception $e) {
                    $msg = "";
                    if ($this->_getSession()->getUseNotice(true)) {
                        $msg = $e->getMessage();
                    } else {
                        $messages = array_unique(explode("\n", $e->getMessage()));
                        foreach ($messages as $message) {
                            $msg .= $message . '<br/>';
                        }
                    }

                    $response['status'] = 'ERROR';
                    $response['message'] = $msg;
                } catch (Exception $e) {
                    $response['status'] = 'ERROR';
                    $response['message'] = $this->__('Cannot add the item to shopping cart.');
                    Mage::logException($e);
                }
                $response["cart_sidebar"] = $this->getLayout()
                ->createBlock('checkout/cart_sidebar')
                ->setTemplate('csmarketplace/cart/minicart.phtml')
                ->addItemRender("default", "checkout/cart_item_renderer", "checkout/cart/minicart/default.phtml")
                ->addItemRender("simple", "checkout/cart_item_renderer", "checkout/cart/minicart/default.phtml")
                ->addItemRender("grouped", "checkout/cart_item_renderer_grouped", "checkout/cart/minicart/default.phtml")
                ->addItemRender("configurable", "checkout/cart_item_renderer_configurable", "checkout/cart/minicart/default.phtml")
                ->toHtml();

                $cart = Mage::getSingleton('checkout/session')->getQuote();
                //$cart->getAllItems() to get ALL items, parent as well as child, configurable as well as it's simple associated item
                $qty = 0;
                foreach ($cart->getAllVisibleItems() as $item) {
                    $qty = $qty + $item->getQty();
                }

                //   $count = count($cart->getAllItems());

                $response['qty'] = $qty;

                //print_r($response);die("k");
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
                return;
            } else {
                $response = array();
                $response['status'] = "ERROR";
                $response['message'] = 'Only One Vendors Products Can be added into Cart';
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            }
        } else {
            return parent::addAction();
        }
    }

    public function eligible($products) {

        $cart = Mage::getSingleton('checkout/session')->getQuote();
        //$cart->getAllItems() to get ALL items, parent as well as child, configurable as well as it's simple associated item
        if (count($cart->getAllVisibleItems())) {

            foreach ($cart->getAllVisibleItems() as $item) {

                try {
                    $itemId = $item->getId();
                    $product = $item->getProduct();
                    $id = $product->getId();
                    $name = $product->getName();
                    $sku = $product->getSku();
                    $paramProductId = $products;

                    $vproduct = Mage::getModel('csmarketplace/vproducts')->getCollection()->addFieldToFilter('product_id', ['in' => $paramProductId])->addFieldToFilter('check_status', ['nin' => 3])->getFirstItem();
                    $vid = $vproduct->getVendorId();

                    $cartProduct = Mage::getModel('csmarketplace/vproducts')->getCollection()->addFieldToFilter('product_id', ['in' => $id])->addFieldToFilter('check_status', ['nin' => 3])->getFirstItem();
                    $cprovid = $cartProduct->getVendorId();
                    //   echo $cprovid; echo "<br>"; echo $vid; die("ifg");
                    if ($vid != $cprovid) {


                        $error = 'Only One Vendors Products Can be added into Cart';
                        //set false if you not want to add product to cart
                        //Mage::app()->getRequest()->setParam('product', false);
                        return "error";
                    } else {
                        return "success";
                    }
                } catch (Exception $e) {
                    Mage::getSingleton('checkout/session')->addError($e);
                }
            }
        } else {
            return "success";
        }
    }
}
