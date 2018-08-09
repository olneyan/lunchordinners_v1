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
 * CsMarketplace abstract model
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
abstract class Ced_CsMarketplace_Model_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Identifuer of default store
     * used for loading default data for entity
     */
    const DEFAULT_STORE_ID = 0;

    /**
     * Attribute default values
     *
     * This array contain default values for attributes which was redefine
     * value for store
     *
     * @var array
     */
    protected $_defaultValues = array();

    /**
     * This array contains codes of attributes which have value in current store
     *
     * @var array
     */
    protected $_storeValuesFlags = array();

    /**
     * Locked attributes
     *
     * @var array
     */
    protected $_lockedAttributes = array();

    /**
     * Is model deleteable
     *
     * @var boolean
     */
    protected $_isDeleteable = true;

    /**
     * Is model readonly
     *
     * @var boolean
     */
    protected $_isReadonly = false;
	
	/**
	 * mass collection
	 *
	 */
	protected $_massCollection = null;
	 
    /**
     * Lock attribute
     *
     * @param string $attributeCode
     * @return Ced_CsMarketplace_Model_Abstract
     */
    public function lockAttribute($attributeCode)
    {
        $this->_lockedAttributes[$attributeCode] = true;
        return $this;
    }

    /**
     * Unlock attribute
     *
     * @param string $attributeCode
     * @return Ced_CsMarketplace_Model_Abstract
     */
    public function unlockAttribute($attributeCode)
    {
        if ($this->isLockedAttribute($attributeCode)) {
            unset($this->_lockedAttributes[$attributeCode]);
        }

        return $this;
    }

    /**
     * Unlock all attributes
     *
     * @return Ced_CsMarketplace_Model_Abstract
     */
    public function unlockAttributes()
    {
        $this->_lockedAttributes = array();
        return $this;
    }

    /**
     * Retrieve locked attributes
     *
     * @return array
     */
    public function getLockedAttributes()
    {
        return array_keys($this->_lockedAttributes);
    }

    /**
     * Checks that model have locked attributes
     *
     * @return boolean
     */
    public function hasLockedAttributes()
    {
        return !empty($this->_lockedAttributes);
    }

    /**
     * Retrieve locked attributes
     *
     * @return boolean
     */
    public function isLockedAttribute($attributeCode)
    {
        return isset($this->_lockedAttributes[$attributeCode]);
    }

    /**
     * Overwrite data in the object.
     *
     * $key can be string or array.
     * If $key is string, the attribute value will be overwritten by $value
     *
     * If $key is an array, it will overwrite all the data in the object.
     *
     * $isChanged will specify if the object needs to be saved after an update.
     *
     * @param string|array $key
     * @param mixed $value
     * @param boolean $isChanged
     * @return Varien_Object
     */
    public function setData($key, $value = null)
    {
        if ($this->hasLockedAttributes()) {
            if (is_array($key)) {
                 foreach ($this->getLockedAttributes() as $attribute) {
                     if (isset($key[$attribute])) {
                         unset($key[$attribute]);
                     }
                 }
            } elseif ($this->isLockedAttribute($key)) {
                return $this;
            }
        } elseif ($this->isReadonly()) {
            return $this;
        }

        return parent::setData($key, $value);
    }

    /**
     * Unset data from the object.
     *
     * $key can be a string only. Array will be ignored.
     *
     * $isChanged will specify if the object needs to be saved after an update.
     *
     * @param string $key
     * @param boolean $isChanged
     * @return Ced_CsMarketplace_Model_Abstract
     */
    public function unsetData($key = null)
    {
        if ((!is_null($key) && $this->isLockedAttribute($key)) ||
            $this->isReadonly()) {
            return $this;
        }

        return parent::unsetData($key);
    }

    /**
     * Get collection instance
     *
     * @return Mage_Catalog_Model_Resource_Collection_Abstract
     */
    public function getResourceCollection()
    {
        $collection = parent::getResourceCollection();
        return $collection;
    }

    /**
     * Load entity by attribute
     *
     * @param Mage_Eav_Model_Entity_Attribute_Interface|integer|string|array $attribute
     * @param null|string|array $value
     * @param string $additionalAttributes
     * @return bool|Ced_CsMarketplace_Model_Abstract
     */
    public function loadByAttribute($attribute, $value, $additionalAttributes = '*')
    {
        $collection = $this->getResourceCollection()
            ->addAttributeToSelect($additionalAttributes)
            ->addAttributeToFilter($attribute, $value)
            ->setPage(1,1);

        foreach ($collection as $object) {
            return $object;
        }
        return false;
    }
	
	/**
     * Load entity by attribute
     *
     * @param string|array field
     * @param null|string|array $value
     * @param string $additionalAttributes
     * @return bool|Ced_CsMarketplace_Model_Abstract
     */
    public function loadByField($field, $value, $additionalAttributes = '*')
    {
		$helper = Mage::helper('csmarketplace');
		$collection = $this->getResourceCollection()
						   ->addFieldToSelect($additionalAttributes);
		if(is_array($field) && is_array($value)){
			foreach($field as $key=>$f) {
				if(isset($value[$key])) {
					//$f = $helper->getTableKey($f);
					$collection->addFieldToFilter($f, $value[$key]);
				}
			}
		} else {
			 /* echo "{{".$field.' == '.$value."}}"; */
			 //$field = $helper->getTableKey($field);
			 $collection->addFieldToFilter($field, $value);
			 /* echo $collection->getSelect();die; */
		}
		
        $collection->setCurPage(1)
				   ->setPageSize(1);
		/* echo $collection->getSize();die; */
        foreach ($collection as $object) {
			/* print_r($object->getData());die; */
			$this->load($object->getId());
            return $this;
        }
        return $this;
    }
	
	/**
     * Check for empty values for provided Attribute Code on each entity
     *
	 * @param  String $attributeCode
     * @param  array $entityIds
     * @return boolean|null
     */
    public function validateMassAttribute($attributeCode = '',array $entityIds)
    {
		
        $collection = $this->getResourceCollection()
						   ->addAttributeToSelect($attributeCode)
						   ->addAttributeToFilter('entity_id', array('in'=>$entityIds));
		if (count($collection)) {
			$this->_massCollection = $collection;
            foreach ($collection as $model) {
                if (!strlen($model->getData($attributeCode))) {
                    return false;
                }
            }
            return true;
        }
        return null;
    }

	/**
     * Check for empty values for provided Attribute Code on each entity
     *
	 * @param  array $entityIds
	 * @param  String $attributeCode
     * @return boolean|null
     */
    public function saveMassAttribute(array $entityIds,array $values)
    {
		if(!isset($values['code']) || !isset($values['value'])) {
			throw new Mage_Core_Exception(
						Mage::helper('csmarketplace')->__('New values was missing.')
					);
		}
		if($this->_massCollection == null) {
			$collection = $this->getResourceCollection()
							   ->addAttributeToSelect($values['code'])
							   ->addAttributeToFilter('entity_id', array('in'=>$entityIds));
		} else {
			$collection = $this->_massCollection;
		}
		if (count($collection)) {
			$this->_massCollection = $collection;
            foreach ($collection as $model) {
				$model->load($model->getId());
				$model->setData($values['code'],$values['value'])->save();
            }
            return true;
        }
        return null;
    }
    /**
     * Retrieve sore object
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore($this->getStoreId());
    }

    /**
     * Retrieve all store ids of object current website
     *
     * @return array
     */
    public function getWebsiteStoreIds()
    {
        return $this->getStore()->getWebsite()->getStoreIds(true);
    }

    /**
     * Adding attribute code and value to default value registry
     *
     * Default value existing is flag for using store value in data
     *
     * @param   string $attributeCode
     * @value   mixed  $value
     * @return  Ced_CsMarketplace_Model_Abstract
     */
    public function setAttributeDefaultValue($attributeCode, $value)
    {
        $this->_defaultValues[$attributeCode] = $value;
        return $this;
    }

    /**
     * Retrieve default value for attribute code
     *
     * @param   string $attributeCode
     * @return  array|boolean
     */
    public function getAttributeDefaultValue($attributeCode)
    {
        return array_key_exists($attributeCode, $this->_defaultValues) ? $this->_defaultValues[$attributeCode] : false;
    }

    /**
     * Set attribute code flag if attribute has value in current store and does not use
     * value of default store as value
     *
     * @param   string $attributeCode
     * @return  Ced_CsMarketplace_Model_Abstract
     */
    public function setExistsStoreValueFlag($attributeCode)
    {
        $this->_storeValuesFlags[$attributeCode] = true;
        return $this;
    }

    /**
     * Check if object attribute has value in current store
     *
     * @param   string $attributeCode
     * @return  bool
     */
    public function getExistsStoreValueFlag($attributeCode)
    {
        return array_key_exists($attributeCode, $this->_storeValuesFlags);
    }

    /**
     * Before save unlock attributes
     *
     * @return Ced_CsMarketplace_Model_Abstract
     */
    protected function _beforeSave()
    {
        $this->unlockAttributes();
        return parent::_beforeSave();
    }

    /**
     * Checks model is deletable
     *
     * @return boolean
     */
    public function isDeleteable()
    {
        return $this->_isDeleteable;
    }

    /**
     * Set is deletable flag
     *
     * @param boolean $value
     * @return Ced_CsMarketplace_Model_Abstract
     */
    public function setIsDeleteable($value)
    {
        $this->_isDeleteable = (bool) $value;
        return $this;
    }

    /**
     * Checks model is deletable
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->_isReadonly;
    }

    /**
     * Set is deletable flag
     *
     * @param boolean $value
     * @return Ced_CsMarketplace_Model_Abstract
     */
    public function setIsReadonly($value)
    {
        $this->_isReadonly = (bool)$value;
        return $this;
    }
	
	/**
	 * Server side validation classes
	 * 
	 */
	public function zendValidate($field,$value,$classes = '',$isRequired = false) {
		$classes = explode(' ',trim($classes));
		$errors = array();
		if(is_array($value)){
			$value=isset($value['value'])?$value['value']:count($value) > 0 && !is_array($value)?implode(',',$value):'';
		}
		if(is_array($classes) && count($classes) > 0 && strlen($value)) {	
			foreach ($classes as $class) {
				$class = trim($class);
				switch($class) {
					case 'validate-url'     :
					case 'validate-shopurl' : $availability =  $this->checkAvailability(array('shop_url'=>trim($value)));
											 //print_r($availability);die;
											 if(isset($availability['success']) && $availability['success'] == 0) {
													if(isset($availability['message']) && strlen($availability['success']) > 0) {
														$errors[] = $availability['message'];
													} else {
														$errors[] = Mage::helper('csmarketplace')->__('Please enter a valid Shop URL Key. For example "my-shop-url".');
													}
											  }
											 
											  break;
					case 'validate-email'   : if (!Zend_Validate::is($value, 'EmailAddress')) {
												$errors[] = Mage::helper('csmarketplace')->__('Invalid email address "%s" in "%s" Field.', $value,$field);
											  }
											  break;
					case 'validate-digits'  : 
					case 'validate-number' : if (!Zend_Validate::is($value, 'Digits')) {
												$errors[] = Mage::helper('csmarketplace')->__('"%s" must contain only numbers.', $field);
											  }
											  break;
					case 'validate-alpha'  : if (!Zend_Validate::is($value, 'Alpha')) {
												$errors[] = Mage::helper('csmarketplace')->__('"%s" contains non alphabetic characters', $field);
											  }
											  break;
					case 'validate-alphanum'  : if (!Zend_Validate::is($value, 'Alnum')) {
												$errors[] = Mage::helper('csmarketplace')->__('"%s" contains characters which are non alphabetic and no digits', $field);
											  }
											  break;
				    case 'validate-no-html-tags' : if(preg_match('/<(\/)?\w+/',$value)) {
													$errors[] =  Mage::helper('csmarketplace')->__('HTML tags are not allowed').' '. Mage::helper('csmarketplace')->__('in "%s" Field.',$field);
											  } 
											 break;
					case 'validate-mobile-number' : $mob_availability =  $this->validateMobileNumber($value);
											 		if(!$mob_availability) {
														$errors[] = Mage::helper('csmarketplace')->__('Mobile No. Already exists');
													}
											 		break;
				}
			}
		}
		
		if ($isRequired && trim($value) == '') {
            $errors[] = Mage::helper('csmarketplace')->__('"%s" is a required field.',$field);
        }
		return $errors;
	}
	
	/**
	 * Check Shop Url availability
	 * 
	 */
	public function checkAvailability($venderData = array(),$id = 0) {
		if(!$id && $this->getId()) $id = $this->getId();
		$rawShopUrl = isset($venderData['shop_url']) && $venderData['shop_url'] ? $venderData['shop_url'] : '';
		$rawShopUrl = str_replace('"','',$rawShopUrl);
		/* remove the slash amke a empty url there */	
		$shopUrl = $this->formatShopUrl($rawShopUrl);
		$json = array('success'=>0,'message'=>Mage::helper('csmarketplace')->__('Please enter a valid Shop URL Key. For example "my-shop-url".'),'shop_url' => $shopUrl,'raw_shop_url'=>$rawShopUrl,'suggestion'=>'');
		if (strlen($shopUrl) && preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/',$shopUrl )) {	
			$vendor = $this->loadByAttribute('shop_url',$shopUrl);
			$json = array('success'=>0,'message'=>Mage::helper('csmarketplace')->__('Shop Url is not available.'),'shop_url'=>$shopUrl, 'raw_shop_url'=>$rawShopUrl,'suggestion'=>'');
			if($rawShopUrl != $shopUrl) {
				$json = array('success'=>0,'message'=>Mage::helper('csmarketplace')->__('Please enter a valid Shop URL Key. For example "my-shop-url".'),'shop_url' => $shopUrl,'raw_shop_url'=>$rawShopUrl,'suggestion' => Mage::helper('csmarketplace')->__('Suggested Shop URL').' : <b>'.$shopUrl.'</b>');
			} elseif($id) {
				if ((!$vendor || !$vendor->getId()) || ($vendor && $vendor->getId() && $vendor->getId() == $id)) {
					$json = array('success'=>1,'message'=>Mage::helper('csmarketplace')->__('Shop Url is available'),'shop_url' => $shopUrl,'raw_shop_url'=>$rawShopUrl,'suggestion'=>'');	
				}
			} else {
				if(!$vendor || !$vendor->getId()) {
					$json = array('success'=>1,'message'=>Mage::helper('csmarketplace')->__('Shop Url is available'),'shop_url' => $shopUrl,'raw_shop_url'=>$rawShopUrl,'suggestion'=>'');	
				}
			}
		}
		return $json;	
		
	}
	
	public function getUnusedPath($requestPath = '',$count = 0, $suffix = '')
    {
		$tempPath = $requestPath;
		if(strlen($requestPath) == 0) return $requestPath;
		if($count) $requestPath = $requestPath . '-' . $count;
        $existModel = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('request_path',array('eq'=>$requestPath.$suffix))->getFirstItem();
		if($existModel && $existModel->getId()) {
			return $this->getUnusedPath($tempPath,$count+1);
		}
		return  $requestPath;
	}
	
	function formatShopUrl($shopUrl = '') {
		return strtolower(trim(str_replace('.','',preg_replace('#[^0-9a-z-]+#i', '', Mage::helper('catalog/product_url')->format($shopUrl)))));
	}
	
	public function validateMobileNumber($mobile, $id=0){
		if(!$id && $this->getId()) $id = $this->getId();
		$vendor = $this->loadByAttribute('contact_number',$mobile);
		if ((!$vendor || !$vendor->getId()) || ($vendor && $vendor->getId() && $vendor->getId() == $id)) {
			return true;
		}
		return false;
	}

}
