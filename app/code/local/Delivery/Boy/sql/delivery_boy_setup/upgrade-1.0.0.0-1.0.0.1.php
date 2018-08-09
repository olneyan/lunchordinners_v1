<?php

$installer = $this;
$installer->startSetup();
 
$installer->addAttribute('delivery_boy', 'bank_name', array(
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'Bank Name',
    'input' => 'text',
    'class' => '',
    'source' => '',
    'global' => 0,
    'visible' => true,
    'required' => true,
    'user_defined' => false,
    'default' => '',
    'searchable' => true,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => true,
    'unique' => false,
));
$installer->addAttribute('delivery_boy', 'bank_account_number', array(
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'Bank Account Number',
    'input' => 'text',
    'class' => '',
    'source' => '',
    'global' => 0,
    'visible' => true,
    'required' => true,
    'user_defined' => false,
    'default' => '',
    'searchable' => true,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
));
$installer->addAttribute('delivery_boy', 'ifsc_code', array(
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'IFSC Code',
    'input' => 'text',
    'class' => '',
    'source' => '',
    'global' => 0,
    'visible' => true,
    'required' => true,
    'user_defined' => false,
    'default' => '',
    'searchable' => true,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => true,
    'unique' => false,
));

$installer->endSetup();
 