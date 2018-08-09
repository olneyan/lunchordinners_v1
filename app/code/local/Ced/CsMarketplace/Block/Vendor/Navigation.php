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
 * CsMarketplace vendor navigation sidebar
 *
 * @category   Ced
 * @package    Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */

class Ced_CsMarketplace_Block_Vendor_Navigation extends Ced_CsMarketplace_Block_Vendor_Abstract
{

    protected $_links = array();
	
	protected $_sortOrder = 1;

    protected $_activeLink = false;
	
	protected $_count = null;
	
	protected $_tmpLinks = array();
	
	protected $_sublink=array();

    public function addLink($name, $path, $label, $font_awesome, $sort_order = -1, $children = array(), $parent = '', $urlParams=array(), $count=0)
    {
    	if(Mage::app()->getStore()->isCurrentlySecure() && !isset($urlParams['_secure'])) {
    		$urlParams['_secure'] = true;
    	}
		
		$childArray = array();

		if(count($children) > 0) {
			foreach($children as $childName=>$options) {
				$childPath = isset($options['path'])?$options['path']:'';
				$childLabel = isset($options['label'])?$options['label']:'';
				$childFontAwesome = isset($options['font_awesome'])?$options['font_awesome']:'';
				$childSortOrder = isset($options['sort_order']) && $options['sort_order']>-1?(int)$options['sort_order']:$this->_sortOrder++;
				$childChildren = isset($options['children'])?$options['children']:array();
				$childParent = $name;
				$childArrayTemp = array();
				$childArrayTemp = $this->addLink($childName,$childPath,$childLabel,$childFontAwesome,$childSortOrder,$childChildren,$childParent);

				$childArray = $this->arrayInsert($childArray, $childSortOrder, array($childName=>$childArrayTemp));	

			}

		}
		$sort_order  = $sort_order > -1? (int)$sort_order:$this->_sortOrder++;
		$this->_tmpLinks[$name] = new Varien_Object(array(
				'name' => $name,
				'path' => $path,
				'label' => $label,
				'font_awesome' => $font_awesome,
				'sort_order' => $sort_order,
				'url' => $this->getUrl($path, $urlParams),
				'children' => $childArray,
			));	
		if($parent == '') {
		
			$this->_links = $this->arrayInsert($this->_links, $sort_order, array($name=>$this->_tmpLinks[$name]));
			
			return $this;
		} else {
			return $this->_tmpLinks[$name];
		}
    }
    
    public function addSubLink($name, $path, $label, $font_awesome, $sort_order = 0, $children = array(), $parent = '', $urlParams=array(), $count=0)
    {
		if(Mage::app()->getStore()->isCurrentlySecure() && !isset($urlParams['_secure'])){
    		$urlParams['_secure'] = true;
    	}
		
    	$childArray = array();

    	if(count($children) > 0) {
    		foreach($children as $childName=>$options) {
    			$childPath = isset($options['path'])?$options['path']:'';
    			$childLabel = isset($options['label'])?$options['label']:'';
    			$childFontAwesome = isset($options['font_awesome'])?$options['font_awesome']:'';
    			$childSortOrder = isset($options['sort_order']) && $options['sort_order']>-1?(int)$options['sort_order']:$this->_sortOrder++;
    			$childChildren = isset($options['children'])?$options['children']:array();
    			$childParent = $name;
    			//$childArray[$childName] = $this->addLink($childName,$childPath,$childLabel,$childFontAwesome,$childSortOrder,$childChildren,$childParent);
    			$childArrayTemp = array();
    			$childArrayTemp = $this->addLink($childName,$childPath,$childLabel,$childFontAwesome,$childSortOrder,$childChildren,$childParent);
    			
    			$childArray = $this->arrayInsert($childArray, $childSortOrder, array($childName=>$childArrayTemp));
    		}
    	}
    	$sort_order  = $sort_order > -1? (int)$sort_order:$this->_sortOrder++;
    	$this->_tmpLinks[$name] = new Varien_Object(array(
    			'name' => $name,
    			'path' => $path,
    			'label' => $label,
    			'font_awesome' => $font_awesome,
    			'sort_order' => $sort_order,
    			'url' => $this->getUrl($path, $urlParams),
    			'children' => $childArray,
    	));
    	if($parent){
    		$this->_sublink[$parent] = isset($this->_sublink[$parent])?$this->_sublink[$parent]:array();
    		$this->_sublink[$parent] = $this->arrayInsert($this->_sublink[$parent], $sort_order, array($name=>$this->_tmpLinks[$name]));
    		//$this->_sublink[$parent][$name] = $this->_tmpLinks[$name];
    	}
    	
    }
    
	public function removeLink($name = '',$parent='') {
		if(isset($this->_links[$name])) {
			unset($this->_links[$name]);
		}
		else if($parent!='' && isset($this->_links[$parent])){
			$children=array();
			$children = $this->_links[$parent]->getChildren();
			if(count($children)>0 && isset($children[$name])){
				unset($children[$name]);
				$this->_links[$parent]->setChildren($children);
			}
		}
		else if($parent!='' && !isset($this->_links[$parent])){
			$parents=explode('~',$parent);
			$toparent=$parents[0];
			$subparent=$parents[1];
			if(isset($this->_links[$toparent])){
				$children=array();
				$children = $this->_links[$toparent]->getChildren();				
				if(count($children)>0 && isset($children[$subparent])){
					$subchildren=array();
					$subchildren = $children[$subparent]->getChildren();
					if(count($subchildren)>0 && isset($subchildren[$name]))
						unset($subchildren[$name]);
					$children[$subparent]->setChildren($subchildren);
				}
				$this->_links[$toparent]->setChildren($children);
			}
		}
		
		return $this;
	}

    public function setActive($path)
    {
        $this->_activeLink = $this->_completePath($path);
        return $this;
    }

    public function getLinks($flag = true)
    {
    	if(is_array($this->_sublink) && count($this->_sublink)>0){
	    	if(is_array($this->_links) && count($this->_links) > 0) {
	    		foreach ($this->_links as $object){		
	    			if(isset($this->_sublink[$object->getName()])){
	    				if(count($object->getChildren())>0){
	    					/* array_merge($object->getChildren(),$this->_sublink[$object->getName()]) */
	    					$finalChildArr = $object->getChildren();
	    					foreach($this->_sublink[$object->getName()] as $name=>$item) {
	    						$sort_order  =  $item->getSortOrder() > -1? (int)$item->getSortOrder():$this->_sortOrder++;
	    						$finalChildArr = $this->arrayInsert($finalChildArr,$sort_order,array($name=>$item));
	    						
	    					}
	    					//$this->arrayInsert($object->getChildren(),$sort_order, array($object->getName()=>$this->_sublink[$object->getName()]))
	    					$object->setChildren($finalChildArr);
	    				}
	    				else
	    					$object->setChildren($this->_sublink[$object->getName()]);
	    			}
	    			if(count($object->getChildren())>0){
	    				$children=$object->getChildren();
	    				foreach ($children as $child){
	    					if(isset($this->_sublink[$child->getName()])){
	    						if(count($child->getChildren())>0){
			    					$finalChildArrc = $child->getChildren();
			    					foreach($this->_sublink[$child->getName()] as $name=>$item) {
			    						$sort_order  =  $item->getSortOrder() > -1? (int)$item->getSortOrder():$this->_sortOrder++;
			    						$finalChildArrc = $this->arrayInsert($finalChildArrc,$sort_order,array($name=>$item));
			    						
			    					}
	    							/* array_merge($object->getChildren(),$this->_sublink[$object->getName()]) */
	    							$child->setChildren($finalChildArrc);
	    							//$child->setChildren(array_merge($child->getChildren(),$this->_sublink[$child->getName()]));
	    						}
	    						else
	    							$child->setChildren($this->_sublink[$child->getName()]);
	    					}
	    				}
	    				
	    			}
	    		}
	    	}
    	}
		$links = $this->_links;
		if($flag){
			Mage::dispatchEvent('ced_csmarketplace_vendor_navigation_links_prepare', array(
				'links' => $links,
				'block' => $this,
			));	
		}	

		return $this->_links;
    }

    public function isActive($link)
    {
        if (empty($this->_activeLink)) {
            $this->_activeLink = $this->getAction()->getFullActionName('/');
        }
        if ($this->_completePath($link->getPath()) == $this->_activeLink) {
            return true;
        } else {
			if(count($link->getChildren()) > 0) {
				$isParentActive = false;
				foreach($link->getChildren() as $ch1_link) {
					if($this->isActive($ch1_link)){
						$isParentActive = true;
						break;
					}
				}
				return $isParentActive;
			}
		}
        return false;
    }

    protected function _completePath($path)
    {
        $path = rtrim($path, '/');
        switch (sizeof(explode('/', $path))) {
            case 1:
                $path .= '/index';
                // no break

            case 2:
                $path .= '/index';
        }
        return $path;
    }
	
	protected function _prepareLayout()
    {
        parent::_prepareLayout();
		$this->setChild('csmarketplace_vendor_navigation_statatics',
            $this->getLayout()->createBlock('csmarketplace/vendor_navigation_statatics','csmarketplace_vendor_navigation_statatics')
                ->setTemplate('csmarketplace/vendor/navigation/statatics.phtml')
                );
		
        return $this;
    }
	
	public function isPaymentDetailAvailable () {
		return count($this->getVendor()->getPaymentMethodsArray($this->getVendorId(),false));
	}
	
	public function arrayInsert($array, $position, $insert_array) {
		$first_array = array();
		foreach($array as $key=>$value) {
			if($value->sort_order <= $position) {
				$first_array[$key] = $value;
			} elseif($value->sort_order > $position) {
				break;
			}
		}
		$array = array_merge ($first_array, $insert_array, $array);
		return $array;
	}
	
}
