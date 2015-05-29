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
 * @package    Oggetto_Yandex Prices
 * @copyright  Copyright (C) 2015 Oggetto Web (http://oggettoweb.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper data test class
 *
 * @category   Oggetto
 * @package    Oggetto_YandexPrices
 * @subpackage Test
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_YandexPrices_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Helper Data
     *
     * @var Oggetto_YandexPrices_Helper_Data
     */
    protected $_helper;

    /**
     * Set Up initial variables
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_helper = Mage::helper('oggetto_yandexprices');
    }

    /**
     * Format price by getting only numbers from it, converting and rounding it
     *
     * @return void
     */
    public function testFormatsPriceByGettingOnlyNumbersConvertingAndRoundingIt()
    {
        $price          = '123.456 руб';
        $priceNumbers   = '123.456';
        $priceFormatted = 123.45;
        $currencyFrom   = 'CurFrom';
        $currencyTo     = 'CurTo';

        $helperDataMock = $this->getHelperMock('oggetto_yandexprices', [
            'getNumbersFromString', 'convertPrice', 'roundPrice'
        ]);

        $helperDataMock->expects($this->once())
            ->method('getNumbersFromString')
            ->with($price)
            ->willReturn($priceNumbers);

        $helperDataMock->expects($this->once())
            ->method('convertPrice')
            ->with($priceNumbers, $currencyFrom, $currencyTo)
            ->willReturn($priceNumbers);

        $helperDataMock->expects($this->once())
            ->method('roundPrice')
            ->with($priceNumbers)
            ->willReturn($priceFormatted);

        $this->replaceByMock('helper', 'oggetto_yandexprices', $helperDataMock);

        $this->assertEquals($priceFormatted, $helperDataMock->formatPrice($price, $currencyFrom, $currencyTo));
    }

    /**
     * Return numbers from string
     *
     * @return void
     */
    public function testReturnsNumbersFromString()
    {
        $testString = 'h21j5 l4f';
        $testNumber = '2154';

        $this->assertEquals($testNumber, $this->_helper->getNumbersFromString($testString));
    }

    /**
     * Return converted price
     *
     * @return void
     */
    public function testReturnsConvertedPrice()
    {
        $priceString  = '123.45';
        $priceFloat   = 123.45;
        $currencyFrom = 'CurFrom';
        $currencyTo   = 'CurTo';

        $helperDirectoryMock = $this->getHelperMock('directory', ['currencyConvert']);

        $helperDirectoryMock->expects($this->once())
            ->method('currencyConvert')
            ->with($priceFloat, $currencyFrom, $currencyTo)
            ->willReturn($priceFloat);

        $this->replaceByMock('helper', 'directory', $helperDirectoryMock);

        $this->assertEquals($priceFloat, $this->_helper->convertPrice($priceString, $currencyFrom, $currencyTo));
    }

    /**
     * Round price to 2 decimal places
     *
     * @return void
     */
    public function testRoundsPriceToTwoDecimalPlaces()
    {
        $price        = '123.456';
        $priceRounded = 123.46;

        $this->assertEquals($priceRounded, $this->_helper->roundPrice($price));
    }

    /**
     * Add 10% to adducing price if it's less then comparing price
     *
     * @param string $ratio  Ratio between prices
     * @param array  $prices Comparing prices
     *
     * @return void
     *
     * @dataProvider dataProvider
     */
    public function testAddsTenPercentsToAdducingPriceItIsLessThenComparingPrice($ratio, $prices)
    {
        $adducedPrice = $this->_helper->adducePrice($prices['adducing_price'], $prices['price']);

        $this->assertEquals($this->expected($ratio)->getAdducedPrice(), $adducedPrice);
    }
}
