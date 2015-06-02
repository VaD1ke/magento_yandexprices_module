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

    /**
     * Return product link from parsed search page with OK(200) response status
     *
     * @return void
     */
    public function testReturnsProductLinkFromParsedSearchPageWithOkResponseStatus()
    {
        $productName  = 'name';
        $parameterGet = [
            'cvredirect' => '1',
            'text'       => $productName
        ];
        $body = 'body';
        $link = 'link';

        $httpResponseMock = $this->_getHttpResponseMockWithStatusOkAndGetBodyMethod($body);

        $httpClientMock =
            $this->_getHttpClientMockWithResetAndSetParametersMethodsAndRequest($httpResponseMock, $parameterGet);

        $modelMarketMock = $this->_getMarketModelMockForGettingHttpClient($httpClientMock);

        $modelParserMock = $this->getModelMock('oggetto_yandexprices/api_parser', ['parseProductLink']);

        $modelParserMock->expects($this->once())
            ->method('parseProductLink')
            ->with($body)
            ->willReturn($link);

        $this->replaceByMock('model', 'oggetto_yandexprices/api_parser', $modelParserMock);

        $this->assertEquals($link, $modelMarketMock->searchProduct($productName));
    }

    /**
     * Return null link from parsed search page with not OK(200) response status
     *
     * @return void
     */
    public function testReturnsNullLinkFromParsedSearchPageWithNotOkResponseStatus()
    {
        $productName  = 'name';
        $parameterGet = [
            'cvredirect' => '1',
            'text'       => $productName
        ];

        $httpResponseMock = $this->_getHttpResponseMockWithStatusNotOk();

        $httpClientMock =
            $this->_getHttpClientMockWithResetAndSetParametersMethodsAndRequest($httpResponseMock, $parameterGet);

        $modelMarketMock = $this->_getMarketModelMockForGettingHttpClient($httpClientMock);

        $this->assertNull($modelMarketMock->searchProduct($productName));
    }

    /**
     * Return product price from parsed product page with OK(200) http response status
     *
     * @return void
     */
    public function testReturnsProductPriceFromParsedProductPageWithOkResponseStatus()
    {
        $url   = 'url';
        $body  = 'body';
        $price = 'price';

        $httpResponseMock = $this->_getHttpResponseMockWithStatusOkAndGetBodyMethod($body);

        $httpClientMock =
            $this->_getHttpClientMockWithResetAndSetParametersMethodsAndRequest($httpResponseMock);

        $modelMarketMock = $this->_getMarketModelMockForGettingHttpClient($httpClientMock, $url);

        $modelParserMock = $this->getModelMock('oggetto_yandexprices/api_parser', ['parseProductPrice']);

        $modelParserMock->expects($this->once())
            ->method('parseProductPrice')
            ->with($body)
            ->willReturn($price);

        $this->replaceByMock('model', 'oggetto_yandexprices/api_parser', $modelParserMock);

        $this->assertEquals($price, $modelMarketMock->getProductPrice($url));
    }

    /**
     * Return null price from parsed product page with not OK(200) http response status
     *
     * @return void
     */
    public function testReturnsNullPriceFromParsedProductPageWithNotOkResponseStatus()
    {
        $url   = 'url';

        $httpResponseMock = $this->_getHttpResponseMockWithStatusNotOk();

        $httpClientMock =
            $this->_getHttpClientMockWithResetAndSetParametersMethodsAndRequest($httpResponseMock);

        $modelMarketMock = $this->_getMarketModelMockForGettingHttpClient($httpClientMock, $url);

        $this->assertNull($modelMarketMock->getProductPrice($url));
    }


    /**
     * Get Http Client mock with reset and set parameters methods and request
     *
     * @param PHPUnit_Framework_MockObject_MockObject $httpResponseMock Http Response Mock
     * @param array                                   $get              GET parameter
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getHttpClientMockWithResetAndSetParametersMethodsAndRequest($httpResponseMock, $get = null)
    {
        $httpClientMock = $this->getMock('Varien_Http_Client', ['resetParameters', 'setParameterGet', 'request']);

        if (!is_null($get)) {
            $httpClientMock->expects($this->once())
                ->method('resetParameters')
                ->with(true);

            $httpClientMock->expects($this->once())
                ->method('setParameterGet')
                ->with($get);
        }

        $httpClientMock->expects($this->once())
            ->method('request')
            ->with('GET')
            ->willReturn($httpResponseMock);

        return $httpClientMock;
    }

    /**
     * Get market model mock for getting Http client
     *
     * @param PHPUnit_Framework_MockObject_MockObject $httpClientMock Http client mock
     * @param string|null                             $url            URL
     *
     * @return EcomDev_PHPUnit_Mock_Proxy
     */
    protected function _getMarketModelMockForGettingHttpClient($httpClientMock, $url = null)
    {
        $modelMarketMock = $this->getModelMock('oggetto_yandexprices/api_market', ['_getHttpClient']);

        $modelMarketMock->expects($this->once())
            ->method('_getHttpClient')
            ->with($url)
            ->willReturn($httpClientMock);

        $this->replaceByMock('model', 'oggetto_yandexprices/api_market', $modelMarketMock);


        return $modelMarketMock;
    }

    /**
     * Get Http response mock with status OK(200) and getBody method
     *
     * @param string $body Response body
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getHttpResponseMockWithStatusOkAndGetBodyMethod($body)
    {
        $httpResponseMock = $this->getMockBuilder('Zend_Http_Response')
            ->disableOriginalConstructor()
            ->setMethods(['getStatus', 'getBody'])
            ->getMock();

        $httpResponseMock->expects($this->once())
            ->method('getStatus')
            ->willReturn(200);

        $httpResponseMock->expects($this->once())
            ->method('getBody')
            ->willReturn($body);

        return $httpResponseMock;
    }

    /**
     * Get Http response mock with status not OK(200)
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getHttpResponseMockWithStatusNotOk()
    {
        $httpResponseMock = $this->getMockBuilder('Zend_Http_Response')
            ->disableOriginalConstructor()
            ->setMethods(['getStatus', 'getBody'])
            ->getMock();

        $httpResponseMock->expects($this->once())
            ->method('getStatus')
            ->willReturn(777);

        $httpResponseMock->expects($this->never())
            ->method('getBody');

        return $httpResponseMock;
    }

}
