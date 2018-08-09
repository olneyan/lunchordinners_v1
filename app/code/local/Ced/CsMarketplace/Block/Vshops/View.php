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
 * CsMarketplace shop view block
 *
 * @category   Ced
 * @package    Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Block_Vshops_View extends Mage_Core_Block_Template
{
	protected $_vendor;
	
	public function __construct() {
		$this->_vendor=Mage::registry('current_vendor');			
		if($this->_vendor && $this->_vendor->getEntityId())
			$this->addData($this->_vendor->getData());
	}
	
	public function getVendor() {
		return $this->_vendor;
	}
	
	public function camelize($key){
		return $this->_camelize($key);
	}

	public function getLeftProfileAttributes($storeId = null){
		if($storeId == null) $storeId = Mage::app()->getStore()->getId();
		$attributes =  Mage::getModel('csmarketplace/vendor_attribute')
							->setStoreId($storeId)
							->getCollection()
							->addFieldToFilter('use_in_left_profile',array('gt'=>0))
							->setOrder('position_in_left_profile','ASC');
		Mage::dispatchEvent('ced_csmarketplace_left_profile_attributes_load_after',array('attributes'=>$attributes));
		return $attributes;
	}
	
	public function getPublicName($attribute = ''){
		return $this->getData('public_name');
	}
	
	public function getVendorLogo($attribute = ''){
		return $this->getData('company_logo');
	}
	
	public function getVendorBanner($attribute = ''){
		return $this->getData('company_banner');
	}
	
	public function getAbout($attribute = ''){
		return $this->getData('about');
	}
	
	public function getSupportEmail($attribute = ''){
		$html = '';
		$html .= '<label><i class="'.$attribute->getData('fontawesome_class_for_left_profile').'"></i>';
		if(strlen($attribute->getStoreLabel()) > 0) { 
			$html .= $attribute->getStoreLabel(); 
		} else { 
			$html .= $attribute->getAttributeCode(); 
		}
		$html .='</label>:<a href="mailto:'.$this->getData('support_email').'">'.$this->getData('support_email').'</a>';
		return $html;
	}
	
	public function getCreatedAt($attribute = ''){
		$html = '';
		$sinceHtml = '';
		$today=new DateTime(); 
		if(method_exists($today,'diff')) {
			$diff=$today->diff(new DateTime($this->getData('created_at')));
			if($diff->y>0)
				$sinceHtml .= $diff->y.' year(s), '.$diff->m.' month(s), '.$diff->d.' day(s)';
			else if($diff->m>0&&$diff->y<=0)
				$sinceHtml .= $diff->m.' month(s), '.$diff->d.' day(s)';
			else if($diff->d>0 && $diff->m<=0 )
				$sinceHtml .= $diff->d.' day(s)';
			else 
			//	$sinceHtml .= 'today';
			$sinceHtml .= $diff->d.' day(s)';
		} else {
			$datetime1 = $this->getData('created_at');
			$datetime2 = date("Y-m-d");
			$sinceHtml = Mage::helper('csmarketplace')->dateDiff($datetime1, $datetime2);
		}
		$html .= '<label><i class="'.$attribute->getData('fontawesome_class_for_left_profile').'"></i>';
		if(strlen($attribute->getStoreLabel()) > 0) { 
			$html .= $attribute->getStoreLabel(); 
		} else { 
			$html .= $attribute->getAttributeCode(); 
		}
		$html .='</label>:'.$sinceHtml;
		
		return $html;

	}
	
	public function getFacebookId($attribute = ''){
		$html = '';
		if(strlen($this->getData('facebook_id')) > 0) {
			$html .= '<a target="_blank" href="'.$this->escapeHtml('http://www.facebook.com/'.$this->getData('facebook_id')).'"><i class="'.$attribute->getData('fontawesome_class_for_left_profile').'"></i>';
			if(strlen($attribute->getStoreLabel()) > 0) { 
				$html .= $attribute->getStoreLabel(); 
			} else { 
				$html .= $attribute->getAttributeCode(); 
			}
			$html .= '</a>';
		}
		return $html;
	}
	
	public function getTwitterId($attribute = ''){
		$html = '';
		if(strlen($this->getData('facebook_id')) > 0) {
			$html .= '<a target="_blank" href="'.$this->escapeHtml('http://www.twitter.com/'.$this->getData('twitter_id')).'"><i class="'.$attribute->getData('fontawesome_class_for_left_profile').'"></i>';
			if(strlen($attribute->getStoreLabel()) > 0) { 
				$html .= $attribute->getStoreLabel(); 
			} else { 
				$html .= $attribute->getAttributeCode(); 
			}
			$html .= '</a>';
		}
		return $html;
	}
	
	protected function _toHtml(){
		if(!$this->getEntityId()) return '';
		return parent::_toHtml();
	}
}
