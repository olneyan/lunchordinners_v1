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

/* @var $installer Ced_CsMarketplace_Model_Entity_Setup */
$installer = $this;

$installer->run("CREATE TABLE IF NOT EXISTS `{$installer->getTable('csmarketplace/vendor_form')}` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
		  `attribute_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Attribut Id',
		  `attribute_code` varchar(256) NOT NULL COMMENT 'Attribute Code',
		  `is_visible` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Visible On Frontend',
		  `sort_order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Sort Order',
		  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store Id',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Vendor Attribute Position And Visibility' AUTO_INCREMENT=1 ;");


$installer->addEntityType('csmarketplace_vendor', array(
    'entity_model'    => 'csmarketplace/vendor',
    'table'           =>'csmarketplace/vendor',
));

if(version_compare(Mage::getVersion(), '1.6', '<=')) {
	$installer->createEntityTables(
			$installer->getTable('csmarketplace/vendor')
	);
} else {
	$installer->createEntityTables(
			'csmarketplace/vendor'
	);
}
/* Add attribute to vendor table */

$this->addAttribute('csmarketplace_vendor', 'customer_id', array(
		'group'			=> 'General Information',
		'visible'      	=> false,
		'position'      => 0,
		'type'          => 'int',
		'label'         => 'Associated Customer',
		'input'         => 'select',
		'source'        => 'csmarketplace/system_config_source_customers',
		'required'      => true,
		'user_defined'  => false,
		'unique'		=> true,
		'note'			=> "After selecting customer association can't be changed."
));

$this->addAttribute('csmarketplace_vendor', 'created_at', array(
		'group'			=> 'General Information',
		'visible'      	=> true,
		'position'      => 1,
		'required'     => false,
		'type'         => 'datetime',
		'input'        => 'label',
		'label'		   => 'Created At',
		'source'        => '',
		'user_defined'  => false
));

$this->addAttribute('csmarketplace_vendor', 'shop_url', array(
		'group'			=> 'General Information',
		'visible'      	=> true,
		'position'      => 2,
		'type'          => 'varchar',
		'label'         => 'Shop Url',
		'input'         => 'text',
		'required'      => true,
		'class'			=>'validate-shopurl',
		'validate_rules'    => array(
									'input_validation'  => 'identifier'
								),
		'user_defined'  => false
));


$this->addAttribute('csmarketplace_vendor', 'status', array(
		'group'			=> 'General Information',
		'visible'      	=> true,
		'position'      => 3,
		'type'          => 'varchar',
		'label'         => 'Status',
		'input'         => 'select',
		'source'        => 'csmarketplace/system_config_source_status',
		'default_value'	=> 'disabled',
		'required'      => true,
		'user_defined'  => false,
		'note'			=> ''
));

$this->addAttribute('csmarketplace_vendor', 'group', array(
		'group'			=> 'General Information',
		'visible'      	=> true,
		'position'      => 4,
		'type'          => 'varchar',
		'label'         => 'Vendor Group',
		'input'         => 'select',
		'source'        => 'csmarketplace/system_config_source_group',
		'default_value'	=> 'general',
		'required'      => true,
		'user_defined'  => false,
		'note'			=> ''
));

$this->addAttribute('csmarketplace_vendor', 'public_name', array(
		'group'			=> 'General Information',
		'visible'      	=> true,
		'position'      => 4,
		'type'          => 'varchar',
		'label'         => 'Public Name',
		'input'         => 'text',
		'required'      => true,
		'user_defined'  => false,
));

$installer->addAttribute(
    'csmarketplace_vendor',
    'website_id',
    array(
		'group'	=> 'General Information',
        'label' => 'Website ID',
        'type'  => 'static',
		'user_defined'  => false,
		'required'      => false,
    )
);

$this->addAttribute('csmarketplace_vendor', 'name', array(
		'group'			=> 'General Information',
		'visible'      	=> true,
		'position'      => 5,
		'type'          => 'varchar',
		'label'         => 'Name',
		'input'         => 'text',
		'required'      => true,
		'user_defined'  => false
));

$this->addAttribute('csmarketplace_vendor', 'gender', array(
		'group'			=> 'General Information',
		'visible'      	=> true,
		'position'      => 6,
		'required'     => false,
		'type'         => 'int',
		'input'        => 'select',
		'label'         => 'Gender',
		'source'        => 'csmarketplace/system_config_source_dob',
		'user_defined'  => false
));

$this->addAttribute('csmarketplace_vendor', 'profile_picture', array(
		'group'			=> 'General Information',
		'visible'      	=> true,
		'position'      => 7,
		'required'     => false,
		'type'         => 'varchar',
		'input'        => 'image',
		'label'         => 'Profile Picture',
		'source'        => '',
		'user_defined'  => false
));

$this->addAttribute('csmarketplace_vendor', 'email', array(
		'group'			=> 'General Information',
		'visible'      	=> true,
		'position'      => 8,
		'required'     => true,
		'unique'		=> true,
		'type'         => 'varchar',
		'input'        => 'text',
		'source'        => '',
		'label'         => 'Email',
		'class'			=>'validate-email',
		'validate_rules'    => array(
									'input_validation'  => 'email'
								),
		'user_defined'  => false
));

$this->addAttribute('csmarketplace_vendor', 'contact_number', array(
		'group'			=> 'General Information',
		'visible'      	=> true,
		'position'      => 9,
		'required'     => false,
		'type'         => 'varchar',
		'input'        => 'text',
		'label'         => 'Contact Number',
		'class'			=> 'validate-digits',
		'source'        => '',
		'user_defined'  => false
));


$this->addAttribute('csmarketplace_vendor', 'company_name', array(
		'group'			=> 'Company Information',
		'visible'      	=> true,
		'position'      => 10,
		'required'     => false,
		'type'         => 'varchar',
		'input'        => 'text',
		'label'         => 'Company Name',
		'source'        => '',
		'user_defined'  => false
));

$this->addAttribute('csmarketplace_vendor', 'about', array(
		'group'			=> 'Company Information',
		'visible'      	=> true,
		'position'      => 11,
		'required'     => false,
		'type'         => 'text',
		'input'        => 'textarea',
		'label'         => 'About',
		'source'        => '',
		'user_defined'  => false
));

$this->addAttribute('csmarketplace_vendor', 'company_logo', array(
		'group'			=> 'Company Information',
		'required'      => false,
		'visible'      	=> true,
		'position'      => 12,
		'type'         => 'varchar',
		'input'        => 'image',
		'label'         => 'Company Logo',
		'source'        => '',
		'user_defined'  => false
));

$this->addAttribute('csmarketplace_vendor', 'company_banner', array(
		'group'			=> 'Company Information',
		'visible'      	=> true,
		'position'      => 13,
		'required'     => false,
		'type'         => 'varchar',
		'input'        => 'image',
		'label'         => 'Company Banner',
		'source'        => '',
		'user_defined'  => false
));

$this->addAttribute('csmarketplace_vendor', 'company_address', array(
		'group'			=> 'Company Information',
		'visible'      	=> true,
		'position'      => 14,
		'required'     => false,
		'type'         => 'text',
		'input'        => 'textarea',
		'label'         => 'Company Address',
		'source'        => '',
		'user_defined'  => false
));

$this->addAttribute('csmarketplace_vendor', 'support_number', array(
		'group'			=> 'Support Information',
		'visible'      	=> true,
		'position'      => 15,
		'required'     => false,
		'type'         => 'varchar',
		'input'        => 'text',
		'label'         => 'Support Number',
		'class'			=> 'validate-digits',
		'source'        => '',
		'user_defined'  => false
));

$this->addAttribute('csmarketplace_vendor', 'support_email', array(
		'group'			=> 'Support Information',
		'visible'      	=> true,
		'position'      => 16,
		'required'     => false,
		'type'         => 'varchar',
		'input'        => 'text',
		'label'         => 'Support Email',
		'class'			=> 'validate-email',
		'source'        => '',
		'user_defined'  => false
));

$installer->endSetup();