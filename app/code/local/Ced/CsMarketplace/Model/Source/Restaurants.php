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
  * @package     Ced_CsMarketplace
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */


class Ced_CsMarketplace_Model_Source_Restaurants extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * Default values for option cache
     *
     * @var array
     */
    protected $_optionsDefault = array();

    /**
     * Retrieve Full Option values array
     *
     * @param bool $withEmpty       Add empty option to array
     * @param bool $defaultValues
     * @return array
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
    	$options =[];
        $resots = Mage::getModel('csmarketplace/vendor')->getCollection()->addAttributeToSelect('*')->addAttributeToSort('public_name', 'asc');
        $i=0;$options[$i]['value']=0;$options[$i]['label']='--Please Select Restaurant--';
        foreach ($resots as $values)
        {
        	$i++;
        	$options[$i]['value'] = $values->getEntityId();
        	$options[$i]['label'] = $values->getPublicName();
        }
        return $options;
    }

    
}
