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
 * CsMarketplace abstract resource model
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsMarketplace_Model_Mysql4_Setup_Abstract extends Mage_Eav_Model_Entity_Setup
{

	/**
     * Create entity tables
     *
     * @param string $baseName
     * @param array $options
     * - no-main
     * - no-default-types
     * - types
     * @return unknown
     */
	 
	public function createEntityTables($baseTableName, array $options = array())
	{
		if(version_compare(Mage::getVersion(), '1.6', '<')) {
			return $this->createEntityTablesBelow16($baseTableName, $options);
		} else {
			return $this->createEntityTablesAbove16($baseTableName, $options);
		}
	}
	
	 public function createEntityTablesBelow16($baseName, array $options=array())
    {        
		$sql = '';

        if (empty($options['no-main'])) {
            $sql = "
DROP TABLE IF EXISTS `{$baseName}`;
CREATE TABLE `{$baseName}` (
`entity_id` int(10) unsigned NOT NULL auto_increment,
`entity_type_id` smallint(8) unsigned NOT NULL default '0',
`attribute_set_id` smallint(5) unsigned NOT NULL default '0',
`increment_id` varchar(50) NOT NULL default '',
`parent_id` int(10) unsigned NULL default '0',
`store_id` smallint(5) unsigned NOT NULL default '0',
`created_at` datetime NOT NULL default '0000-00-00 00:00:00',
`updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
`is_active` tinyint(1) unsigned NOT NULL default '1',
PRIMARY KEY  (`entity_id`),
CONSTRAINT `FK_{$baseName}_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav/entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `FK_{$baseName}_store` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        }

        $types = array(
            'datetime'=>'datetime',
            'decimal'=>'decimal(12,4)',
            'int'=>'int',
            'text'=>'text',
            'varchar'=>'varchar(255)',
        );
        if (!empty($options['types']) && is_array($options['types'])) {
            if ($options['no-default-types']) {
                $types = array();
            }
            $types = array_merge($types, $options['types']);
        }

        foreach ($types as $type=>$fieldType) {
            $sql .= "
DROP TABLE IF EXISTS `{$baseName}_{$type}`;
CREATE TABLE `{$baseName}_{$type}` (
`value_id` int(11) NOT NULL auto_increment,
`entity_type_id` smallint(8) unsigned NOT NULL default '0',
`attribute_id` smallint(5) unsigned NOT NULL default '0',
`store_id` smallint(5) unsigned NOT NULL default '0',
`entity_id` int(10) unsigned NOT NULL default '0',
`value` {$fieldType} NOT NULL,
PRIMARY KEY  (`value_id`),
UNIQUE KEY `IDX_BASE` (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
".($type!=='text' ? "
KEY `value_by_attribute` (`attribute_id`,`value`),
KEY `value_by_entity_type` (`entity_type_id`,`value`),
" : "")."
CONSTRAINT `FK_{$baseName}_{$type}` FOREIGN KEY (`entity_id`) REFERENCES `{$baseName}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `FK_{$baseName}_{$type}_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav/attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `FK_{$baseName}_{$type}_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav/entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `FK_{$baseName}_{$type}_store` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        }

        try {
            $this->_conn->multi_query($sql);
        } catch (Exception $e) {
            throw $e;
        }

        return $this;
    }
	
	
    public function createEntityTablesAbove16($baseTableName, array $options = array())
    {
        $isNoCreateMainTable = $this->_getValue($options, 'no-main', false);
        $isNoDefaultTypes    = $this->_getValue($options, 'no-default-types', false);
        $customTypes         = $this->_getValue($options, 'types', array());
        $tables              = array();

        if (!$isNoCreateMainTable) {
            /**
             * Create table main eav table
             */
            $connection = $this->getConnection();
            $mainTable = $connection
                ->newTable($this->getTable($baseTableName))
                ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                    'identity'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'unsigned'  => true,
                 ), 'Entity Id')
                ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                ), 'Entity Type Id')
                ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                ), 'Attribute Set Id')
                ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
                    'nullable'  => false,
                    'default'   => '',
                ), 'Increment Id')
                ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                ), 'Store Id')
                ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
                    'nullable'  => false,
                ), 'Created At')
                ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
                    'nullable'  => false,
                ), 'Updated At')
                ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '1',
                ), 'Defines Is Entity Active')
                ->addIndex($this->getIdxName($baseTableName, array('entity_type_id')),
                    array('entity_type_id'))
                ->addIndex($this->getIdxName($baseTableName, array('store_id')),
                    array('store_id'))
                ->addForeignKey($this->getFkName($baseTableName, 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
                    'entity_type_id', $this->getTable('eav/entity_type'), 'entity_type_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                ->addForeignKey($this->getFkName($baseTableName, 'store_id', 'core/store', 'store_id'),
                    'store_id', $this->getTable('core/store'), 'store_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                ->setComment('Eav Entity Main Table');

            $tables[$this->getTable($baseTableName)] = $mainTable;
        }

        $types = array();
        if (!$isNoDefaultTypes) {
            $types = array(
                'datetime'  => array(Varien_Db_Ddl_Table::TYPE_DATETIME, null),
                'decimal'   => array(Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4'),
                'int'       => array(Varien_Db_Ddl_Table::TYPE_INTEGER, null),
                'text'      => array(Varien_Db_Ddl_Table::TYPE_TEXT, '64k'),
                'varchar'   => array(Varien_Db_Ddl_Table::TYPE_TEXT, '255'),
                'char'   => array(Varien_Db_Ddl_Table::TYPE_TEXT, '255')
            );
        }

        if (!empty($customTypes)) {
            foreach ($customTypes as $type => $fieldType) {
                if (count($fieldType) != 2) {
                    throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Wrong type definition for %s', $type));
                }
                $types[$type] = $fieldType;
            }
        }

        /**
         * Create table array($baseTableName, $type)
         */
        foreach ($types as $type => $fieldType) {
            $eavTableName = array($baseTableName, $type);

            $eavTable = $connection->newTable($this->getTable($eavTableName));
            $eavTable
                ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                    'identity'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'unsigned'  => true,
                    ), 'Value Id')
                ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                    ), 'Entity Type Id')
                ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                    ), 'Attribute Id')
                ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                    ), 'Store Id')
                ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                    ), 'Entity Id')
                ->addColumn('value', $fieldType[0], $fieldType[1], array(
                    'nullable'  => false,
                    ), 'Attribute Value')
                ->addIndex($this->getIdxName($eavTableName, array('entity_type_id')),
                    array('entity_type_id'))
                ->addIndex($this->getIdxName($eavTableName, array('attribute_id')),
                    array('attribute_id'))
                ->addIndex($this->getIdxName($eavTableName, array('store_id')),
                    array('store_id'))
                ->addIndex($this->getIdxName($eavTableName, array('entity_id')),
                    array('entity_id'));
            if ($type !== 'text') {
                $eavTable->addIndex($this->getIdxName($eavTableName, array('attribute_id', 'value')),
                    array('attribute_id', 'value'));
                $eavTable->addIndex($this->getIdxName($eavTableName, array('entity_type_id', 'value')),
                    array('entity_type_id', 'value'));
            }

            $eavTable
                ->addForeignKey($this->getFkName($eavTableName, 'entity_id', $baseTableName, 'entity_id'),
                    'entity_id', $this->getTable($baseTableName), 'entity_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                ->addForeignKey($this->getFkName($eavTableName, 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
                    'entity_type_id', $this->getTable('eav/entity_type'), 'entity_type_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                ->addForeignKey($this->getFkName($eavTableName, 'store_id', 'core/store', 'store_id'),
                    'store_id', $this->getTable('core/store'), 'store_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                ->setComment('Eav Entity Value Table');

            $tables[$this->getTable($eavTableName)] = $eavTable;
        }

        // DDL operations are forbidden within transactions
        // See Varien_Db_Adapter_Pdo_Mysql::_checkDdlTransaction()
        try {
            foreach ($tables as $tableName => $table) {
                $connection->createTable($table);
            }
        } catch (Exception $e) {
           throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Can\'t create table: %s', $tableName));
        }

        return $this;
    }
}