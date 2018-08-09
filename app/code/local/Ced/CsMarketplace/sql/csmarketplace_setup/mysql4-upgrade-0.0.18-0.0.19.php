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
 * @package     Ced_CsVAttribute
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

 
$installer = $this;
$installer->startSetup();
$installer->removeAttribute('csmarketplace_vendor', 'address');
$installer->removeAttribute('csmarketplace_vendor', 'city');
$installer->removeAttribute('csmarketplace_vendor', 'zip_code');
$installer->removeAttribute('csmarketplace_vendor', 'region_id');
$installer->removeAttribute('csmarketplace_vendor', 'country_id');
$installer->removeAttribute('csmarketplace_vendor', 'region');

$this->addAttribute('csmarketplace_vendor', 'address', array(
		'group'			=> 'Address Information',
		'visible'      	=> true,
		'position'      => 25,
		'required'     => true,
		'type'         => 'varchar',
		'input'        => 'text',
		'label'         => 'Address',
		'source'        => '',
		'user_defined'  => false
));

$this->addAttribute('csmarketplace_vendor', 'city', array(
		'group'			=> 'Address Information',
		'visible'      	=> true,
		'position'      => 26,
		'required'     => true,
		'type'         => 'varchar',
		'input'        => 'text',
		'label'         => 'City',
		'source'        => '',
		'user_defined'  => false
));

$this->addAttribute('csmarketplace_vendor', 'zip_code', array(
		'group'			=> 'Address Information',
		'visible'      	=> true,
		'position'      => 27,
		'required'     => true,
		'type'         => 'int',
		'input'        => 'text',
		'label'         => 'Zip/Postal Code',
		'source'        => '',
		'user_defined'  => false
));

$this->addAttribute('csmarketplace_vendor', 'region', array(
		'group'			=> 'Address Information',
		'visible'      	=> true,
		'position'      => 29,
		'type'          => 'varchar',
		'label'         => 'State',
		'input'         => 'text',
		'source'        => '',
		'required'      => false,
		'user_defined'  => false,
		'note'			=> ''
));


$this->addAttribute('csmarketplace_vendor', 'region_id', array(
		'group'			=> 'Address Information',
		'visible'      	=> true,
		'position'      => 28,
		'type'          => 'int',
		'label'         => 'State',
		'input'         => 'select',
		'source'        => 'csmarketplace/vendor_address_source_region',
		'required'      => false,
		'user_defined'  => false,
		'note'			=> ''
));

$this->addAttribute('csmarketplace_vendor', 'country_id', array(
		'group'			=> 'Address Information',
		'visible'      	=> true,
		'position'      => 30,
		'type'          => 'varchar',
		'label'         => 'Country',
		'input'         => 'select',
		'source'        => 'customer/entity_address_attribute_source_country',
		'required'      => true,
		'user_defined'  => false,
		'note'			=> ''
));

$installer->endSetup();
