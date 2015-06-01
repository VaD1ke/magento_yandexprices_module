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
 * Market model test class
 *
 * @category   Oggetto
 * @package    Oggetto_YandexPrices
 * @subpackage Test
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_YandexPrices_Test_Model_Api_Market extends EcomDev_PHPUnit_Test_Case
{

    /**
     * Check model alias
     *
     * @return void
     */
    public function testChecksModelAlias()
    {
        $this->assertInstanceOf(
            'Oggetto_YandexPrices_Model_Api_Market', Mage::getModel('oggetto_yandexprices/api_market')
        );
    }

    /**
     * Return fetched price from searched product ID in Yandex Market
     *
     * @return void
     */
    public function testReturnsFetchedPriceFromSearchedProductInYandexMarket()
    {
        $productName   = 'name';
        $linkToProduct = 'link';
        $price         = '123';


        $modelMarketMock = $this->getModelMock('oggetto_yandexprices/api_market', [
            'searchProduct', 'getProductPrice'
        ]);

        $modelMarketMock->expects($this->once())
            ->method('searchProduct')
            ->with($productName)
            ->willReturn($linkToProduct);

        $modelMarketMock->expects($this->once())
            ->method('getProductPrice')
            ->with($linkToProduct)
            ->willReturn($price);

        $this->replaceByMock('model', 'oggetto_yandexprices/api_market', $modelMarketMock);

        $this->assertEquals($price, $modelMarketMock->fetchPriceFromMarket($productName));
    }

}
