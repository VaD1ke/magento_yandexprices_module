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
 * Block test class for displaying price
 *
 * @category   Oggetto
 * @package    Oggetto_YandexPrices
 * @subpackage Block
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_YandexPrices_Test_Block_Price extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Block price
     *
     * @var Oggetto_YandexPrices_Block_Price
     */
    protected $_priceBlock;

    /**
     * Set up initial variables
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_priceBlock = new Oggetto_YandexPrices_Block_Price;
    }

    /**
     * Return price by product ID from Yandex prices model
     *
     * @return void
     */
    public function testReturnsPriceByProductIdFromYandexPricesModelIfPriceExists()
    {
        $testId    = '777';
        $testPrice = '123.45';

        $modelPriceMock = $this->getModelMock('oggetto_yandexprices/price', [
            'load', 'isPriceExist', 'getPrice'
        ]);

        $modelPriceMock->expects($this->once())
            ->method('load')
            ->with($testId)
            ->willReturnSelf();

        $modelPriceMock->expects($this->once())
            ->method('isPriceExist')
            ->willReturn(true);

        $modelPriceMock->expects($this->once())
            ->method('getPrice')
            ->willReturn($testPrice);

        $this->replaceByMock('model', 'oggetto_yandexprices/price', $modelPriceMock);

        $this->assertEquals($testPrice, $this->_priceBlock->getPrice($testId));
    }

    /**
     * Return price by product ID from Yandex prices model
     *
     * @return void
     */
    public function testReturnsPriceByProductIdFromYandexPricesModelIfPriceNotExists()
    {
        $testId = '777';

        $modelPriceMock = $this->getModelMock('oggetto_yandexprices/price', [
            'load', 'isPriceExist', 'getPrice'
        ]);

        $modelPriceMock->expects($this->once())
            ->method('load')
            ->with($testId)
            ->willReturnSelf();

        $modelPriceMock->expects($this->once())
            ->method('isPriceExist')
            ->willReturn(false);

        $modelPriceMock->expects($this->never())
            ->method('getPrice');

        $this->replaceByMock('model', 'oggetto_yandexprices/price', $modelPriceMock);

        $this->assertNull($this->_priceBlock->getPrice($testId));
    }
}
