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
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * CsMarketplace shop list block
 *
 * @category   Ced
 * @package    Ced_CsMarketplace
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Block_Vshops_List extends Mage_Core_Block_Template
{
	/**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'csmarketplace/vshops_list_toolbar';

    /**
     * Product Collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_vendorCollection;

	/**
     * Default product amount per row
     *
     * @var int
     */
    protected $_defaultColumnCount = 4;
	
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    
    protected function getVendorCollection()
    {
    	
    	return Mage::registry('vshop_lists');
    }
    protected function fvfvfgetVendorCollection()
    {
    	
       
        	$vendorIds =array();
        	$model = Mage::getModel('csmarketplace/vshop')->getCollection()->addFieldToFilter('shop_disable',array('eq'=>Ced_CsMarketplace_Model_Vshop::DISABLED));
        	if(count($model)>0){
	        	foreach($model as $row){
	        		$vendorIds[] = $row->getVendorId();
	        	}
        	}
            $this->_vendorCollection = Mage::getModel('csmarketplace/vendor')
											->getCollection()
											->addAttributeToSelect('*')
											->addAttributeToFilter('status',array('eq'=>Ced_CsMarketplace_Model_Vendor::VENDOR_APPROVED_STATUS));
			
			if( ($countryId=$this->getRequest()->getParam('country_id') ) && $this->getRequest()->getParam('country_id')!='')
			{
				$this->_vendorCollection->addAttributeToFilter('country_id',$countryId);
			}
			if(($postCode=$this->getRequest()->getParam('estimate_postcode')) && $this->getRequest()->getParam('estimate_postcode')!='')
			{
				$this->_vendorCollection->addAttributeToFilter('zip_code',$postCode);
			}
			if(($region=$this->getRequest()->getParam('region')) && $this->getRequest()->getParam('region')!='')
			{
				$this->_vendorCollection->addAttributeToFilter('region',$region);
			}
			if(($region_id=$this->getRequest()->getParam('region_id')) && $this->getRequest()->getParam('region_id')!='')
			{
				$this->_vendorCollection->addAttributeToFilter('region_id',$region_id);
			}
			
			if(count($vendorIds)>0)
			{
				$this->_vendorCollection = $this->_vendorCollection->addAttributeToFilter('entity_id',array('nin'=>$vendorIds));
				 
			}
			$char = $this->getRequest()->getParam('char');
			if(strlen($char))
				$this->_vendorCollection->addAttributeToFilter('public_name',array('like'=>'%'.$char.'%'));
			
		
		$rest = $this->getRequest()->getParam('rest');
		$validIds =[];$disatanceArray=[];
			if(strlen($rest))
			{
               $uri ="https://maps.googleapis.com/maps/api/distancematrix/";
				$i= 0;
		
				foreach($this->_vendorCollection as $val)
				{
				
				    $origin = str_replace(' ', '',$rest);
					$desination = str_replace(' ', '',$val->getAddress());
				
						$url = $uri.'json?origins=' . $origin . '&destinations=' . $desination . '&mode=driving&sensor=false&key=AIzaSyCITgXxySbOP2A0OWtAZQ803TIGGgUpUEA&units=' . 'metric';
						//echo $url; die();
						$result = file_get_contents($url);
						
						if(!$result) {
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $url);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
							curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
							$data = curl_exec($ch);
							
						} else {
							$data = json_decode(utf8_encode($result), true);
							
						}
				
						if(isset($data['rows'][0]['elements'][0]['distance']['text'])) {
							
							$distance= $data['rows'][0]['elements'][0]['distance']['text'];
							$distance1 = explode(' ', $distance);
							
							$dist  = str_replace(',', '',$distance1[0]);
							if($dist<=100)
							{
								$validIds[] =$val->getEntityId() ;
								$disatanceArray[$i]['id']=$val->getEntityId();
								$disatanceArray[$i]['distance']=$distance;
								$i++;
							}
							
						}
	    		
						
				}
			}
			
           if(strlen($rest))
			{
				$vcollection = Mage::getModel('csmarketplace/vendor')
				->getCollection()
				->addAttributeToSelect('*')
				->addAttributeToFilter('status',array('eq'=>Ced_CsMarketplace_Model_Vendor::VENDOR_APPROVED_STATUS))
				->addAttributeToFilter('entity_id',['in'=>$validIds]);
			
			}
			
		    $fgroup = $this->getRequest()->getParam('fgroup');
		    if(strlen($fgroup))
		    {  
		    	//print_r($this->_vendorCollection->getIds()); die("fkj");
		    	
		    	$vColllection = Mage::getModel('csmarketplace/vendor')
				->getCollection()
				->addAttributeToSelect('*')
				->addAttributeToFilter('status',array('eq'=>Ced_CsMarketplace_Model_Vendor::VENDOR_APPROVED_STATUS))
				->addAttributeToFilter('entity_id',['in'=>$validIds]);
		    	
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
		    	$vcollection->addFieldToFilter('entity_id',array('in'=>$validId));
		    	
		    	//print_r($this->_vendorCollection->getData()); die("lfg");
		    }
		 //  print_r($vendorIds); die("kjdf");
          
           
          
            if(!Mage::helper('csmarketplace')->isSharingEnabled()){
            		$vcollection->addAttributeToFilter('website_id',array('eq'=>Mage::app()->getStore()->getWebsiteId()));
            }
		//	$this->prepareSortableFields();
		
		
		
		if(strlen($rest)==1)
		{
			return ['vendor' =>'','distance'=>$disatanceArray];
		}
		
		
		if(strlen($rest) && !count($validIds))
		{
			return ['vendor' =>'','distance'=>$disatanceArray];
		}
		elseif(strlen($rest))
		{
			return ['vendor' =>$vcollection,'distance'=>$disatanceArray];
		}
		else{
			return ['vendor' =>$this->_vendorCollection,'distance'=>$disatanceArray];
		}
		
    }
    

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedVendorCollection()
    {
        return $this->_getVendorCollection();
    }

	/**
     * Retrieve current view mode
     *
     * @return string
     */
    public function setColumnCount($count = 3)
    {
        return $this->_defaultColumnCount = $count;
    }
	
	/**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getColumnCount()
    {
        return $this->_defaultColumnCount;
    }
	
    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getChild('toolbar')->getCurrentMode();
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
   /*  protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->_getVendorCollection();

        // use sortable parameters
        if ($orders = $this->getAvailableOrders()) {
            $toolbar->setAvailableOrders($orders);
        }
        if ($sort = $this->getSortBy()) {
            $toolbar->setDefaultOrder($sort);
        }
        if ($dir = $this->getDefaultDirection()) {
            $toolbar->setDefaultDirection($dir);
        }
        if ($modes = $this->getModes()) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        Mage::dispatchEvent('csmarketplace_block_vshops_list_collection', array(
            'collection' => $this->_getVendorCollection()
        ));

        $this->_getVendorCollection()->load();

        return parent::_beforeToHtml();
    }
 */
    /**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getToolbarBlock()
    {
        if ($blockName = $this->getToolbarBlockName()) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime())->setChild('product_list_toolbar_pager', $this->getLayout()->createBlock('page/html_pager', 'product_list_toolbar_pager'));
        return $block;
    }

    /**
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getChildHtml('additional');
    }

    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    public function setCollection($collection)
    {
        $this->_vendorCollection = $collection;
        return $this;
    }

    public function addAttribute($code)
    {
        $this->_getVendorCollection()->addAttributeToSelect($code);
        return $this;
    }

    /**
     * Retrieve Catalog Config object
     *
     * @return Mage_Catalog_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('csmarketplace/vendor');
    }

    /**
     * Prepare Sort By fields from Category Data
     *
     * @param Mage_Catalog_Model_Category $category
     * @return Mage_Catalog_Block_Product_List
     */
    public function prepareSortableFields() {
        if (!$this->getAvailableOrders()) {
            $this->setAvailableOrders($this->_getConfig()->getAttributeUsedForSortByArray());
        }
        $availableOrders = $this->getAvailableOrders();
        if (!$this->getSortBy()) {
            if ($defaultSortBy = $this->_getConfig()->getDefaultSortBy()) {
                if (isset($availableOrders[$defaultSortBy])) {
                    $this->setSortBy($defaultSortBy);
                }
            }
        }

        return $this;
    }

    /**
     * Retrieve block cache tags based on product collection
     *
     * @return array
     */
    public function getCacheTags()
    {
        return array_merge(
            parent::getCacheTags(),
            $this->getItemsTags($this->_getVendorCollection())
        );
    }
}
