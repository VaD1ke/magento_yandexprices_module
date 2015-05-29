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
 * Helper Data
 *
 * @category   Oggetto
 * @package    Oggetto_YandexPrices
 * @subpackage Helper
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_YandexPrices_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Format price from Yandex Market
     *
     * @param string $price        Price to format
     * @param string $currencyFrom From currency
     * @param string $currencyTo   To currency
     *
     * @return string
     */
    public function formatPrice($price, $currencyFrom, $currencyTo = null)
    {
        $price = $this->getNumbersFromString($price);
        $price = $this->convertPrice($price, $currencyFrom, $currencyTo);
        $priceFormatted = $this->roundPrice($price);

        return strval($priceFormatted);
    }

    /**
     * Get numbers from string
     *
     * @param string $str String
     * @return mixed
     */
    public function getNumbersFromString($str)
    {
        return preg_replace("/[^0-9]/", '', $str);
    }

    /**
     * Convert price from currency to currency
     *
     * @param string|float $price        Converting price
     * @param string       $currencyFrom Currency from
     * @param string       $currencyTo   Currency to
     *
     * @return float
     */
    public function convertPrice($price, $currencyFrom, $currencyTo = null)
    {
        /** @var Mage_Directory_Helper_Data $helperDirectory */
        $helperDirectory = Mage::helper('directory');

        $priceConverted = $helperDirectory->currencyConvert((float) $price, $currencyFrom, $currencyTo);
        return $priceConverted;
    }

    /**
     * Round price to 2 decimal places
     *
     * @param string|float $price Price to round
     * @return float
     */
    public function roundPrice($price)
    {
        $priceRounded = Mage::app()->getStore()->roundPrice($price);
        return $priceRounded;
    }

    /**
     * Add to adducing price 10 percents if it less then price
     *
     * @param mixed $adducingPrice Adducing price
     * @param mixed $price         Price
     * 
     * @return string
     */
    public function adducePrice($adducingPrice, $price)
    {
        $adducedPrice = $adducingPrice >= $price ? $adducingPrice : $adducingPrice * 1.1;

        return strval($adducedPrice);
    }
}
