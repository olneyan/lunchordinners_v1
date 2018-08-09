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
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('mobiconnect_banner'), 'category_id', 'int(11) unsigned  NOT NULL');
$installer->getConnection()->addColumn($installer->getTable('mobiconnect_banner'), 'product_id', 'int(11) unsigned  NOT NULL');

$installer->getConnection()->addColumn($installer->getTable('mobiconnect_widget'), 'category_ids', 'int(11) unsigned  NOT NULL');
$installer->getConnection()->addColumn($installer->getTable('mobiconnect_widget'), 'product_ids', 'int(11) unsigned  NOT NULL');
$installer->getConnection()->addColumn($installer->getTable('mobiconnect_widget'), 'website_urls', 'varchar(20)  NOT NULL');

$installer->endSetup();
