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

class Ced_CsMarketplace_Model_Order_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Invoice
{

	public function getAdminToSellerPdf($id,$order)
    {
    	//die(get_class($this));
    	$collection = Mage::getModel('csmarketplace/vpayment')->load($id);
    	$vendorId = Mage::getSingleton('customer/session')->getVendor()->getEntityId();
    	$totalArray = json_decode($collection->getAmountDesc(), true);
    	$totalAmount = 0;
    	foreach($totalArray as $value){
    		$totalAmount = $totalAmount + $value;
    	}
    	//print_r($collection->getData());die;
    	if(Mage::helper('core')->isModuleEnabled('csmultisipping')){
	    	$vsetting_model = Mage::getModel('csmarketplace/vsettings')->getCollection()->addFieldToFilter('vendor_id',$vendorId);
	    	foreach($vsetting_model as $value){
	    			
	    		if($value['key'] == 'shipping/address/country_id')
	    			$seller_country = Mage::app()->getLocale()->getCountryTranslation($value['value']);
	    		if($value['key'] == 'shipping/address/region_id')
	    			$seller_regionId = $value['value'];
	    		if($value['key'] == 'shipping/address/postcode')
	    			$seller_zipcode = $value['value'];
	    		/* if($value['key'] == 'shipping/address/ship_id')
	    		 $shipId = $value['value'];  */
	    		if($value['key'] == 'shipping/address/city')
	    			$seller_city = $value['value'];
	    		if($value['key'] == 'shipping/address/phoneno')
	    			$seller_contact = $value['value'];
	    	}
    	}
    	$vorder = Mage::getModel('csmarketplace/vorders')->getCollection()->addFieldToFilter('vendor_id',$vendorId)
    				->addFieldToFilter('order_id',$order->getIncrementId());
    	foreach($vorder as $vorders){
    		$commission = number_format($vorders['shop_commission_fee'],2);
    	}
    	
    	$vendor = Mage::getModel('csmarketplace/vendor')->load($vendorId);
    	
    	//print_r($vorder);die;
    	//$vendor_data = 
    	
    	/* $attribute = array();
    	foreach($vendor_attr as $attr){
    		$attribute['id'] = $attr['attribute_id'];
    		$attribute['code'] = $attr['attribute_code'];
    	} */
    	
    	
    	//print_r($seller_regionId);die;
    	/* print_r($vendor->getData());die;
    	echo 'country :'.$seller_country;
    	echo '<br>state :'.$seller_regionId;
    	echo '<br>zip :'.$seller_zipcode;
    	echo '<br>city :'.$seller_city;
    	echo '<br>contact :'.$seller_contact;die; */
    	//$vendor = $this->getVendor()->getData();
    	$store_name = Mage::getStoreConfig('general/store_information/name');
    	//$logo_src = Mage::getStoreConfig('sales/identity/logo');
    	$storeId = Mage::app()->getStore()->getStoreId();

	    $logo_src = Mage::getStoreConfig('sales/identity/logo', $storeId);

    	//print_r()
    	$store_telephone = Mage::getStoreConfig('general/store_information/phone');
    	$store_address = Mage::getStoreConfig('general/store_information/address');
    	$inoice_attr = Mage::getModel('csmarketplace/vendor_form')->getCollection()
				    	->addFieldToFilter('use_in_admin_to_seller_invoice',array('eq' => 1))
				    	->addFieldToFilter('store_id',array('eq' => 0));
    	$attribute = Mage::getModel('catalog/resource_eav_attribute');
    	$orderArray = json_decode($collection->getTransCreditSummary(), true);
    	//print_r($orderArray);die('lll');
    	
		$this->_beforeGetPdf();
    	$this->_initRenderer('invoice');
    	
    	$pdf = new Zend_Pdf();
    	$this->_setPdf($pdf);
    	$page  = $this->newPage();
    	$style = new Zend_Pdf_Style();
    	//$this->_setFontBold($style, 10);
    	$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD);
    	$page->setFont($font, 12);
    	
    	$pos = 830;
	 	$top         = $pos; //top border of the page
        $widthLimit  = 100; //half of the page width
        $heightLimit = 70; //assuming the image is not a "skyscraper"
        $width=195;
        $height=150;
        $ratio = $width / $height;
        if ($ratio > 1 && $width > $widthLimit)
        {
            $width  = $widthLimit;
            $height = $width / $ratio;
        } elseif ($ratio < 1 && $height > $heightLimit)
        {
            $height = $heightLimit;
            $width  = $height * $ratio;
        } elseif ($ratio == 1 && $height > $heightLimit)
        {
            $height = $heightLimit;
            $width  = $widthLimit;
        }

        $y1 = $top - $height;
        $y2 = $top;
        $x1 = 25;
        $x2 = $x1 + $width;
        $addressy = $this->y;

    	if ($logo_src) {
        $image = Mage::getBaseDir('media') . '/sales/store/logo/' . $logo_src;
        if (is_file($image)) {
   			$image       = Zend_Pdf_Image::imageWithPath($image);
    		$page->drawImage($image, $x1+10, $y1, $x2, $y2);

	        }
        }


	    	$page->drawText($store_name, $x1+350, $addressy+10 , 'UTF-8');
	    	$this->_setFontRegular($page, 10);
    		$text = array();
    		$addressy = $this->y-5;
			foreach (Mage::helper('core/string')->str_split($store_address, 30, true, true) as $_value) {
				$text[] = $_value;
			}
			foreach ($text as $part)
			{
				$page->drawText(strip_tags(ltrim($part)), $x1+350, $addressy, 'UTF-8');
				$addressy -= 11;
			}
			//$page->setFillColor(new Zend_Pdf_Color_Html('#808080'));
			$y1 = $addressy-20;
			if(isset($logo_src) || isset($store_address))
				$page->drawRectangle($x1-5, $top+5, $page->getWidth()-30, $y1, Zend_Pdf_Page::SHAPE_DRAW_STROKE);
			
			$this->_setFontRegular($page, 12);
			
			$page->drawText('Invoice # '.$collection->getTransactionId(), $x1+10, $y1-30 , 'UTF-8');
			$page->drawText('Invoice Date : '.$collection->getCreatedAt(), $x1+10, $y1-40 , 'UTF-8');
			$page->drawText('Telephone : '.$store_telephone, $x1+300, $y1-30 , 'UTF-8');
			$page->drawText('Email : '.Mage::getStoreConfig('trans_email/ident_general/email'), $x1+300, $y1-40 , 'UTF-8');
	    	//$page->drawText(str_split($store_address,30), $x, $y , 'UTF-8');
			$this->_setFontRegular($page, 10);
			$y1 = $y1-60;
			
			$page->drawText('Display Name : '.$vendor->getName(), $x1+10, $y1 , 'UTF-8');
			$page->drawText('Bussiness Name : '.$vendor->getPublicName(), $x1+10, $y1-10 , 'UTF-8');
			$page->drawText('Address : '.$vendor->getAddress(), $x1+10, $y1-20 , 'UTF-8');
			$page->drawText($vendor->getCity().' - '.$vendor->getZipCode(), $x1+10, $y1-30 , 'UTF-8');
			$page->drawText($vendor->getRegion().' , '.Mage::app()->getLocale()->getCountryTranslation($vendor->getCountryId()), $x1+10, $y1-40 , 'UTF-8');
			
			$page->drawText('Contact Number : '.$vendor->getContactNumber(), $x1+300, $y1 , 'UTF-8');
			$page->drawText('Email : '.$vendor->getEmail(), $x1+300, $y1-10 , 'UTF-8');

			if(Mage::helper('core')->isModuleEnabled('csvattribute')){
				$vendor_attr = Mage::getModel('csmarketplace/vendor_form')->getCollection()->addFieldToFilter('use_in_invoice',1);
				$page->drawText('Pan Number : '.$vendor->getPan(), $x1+300, $y1-20 , 'UTF-8');
				$page->drawText('Tin Number : '.$vendor->getTin(), $x1+300, $y1-30 , 'UTF-8');
				$page->drawText('Vat Number : '.$vendor->getVatNumber(), $x1+300, $y1-40 , 'UTF-8');
			}
			$y1 = $y1-70;
			
			$this->_setFontRegular($page, 11);
			$page->drawText('Sr. No.  ', $x1+10, $y1-10 , 'UTF-8');
			$page->drawText('Description  ', $x1+150, $y1-10 , 'UTF-8');
			$page->drawText('Amount  ', $x1+450, $y1-10 , 'UTF-8');
			
			$page->drawLine($x1+50 , $y1+5 , $x1+50 , $y1-15);
			$page->drawLine($x1+400 , $y1+5 , $x1+400 , $y1-15);
			$page->drawRectangle($x1, $y1+5, $page->getWidth()-30, $y1-15, Zend_Pdf_Page::SHAPE_DRAW_STROKE);

			/* $page->drawLine($x + 70, $this->y + 12, $x + 70, $this->y - 8);
			$page->drawLine($x + 170, $this->y + 12, $x + 170, $this->y - 8);
			$page->drawLine($x + 260, $this->y + 12, $x + 260, $this->y - 8); */
			$i = 0;
			
			$page->drawText('1', $x1+10, $y1-28 , 'UTF-8');
			$page->drawText('Total Amount  ', $x1+150, $y1-28 , 'UTF-8');
			$page->drawText($totalAmount, $x1+450, $y1-28 , 'UTF-8');
			$page->drawLine($x1+50 , $y1-15 , $x1+50 , $y1-35);
			$page->drawLine($x1+400 , $y1-15 , $x1+400 , $y1-35);
			$page->drawLine($x1, $y1-15, $x1, $y1-35);
			$page->drawLine(565, $y1-15, 565, $y1-35);
			$page->drawLine($x1,$y1-35 , 565, $y1-35);
			//$page->getWidth() = 565
			$page->drawText('2', $x1+10, $y1-49 , 'UTF-8');
			$page->drawText('Commission Fee', $x1+150, $y1-49 , 'UTF-8');
			$page->drawText($commission, $x1+450, $y1-49 , 'UTF-8');
			$page->drawLine($x1+50 , $y1-35 , $x1+50 , $y1-55);
			$page->drawLine($x1+400 , $y1-35 , $x1+400 , $y1-55);
			$page->drawLine($x1, $y1-35, $x1, $y1-55);
			$page->drawLine(565, $y1-35, 565, $y1-55);
			$page->drawLine($x1,$y1-55 , 565, $y1-55);
			
			
			$page->drawText('3', $x1+10, $y1-67 , 'UTF-8');
			$page->drawText('Shipping Fee', $x1+150, $y1-67 , 'UTF-8');
			$page->drawText($collection->getTotalShippingAmount(), $x1+450, $y1-67 , 'UTF-8');
			$page->drawLine($x1+50 , $y1-55 , $x1+50 , $y1-75);
			$page->drawLine($x1+400 , $y1-55 , $x1+400 , $y1-75);
			$page->drawLine($x1, $y1-55, $x1, $y1-75);
			$page->drawLine(565, $y1-55, 565, $y1-75);
			$page->drawLine($x1,$y1-75 , 565, $y1-75);
			
			$page->drawText('4', $x1+10, $y1-87 , 'UTF-8');
			$page->drawText('Fixed fee', $x1+150, $y1-87 , 'UTF-8');
			$page->drawText('0 ', $x1+450, $y1-87 , 'UTF-8');
			$page->drawLine($x1+50 , $y1-75 , $x1+50 , $y1-95);
			$page->drawLine($x1+400 , $y1-75 , $x1+400 , $y1-95);
			$page->drawLine($x1, $y1-75, $x1, $y1-95);
			$page->drawLine(565, $y1-75, 565, $y1-95);
			$page->drawLine($x1,$y1-95 , 565, $y1-95);
			
			$page->drawText('5', $x1+10, $y1-107 , 'UTF-8');
			$page->drawText('Cancellation fee', $x1+150, $y1-107 , 'UTF-8');
			$page->drawText('0 ', $x1+450, $y1-107 , 'UTF-8');
			$page->drawLine($x1+50 , $y1-95 , $x1+50 , $y1-115);
			$page->drawLine($x1+400 , $y1-95 , $x1+400 , $y1-115);
			$page->drawLine($x1, $y1-95, $x1, $y1-115);
			$page->drawLine(565, $y1-95, 565, $y1-115);
			//$page->drawLine($x1,$y1-115 , 565, $y1-115);
			//	$page->drawLine($x1);
			$total = $commission+$collection->getTotalShippingAmount()+10;
			$page->drawText('6', $x1+10, $y1-127 , 'UTF-8');
			$page->drawText('Adjustment Amount', $x1+150, $y1-127 , 'UTF-8');
			$page->drawText($collection->getFee(), $x1+450, $y1-127 , 'UTF-8');
			$page->drawRectangle($x1, $y1-115, $page->getWidth()-30, $y1-135, Zend_Pdf_Page::SHAPE_DRAW_STROKE);
			//print_r($orderArray['service_tax_amount']);die;
		 	$page->drawText('Service Tax', $x1+10, $y1-148 , 'UTF-8');
			$page->drawText($orderArray['service_tax_amount'], $x1+450, $y1-148 , 'UTF-8');
			$page->drawRectangle($x1, $y1-135, $page->getWidth()-30, $y1-155, Zend_Pdf_Page::SHAPE_DRAW_STROKE); 
			
			/*$page->drawText('Swachh Bharat Cess @0.5%', $x1+10, $y1-170 , 'UTF-8');
			$page->drawText($total*0.005, $x1+450, $y1-170 , 'UTF-8');
			$page->drawRectangle($x1, $y1-155, $page->getWidth()-30, $y1-175, Zend_Pdf_Page::SHAPE_DRAW_STROKE);*/
			
			$page->drawText('Payable Amount', $x1+10, $y1-190 , 'UTF-8');
			$page->drawText($collection->getNetAmount(), $x1+450, $y1-190 , 'UTF-8');
			$page->drawRectangle($x1, $y1-175, $page->getWidth()-30, $y1-195, Zend_Pdf_Page::SHAPE_DRAW_STROKE);
			
			
					
					
			/*$page->drawText('* Service tax of 12.36% applied for the services completed before June 1, 2015, and 14% for the services', $x1, $y1-220 , 'UTF-8');
			$page->drawText('completed on or after June 1, 2015. Swachh Bharat Cess applied for the service completed on or after', $x1, $y1-235 , 'UTF-8');
			$page->drawText('November 15, 2015.', $x1+200, $y1-250 , 'UTF-8');*/
			
			
			/*$page->drawText('This is a computer generated invoice. No signature required.', $x1+100, $y1-285 , 'UTF-8');*/
			return $pdf;
    	
    		//$page->drawText($store_name, $x, $y , 'UTF-8');
    		
    		/* Add head */
    		
    	//print_r($id);die('abc');
    }



}