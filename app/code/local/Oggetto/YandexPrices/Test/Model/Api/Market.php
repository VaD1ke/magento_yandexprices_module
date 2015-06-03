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
     * Return fetched price from searched product ID in Yandex Market
     *
     * @return void
     */
    public function testReturnsNullPriceFromSearchedProductInYandexMarketIfProxyExistAndConfigIsNull()
    {
        $productName   = 'name';

        $modelMarketMock = $this->getModelMock('oggetto_yandexprices/api_market', [
            'searchProduct', 'getProductPrice', 'getConfig'
        ]);

        $modelMarketMock->expects($this->never())
            ->method('searchProduct');

        $modelMarketMock->expects($this->never())
            ->method('getProductPrice');

        $modelMarketMock->expects($this->once())
            ->method('getConfig')
            ->with(0)
            ->willReturn(null);

        $this->replaceByMock('model', 'oggetto_yandexprices/api_market', $modelMarketMock);

        $this->assertNull($modelMarketMock->fetchPriceFromMarket($productName, true));
    }

    /**
     * Return fetched price from searched product ID in Yandex Market
     *
     * @return void
     */
    public function testReturnsFetchedPriceFromSearchedProductInYandexMarketIfProxyExistsConfigIsNotNullAndNoException()
    {
        $productName   = 'name';
        $linkToProduct = 'link';
        $price         = '123';
        $config        = ['config'];

        $modelMarketMock = $this->getModelMock('oggetto_yandexprices/api_market', [
            'searchProduct', 'getProductPrice', 'getConfig'
        ]);

        $modelMarketMock->expects($this->once())
            ->method('searchProduct')
            ->with($productName, $config)
            ->willReturn($linkToProduct);

        $modelMarketMock->expects($this->once())
            ->method('getProductPrice')
            ->with($linkToProduct, $config)
            ->willReturn($price);

        $modelMarketMock->expects($this->once())
            ->method('getConfig')
            ->willReturn($config);

        $this->replaceByMock('model', 'oggetto_yandexprices/api_market', $modelMarketMock);

        $this->assertEquals($price, $modelMarketMock->fetchPriceFromMarket($productName, true));
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

        $this->_mockOggettoApiMarketModelForParsingOnCaptchaAndGetProductLink($body, $link);

        $this->assertEquals($link, $modelMarketMock->searchProduct($productName));
    }

    /**
     * Throw exception from parsing search page with OK(200) response status wher page is captcha
     *
     * @return void
     */
    public function testThrowsExceptionFromParsingSearchPageWithOkResponseStatusWhenPageIsCaptcha()
    {
        $productName  = 'name';
        $parameterGet = [
            'cvredirect' => '1',
            'text'       => $productName
        ];
        $body = 'body';

        $httpResponseMock = $this->_getHttpResponseMockWithStatusOkAndGetBodyMethod($body);

        $httpClientMock =
            $this->_getHttpClientMockWithResetAndSetParametersMethodsAndRequest($httpResponseMock, $parameterGet);

        $modelMarketMock = $this->_getMarketModelMockForGettingHttpClient($httpClientMock);

        $this->_mockOggettoApiMarketModelForParsingOnCaptchaAndNotGetProductLink($body);

        $this->setExpectedException('Oggetto_YandexPrices_Model_Exception_CaptchaInputRequirement');

        $modelMarketMock->searchProduct($productName);
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

        $this->_mockOggettoApiMarketModelForParsingOnCaptchaAndGetProductPrice($body, $price);

        $this->assertEquals($price, $modelMarketMock->getProductPrice($url));
    }

    /**
     * Throw exception from parsing product page with OK(200) response status wher page is captcha
     *
     * @return void
     */
    public function testThrowsExceptionFromParsingProductPageWithOkResponseStatusWhenPageIsCaptcha()
    {
        $url   = 'url';
        $body  = 'body';

        $httpResponseMock = $this->_getHttpResponseMockWithStatusOkAndGetBodyMethod($body);

        $httpClientMock =
            $this->_getHttpClientMockWithResetAndSetParametersMethodsAndRequest($httpResponseMock);

        $modelMarketMock = $this->_getMarketModelMockForGettingHttpClient($httpClientMock, $url);

        $this->_mockOggettoApiMarketModelForParsingOnCaptchaAndNotGetProductPrice($body);

        $this->setExpectedException('Oggetto_YandexPrices_Model_Exception_CaptchaInputRequirement');

        $modelMarketMock->getProductPrice($url);
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
     * Return config array for proxy
     *
     * @return void
     */
    public function testReturnsConfigArrayForProxy()
    {
        $ip   = '123';
        $port = '45';

        $proxyArray = [
            [
                'ip'   => 'ip12',
                'port' => 'port12'
            ],
            [
                'ip'   => $ip,
                'port' => $port
            ]
        ];

        $configArray = [
            'adapter'    => 'Zend_Http_Client_Adapter_Proxy',
            'proxy_host' => $ip,
            'proxy_port' => $port,
        ];

        $modelMarketMock = $this->getModelMock('oggetto_yandexprices/api_market', ['_getConfigForProxy']);

        $modelMarketMock->expects($this->once())
            ->method('_getConfigForProxy')
            ->with($ip, $port)
            ->willReturn($configArray);

        $this->replaceByMock('model', 'oggetto_yandexprices/api_market', $modelMarketMock);


        $this->_mockOggettoProxyFetcherModelForGettingProxyArray($proxyArray);

        $this->assertEquals($configArray, $modelMarketMock->getConfig(1));
    }

    /**
     * Return config array for proxy
     *
     * @return void
     */
    public function testReturnsNullWhenProxyArrayDoesNotHaveEstablishedKey()
    {
        /** @var Oggetto_YandexPrices_Model_Api_Market $apiMarketModel */
        $apiMarketModel = Mage::getModel('oggetto_yandexprices/api_market');

        $ip   = '123';
        $port = '45';

        $proxyArray = [
            [
                'ip'   => 'ip12',
                'port' => 'port12'
            ],
            [
                'ip'   => $ip,
                'port' => $port
            ]
        ];

        $this->_mockOggettoProxyFetcherModelForGettingProxyArray($proxyArray);

        $this->assertNull($apiMarketModel->getConfig(3));
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

    /**
     * Mock oggetto api market model for parsing on captcha and get product link
     *
     * @param string $body      Html body
     * @param string $link      Product link
     *
     * @return void
     */
    protected function _mockOggettoApiMarketModelForParsingOnCaptchaAndGetProductLink($body, $link)
    {
        $modelParserMock = $this->getModelMock('oggetto_yandexprices/api_parser', [
            'parseProductLink', 'parseCheckCaptchaPage']);

        $modelParserMock->expects($this->once())
            ->method('parseCheckCaptchaPage')
            ->with($body)
            ->willReturn(false);

        $modelParserMock->expects($this->once())
            ->method('parseProductLink')
            ->with($body)
            ->willReturn($link);

        $this->replaceByMock('model', 'oggetto_yandexprices/api_parser', $modelParserMock);
    }

    /**
     * Mock oggetto api market model for parsing on captcha and get product link
     *
     * @param string $body      Html body
     *
     * @return void
     */
    protected function _mockOggettoApiMarketModelForParsingOnCaptchaAndNotGetProductLink($body)
    {
        $modelParserMock = $this->getModelMock('oggetto_yandexprices/api_parser', [
            'parseProductLink', 'parseCheckCaptchaPage']);

        $modelParserMock->expects($this->once())
            ->method('parseCheckCaptchaPage')
            ->with($body)
            ->willReturn(true);

        $modelParserMock->expects($this->never())
            ->method('parseProductLink');

        $this->replaceByMock('model', 'oggetto_yandexprices/api_parser', $modelParserMock);
    }

    /**
     * Mock oggetto api market model for parsing on captcha and get product price
     *
     * @param string $body      Html body
     * @param string $price      Product price
     *
     * @return void
     */
    protected function _mockOggettoApiMarketModelForParsingOnCaptchaAndGetProductPrice($body, $price)
    {
        $modelParserMock = $this->getModelMock('oggetto_yandexprices/api_parser', [
            'parseProductPrice', 'parseCheckCaptchaPage']);

        $modelParserMock->expects($this->once())
            ->method('parseCheckCaptchaPage')
            ->with($body)
            ->willReturn(false);

        $modelParserMock->expects($this->once())
            ->method('parseProductPrice')
            ->with($body)
            ->willReturn($price);

        $this->replaceByMock('model', 'oggetto_yandexprices/api_parser', $modelParserMock);
    }

    /**
     * Mock oggetto api market model for parsing on captcha and get product link
     *
     * @param string $body      Html body
     *
     * @return void
     */
    protected function _mockOggettoApiMarketModelForParsingOnCaptchaAndNotGetProductPrice($body)
    {
        $modelParserMock = $this->getModelMock('oggetto_yandexprices/api_parser', [
            'parseProductPrice', 'parseCheckCaptchaPage']);

        $modelParserMock->expects($this->once())
            ->method('parseCheckCaptchaPage')
            ->with($body)
            ->willReturn(true);

        $modelParserMock->expects($this->never())
            ->method('parseProductPrice');

        $this->replaceByMock('model', 'oggetto_yandexprices/api_parser', $modelParserMock);
    }

    /**
     * Mock Oggetto proxy fetcher model for getting proxy array
     *
     * @param array $proxyArray Proxy array
     *
     * @return void
     */
    protected function _mockOggettoProxyFetcherModelForGettingProxyArray($proxyArray)
    {
        $modelFetcherMock = $this->getModelMock('oggetto_yandexprices/proxy_fetcher', ['getProxyArray']);

        $modelFetcherMock->expects($this->once())
            ->method('getProxyArray')
            ->willReturn($proxyArray);

        $this->replaceByMock('model', 'oggetto_yandexprices/proxy_fetcher', $modelFetcherMock);
    }
}
