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
 * Price model test class
 *
 * @category   Oggetto
 * @package    Oggetto_YandexPrices
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_YandexPrices_Test_Model_Price extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Model price
     *
     * @var Oggetto_YandexPrices_Model_Price
     */
    protected $_modelPrice = null;

    /**
     * Set up initial variables
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_modelPrice = Mage::getModel('oggetto_yandexprices/price');
    }

    /**
     * Check model alias
     *
     * @return void
     */
    public function testChecksModelAlias()
    {
        $this->assertInstanceOf('Oggetto_YandexPrices_Model_Price', Mage::getModel('oggetto_yandexprices/price'));
    }

    /**
     * Check resource model name
     *
     * @return void
     */
    public function testChecksResourceModelName()
    {
        $this->assertEquals('oggetto_yandexprices/price', $this->_modelPrice->getResourceName());
    }

    /**
     * Check price existense
     *
     * @return void
     */
    public function testChecksPriceExistence()
    {
        $hasPrice = true;

        $modelPriceMock = $this->getModelMock('oggetto_yandexprices/price', ['hasPrice']);

        $modelPriceMock->expects($this->once())
            ->method('hasPrice')
            ->willReturn($hasPrice);

        $this->replaceByMock('model', 'oggetto_yandexprices/price', $modelPriceMock);

        $this->assertEquals($hasPrice, $modelPriceMock->isPriceExist());
    }

    /**
     * Return products IDs and Yandex Market Prices from product collection
     *
     * @return void
     */
    public function testReturnsProductsIdsAndYandexPricesFromProductCollectionIfPriceIsNotNull()
    {
        $id             = '777';
        $currency       = Oggetto_YandexPrices_Model_Price::YANDEX_MARKET_PRICE_CURRENCY;
        $price          = '123a';
        $formattedPrice = '123.00';
        $adducedPrice   = '124.00';

        $productExpects = [
            'id'          => $id,
            'name'        => 'name1',
            'final_price' => '123.45'
        ];

        $returnData = [
            [
                'product_id' => $id,
                'price'      => $adducedPrice
            ]
        ];

        $modelProductMock = $this->getModelMock('catalog/product', [
            'getName', 'getFinalPrice', 'getId'
        ]);

        $modelProductMock->expects($this->once())
            ->method('getName')
            ->willReturn($productExpects['name']);

        $modelProductMock->expects($this->once())
            ->method('getFinalPrice')
            ->willReturn($productExpects['final_price']);

        $modelProductMock->expects($this->once())
            ->method('getId')
            ->willReturn($productExpects['id']);

        $this->replaceByMock('model', 'catalog/product', $modelProductMock);


        $productCollection = [ $modelProductMock ];


        $this->_mockMarketModelForFetchingPriceFromYandex($productExpects['name'], $price);


        $helperDataMock = $this->getHelperMock('oggetto_yandexprices', ['formatPrice', 'adducePrice']);

        $helperDataMock->expects($this->once())
            ->method('formatPrice')
            ->with($price, $currency)
            ->willReturn($formattedPrice);

        $helperDataMock->expects($this->once())
            ->method('adducePrice')
            ->with($formattedPrice, $productExpects['final_price'])
            ->willReturn($adducedPrice);

        $this->replaceByMock('helper', 'oggetto_yandexprices', $helperDataMock);


        $this->assertEquals($returnData, $this->_modelPrice->getProductsData($productCollection));
    }

    /**
     * Return products IDs and Yandex Market Prices from product collection
     *
     * @return void
     */
    public function testReturnsProductsIdsAndNullPricesFromProductCollectionIfPriceIsNull()
    {
        $id             = '777';

        $productExpects = [
            'id'          => $id,
            'name'        => 'name1'
        ];

        $returnData = [
            [
                'product_id' => $id,
                'price'      => null
            ]
        ];

        $modelProductMock = $this->getModelMock('catalog/product', [
            'getName', 'getId'
        ]);

        $modelProductMock->expects($this->once())
            ->method('getName')
            ->willReturn($productExpects['name']);

        $modelProductMock->expects($this->once())
            ->method('getId')
            ->willReturn($productExpects['id']);

        $this->replaceByMock('model', 'catalog/product', $modelProductMock);


        $productCollection = [ $modelProductMock ];


        $this->_mockMarketModelForFetchingPriceFromYandex($productExpects['name']);


        $this->assertEquals($returnData, $this->_modelPrice->getProductsData($productCollection));
    }


    /**
     * Mock api model for Yandex Market for fetching price by name
     *
     * @param string      $expectedName Expected product name
     * @param string|null $fetchedPrice Fetched from market price
     *
     * @return void
     */
    protected function _mockMarketModelForFetchingPriceFromYandex($expectedName, $fetchedPrice = null)
    {
        $modelApiMock = $this->getModelMock('oggetto_yandexprices/api_market', ['fetchPriceFromMarket']);

        $modelApiMock->expects($this->once())
            ->method('fetchPriceFromMarket')
            ->with($expectedName)
            ->willReturn($fetchedPrice);

        $this->replaceByMock('model', 'oggetto_yandexprices/api_market', $modelApiMock);
    }
}

