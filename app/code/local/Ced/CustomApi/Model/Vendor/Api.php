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

class Ced_CustomApi_Model_Vendor_Api extends Ced_VendorApi_Model_Vendor_Api
{
	
	
	public function items($data)
	{
		
		if(isset($data['sellersearch'])){
			$search = $data['sellersearch'];
			$data['search'] = json_decode($search,true);
		}

		$limit ='5';$offset='';
		if(isset($data['page']))
		$offset = $data['page'];
		$curr_page = '1';
		
		if ($offset != 'null' && $offset != '') {
			$curr_page = $offset;
		}

		$offset = ($curr_page - 1) * $limit;
		$helper = Mage::helper('csmarketplace/tool_image');

		$img = Mage::getStoreConfig("ced_vshops/general/vshoppage_banner",Mage::app()->getStore()->getId())?"ced/csmarketplace/".Mage::getStoreConfig("ced_vshops/general/vshoppage_banner",Mage::app()->getStore()->getId()):'';

		//echo $img;die;
		
		$vendorIds = array ();
		$model = Mage::getModel ( 'csmarketplace/vshop' )->getCollection ()->addFieldToFilter ( 'shop_disable', array (
				'eq' => Ced_CsMarketplace_Model_Vshop::DISABLED
		) );
		if (count ( $model ) > 0) {
			foreach ( $model as $row ) {
				$vendorIds [] = $row->getVendorId ();
			}
		}
		$collection = Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('status','approved');


		if (count ( $vendorIds ) > 0) {
			$collection = $collection->addAttributeToFilter ( 'entity_id', array (
					'nin' => $vendorIds
			) );
		}
		
		/**
		 * customization for Ilyas 
		 * 
		 */
		
		$rest =$data['rest'];
			
		$validIds = [ ];
		$disatanceArray = [ ];
		
		if (strlen ( $rest )) {
			
			//echo $apikey; die("dfk");
			$origin = str_replace ( ' ', '', $rest );
			
			$address_url = "http://maps.google.com/maps/api/geocode/json?address=$origin&sensor=false";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $address_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$response = curl_exec($ch);
			curl_close($ch);
			$response_a = json_decode($response);
			$lat1 ="";$lon1="";
			if(isset($response_a->results[0]))
			{
				$lat1 = $response_a->results[0]->geometry->location->lat;
				$lon1 = $response_a->results[0]->geometry->location->lng;
			}
			
			//echo $lat1; echo "<br>"; echo $lon1; echo "<br>";die("fkj");
			if($lat1)
			{$i = 0;
				foreach ($collection as $val ) {
					
				    $lat2=  $val->getStoreLatitude();
				    $lon2 =  $val->getStoreLongitude();
				    if(!$lat2)
				    {
				    	continue;
				    }
// 				   if($val->getEntityId() != 58)
//                     {
//                        continue;
//                     }
				  //  echo $lat2; echo "<br>"; echo $lon2; echo "<br>";die("fkj");
				    $unit ='K';
					$theta = $lon1 - $lon2;
					$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
					
					$dist = acos($dist);
					$dist = rad2deg($dist);
                    //$miles = $dist * 60 * 1.4915; 1.1515
					$miles = $dist * 60 * 1.1515 ;
					$unit = strtoupper($unit);
				    
				    if ($unit == "K") {
				    	$Adist =  $miles * 1.609344;
				    
				    	if ($Adist <= $val->getDeliveryRadius()) {
				    		$validIds [] = $val->getEntityId ();
				    		$disatanceArray [$i] ['id'] = $val->getEntityId ();
				    		$disatanceArray [$i] ['distance'] = round($Adist,2)." "."km";
				    		$i++;
				    	}
				    }
					
				}
			}
		}
		
		
		//print_r($disatanceArray);echo "<br>";print_r($validIds); die("dfkjv");
	//	print_r($collection->getSize());echo "<br>";
	//	print_r($validIds); echo "<br>";
		if(strlen($rest))
		{
		 $collection = Mage::getModel('csmarketplace/vendor')
			->getCollection()
			->addAttributeToSelect('*')->addFieldToFilter('entity_id', array (
					'in' => $validIds
			) )->addAttributeToSort('sponsored', 'desc');
		}
		//print_r($collection->getSize());die("fdkvj");
		$fgroup = $data['fgroup'];
		if(strlen($fgroup))
		{
			//print_r($this->_vendorCollection->getIds()); die("fkj");
			 
			$vColllection = Mage::getModel('csmarketplace/vendor')
			->getCollection()
			->addAttributeToSelect('*')
			->addAttributeToFilter('status',array('eq'=>Ced_CsMarketplace_Model_Vendor::VENDOR_APPROVED_STATUS));
			
			$vColllection->addAttributeToSelect ('*')->addAttributeToFilter ('status', array (
					'eq' => Ced_CsMarketplace_Model_Vendor::VENDOR_APPROVED_STATUS 
			) )->addAttributeToFilter ('entity_id', [ 
					'in' => $validIds 
			] );
			 
			$ids = [];$validId =[];
			foreach ($vColllection as $key=>$val)
			{
				$ids[] = $val->getId();
			}
			//print_r($ids); die("dfkhj");
			for($i=0;$i<count($ids);$i++)
			{
				$fGroup = Mage::getModel('csmarketplace/vendor')->load($ids[$i])->getFoodGroup();
				$fGroup = explode(',',$fGroup);
				if (in_array($fgroup, $fGroup))
				{
				   $validId[] = $ids[$i];
				}
			}
			//print_r($validId); die("kjf");
			$collection->addFieldToFilter('entity_id',array('in'=>$validId));
			
			
			$j=0;
			foreach ($disatanceArray as $k=>$v)
			{
				if (!in_array($v['id'], $validId))
				{
					//print_r($v);die("dfkj");
					unset($disatanceArray[$j]);
						
				}
				$j++;
			}
			// print_r($this->_vendorCollection->getData()); die("lfg");
			$disatanceArray = array_values($disatanceArray);
			 
			//print_r($this->_vendorCollection->getData()); die("lfg");
		}
		
		if(strlen($rest)==1)
		{
			$collection->addAttributeToFilter('entity_id',0);
		}
		
		
		
		
	//	$collection->getSelect()->limit($limit,$offset);
		/*if (is_array($filters)) {
			try {
				foreach ($filters as $field => $value) {
					$collection->addFieldToFilter($field, $value);
				}
			} catch (Mage_Core_Exception $e) {
				$this->_fault('filters_invalid', $e->getMessage());
			}
		}*/
		$vendors = array();
		$count = 0;
		
		if(strlen($rest))
		{
			foreach ($collection as $key => $value) {
				$vids[] = $value->getEntityId();
			}
				
			for($i=0;$i<count($vids);$i++)
			{
				$fgroup = Mage::getModel('csmarketplace/vendor')->load($vids[$i])->getFoodGroup();
				if(!empty($fgroup))
				{
				   $fgroups[] = $fgroup;
				}
					
			}
		}
		 $totals =[];
		 foreach ($fgroups as $_filter){ 
		             
		   $values = $this->getValue($_filter,$data,$validIds); 
		//print_r($values); die("jd");
		$vals = explode(',',$values);
		$totals = array_merge($vals,$totals);
		}
		  $totals = array_filter($totals); 
		  $totals = array_unique($totals);
		  $totals = array_values($totals);
		
		  
		foreach ($collection as $vendor) { 
			$changeData = new Varien_Object ();
			$changeData->setResponse ($vendor);
			$changeData->setIsVendorList('1');
			Mage::dispatchEvent ( 'get_vendor_review', array (
							'vendor_info' => $changeData 
					) );
		//	print_r($vendor->toArray()); die("fkj");
			$vendorData = $vendor->toArray();
			foreach ($vendor->toArray() as $k=>$v)
			{
			 if(strpos($k, 'time') !== false) 
			  {
				$day = Mage::getSingleton('core/date')->date('w');
			
				if($day ==0)
				{
					if (strpos($k, 'sun_close_time') !== false) {
							
						$value= $v;
						$value = explode(',',$value);
						$value = implode(':',$value);
						 
						$value = date("h:i a", strtotime($value));
						
					}
					else{
						continue;
					}
			
				}
				if($day ==1)
				{
					if (strpos($k, 'mon_close_time') !== false) {
							
						$value= $v;
						$value = explode(',',$value);
						$value = implode(':',$value);
						 
						$value = date("h:i a", strtotime($value));
					}
					else{
						continue;
					}
			
				}
				if($day ==2)
				{
					if (strpos($k, 'tue_close_time') !== false) {
							
						$value= $v;
						$value = explode(',',$value);
						$value = implode(':',$value);
						 
						$value = date("h:i a", strtotime($value));
					}
					else{
						continue;
					}
			
				}
				if($day ==3)
				{
					if (strpos($k, 'wed_close_time') !== false) {
							
						$value= $v;
						$value = explode(',',$value);
						$value = implode(':',$value);
						 
						$value = date("h:i a", strtotime($value));
					}
					else{
						continue;
					}
			
				}
				if($day ==4)
				{
					if (strpos($k, 'thu_close_time') !== false) {
							
						$value= $v;
						$value = explode(',',$value);
						$value = implode(':',$value);
						 
						$value = date("h:i a", strtotime($value));
					}
					else{
						continue;
					}
			
				}
				if($day ==5)
				{
					if (strpos($k, 'fri_close_time') !== false) {
							
						$value= $v;
						$value = explode(',',$value);
						$value = implode(':',$value);
						 
						$value = date("h:i a", strtotime($value));
					}
					else{
						continue;
					}
			
				}
				if($day ==6)
				{
					if (strpos($k, 'sat_close_time') !== false) {
							
						$value= $v;
						$value = explode(',',$value);
						$value = implode(':',$value);
						 
						$value = date("h:i a", strtotime($value));
					}
					else{
						continue;
					}
			
				}
			
				$vendorData['open_until']= $value;
			 }
			 if($k=="min_freeship")
			 {
			 	$currency = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
			 	
			 	$vendorData['min_delivery_order']= $currency.$v;
			 }
			 if($k=="delivery_fee")
			 {
			 	$currency = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
			 	if($v)	
			 	{
			 		$vendorData['delivery_charges']= $currency.$v;
			 	}
			 	else{
			 		$vendorData['delivery_charges']= "free";
			 	}
			 	
			 }
			}
			//print_r($vendorData); die("fk");
			$vendors[] = $vendorData;

			$url = (isset($vendors[$count]['company_logo']) && $vendors[$count]['company_logo'])? $vendors[$count]['company_logo']:'';
			$newurl= ($url=='')?Mage::getBaseUrl():$helper->init($url)->__toString ();
			$vendors[$count]['company_logo']= $newurl;

			$banner_url = (isset($vendors[$count]['company_banner']) && $vendors[$count]['company_banner']) ? $vendors[$count]['company_banner']:'';
			$vendors[$count]['company_banner']=Mage::getBaseUrl('media').$banner_url;

			$profile_url = (isset($vendors[$count]['profile_picture']) && $vendors[$count]['profile_picture']) ? $vendors[$count]['profile_picture']:'';

			$vendors[$count]['profile_picture']=($profile_url=='')? Mage::getBaseUrl():$helper->init($profile_url)->__toString ();
			$collection11 = Mage::getModel('csmarketplace/vproducts')->getCollection()->addFieldToFilter('check_status','1')->addFieldToFilter('vendor_id',$vendors[$count]['entity_id']);
			$vendors[$count]['product_count'] = count($collection11->getData());
			//$newurl='';
			++$count;	
		}
		$response = array();

		if(count($vendors) && count($validIds)){
			$response['data']['sellers']=$vendors;
			$response['data']['banner_url']=($img=='')?Mage::getBaseUrl():$helper->init($img,'csbanner')->__toString ();
			$response['data']['success']=true;
			$response['data']['fgroups']=$totals;
			$response['data']['distance']=$disatanceArray;
		}
		elseif(strlen($rest) && !count($validIds)){
			$response['data']['sellers']='';
			$response['data']['banner_url']=($img=='')?Mage::getBaseUrl():$helper->init($img,'csbanner')->__toString ();
			$response['data']['message']='Sorry!! No vendor available.';
			$response['data']['success']=false;
		}
		else{
			$response['data']['sellers']=$vendors;
			$response['data']['banner_url']=($img=='')?Mage::getBaseUrl():$helper->init($img,'csbanner')->__toString ();
			$response['data']['success']=true;
			$response['data']['fgroups']=$totals;
			$response['data']['distance']=$disatanceArray;
		}
		
		return $response;
	}
	
	
	public function getValue($data,$params,$validIds)
	{
	
		$data = explode(',', $data);
	
	
		$attribute = Mage::getSingleton('eav/config')
		->getAttribute('csmarketplace_vendor','food_group');
	
		if ($attribute->usesSource()) {
			$options = $attribute->getSource()->getAllOptions(false);
		}
		 
		$result ='';
		foreach ($options as $key => $value) {
			 
	
	
			for($i=0;$i<count($data);$i++) {
	
				if($value['value'] == $data[$i])
				{
					$vids = $this->getVendorColl($params,$validIds);
	
					$products = Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToFilter(
							array(
									array('attribute'=> 'food_group','finset' => array($data[$i]))
							))->addFieldToFilter('entity_id',['in'=>$vids]);
					// 	echo count($products->getData()); die("dfvkj");
					//echo "<pre>";		print_r($vids); echo "<br>";print_r($products->getData());echo "</pre>";die("dfjv");
					$count = count($products->getData());
					 
					//echo $value['value'];echo "<br>";
					$result.= $value['value'].":".$value['label'].'('.$count.')'.',' ;
				}
							else{
									continue;
				}
											 
				}
				}
	
				return $result;
					 
				 
			}
			public function getVendorColl($params,$validIds)
			{
			
				$vids = [];$fgroups =[];
				$vendor = Mage::getModel('csmarketplace/vendor')
				->getCollection()
				->addAttributeToSelect('*')
				->addAttributeToFilter('status',array('eq'=>Ced_CsMarketplace_Model_Vendor::VENDOR_APPROVED_STATUS));
			
				$rest = $params['rest'];
				if(strlen($rest) < 2)
				{
					$rest = "";
				}
			
				if(strlen($rest))
				{
					$vendor->addAttributeToSelect ('*')->addAttributeToFilter ('status', array (
					'eq' => Ced_CsMarketplace_Model_Vendor::VENDOR_APPROVED_STATUS 
			          ) )->addAttributeToFilter ('entity_id', [ 
					    'in' => $validIds 
			           ] );
				}
			
				foreach ($vendor as $key => $value) {
					$vids[] = $value->getId();
				}
				//print_r($vids); die("dflk");
				if(strlen($rest) == 1)
				{
					return $arr =[];
				}
				else{
					return $vids;
				}
			
			}	
			
	
	
}

