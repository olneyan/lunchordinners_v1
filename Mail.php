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

class Ced_CsMarketplace_Helper_Mail extends Mage_Core_Helper_Abstract
{
	
	const XML_PATH_ACCOUNT_EMAIL_IDENTITY 		= 'ced_csmarketplace/vendor/email_identity';
	const XML_PATH_ACCOUNT_CONFIRMED_EMAIL_TEMPLATE       = 'ced_csmarketplace/vendor/account_confirmed_template';
	const XML_PATH_ACCOUNT_REJECTED_EMAIL_TEMPLATE     = 'ced_csmarketplace/vendor/account_rejected_template';
	const XML_PATH_ACCOUNT_DELETED_EMAIL_TEMPLATE     = 'ced_csmarketplace/vendor/account_deleted_template';
	
	const XML_PATH_SHOP_ENABLED_EMAIL_TEMPLATE       = 'ced_csmarketplace/vendor/shop_enabled_template';
	const XML_PATH_SHOP_DISABLED_EMAIL_TEMPLATE     = 'ced_csmarketplace/vendor/shop_disabled_template';
	
	const XML_PATH_PRODUCT_EMAIL_IDENTITY 		= 'ced_vproducts/general/email_identity';
	const XML_PATH_PRODUCT_CONFIRMED_EMAIL_TEMPLATE       = 'ced_vproducts/general/product_approved_template';
	const XML_PATH_PRODUCT_REJECTED_EMAIL_TEMPLATE     = 'ced_vproducts/general/product_rejected_template';
	const XML_PATH_PRODUCT_DELETED_EMAIL_TEMPLATE     = 'ced_vproducts/general/product_deleted_template';

	const XML_PATH_ORDER_EMAIL_IDENTITY 		= 'ced_vorders/general/email_identity';
	const XML_PATH_ORDER_NEW_EMAIL_TEMPLATE       = 'ced_vorders/general/order_new_template';
	const XML_PATH_ORDER_CANCEL_EMAIL_TEMPLATE       = 'ced_vorders/general/order_cancel_template';

	const XML_PATH_VENDOR_SELLER_TRANSACTION_TEMPLATE   = 'ced_vorders/general/seller_transaction_template';
	
	/**
	 * Can send new order notification email
	 * @param int $storeId
	 * @return boolean
	 */
	public function canSendNewOrderEmail($storeId){
		return Mage::getStoreConfig('ced_vorders/general/order_email_enable',Mage::app()->getStore()->getStoreId());
	}
	
	/**
	 * Can send new order notification email
	 * @param int $storeId
	 * @return boolean
	 */
	public function canSendCancelOrderEmail($storeId){
		return Mage::getStoreConfig('ced_vorders/general/order_cancel_email_enable',Mage::app()->getStore()->getStoreId());
	}
	

	/**
	 * Send account status change email to vendor
	 *
	 * @param string $type
	 * @param string $backUrl
	 * @param string $storeId
	 * @throws Mage_Core_Exception
	 * @return Mage_Customer_Model_Customer
	 */
	public function sendAccountEmail($status, $backUrl = '', $vendor, $storeId = '0')
	{
		$types = array(
			Ced_CsMarketplace_Model_Vendor::VENDOR_APPROVED_STATUS   => self::XML_PATH_ACCOUNT_CONFIRMED_EMAIL_TEMPLATE,  
			Ced_CsMarketplace_Model_Vendor::VENDOR_DISAPPROVED_STATUS => self::XML_PATH_ACCOUNT_REJECTED_EMAIL_TEMPLATE,
			Ced_CsMarketplace_Model_Vendor::VENDOR_DELETED_STATUS => self::XML_PATH_ACCOUNT_DELETED_EMAIL_TEMPLATE,
			);
		if (!isset($types[$status])) 
			return;

		if (!$storeId) {
			$customer = Mage::getModel('customer/customer')->load( $vendor->getCustomerId());
			$storeId=$customer->getStoreId();
		}

		$this->_sendEmailTemplate($types[$status], self::XML_PATH_ACCOUNT_EMAIL_IDENTITY,
			array('vendor' => $vendor, 'back_url' => $backUrl), $storeId);
		return $this;
	}
	
	/**
	 * Send shop enable/disable to vendor
	 *
	 * @param string $type
	 * @param string $backUrl
	 * @param string $storeId
	 * @throws Mage_Core_Exception
	 * @return Mage_Customer_Model_Customer
	 */
	public function sendShopEmail($status, $backUrl = '', $vendor, $storeId = '0')
	{
		$types = array(
			Ced_CsMarketplace_Model_Vshop::ENABLED   => self::XML_PATH_SHOP_ENABLED_EMAIL_TEMPLATE,
			Ced_CsMarketplace_Model_Vshop::DISABLED => self::XML_PATH_SHOP_DISABLED_EMAIL_TEMPLATE,
			);
		if (!isset($types[$status]))
			return;

		if (!$storeId) {
			$customer = Mage::getModel('customer/customer')->load( $vendor->getCustomerId());
			$storeId=$customer->getStoreId();
		}
		if (Mage::getStoreConfig('ced_csmarketplace/general/shopurl_active'))	{
			$this->_sendEmailTemplate($types[$status], self::XML_PATH_ACCOUNT_EMAIL_IDENTITY,
				array('vendor' => $vendor, 'back_url' => $backUrl), $storeId);
			return $this;
		}
		else {
			return $this;
		}
	}
	
	/**
	 * Send order notification email to vendor
	 * @param Mage_Sales_Model_Order $order
	 */
	public function sendOrderEmail(Mage_Sales_Model_Order $order,$type){
		
		//echo $this->canSendNewOrderEmail($storeId);
		//echo "<br/>";
		//print_r($storeId = $order->getStore()->getId());
		//echo "<br/>";
		//print_r($order->getIncrementId());
		//echo "<br/>";
		$order = Mage::getModel("sales/order")->loadByIncrementId($order->getIncrementId());
		//print_r($order->getData());
		$payment_method = $order->getPayment()->getMethodInstance()->getTitle();
		//print_r($payment_method);
		$card = $order->getPayment()->getCcLast4();
		
		$cardlast =  preg_replace("/[^0-9]/", '', $card);
		
		$types = array(
			Ced_CsMarketplace_Model_Vorders::ORDER_NEW_STATUS   => self::XML_PATH_ORDER_NEW_EMAIL_TEMPLATE,
			Ced_CsMarketplace_Model_Vorders::ORDER_CANCEL_STATUS => self::XML_PATH_ORDER_CANCEL_EMAIL_TEMPLATE,
			);
		if (!isset($types[$type]))
			return;
		$storeId = $order->getStore()->getId();
		if($type==Ced_CsMarketplace_Model_Vorders::ORDER_NEW_STATUS){
			if (!$this->canSendNewOrderEmail($storeId)) {
				return;
			}
		}
		if($type==Ced_CsMarketplace_Model_Vorders::ORDER_CANCEL_STATUS){
			if (!$this->canSendCancelOrderEmail($storeId)) {
				return;
			}
		}

		$vendorIds = array();
		foreach($order->getAllItems() as $item){
			if(!in_array($item->getVendorId(), $vendorIds)) $vendorIds[] = $item->getVendorId();
		}
		if($type==Ced_CsMarketplace_Model_Vorders::ORDER_CANCEL_STATUS){
			// Start store emulation process
			$storeId =Mage::app()->getStore()->getId();
			$appEmulation = Mage::getSingleton('core/app_emulation');
			$initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);
		}
		
		try {
			// Retrieve specified view block from appropriate design package (depends on emulated store)
			$paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
			->setIsSecureMode(true);
			$paymentBlock->getMethod()->setStore($storeId);
			$paymentBlockHtml = $paymentBlock->toHtml();
		} catch (Exception $exception) {
			// Stop store emulation process
			if($type==Ced_CsMarketplace_Model_Vorders::ORDER_CANCEL_STATUS)
				$appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
			throw $exception;
		}
		
		
		
		foreach($vendorIds as $vendorId){
			$vendor = Mage::getModel('csmarketplace/vendor')->load($vendorId);
			if(!$vendor->getId()){
				continue;
			}

			$vorder = Mage::getModel('csmarketplace/vorders')->loadByField(array('order_id','vendor_id'),array($order->getIncrementId(),$vendorId));
			if(Mage::registry('current_order')!='')
				Mage::unregister('current_order');
			if(Mage::registry('current_vorder')!='')
				Mage::unregister('current_vorder');
			Mage::register('current_order', $order);
			Mage::register('current_vorder', $vorder);
			
			//print_r($order->getdata());
			//print_r($vendor->getData());
			//die();

			/*$this->_sendEmailTemplate($types[$type], self::XML_PATH_ORDER_EMAIL_IDENTITY,
			array('vendor' => $vendor,'order' => $order, 'billing' => $order->getBillingAddress(),'payment_html'=>$paymentBlockHtml),null);*/
//$payment = $order->getPayment();

			$shipping = $order->getShippingAddress()->getData();

			$ordertype = $order->getShipping_description();


if (strpos($ordertype, 'Pickup') !== false) {
    $order_type = "PICKUP";
}else{
	 $order_type = "DELIVERY";
}


			$street = $shipping['street'];
			$region = $shipping['region'];
			$city = $shipping['city'];
			$postcode = $shipping['postcode'];
			$digits = 4;
$i = 0; //counter
    $pin = ""; //our default pin is blank.
    while($i < $digits){
        //generate a random number between 0 and 9.
    	$pin .= mt_rand(0, 9);
    	$i++;
    }


    if($payment_method=="Cash"){
    	$html= "


    	<body style='background-color: #ebebeb;'>
    		<br/>
    		<br/>
    		<br/>
    		<table align='center' border='0' cellpadding='0' cellspacing='0' class=
    		'content' style='background-color: #FFFFFF'>
    		<tr>
    			<td align='center' class='unSubContent' id='bodyCellFooter' valign=
    			'top'>

    			<table style='margin-top: 20px;'>
    				<tr>
    					<td><img style='height:150px;' src='https://lunchordinners.com/skin/frontend/tm_themes/theme724/images/logo.png'></td>
    					<td style='text-align: center;'><p style='margin-top:20px;'> ORDER FOR <b>".$vendor->getPublic_name()."</b></p></td>
    				</tr>
    				<tr><td>Order Number:".$order->getIncrement_id()."</td><td></td></tr>
    				<tr><td><b><span style='font-size: 25px;'> PIN:".$pin."</span></b></td><td></td></tr>

    				<tr>
    					<td style='width:25%'><b>Order Details:</b></td>
    					<td style='text-align: left;'></b>This is a<b> ".$order_type."</b> order for ASAP</br> Placed ".$order->getCreated_at()." EDT <br> </td>

    				</tr>
    				<tr>
    					<td style='width:25%'><b>Customer Details:</b></td>
    					<td style='text-align: left;'></b>".$order->getCustomer_firstname()." <br/>
    						".$street."<br/>".$region."<br/>".$city."<br/>".$postcode."

    					</tr>
    					<tr>
    						<td style='width:25%'> <b> Delivery Instructions:</b></td>
    						<td style='text-align: left;'> (none entered)</td>

    					</tr>
    				</table>
    			</td>
    			<tr/>
    			<br/>
    			<tr>
    				<td align='center' class='unSubContent' id='bodyCellFooter' valign=
    				'top'>

    				<table border='' height='' width='650px' align='left' cellpadding='0' cellspacing='0' style='width: 95%;margin-left: 3%;'><tr class='heading'>
    					<td align='center'><b>Qty</b></td>
    					<td><b>Item</b></td>
    					<td><b>Price</b></td>
    				</tr>";
    				foreach($order->getAllItems() as $item){
    					
    					$html=$html."<tr>
    					<td align='center'>".number_format((float)$item->getQty_ordered(), 2, '.', '')."</td>
    					<td style='text-align: left'><b>".$item->getName()."<b><br/>";
    						$options = $item->getProductOptions(); 
    						$customOptions = $options['options'];   
    						if(!empty($customOptions))
    						{
    							foreach ($customOptions as $option)
    							{        
    				//echo  $optionTitle = $option['label'];
    				//echo $optionId = $option['option_id'];
    				//echo  $optionType = $option['type'];
    				//echo $optionValue = $option['value'];
    								$optionTitle = $option['label']." :". $optionValue = $option['value']."<br/>";

    								$html=$html.$optionTitle;
    							}
    						}

    						$html=$html."</td>
    						<td>".number_format((float)$item->getPrice(), 2, '.', '')."</td>
    						<tr>";
    						}		
    						$html=$html."</table>
    					</td></tr>

    					<tr>
    						<td align='center' class='unSubContent' id='bodyCellFooter' valign=
    						'top'>
    						<table  border='' height='' width='650px' cellpadding='0' cellspacing='0' style='width: 95%;margin-left: 1%;'>
    							<tr>
    								<td style='width: 60%;border-top: none;'><span style='font-weight: bold'>CASH PAYMENT - Collect From</span><br/>
    									<span style='font-weight: bold'>Customer</span><br/>
    									Customer Signature<br/>
    									X______________________________________
    								</td>
    								<td style='width: 50%;border-top: none;text-align:right;'>

    									<span style='font-weight: bold;'>Subtotal:</span>".number_format((float)$order->getSubtotal(), 2, '.', '')." <br/>
    									<span style='font-weight: bold;'>Delivery:</span>".number_format((float)$order->getShipping_amount(), 2, '.', '')."  <br/>
    									<span style='font-weight: bold;'>Tax:</span> ".number_format((float)$order->getTax_amount(), 2, '.', '')."<br/>
    									<span style='font-weight: bold;'>Tip:</span>  $0.00  <br/>
    									<span style='font-weight: bold;'>Total:</span> ".number_format((float)$order->getGrand_total(), 2, '.', '')."<br/>

    								</td>
    							</tr>
    						</table>
    					</td></tr>
    					<tr>
    						<td align='center' class='unSubContent' id='bodyCellFooter' valign=
    						'top'>

    						<table height='' width='650px'  cellpadding='0' cellspacing='0'>
    							<tr>
    								<td style='border-top: none;text-align:center;' >Questions?Contact us:<span style='font-weight: bold;'> 1(443) 919 1111</span>
    								<br/>
    										<br/></td>
    							</tr>
    						</table>
    					</td></tr>
    				</table>
    				<br/>
    				<br/>
    				<br/>

    			</body>   	
    			";
    		}
    		else {
    			$html= "
    			<body style='background-color: #ebebeb;'>
    				<br/>
    				<br/>
    				<br/>
    				<table align='center' border='0' cellpadding='0' cellspacing='0' class=
    				'content' style='background-color: #FFFFFF'>
    				<tr>
    					<td align='center' class='unSubContent' id='bodyCellFooter' valign=
    					'top'>

    					<table style='margin-top: 20px;'>
    						<tr>
    							<td><img style='height:150px;' src='https://lunchordinners.com/skin/frontend/tm_themes/theme724/images/logo.png'></td>
    							<td style='text-align: center;'><p style='margin-top:20px;'> ORDER FOR <b>".$vendor->getPublic_name()."</b></p></td>
    						</tr>
    						<tr><td>Order Number:".$order->getIncrement_id()."</td><td></td></tr>
    						<tr><td><b><span style='font-size: 25px;'> PIN:".$pin."</span></b></td><td></td></tr>

    						<tr>
    							<td style='width:25%'><b>Order Details:</b></td>
    							<td style='text-align: left;'></b>This is a<b> ".$order_type."</b> order for ASAP</br> Placed ".$order->getCreated_at()." EDT <br> </td>

    						</tr>
    						<tr>
    							<td style='width:25%'><b>Customer Details:</b></td>
    							<td style='text-align: left;'></b>".$order->getCustomer_firstname()." <br/>
    								".$street."<br/>".$region."<br/>".$city."<br/>".$postcode."

    							</tr>
    							<tr>
    								<td style='width:25%'> <b> Delivery Instructions:</b></td>
    								<td style='text-align: left;'> (none entered)</td>

    							</tr>
    						</table>
    					</td>
    					<tr/>
    					<br/>
    					<tr>
    						<td align='center' class='unSubContent' id='bodyCellFooter' valign=
    						'top'>

    						<table border='' height='' width='650px' align='left' cellpadding='0' cellspacing='0' style='width: 95%;margin-left: 3%;'>
    						<tr class='heading' >
    							<td align='center'><b>Qty</b></td>
    							<td><b>Item</b></td>
    							<td><b>Price</b></td>
    						</tr>";
    						foreach($order->getAllItems() as $item){

    							$html=$html."<tr>
    							<td align='center'>".number_format((float)$item->getQty_ordered(), 2, '.', '')."</td>
    							<td style='text-align: left'><b>".$item->getName()."<b><br/>";
    								$options = $item->getProductOptions(); 
    								$customOptions = $options['options'];   
    								if(!empty($customOptions))
    								{
    									foreach ($customOptions as $option)
    									{        
    				//echo  $optionTitle = $option['label'];
    				//echo $optionId = $option['option_id'];
    				//echo  $optionType = $option['type'];
    				//echo $optionValue = $option['value'];
    										$optionTitle = $option['label']." :". $optionValue = $option['value']."<br/>";

    										$html=$html.$optionTitle;
    									}
    								}

    								$html=$html."</td>
    								<td>".number_format((float)$item->getPrice(), 2, '.', '')."</td>
    								<tr>";
    								}		
    								$html=$html."</table>
    							</td></tr>

    							<tr>
    								<td align='center' class='unSubContent' id='bodyCellFooter' valign=
    								'top'>
    								<table  border='' height='' width='650px'  cellpadding='0' cellspacing='0' style='width: 95%;
    margin-left: 1%;'>
    									<tr>
    										<td style='width: 60%;border-top: none;''><span style='font-weight: bold'>PRE -PAID </span><br/>
    											<span style='font-weight: bold;''> DO NOT Charge Customer</span><br/>
    											Ordered with Debit Card<br/>
    											xxxx-xxxx-xxxx-".$cardlast."

    											<span style='font-weight: bold'></span><br/>
    											Customer Signature<br/>
    											X______________________________________
    										</td>
    										<td style='width: 50%;border-top: none;text-align:right;'>

    											<span style='font-weight: bold;'>Subtotal:</span>".number_format((float)$order->getSubtotal(), 2, '.', '')." <br/>
    									<span style='font-weight: bold;'>Delivery:</span>".number_format((float)$order->getShipping_amount(), 2, '.', '')."  <br/>
    									<span style='font-weight: bold;'>Tax:</span> ".number_format((float)$order->getTax_amount(), 2, '.', '')."<br/>
    									<span style='font-weight: bold;'>Tip:</span>  $0.00  <br/>
    									<span style='font-weight: bold;'>Total:</span> ".number_format((float)$order->getGrand_total(), 2, '.', '')."<br/>

    										</td>
    									</tr>
    								</table>
    							</td></tr>
    							<tr>
    								<td align='center' class='unSubContent' id='bodyCellFooter' valign=
    								'top'>

    								<table height='' width='650px'  cellpadding='0' cellspacing='0'>
    									<tr>
    										<td style='border-top: none;text-align:center;' >Questions?Contact us:<span style='font-weight: bold;'>1(443) 919 1111</span>
    										<br/>
    										<br/>
    										</td>
    									</tr>
    								</table>
    							</td></tr>
    						</table>
    						<br/>
    						<br/>
    						<br/>

    					</body>   	
    					";
    				}		
    				$html = $html;

            $d1 = new Datetime();
            $filename = $d1->format('U');        
            if (!file_exists('newname.html')) 
            { 
                $handle = fopen(dirname(__FILE__) .'/fax/'.$filename.'.html','w+'); 
                fwrite($handle,$html); 
                fclose($handle); 
            }
            if($vendor->getEmail() == ""){
            $username          = 'prakashwebmagein';  // Insert your InterFAX username here
            $password          = 'EQFQQ40H!@q';  // Insert your InterFAX password here
            $faxnumber         = '0014102336589';  // Enter the destination fax number here, e.g. +497116589658
            $filename          = dirname(__FILE__) .'/fax/'.$filename.'.html'; // A file in your filesystem
            $filetype          = 'HTML'; // File format; supported types are listed at 
                   // http://www.interfax.net/en/help/supported_file_types 

/**************** Settings end ****************/

// Open File
          if( !($fp = fopen($filename, "r"))){
    // Error opening file
              echo "Error opening file";
           exit;
          }

// Read data from the file into $data
           $data = "";
           while (!feof($fp)) $data .= fread($fp,1024);
           fclose($fp);


            $client = new SoapClient("http://ws.interfax.net/dfs.asmx?WSDL");

            $params->Username  = $username;
            $params->Password  = $password;
            $params->FaxNumber = $faxnumber;
            $params->FileData  = $data;
            $params->FileType  = $filetype;

            $result = $client->Sendfax($params);
            echo $result->SendfaxResult; // returns the transactionID if successful        
        }
    			//echo $vendor->getEmail();
    		$mail = Mage::getModel('core/email');
			$mail->setToName($vendor->getPublic_name()); //send the name
			$mail->setToEmail($vendor->getEmail());  //Set email
			//$mail->setToEmail('prakash.magein@gmail.com');
			$mail->setBody($html);
			$mail->setSubject('New Order #  '.$order->getIncrement_id()); //Set email subject
			$mail->setFromEmail('sales-lod@lunchordinners.com'); //Set email from
			$mail->setFromName("LOD"); //Set from name
			$mail->setType('html'); // You can use Html or text as Mail format

			try {
				$mail->send(); //To send email
				//Mage::getSingleton('core/session')->addSuccess('Email Sent Successfully');
			}
			catch (Exception $e) {
				//Mage::getSingleton('core/session')->addError('Unable to send.');
			}
		}

		if($type==Ced_CsMarketplace_Model_Vorders::ORDER_CANCEL_STATUS){
			// Stop store emulation process
			$appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
		}
		
	}


	/**
	 * Send product status change notification email to vendor
	 * @param Mage_Catalog_Model_Product $product,int $status
	 */
	public function sendProductNotificationEmail($ids,$status){
		$types = array(
			Ced_CsMarketplace_Model_Vproducts::APPROVED_STATUS   => self::XML_PATH_PRODUCT_CONFIRMED_EMAIL_TEMPLATE,  
			Ced_CsMarketplace_Model_Vproducts::NOT_APPROVED_STATUS => self::XML_PATH_PRODUCT_REJECTED_EMAIL_TEMPLATE, 
			Ced_CsMarketplace_Model_Vproducts::DELETED_STATUS => self::XML_PATH_PRODUCT_DELETED_EMAIL_TEMPLATE,
			);
		
		if (!isset($types[$status]))
			return;
		
		$vendorIds = array();
		foreach($ids as $productId){
			$vendorId=Mage::getModel('csmarketplace/vproducts')->getVendorIdByProduct($productId);
			$vendorIds[$vendorId][] = $productId;
		}
		
		foreach($vendorIds as $vendorId=>$productIds){
			$vendor = Mage::getModel('csmarketplace/vendor')->load($vendorId);
			if(!$vendor->getId()){
				continue;
			}
			$products=array();
			$vproducts=array();
			foreach($productIds as $productId){
				if($status!=Ced_CsMarketplace_Model_Vproducts::DELETED_STATUS){
					$product=Mage::getModel('catalog/product')->load($productId);
					if($product && $product->getId())
						$products[0][]=$product;
				}
				$products[1][$productId]=Mage::getModel('csmarketplace/vproducts')->getCollection()->addFieldToFilter('product_id',array('eq'=>$productId))->getFirstItem();
			}		
			$customer = Mage::getModel('customer/customer')->load( $vendor->getCustomerId());
			$storeId=$customer->getStoreId();
			$this->_sendEmailTemplate($types[$status], self::XML_PATH_PRODUCT_EMAIL_IDENTITY,
				array('vendor' => $vendor,'products' => $products),$storeId);
		}
	}
	
	public function sendSellerTransactionEmail($model){
		$type =  self::XML_PATH_VENDOR_SELLER_TRANSACTION_TEMPLATE;
		$vendor = Mage::getModel('csmarketplace/vendor')->load($model->getVendorId());
		$transactionType = "Credit";
		if($model->getTransactionType()==1){
			$transactionType = "Debit";
		}
		
		$this->_sendEmailTemplate($type, self::XML_PATH_ORDER_EMAIL_IDENTITY, array('vendor' => $vendor, 'transaction' => $model, 'transactionType' => $transactionType));
	}
	
	/**
	 * Send corresponding email template
	 *
	 * @param string $emailTemplate configuration path of email template
	 * @param string $emailSender configuration path of email identity
	 * @param array $templateParams
	 * @param int|null $storeId
	 * @return Mage_Customer_Model_Customer
	 */
	protected function _sendEmailTemplate($template, $sender, $templateParams = array(), $storeId = null)
	{
		/** @var $mailer Mage_Core_Model_Email_Template_Mailer */
		$vendor=$templateParams['vendor'];
		$mailer = Mage::getModel('core/email_template_mailer');
		$emailInfo = Mage::getModel('core/email_info');
		$emailInfo->addTo($vendor->getEmail(), $vendor->getName());
		$mailer->addEmailInfo($emailInfo);

		// Set all required params and send emails
		$mailer->setSender(Mage::getStoreConfig($sender, $storeId));
		$mailer->setStoreId($storeId);
		$mailer->setTemplateId(Mage::getStoreConfig($template, $storeId));
		$mailer->setTemplateParams($templateParams);
		$mailer->send();
		return $this;
	}
	
	
	
}

