<?php

class Oggetto_YandexPrices_Model_Resource_Indexer_Prices extends Mage_Index_Model_Resource_Abstract
{
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
                ->addAttributeToSelect('name')
                ->addAttributeToFilter('entity_id', $productId);

            /** @var Oggetto_YandexPrices_Model_Api_Market $api */
            $api = Mage::getModel('oggetto_yandexprices/api_market');
            /** @var Oggetto_YandexPrices_Helper_Data $helper */
            $helper = Mage::helper('oggetto_yandexprices');


            $data = [];
            /** @var Mage_Catalog_Model_Product $product */
            foreach ($productCollection as $product) {
                $price = $api->fetchPriceFromMarket($product->getName());
                $data[] = [
                    'product_id' => $product->getId(),
                    'price'      => $helper->getNumbersFromString($price)
                ];
            }

        } else {
        }
    }
}
