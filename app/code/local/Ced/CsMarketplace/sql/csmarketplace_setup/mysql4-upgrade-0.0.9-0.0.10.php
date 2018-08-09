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
$installer->startSetup();

$this->addAttribute('csmarketplace_vendor', 'meta_keywords', array(
		'group'			=> 'SEO Information',
		'visible'      	=> true,
		'position'      => 19,
		'required'     => false,
		'type'         => 'text',
		'input'        => 'textarea',
		'label'         => 'Meta Keywords',
		'source'        => '',
		'user_defined'  => false
));

$this->addAttribute('csmarketplace_vendor', 'meta_description', array(
		'group'			=> 'SEO Information',
		'visible'      	=> true,
		'position'      => 20,
		'required'     => false,
		'type'         => 'text',
		'input'        => 'textarea',
		'label'         => 'Meta Description',
		'source'        => '',
		'user_defined'  => false
));

$this->addAttribute('csmarketplace_vendor', 'facebook_id', array(
		'group'			=> 'Support Information',
		'visible'      	=> true,
		'position'      => 21,
		'required'     => false,
		'type'         => 'varchar',
		'input'        => 'text',
		'label'         => 'Facebook ID',
		'source'        => '',
		'user_defined'  => false
));

$this->addAttribute('csmarketplace_vendor', 'twitter_id', array(
		'group'			=> 'Support Information',
		'visible'      	=> true,
		'position'      => 22,
		'required'     => false,
		'type'         => 'varchar',
		'input'        => 'text',
		'label'         => 'Twitter ID',
		'source'        => '',
		'user_defined'  => false
));

$installer->endSetup();

	
	
	