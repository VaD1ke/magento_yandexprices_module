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
 * Price Model
 *
 * @category   Oggetto
 * @package    Oggetto_YandexPrices
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_YandexPrices_Model_Price extends Mage_Core_Model_Abstract
{
    /**
     * Yandex Market price currency
     */
    const YANDEX_MARKET_PRICE_CURRENCY = 'RUB';

    /**
     * Init object
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('oggetto_yandexprices/price');
    }

    /**
     * Check price existence
     *
     * @return bool
     */
    public function isPriceExist()
    {
        return $this->hasPrice();
    }

    /**
     * Get products IDs and Yandex Market prices from products collection
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $productCollection Products collection
     * @return array
     */
    public function getProductsData($productCollection)
    {
        /** @var Oggetto_YandexPrices_Model_Api_Market $api */
        $api = Mage::getModel('oggetto_yandexprices/api_market');
        /** @var Oggetto_YandexPrices_Helper_Data $helper */
        $helper = Mage::helper('oggetto_yandexprices');

        $data = [];
        /** @var Mage_Catalog_Model_Product $product */
        foreach ($productCollection as $product) {
            $price = $api->fetchPriceFromMarket($product->getName(), true);
            if (!is_null($price)) {
                $productPrice = $product->getFinalPrice();

                $priceFormatted = $helper->formatPrice($price, $this::YANDEX_MARKET_PRICE_CURRENCY);
                $priceAdduced   = $helper->adducePrice($priceFormatted, $productPrice);

                $data[] = [
                    'product_id' => $product->getId(),
                    'price'      => $priceAdduced
                ];
            } else {
                $data[] = [
                    'product_id' => $product->getId(),
                    'price'      => $price
                ];
            }
        }

        return $data;
    }
}
