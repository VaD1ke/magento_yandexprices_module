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

/**
 * Yandex Prices Indexer Resource Model
 *
 * @category   Oggetto
 * @package    Oggetto_YandexPrices
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_YandexPrices_Model_Resource_Indexer_Prices extends Mage_Index_Model_Resource_Abstract
{
    const YANDEX_MARKET_PRICE_CURRENCY = 'RUB';

    /**
     * Init object
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('oggetto_yandexprices/table_prices', 'product_id');
        $this->_setResource('oggetto_yandexprices');
    }

    /**
     * Handler for "Reindex" action in the admin panel or console
     *
     * @return void
     */
    public function reindexAll()
    {
        $this->_reindexEntity();
    }

    /**
     * Handler for save event on particular product
     *
     * @param Mage_Index_Model_Event $event Event
     * @return void
     */
    public function catalogProductSave($event)
    {
        $this->_reindexEntity($event->getData('product_id'));
    }

    /**
     * Handler for updating products data via massaction
     *
     * @param Mage_Index_Model_Event $event Event
     * @return void
     */
    public function catalogProductMassAction($event)
    {
        $this->_reindexEntity($event->getData('product_ids'));
    }

    /**
     * Reindex entity
     *
     * @param null $productId Product ID
     * @return void
     */
    protected function _reindexEntity($productId = null)
    {
        if (!is_null($productId)) {
            if (!is_array($productId)) {
                $productId = [$productId];
            }
            /** @var Mage_Catalog_Model_Product $modelProduct */
            $modelProduct = Mage::getModel('catalog/product');
            /** @var Mage_Catalog_Model_Resource_Product_Collection $productCollection */
            $productCollection = $modelProduct->getCollection()
                ->addAttributeToSelect('name', 'price')
                ->addAttributeToFilter('entity_id', $productId);

            /** @var Oggetto_YandexPrices_Model_Api_Market $api */
            $api = Mage::getModel('oggetto_yandexprices/api_market');
            /** @var Oggetto_YandexPrices_Helper_Data $helper */
            $helper = Mage::helper('oggetto_yandexprices');


            $data = [];
            /** @var Mage_Catalog_Model_Product $product */
            foreach ($productCollection as $product) {
                $productPrice = $product->getPrice();

                $price = $api->fetchPriceFromMarket($product->getName());

                $priceFormatted = $helper->formatPrice($price, $this::YANDEX_MARKET_PRICE_CURRENCY);
                $priceAdduced   = $helper->adducePrice($priceFormatted, $productPrice);

                $data[] = [
                    'product_id' => $product->getId(),
                    'price'      => $priceAdduced
                ];
            }
            $this->_getIndexAdapter()->delete($this->getMainTable(), ['product_id IN(?)' => $productId]);
        } else {
            $this->_getIndexAdapter()->delete($this->getMainTable());
        }
    }
}
