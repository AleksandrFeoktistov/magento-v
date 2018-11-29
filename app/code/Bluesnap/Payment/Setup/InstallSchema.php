<?php

namespace Bluesnap\Payment\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface {

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('bluesnap_vaulted_shopper'))
            ->addColumn(
                'entity_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )->addColumn('customer_id', \Magento\Framework\Db\Ddl\Table::TYPE_INTEGER, null, [
                'nullable' => false,
            ], 'Customer id'
            )->addColumn(
                'vaulted_shopper_id',
                Table::TYPE_TEXT, 100,
                ['nullable' => true, 'default' => null],
                'Vaulted Shopper ID'
            )->addIndex(
                $installer->getIdxName('customer_entity', ['entity_id']), ['customer_id']
            )->setComment('Bluesnap mappings between customers and vaulted shopper IDs');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}