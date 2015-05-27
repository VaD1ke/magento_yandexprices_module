<?php
/**
 * Oggetto Yandex Prices extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Oggetto Yandex Prices module to newer versions in the future.
 * If you wish to customize the Oggetto Yandex Prices module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_YandexPrices
 * @copyright  Copyright (C) 2015 Oggetto Web (http://oggettoweb.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;

$installer->startSetup();

try {

    $table = $installer->getConnection()
        ->newTable($installer->getTable('oggetto_yandexprices/table_prices'))
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null,
                    [
                        'unsigned' => true,
                        'nullable' => false
                    ], 'Product ID')
        ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,2',
                    [
                        'unsigned' => true,
                        'nullable' => true
                    ], 'Price')
        ->addForeignKey($installer->getFkName(
                                'oggetto_yandexprices/table_prices', 'product_id', 'catalog/product', 'entity_id'
                        ), 'product_id', $installer->getTable('catalog/product'), 'entity_id',
                            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addIndex($installer->getIdxName('oggetto_yandexprices/table_prices', 'product_id'), 'product_id')
        ->setComment('Yandex Market Prices Table');

    $installer->getConnection()->createTable($table);

} catch (Exception $e) {

    Mage::logException($e);

}

$installer->endSetup();
