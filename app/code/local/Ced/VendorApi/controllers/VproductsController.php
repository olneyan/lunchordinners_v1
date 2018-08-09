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
 
	class Ced_VendorApi_VproductsController extends Ced_VendorApi_Controller_Abstract
	{
		/*get all product for particulr vendor id*/

		public function itemAction(){
			$data  = $this->getRequest()->getParams();
			$data['filter'] =  $this->getRequest()->getParam('filter',json_encode(array()));
			//Mage::log($data,null,' vprofil.log');
			$validate=array(
					'vendor_id'=>$this->getRequest()->getParam('vendor_id'),
					'hash'=>$this->getRequest()->getParam('hashkey')
				);
			$validateRequest=$this->validate($validate);
			$vendorProducts = Mage::getModel('vendorapi/product_api')->vproduct($data);

			if(is_array($vendorProducts)){
				$response=json_encode($vendorProducts);
				$this->getResponse()->setBody($response);
			}
		}
		/*get all product for particulr vendor id for Vshops*/
		public function vshopsAction()
		{    
			$data  = $this->getRequest()->getParams();
			//$vsession = 'b8aq8l3eh95oialb0v7d1rqnf4';
			//Mage::log($data,null,'vendorsnewell.log');
			$layer = Mage::getSingleton( "csmarketplace/vshop_layer" );
			$rootCatId = Mage::app()->getWebsite(1)->getDefaultStore()->getRootCategoryId();
			$category = Mage::getModel ( "catalog/category" )->load ($rootCatId);
			$layer->setCurrentCategory ( $category );
			$attributes = $layer->getFilterableAttributes ();
			$filterableAttributes=[];$filterlabel=[];
			foreach ( $attributes as $attribute ) { 
				if ($attribute->getAttributeCode () == 'price') {
					if ($attribute->getAttributeCode () == 'price') {
						$filterBlockName = 'catalog/layer_filter_price';
					} elseif ($attribute->getBackendType () == 'decimal') {
						$filterBlockName = 'catalog/layer_filter_decimal';
					} else {
						$filterBlockName = 'catalog/layer_filter_attribute';
					}
					$result = $this->getLayout ()->createBlock ( $filterBlockName )->setLayer ( $layer )->setAttributeModel ( $attribute )->init ();
					$attCode = $attribute->getAttributeCode ();
					$attlabel=$attribute->getStoreLabel ();
					foreach ( $result->getItems () as $option ) {
						$attOptions [] = strip_tags($option->getValue () . '#' . $option->getLabel ());
					}
					if (count ( $attOptions ) > 0)
						$filterableAttributes [] = array (
								$attCode => array_values ( $attOptions )
						);
						$filterlabel[]=array(
								'att_code'=>$attCode,
								'att_label'=>$attlabel
								);
					$attOptions = array ();
				}
			}
			
			$vendorProducts = Mage::getModel('vendorapi/product_api')->items($data);
//print_r($vendorProducts); die("fhjv");
			if(is_array($vendorProducts)){
				//Mage::log('controller if',null,'vendorsell.log');
				$vendorProducts ['data'] ['filter'] = $filterableAttributes;
				$vendorProducts['data']['filter_label']=$filterlabel;
				$response=json_encode($vendorProducts);
				$this->getResponse()->setBody($response);
			}
			else{
				echo $vendorProducts;
				exit();
			}
		}

		/*fetch info related to product*/
		 public function infoAction()
		 {
		 	$vendorId  = $this->getRequest()->getParam('vendor_id') ;
		    $productId = $this->getRequest()->getParam('product_id') ;
		    $validate=array(
					'vendor_id'=>$this->getRequest()->getParam('vendor_id'),
					'hash'=>$this->getRequest()->getParam('hashkey')
				);
			$validateRequest=$this->validate($validate);
		 	$productInfo = Mage::getModel('vendorapi/product_api')->info($vendorId,$productId);
		 	$response=json_encode($productInfo);
				$this->getResponse()->setBody($response);
		 }

		 /*creating product*/
		 public function createAction()
		 {
		 	$data=$this->getRequest()->getParams();
		 	$validate=array(
					'vendor_id'=>$this->getRequest()->getParam('vendor_id'),
					'hash'=>$this->getRequest()->getParam('hashkey')
				);
			$validateRequest=$this->validate($validate);
		 	$vendorId  = $data['vendor_id']; 
		 	$type =$data['type'];
		 	$set = $data['set'];
		 	$sku =$data['product_sku'];
		 	$categoryIds = json_decode($data['category']);
		 	$websiteIds = json_decode($data['websites']);
		 	
		 	//Mage::log($data,null,'advnattribute.log');
		 	$productData = array(
		 		'name'=>$data['product_name'],
		 		'status'=>1,
		 		'stock_data'=>array(
		 			'qty' =>$data['stock'],
		 			'is_in_stock'=>$data['stock_avail']
		 			),
		 		'website_ids'=>$websiteIds,
		 		'tax_class_id'=>$data['tax_class'],
		 		'price'=>$data['price'],
		 		'weight'=>$data['weight'],
		 		'short_description'=>$data['shortdescription'],
		 		'description'=>$data['description'],
		 		'categories'=>$categoryIds,
		 		'special_price'=>$data['special_price'],
		 		);
		 	//Mage::log($productData,null,'createproduct.log');
		 	$createProduct = Mage::getModel('vendorapi/product_api')->create($vendorId,$type,$set,$sku,$productData);
		 	$response=json_encode($createProduct);
			$this->getResponse()->setBody($response);
		 }
		 /*update product*/
		 public function updateAction()
		 {
		 	$data = $this->getRequest()->getParams();

		 	if (!function_exists('getallheaders')) { 
	        	foreach($_SERVER as $key=>$value) { 
		            if (substr($key,0,5)=="HTTP_") { 
		                $key=str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5))))); 
		                $head[$key]=$value; 
		            } else { 
	                	$head[$key]=$value; 
	        		} 
            	} 
        	} 

        	/*Source Github Darkheir*/
        	else { 
				$head = getallheaders ();
			}

		 	$categoryIds = json_decode($data['category']);
		 	$websiteIds = json_decode($data['websites']);
		 	$defaultimage=false;
		 	foreach ( $head as $key => $val ) {
				if ($key == "Authorization") {
					if(isset($val))
					$productId = $val;
				}
				if($key=='Content-Type'){
					if(isset($val)){
						$img = explode(';', $val);
		 				if(is_array($img) &&  isset($img[1]) && $img[1]=='default=1'){
		 					//Mage::log('tes',null,'updatepro5.log');
							$defaultimage = '1';
						}
					}
					
				}
			}	

			if($productId=='' || $productId==NULL ){
				$productId = $data['product_id'];
			}
		 	$vendorId  = $data['vendor_id'];
		 	$img=json_decode($data['defaultimage'],true);
		 	$productData = array(
		 		'name'=>$data['product_name'],
		 		'status'=>1,
		 		'stock_data'=>array(
		 			'qty' =>$data['stock'],
		 			'is_in_stock'=>$data['stock_avail']
		 			),
		 		'website_ids'=>$websiteIds,
		 		'tax_class_id'=>$data['tax_class'],
		 		'sku' =>$data['product_sku'],
		 		'price'=>$data['price'],
		 		'weight'=>$data['weight'],
		 		'short_description'=>$data['shortdescription'],
		 		'description'=>$data['description'],
		 		'categories'=>$categoryIds,
		 		'special_price'=>$data['special_price'],
		 		'related_product'=>$data['relatedproducts'],
		 		'up_sell'=>$data['upsellproductids'],
		 		'cross_sell'=>$data['crossellproducts'],
		 		);
		 	if(!isset($productData['name'])){
				$image=$this->saveImages($productId,$productData,$defaultimage);
			}

		 	if(isset($productData['name']) || isset($productData['related_product']) || isset($productData['up_sell']) || isset($productData['cross_sell']) || isset($productData['groupedproducts']) ){
		 		$defaultimage=$img['0'];
			 	$updateProduct = Mage::getModel('vendorapi/product_api')->update($vendorId,$productId,$productData,$defaultimage);
			 	$response=json_encode($updateProduct);
				$this->getResponse()->setBody($response);
			}
			else{
				//Mage::log('inside image else',null,'updatepro5.log');
				if(is_array($image)){
					$response = array (
						'data' => array (
							'product_id' => $productId,
							'message' => 'Product Updated Successfully',
							'success' => True, 
						) 
					);
				}
				else{
					$response = array (
						'data' => array (
							'product_id' => $productId,
							'message' => $image,
							'success' => false, 
						) 
					);
				}
				$response=json_encode($response);
				$this->getResponse()->setBody($response);
				
			}
		 }

		 /*delete product*/
		 public function deleteAction()
		 {
		 	$vendorId  = $this->getRequest()->getParam('vendor_id'); 
		 	$productId = $this->getRequest()->getParam('entity_id'); 
		 	$validate=array(
					'vendor_id'=>$this->getRequest()->getParam('vendor_id'),
					'hash'=>$this->getRequest()->getParam('hashkey')
				);
			$validateRequest=$this->validate($validate);
		 	$deleteProduct = Mage::getModel('vendorapi/product_api')->delete($vendorId,$productId);
		 	/*var_dump($deleteProduct);*/
			$response=json_encode($deleteProduct);
			$this->getResponse()->setBody($response);
		 }
		public function allowedprodataAction(){ 
		 	$allowedProductData = Mage::getModel('vendorapi/product_api')->allowedProductData($vendorId,$productId);
			$response=json_encode($allowedProductData);
			$this->getResponse()->setBody($response);
		}
		public function saveImages($productid, $data,$defaultimage) {
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		$targetDir = Mage::getBaseDir ( 'media' ) . DS . 'ced' . DS . 'csmaketplace' . DS . 'vproducts' . DS . $productid . DS;
		$productModel = Mage::getModel('catalog/product')->load($productid)->setStoreId(0);
		//Mage::log($_FILES,null,'appimagecontrol.log');
		//Mage::log($defaultimage,null,'appimagecontrol.log');
		if($productModel && $productModel->getId()){
			if (isset ( $_FILES['images'] ) && count ( $_FILES['images'] ) > 0) {
					if (isset($_FILES['images']['name'])) {
						//Mage::log('File Exist',null,'appimagecontrol.log');
						$uploader = new Varien_File_Uploader ( "images" );
						$uploader->setAllowRenameFiles ( false );
						$uploader->setFilesDispersion ( false );
						$uploader->setAllowedExtensions ( array (
								'jpg',
								'jpeg',
								'gif',
								'png'
								
						) );
						$image = md5 ( $_FILES['images']['tmp_name'] ).$_FILES['images']['name'];
						try {
							if($result=$uploader->save ( $targetDir, $image )){
								$fetchTarget = $targetDir .$result['file'];
								$productModel->addImageToMediaGallery ( $fetchTarget, array (
									//	'image',
									//	'small_image',
									//	'thumbnail'
								), true, false );
								if ($defaultimage) {
									//Mage::log('Inside If',null,'appimagecontrol.log');
									//Mage::log($result,null,'appimagecontrol.log');
									//Mage::log('**********Default Image Before**************',null,'appimagecontrol.log');
									//Mage::log($defaultimage,null,'appimagecontrol.log');

									$defaultimage = $result['file'];

									//Mage::log($defaultimage,null,'appimagecontrol.log');
								}
							}
						}
						catch ( Exception $e ) {
							$data = $e->getMessage();
							return $data;
						}
					}
				
			}
			$productModel->save();
			// set default image
			if ($defaultimage) {
				//case when no new uploads
				if($defaultimage==''){
						$defaultimage=$data ['defaultimage'];
				}
				if($defaultimage!==''){
					$mediaGallery = $productModel->getMediaGallery();
					//if there are images
					if (isset($mediaGallery['images'])){
						//loop through the images
						foreach ($mediaGallery['images'] as $image){
							//Mage::log('Image check start',null,'appimagecontrol.log');
							//Mage::log($image['file'],null,'appimagecontrol.log');
							//Mage::log($defaultimage,null,'appimagecontrol.log');

							//Mage::log('Image check end',null,'appimagecontrol.log');
							if (strpos($image['file'],$defaultimage) !== false){
								//Mage::log('Media Gallery',null,'appimagecontrol.log');
								//Mage::log($image['file'],null,'appimagecontrol.log');
								//Mage::log('product data',null,'appimagecontrol.log');
								//Mage::log($productid,null,'appimagecontrol.log');
								//Mage::log($productModel->getStoreId(),null,'appimagecontrol.log');
								Mage::getSingleton('catalog/product_action')->updateAttributes(array($productid), array('image'=>$image['file'],'small_image'=>$image['file'],'thumbnail'=>$image['file']), $productModel->getStoreId());
								//Mage::log('****Image File Name*****',null,'appimagecontrol.log');
								//Mage::log($image['file'],null,'appimagecontrol.log');
								//Mage::log($image['file'],null,'appimagecontrol.log');
								break;
							}
						}
					}
				}
				$data = array (
				'data' => array (
					'success' => true ,
					'message'=> 'Image Uploaded'
				) 		
			);
			return $data;
			}
			$data = array (
				'data' => array (
					'success' => true ,
					'message'=> 'Image Uploaded'
				) 		
			);
			return $data;
			/*else{
				Mage::log('******Inside Else*******',null,'appimagecontrol.log');
				Mage::getSingleton('catalog/product_action')->updateAttributes(array($productid), array('image'=>'','small_image'=>'','thumbnail'=>''),$productModel->getStoreId());
			}*/
			//Mage::log('******First Image Upload End*******',null,'appimagecontrol.log');
		}
	}
	public function deleteImageAction(){
		
		$result=0;
		$data= $this->getRequest()->getParams();
		$validate=array(
					'vendor_id'=>$this->getRequest()->getParam('vendor_id'),
					'hash'=>$this->getRequest()->getParam('hashkey')
				);
		$validateRequest=$this->validate($validate);
		$im=$data['imagename'];
		$img= json_decode($im,true);
		$data['imagename'] = $img['imagename'];
		//Mage::log('******Posted Data************',null,'deleteim.log');
		//Mage::log($data,null,'deleteim.log');
		if(!isset($data['vendor_id']) || !isset($data['product_id'])) { 
			$response = array (
						'data' => array (
							'message' => 'Vendor Id or Product Id Is Empty',
							'success' => false, 
						) 
			);
			$response=json_encode($response);
			echo $response;
			exit();
		}
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		try{

			$mediaApi = Mage::getModel("catalog/product_attribute_media_api");
			$items = $mediaApi->items($data['product_id']);
			//Mage::log('******Item Data************',null,'deleteim.log');
			//Mage::log($items,null,'deleteim.log');
			if(is_array($items) && count($items)>0 && isset($data['imagename'])){
	    		foreach($items as $item){
	    			if($item['file']==$data['imagename']){
	        			$mediaApi->remove($data['product_id'], $item['file']);
	        			//Mage::log('mediaapi',null,'deleteim.log');
	    			}
				}
			}
			$product=Mage::getModel('catalog/product')->setStoreId('0')->load($data['product_id']);
			//Mage::log('******Product Data************',null,'deleteim.log');
			//Mage::log($product->getImage(),null,'deleteim.log');
			if($product && $product->getId() && isset($data['imagename'])){
				//if($data['imagename']==$product->getImage()){
					//Mage::log('if',null,'deleteim.log');
					$product->setStoreId(0)->setImage(null)->setThumnail(null)->setSmallImage(null)->save();
					$response = array (
						'data' => array (
							'message' => 'Image Deleted Successfully',
							'success' => true, 
						) 
					);
				//}
			}
		}
	 	catch ( Exception $e ) {
			$response = array (
						'data' => array (
							'message' => $e->getMessage(),
							'success' => false, 
						) 
			);
		}
		$response=json_encode($response);
		$this->getResponse()->setBody($response);	
	}
	}
?>