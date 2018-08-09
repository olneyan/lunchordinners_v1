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
  * @package   Ced_Mobiconnect
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
$entity = 'catalog_product';
$code = 'status';
$attr = Mage::getResourceModel('catalog/eav_attribute')
    ->loadByCode($entity,'featured');
if (!$attr->getId()) {
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

$installer->addAttribute('catalog_product', "featured", array(
    'input'      => 'select',
    'label'      => 'Featured Product',
    'sort_order' => 1000,
    'required'   => false,

    'global'     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'backend'    => 'eav/entity_attribute_backend_array',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'default'       => '',
    'visible_on_front' => false,
    'option'     => array (
    'values' => array(
            1 => 'Yes',
            0 => 'No',
            
        )
    ),

));
}
?>
