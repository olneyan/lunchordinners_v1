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
  * @package   Ced_CustomApi
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_CustomApi_SearchController extends Ced_Mobiconnect_Controller_Action {
	
	public function suggestionAction() {
    	
		$Name = $this->getRequest()->getParam('name'); 
		//$Name = 'pizza';
		
		if(empty($Name))
		{
			return;
			// $data=['data'=>array('success'=>false,'message'=>'please ')];
			// $this->printJsonData ($data);
		}
		
		$collection = Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToSelect('*')->
		addAttributeToFilter('name',['like' => '%'.$Name.'%']);
		
		if(count($collection->getData()) >  0)
		{
			$html =[];$names = [];
			foreach($collection->getData() as $val){
				$names[]= $val['name'];
			}
			$vals = array_count_values($names);
			foreach($vals as $k=>$v)
			{
				if($v >1)
					{
						//$html .= '<div onclick="pickval('."'".$k."'".')">'.$k." (".$v.")".'</div>';
						$html[]= $k;
					}
					else{
						$html[]= $k;
					}
			}
			
			 $data=['data'=>array('success'=>true,'search'=>$html)];
			 $this->printJsonData ($data);
		}
		if(count($collection->getData()) ==  0)
		{
			$collection = Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToSelect('*')->
			addAttributeToFilter('public_name',['like' => '%'.$Name.'%']);
			if(count($collection->getData()) > 0)
			{
				$html = [];$publicName=[];
				foreach($collection->getData() as $val){
					$publicName[]= $val['public_name'];
				
				}
				$vals = array_count_values($publicName);
				foreach($vals as $k=>$v)
				{
					if($v >1)
						{
							//$html .= '<div onclick="pickval('."'".$k."'".')">'.$k." (".$v.")".'</div>';
							$html[]= $k;
						}
						else{
							$html[]= $k;
						}
				}
				$data=['data'=>array('success'=>true,'search'=>$html)];
			    $this->printJsonData ($data);
			}
			
			
		}	
		if(count($collection->getData()) ==  0)
		{
			$collection = Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToSelect('*')
			->addAttributeToFilter('status',array('eq'=>Ced_CsMarketplace_Model_Vendor::VENDOR_APPROVED_STATUS))
			->addAttributeToFilter('zip_code',['like' => $Name.'%']);
			//print_r()
			if(count($collection->getData()) >  0)
			{
				$html = [];$zips=[];
				foreach($collection->getData() as $val){
					$zips[]= $val['zip_code'];
					
				}
				$vals = array_count_values($zips);
				foreach($vals as $k=>$v)
				{
					if($v >1)
						{
							//$html .= '<div onclick="pickval('."'".$k."'".')">'.$k." (".$v.")".'</div>';
							$html[]= $k;
						}
						else{
							$html[]= $k;
						}
				}
				$data=['data'=>array('success'=>true,'search'=>$html)];
			    $this->printJsonData ($data);
			}
			
			
		}
		if(count($collection->getData()) ==  0)
		{
			$collection = Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToSelect('*')->
			addAttributeToFilter('address',['like' => '%'.$Name.'%']);
			$html =[];$address=[];
			if(count($collection->getData()) > 0)
			{
				foreach($collection->getData() as $val){
					$address[]=$val['address'];
					
				}
				$vals = array_count_values($address);
				foreach($vals as $k=>$v)
				{
						if($v >1)
						{
							//$html .= '<div onclick="pickval('."'".$k."'".')">'.$k." (".$v.")".'</div>';
							$html[]= $k;
						}
						else{
							$html[]= $k;
						}
				}
				
				$data=['data'=>array('success'=>true,'search'=>$html)];
			    $this->printJsonData ($data);
				
			}
		}
		if(count($collection->getData()) ==  0)
		{
			$collection = Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToSelect('*')->
			addAttributeToFilter('landmark',['like' => '%'.$Name.'%']);
			if(count($collection->getData()) > 0)
			{
				$html =[];$landmarks=[];
				foreach($collection->getData() as $val){
					$landmarks[] =$val['landmark'] ;
				}
				$vals = array_count_values($landmarks);
				
			    foreach($vals as $k=>$v)
				{
					$values = explode(',',$k);
					
					for($i=0;$i<count($values);$i++)
					{  
						$param = strtolower($this->getRequest()->getParam('name'));
						
						if(strpos(strtolower($values[$i]), $this->getRequest()->getParam('name')) !== false)
						{
							$html[]= $values[$i];
							
							
						}
						
					}
					break;
				  
				}
				$data=['data'=>array('success'=>true,'search'=>$html)];
			    $this->printJsonData ($data);
			    
			}
		}
		if(count($collection->getData()) ==  0){
			
			 $data=['data'=>array('success'=>false,'message'=>'no suggestion found')];
			 $this->printJsonData ($data);
		}
		
		
    }
}
	