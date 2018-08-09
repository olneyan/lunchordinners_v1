<?php

$installer = $this;
$installer->startSetup();
 
/*
 * Add Entity type
 */
$installer->addEntityType('delivery_boy',Array(
    'entity_model'          => 'delivery_boy/boy',
    'attribute_model'       => '', //'delivery_boy/boy_attribute',
    'table'                 => 'delivery_boy/boy_entity',
    'increment_model'       => '',
    'increment_per_store'   => '0'
));


/*
 * Create all entity tables
 */
$installer->createEntityTables('delivery_boy/boy_entity');

$installer->installEntities();
$installer->endSetup();