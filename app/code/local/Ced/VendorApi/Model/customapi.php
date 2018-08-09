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
  * @category    Ced
  * @package     VendorApi
  * @author      CedCommerce Core Team <connect@cedcommerce.com>
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://cedcommerce.com/license-agreement.txt
  */

ini_set("soap.wsdl_cache_enabled", "0");
/* // v2 call
$client = new SoapClient('http://localhost/himanshu/training/magento_marketplace/api/v2_soap?wsdl=1');
$session = $client->login('rahul', 'apikey');
$result = $client->customapimoduleProductList($session);
$client->endSession($session);
echo '<pre>';
print_r($result);
 
 
//v1 call
$client = new SoapClient('http://localhost/himanshu/training/magento_marketplace/api/soap/?wsdl=1');
$session = $client->login('rahul', 'apikey');
$result = $client->call($session, 'product.list', array(array()));
$client->endSession($session);
echo '<pre>';
print_r($result); */

//----------------------------------------------************************************-----------------------------------------------------------//

/*	Vendor Functions calling Start	*/

//vendor List call v1
 $client = new SoapClient('http://demo.cedcommerce.com/magento/mobiapp/mobi/api/soap/?wsdl=1');
$session = $client->login('marketplace', 'marketplace123');
$result = $client->call($session, 'vendor.list', array(array()));
$client->endSession($session);
echo '<pre>';
json_encode($result);

//vendor List v2
/* try{
	$client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/v2_soap?wsdl=1');
	$session = $client->login('rahul', 'apikey');
	//$functions = $client->__getFunctions();
	//var_dump ($functions);
	$result = $client->vendorList($session);
	//$client->endSession($session);
	echo '<pre>';
	print_r($result);
} catch(Exception $e) {
	echo $e->getMessage();
} */



//Update Vendor V1
/* $client = new SoapClient('http://localhost/himanshu/training/magento_marketplace/api/soap/?wsdl=1');
$session = $client->login('rahul', 'apikey');
$result = $client->call($session, 'vendor.update', array('vendorId' => '5', 'vendorData' => array('shop_url' => 'new_url', 'public_name' => 'new-public-name')));
$client->endSession($session);
echo '<pre>';
print_r($result); */

//Update Vendor V2
/* $client = new SoapClient('http://localhost/himanshu/training/magento_marketplace/api/v2_soap?wsdl=1');
$session = $client->login('rahul', 'apikey');
$result = $client->vendorUpdate($session, '9', array('shop_url' => 'v2newnewurl', 'public_name' => 'v2-new-new-public-name'));
$client->endSession($session);
echo '<pre>';
print_r($result); */



//Get Vendor Info V1
/* $client = new SoapClient('http://localhost/himanshu/training/magento_marketplace/api/soap/?wsdl=1');
$session = $client->login('rahul', 'apikey');
$result = $client->call($session, 'vendor.info', array(array(7)));
$client->endSession($session);
echo '<pre>';
print_r($result);*/

//Get Vendor Info V2
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/v2_soap?wsdl=1');
$session = $client->login('rahul', 'apikey');
$result = $client->vendorInfo($session,7);
$client->endSession($session);
echo '<pre>';
print_r($result); */



//Create Vendor V1
/* $client = new SoapClient('http://localhost/himanshu/training/magento_marketplace/api/soap/?wsdl=1');
$session = $client->login('rahul', 'apikey');
//$result = $client->call($session, 'vendor.create', array(array('email' => 'connect@cedcommerce.com', 'firstname' => 'Dough', 'lastname' => 'Deeks', 'password' => 'password', 'website_id' => 1, 'store_id' => 1, 'group_id' => 1, 'vendor'=>array('public_name'=>'api_public_name_4', 'shop_url'=>'apishopurl4'))));
$result = $client->call($session, 'vendor.create', array(array('email' => 'connect@cedcommerce.com', 'firstname' => 'Dough', 'lastname' => 'Deeks', 'password' => 'password', 'website_id' => 1, 'store_id' => 1, 'group_id' => 1, 'vendor'=>array(array('key'=>'public_name','value'=>'api_public_name_6',), array('key'=>'shop_url','value'=>'apishopurl6')))));
$client->endSession($session);
echo '<pre>';
print_r($result); */

//Create Vendor V2
/* $vendorCreate = new stdClass();
$additionalAttrs = array();

$vendorCreate->email = "connect@cedcommerce.com";
$vendorCreate->firstname = "Dough";
$vendorCreate->lastname = "Deeks";
$vendorCreate->password = "password";
$vendorCreate->website_id = "1";
$vendorCreate->store_id = "1";
$vendorCreate->group_id = "1";
//you can add other direct attributes here

$public_name = new stdClass();
$public_name->key = "public_name";
$public_name->value = "public_name7";
$vendorAttrs[] = $public_name;

$shop_url = new stdClass();
$shop_url->key = "shop_url";
$shop_url->value = "my-shop-url7";
$vendorAttrs[] = $shop_url;
//you can add other additional attributes here like $manufacturer

// finally we link the additional attributes to the $catalogProductCreateEntity object
$vendorCreate->vendor = $vendorAttrs;

$client = new SoapClient('http://localhost/himanshu/training/magento_marketplace/api/v2_soap?wsdl=1');
$session = $client->login('rahul', 'apikey');
//$result = $client->vendorCreate($session, array('email' => 'connect@cedcommerce.com', 'firstname' => 'Dough', 'lastname' => 'Deeks', 'password' => 'password', 'website_id' => 1, 'store_id' => 1, 'group_id' => 1,  'public_name'=>'api_public_name_3', 'shop_url'=>'apishopurl3', 'vendor'=>$manufacturer));
//$result = $client->vendorCreate($session, array('email' => 'connect@cedcommerce.com', 'firstname' => 'Dough', 'lastname' => 'Deeks', 'password' => 'password', 'website_id' => 1, 'store_id' => 1, 'group_id' => 1,  'vendor'=>array('public_name'=>'api_public_name_4', 'shop_url'=>'apishopurl4')));
try {
	$result = $client->vendorCreate($session, $vendorCreate);
} catch (Exception $e) {
	echo $e->getMessage();
	die;
}
$client->endSession($session);
echo '<pre>';
print_r($result); */



//Delete Vendor V1
/* $client = new SoapClient('http://localhost/himanshu/training/magento_marketplace/api/soap/?wsdl=1');
$session = $client->login('rahul', 'apikey');
$result = $client->call($session, 'vendor.delete', '8');
$client->endSession($session);
echo '<pre>';
print_r($result); */

//Delete Vendor V2
/* $client = new SoapClient('http://localhost/himanshu/training/magento_marketplace/api/v2_soap?wsdl=1');
$session = $client->login('rahul', 'apikey');
$result = $client->vendorDelete($session,12);
$client->endSession($session);
echo '<pre>';
print_r($result); */



//get vendor product list v1
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/soap/?wsdl=1');
$session = $client->login('rahul', 'apikey');
try {
	$result = $client->call($session, 'vendor_product.list', array(2, '8v385fbc8e7h2j1db1fgpq0mg2',  array('sku'=>'CED00191'), '1'));
} catch (Exception $e) {
	echo $e->getMessage();
	
}
$client->endSession($session);
echo '<pre>';
print_r($result); */

//get vendor product list v2
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/v2_soap?wsdl=1');
$session = $client->login('rahul', 'apikey');
try {
	$result = $client->vendorProductList($session, 2, '8v385fbc8e7h2j1db1fgpq0mg2',  array(), '1');
} catch (Exception $e) {
	echo $e->getMessage();

}
$client->endSession($session);
echo '<pre>';
print_r($result); */



//get vendor product info v1
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/soap/?wsdl=1');
$session = $client->login('rahul', 'apikey');
try {
	$result = $client->call($session, 'vendor_product.info', array(2, '8v385fbc8e7h2j1db1fgpq0mg2', 9));
} catch (Exception $e) {
	echo $e->getMessage();

}
$client->endSession($session);
echo '<pre>';
print_r($result); */

//get vendor product info v2
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/v2_soap?wsdl=1');
 $session = $client->login('rahul', 'apikey');
try {
$result = $client->vendorProductInfo($session, 2, '8v385fbc8e7h2j1db1fgpq0mg2', '9');
} catch (Exception $e) {
echo $e->getMessage();
}
$client->endSession($session);
echo '<pre>';
print_r($result); */

//create vendor product v1
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/soap/?wsdl=1');
$session = $client->login('rahul', 'apikey');
try {
	$result = $client->call($session, 'vendor_product.create', array(7, '8p91rfhegldt9cfkqnn0n19e76', 'simple', 4, 'product_sku', array(
    'categories' => array(2),
    'websites' => array(1),
    'name' => 'Product name',
    'description' => 'Product description',
    'short_description' => 'Product short description',
    'weight' => '10',
    'status' => '1',
    'url_key' => 'product-url-key',
    'url_path' => 'product-url-path',
    'visibility' => '4',
    'price' => '100',
    'tax_class_id' => 1,
    'meta_title' => 'Product meta title',
    'meta_keyword' => 'Product meta keyword',
    'meta_description' => 'Product meta description'
)));
} catch (Exception $e) {
	echo $e->getMessage();
}
$client->endSession($session);
echo '<pre>';
print_r($result); */


//create vendor product v2
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/v2_soap?wsdl=1');
 $session = $client->login('rahul', 'apikey');
try {
$result = $client->vendorProductCreate($session, 7, 'q7o6u3ad8h3pleil4e3u30tsn1', 'simple', 4, 'product_sku', array(
		'categories' => array(2),
		'websites' => array(1),
		'name' => 'Product name',
		'description' => 'Product description',
		'short_description' => 'Product short description',
		'weight' => '10',
		'status' => '1',
		'url_key' => 'product-url-key',
		'url_path' => 'product-url-path',
		'visibility' => '4',
		'price' => '100',
		'tax_class_id' => 1,
		'meta_title' => 'Product meta title',
		'meta_keyword' => 'Product meta keyword',
		'meta_description' => 'Product meta description'
));
} catch (Exception $e) {
echo $e->getMessage();
}
$client->endSession($session);
echo '<pre>';
print_r($result); */



//delete vendor product v1
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/soap/?wsdl=1');
$session = $client->login('rahul', 'apikey');
try {
	$result = $client->call($session, 'vendor_product.delete', array(7, '8p91rfhegldt9cfkqnn0n19e76', 24));
} catch (Exception $e) {
	echo $e->getMessage();
}
$client->endSession($session);
echo '<pre>';
print_r($result); */

//delete vendor product v2
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/v2_soap?wsdl=1');
 $session = $client->login('rahul', 'apikey');
try {
$result = $client->vendorProductDelete($session, 7, 'q7o6u3ad8h3pleil4e3u30tsn1', 15);
} catch (Exception $e) {
echo $e->getMessage();
}
$client->endSession($session);
echo '<pre>';
print_r($result); */



//update vendor product v1
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/soap/?wsdl=1');
$session = $client->login('rahul', 'apikey');
try {
	$result = $client->call($session, 'vendor_product.update', array(7, '8p91rfhegldt9cfkqnn0n19e76', 11, array(
																		    'name' => 'Product name new 2',
																		    'description' => 'Product description')
																)
	);
} catch (Exception $e) {
	echo $e->getMessage();
}
$client->endSession($session);
echo '<pre>';
print_r($result); */

//update vendor product v2
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/soap/?wsdl=1');
$session = $client->login('rahul', 'apikey');
try {
	$result = $client->vendorProductUpdate($session, 7, '8p91rfhegldt9cfkqnn0n19e76', 11, array(
			'name' => 'Product name new 2 Again',
			'description' => 'Product description Again'
			));
} catch (Exception $e) {
	echo $e->getMessage();
}
$client->endSession($session);
echo '<pre>';
print_r($result); */


//get vendor order list v1
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/soap/?wsdl=1');
 $session = $client->login('rahul', 'apikey');
try {
$result = $client->call($session, 'vendor_order.list', array(2, '8v385fbc8e7h2j1db1fgpq0mg2'));
} catch (Exception $e) {
echo $e->getMessage();
}
$client->endSession($session);
echo '<pre>';
print_r($result); */

//get vendor order list v2
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/v2_soap?wsdl=1');
$session = $client->login('rahul', 'apikey');
try {
	$result = $client->vendorOrderList($session, 2, '8v385fbc8e7h2j1db1fgpq0mg2');
} catch (Exception $e) {
	echo $e->getMessage();

}
$client->endSession($session);
echo '<pre>';
print_r($result); */



//get vendor order info v1
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/soap/?wsdl=1');
 $session = $client->login('rahul', 'apikey');
try {
$result = $client->call($session, 'vendor_order.info', array(2, '8v385fbc8e7h2j1db1fgpq0mg2', 100000014));
} catch (Exception $e) {
echo $e->getMessage();
}
$client->endSession($session);
echo '<pre>';
print_r($result); */

//get vendor order info v2
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/v2_soap?wsdl=1');
$session = $client->login('rahul', 'apikey');
try {
	$result = $client->vendorOrderInfo($session, 2, '8v385fbc8e7h2j1db1fgpq0mg2', 100000014);
} catch (Exception $e) {
	echo $e->getMessage();

}
$client->endSession($session);
echo '<pre>';
print_r($result); */



//vendor login v1
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/soap/?wsdl=1');
$session = $client->login('rahul', 'apikey');
try {
	$result = $client->call($session, 'vendor.login', array('base','connect@cedcommerce.com','connect@cedcommerce.com'));
} catch (Exception $e) {
	echo $e->getMessage();

}
$client->endSession($session);
echo '<pre>';
print_r($result); */

//vendor login v2
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/v2_soap?wsdl=1');
$session = $client->login('rahul', 'apikey');
$result = $client->vendorLogin($session,'base','connect@cedcommerce.com','123456');
$client->endSession($session);
echo '<pre>';
print_r($result); */



//vendor logout v1
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/soap/?wsdl=1');
$session = $client->login('rahul', 'apikey');
try {
	//$result = $client->call($session, 'vendor.logout', array($result['session'], $result['vendor_id']));
	$result = $client->call($session, 'vendor.logout', array('csbjmea0gofda909g0n2v9nfs5',2));
} catch (Exception $e) {
	echo $e->getMessage();

}
$client->endSession($session);
echo '<pre>';
print_r($result); */

//vendor logout v2
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/v2_soap?wsdl=1');
$session = $client->login('rahul', 'apikey');
$result = $client->vendorLogout($session,'8v385fbc8e7h2j1db1fgpq0mg2','2');
$client->endSession($session);
echo '<pre>';
print_r($result); */


//get vendor links v1
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/soap/?wsdl=1');
$session = $client->login('rahul', 'apikey');
try {
	$result = $client->call($session, 'vendor.getVendorLinks', array(7, 'pcj4f6hgjlit4rp4c1fpkliam2'));
} catch (Exception $e) {
	echo $e->getMessage();

}
$client->endSession($session);
echo '<pre>';
print_r($result); */

//get vendor links v2
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/v2_soap?wsdl=1');
$session = $client->login('rahul', 'apikey');
try {
	$result = $client->vendorGetVendorLinks($session, 7, 'dgq225jcr3itqloft3vo4tval3');
} catch (Exception $e) {
	echo $e->getMessage();
}
//$client->endSession($session);
echo '<pre>';
print_r($result); */


//vendor transactions list v1
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/soap/?wsdl=1');
$session = $client->login('rahul', 'apikey');
try {
	$result = $client->call($session, 'vendor_order.payments', array(7, 'b8dbb92l2iq66nd415mkb8sv31'));
} catch (Exception $e) {
	echo $e->getMessage();

}
$client->endSession($session);
echo '<pre>';
print_r($result); */

//vendor transactions list v2
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/v2_soap?wsdl=1');
$session = $client->login('rahul', 'apikey');
try {
	$result = $client->vendorOrderPayments($session, 7, 'pcj4f6hgjlit4rp4c1fpkliam2');
} catch (Exception $e) {
	echo $e->getMessage();
}
$client->endSession($session);
echo '<pre>';
print_r($result); */


//Save Vendor Settings v1
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/soap/?wsdl=1');
 $session = $client->login('rahul', 'apikey');
try {
$result = $client->call($session, 'vendor_setting.save', array(7, 'dgq225jcr3itqloft3vo4tval3', 'payment', array('vcheque'=>array('active'=>1, 'cheque_payee_name'=>'Himanshu Sahu Again'),
																												'vpaypal'=>array('active'=>1, 'paypal_email'=>'connect@cedcommerce.com/cedcoss'))
																)
					  );
} catch (Exception $e) {
echo $e->getMessage();
}
$client->endSession($session);
echo '<pre>';
print_r($result); */


//Save Vendor Settings v2
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/v2_soap?wsdl=1');
$session = $client->login('rahul', 'apikey');
$vendorData = array('vcheque'=>array('active'=>1, 'cheque_payee_name'=>'Himanshu Sahu'),
				'vpaypal'=>array('active'=>1, 'paypal_email'=>'connect@cedcommerce.com'));
try {
$result = $client->vendorSettingSave($session, 7, '7436aehvhjq5vtqpcg8nl5vuh1', 'payment', $vendorData);
} catch (Exception $e) {
echo $e->getMessage();
}
$client->endSession($session);
echo '<pre>';
print_r($result); */


//Vendor Product Report v1
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/soap/?wsdl=1');
$session = $client->login('rahul', 'apikey');
try {
	$result = $client->call($session, 'vendor_report.getProductReport', array(7, 'dgq225jcr3itqloft3vo4tval3', '10/6/15', '11/6/15'));
} catch (Exception $e) {
	echo $e->getMessage();
}
$client->endSession($session);
echo '<pre>';
print_r($result); */

//Vendor Product Report v2
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/v2_soap?wsdl=1');
$session = $client->login('rahul', 'apikey');
try {
	$result = $client->vendorReportGetProductReport($session, 7, 'dgq225jcr3itqloft3vo4tval3', '10/6/15', '11/6/15');
} catch (Exception $e) {
	echo $e->getMessage();
}
$client->endSession($session);
echo '<pre>';
print_r($result); */



//Vendor Order Report v1
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/soap/?wsdl=1');
$session = $client->login('rahul', 'apikey');
try {
	$result = $client->call($session, 'vendor_report.getOrderReport', array(7, 'dgq225jcr3itqloft3vo4tval3', 'day', '10/6/15', '11/6/15', 'all'));
} catch (Exception $e) {
	echo $e->getMessage();
}
$client->endSession($session);
echo '<pre>';
print_r($result); */

//Vendor Order Report v2
/* $client = new SoapClient('http://192.168.0.15/himanshu/training/magento_marketplace/api/v2_soap?wsdl=1');
$session = $client->login('rahul', 'apikey');
try {
	$result = $client->vendorReportGetOrderReport($session, 7, 'dgq225jcr3itqloft3vo4tval3', 'day', '10/6/15', '11/6/15', 'all');
} catch (Exception $e) {
	echo $e->getMessage();
}
$client->endSession($session);
echo '<pre>';
print_r($result); */

?>