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
 * @package     Ced_Groupgift
 * @author 		CedCommerce Core Team <Ced_MagentoCoreTeam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ced_CsMarketplace_SearchController extends Mage_Core_Controller_Front_Action
{

	
	
	/**
	 * Action for getting the customers email address
	 *
     * Get the customer email suggestion
     */
	
	public function tttsuggestionAction()
    {
    	
		$collection = Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToSelect('*')->
		addAttributeToFilter(array(array('attribute' => 'name','like' => $this->getRequest()->getParam('name').'%'),array('attribute' => 'public_name','like' => $this->getRequest()->getParam('name').'%')))				   
					->addAttributeToSort('name', 'ASC')->setPageSize(5);				
		$html = '';
		foreach($collection->getData() as $val){	
			$html .= '<div onclick="pickval('."'".$val['name']."'".')">'.$val['name'].'</div>';
		}
		echo $html;
    }
    
    
   public function suggestionAction()
    {
    	//print_r(Mage::getModel('csmarketplace/vendor')->load(10)->getData());die("fg");
		$collection = Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToSelect('*')->
		addAttributeToFilter('name',['like' => '%'.$this->getRequest()->getParam('name').'%']);
		if(count($collection->getData()) >  0)
		{
			$html = '<ul class="nav">';$names = [];
			foreach($collection->getData() as $val){
				$names[]= $val['name'];
			}
			$vals = array_count_values($names);
			foreach($vals as $k=>$v)
			{
				if($v >1)
					{
						//$html .= '<div onclick="pickal('."'".$k."'".')">'.$k." (".$v.")".'</div>';
						$html .= '<li  onclick="pickval('."'".$k."'".')" class="sugg">'.$k.'</li>';
					}
					else{
						$html .= '<li  onclick="pickval('."'".$k."'".')" class="sugg">'.$k.'</li>';
					}
			}
			    $html .='</ul>';
				echo $html;
		}
		 if(count($collection->getData()) ==  0)
		{
			$collection = Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToSelect('*')->
			addAttributeToFilter('public_name',['like' => '%'.$this->getRequest()->getParam('name').'%']);
			if(count($collection->getData()) > 0)
			{
				$html = '<ul class="nav">';$publicName=[];
				foreach($collection->getData() as $val){
					$publicName[]= $val['public_name'];
				
				}
				$vals = array_count_values($publicName);
				foreach($vals as $k=>$v)
				{
					if($v >1)
						{
							//$html .= '<div onclick="pickval('."'".$k."'".')">'.$k." (".$v.")".'</div>';
							$html .= '<li class="sugg" onclick="pickval('."'".$k."'".')">'.$k.'</div>';
						}
						else{
							$html .= '<li class="sugg" onclick="pickval('."'".$k."'".')">'.$k.'</div>';
						}
				}
				$html .='</ul>';
				echo $html;
			}
			
			
		}	
		if(count($collection->getData()) ==  0)
		{
			$collection = Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToSelect('*')
			->addAttributeToFilter('status',array('eq'=>Ced_CsMarketplace_Model_Vendor::VENDOR_APPROVED_STATUS))
			->addAttributeToFilter('zip_code',['like' => $this->getRequest()->getParam('name').'%']);
			//print_r()
			if(count($collection->getData()) >  0)
			{
				$html = '<ul class="nav">';$zips=[];
				foreach($collection->getData() as $val){
					$zips[]= $val['zip_code'];
					
				}
				$vals = array_count_values($zips);
				foreach($vals as $k=>$v)
				{
					if($v >1)
						{
							//$html .= '<div onclick="pickval('."'".$k."'".')">'.$k." (".$v.")".'</div>';
							$html .= '<li  onclick="pickval('."'".$k."'".')" class="sugg">'.$k.'</li>';
						}
						else{
							$html .= '<li  onclick="pickval('."'".$k."'".')" class="sugg">'.$k.'</li>';
						}
				}
				$html .='</ul>';
				echo $html;
			}
			
			
		}
		if(count($collection->getData()) ==  0)
		{
			$collection = Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToSelect('*')->
			addAttributeToFilter('address',['like' => '%'.$this->getRequest()->getParam('name').'%']);
			$html = '<ul class="nav">';$address=[];
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
							$html .= '<li  onclick="pickval('."'".$k."'".')" class="sugg">'.$k.'</li>';
						}
						else{
							$html .= '<li  onclick="pickval('."'".$k."'".')" class="sugg">'.$k.'</li>';
						}
				}
				$html .='</ul>';
				echo $html;
				
			}
		}
		if(count($collection->getData()) ==  0)
		{
			$collection = Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToSelect('*')->
			addAttributeToFilter('landmark',['like' => '%'.$this->getRequest()->getParam('name').'%']);
			if(count($collection->getData()) > 0)
			{
				$html = '<ul class="nav">';$landmarks=[];
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
							$html .= '<li  onclick="pickval('."'".$values[$i]."'".')" class="sugg">'.$values[$i].'</li>';
							//$html .= '<div onclick="pickval('."'".$values[$i]."'".')">'.$values[$i].'</div>';
							
						}
						
					}
					break;
				  
				}
				$html .='</ul>';
				  echo $html;
			    
			}
		}
		
/* 	if(count($collection->getData()) ==  0)
		{
			$collection = Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToSelect('*')->
			addAttributeToFilter('city',['like' => '%'.$this->getRequest()->getParam('name').'%']);
			if(count($collection->getData()) > 0)
			{
				$html = '';$cities=[];
				foreach($collection->getData() as $val){
					$cities[] = $val['city'];
					
				}
				$vals = array_count_values($cities);
				
				foreach($vals as $k=>$v)
				{
					if($v >1)
					{
						//$html .= '<div onclick="pickval('."'".$k."'".')">'.$k." (".$v.")".'</div>';
						$html .= '<div onclick="pickval('."'".$k."'".')">'.$k.'</div>';
					}
					else{
						$html .= '<div onclick="pickval('."'".$k."'".')">'.$k.'</div>';
					}
				    
				}
					
				echo $html;
			}
		} */
		
		//print_r($collection->getData());die("gbhg");			
		
    }
	
	/**
     * Return the object of the core session
     */
	
	public function _getSession()
	{
		return Mage::getSingleton('core/session');
	}
	
}
