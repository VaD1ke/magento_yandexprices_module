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
 * Parser model test class
 *
 * @category   Oggetto
 * @package    Oggetto_YandexPrices
 * @subpackage Test
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_YandexPrices_Test_Model_Api_Parser extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Css classes in html document
     *
     * @var array
     */
    protected $_cssClasses;

    /**
     * Set Up initial variables
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_cssClasses = [
            'productLink'  => '.b-minicards__r a',
            'productPrice' => '.b-price',
            'captchaForm'  => '.form__inner'
        ];
    }

    /**
     * Return product link from parsed search page if DOMElement exists
     *
     * @return void
     */
    public function testReturnsProductLinkFromParsedSearchPageIfDomElementExists()
    {
        $link = 'link';
        $html = 'html';

        $domElementMock = $this->getMockBuilder('DOMElement')
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();

        $domElementMock->expects($this->once())
            ->method('getAttribute')
            ->with('href')
            ->willReturn($link);

        $domQueryResultMock = $this->_getDomQueryResultMockForGettingDomElement($domElementMock);

        $domQueryMock =
            $this->_getDomQueryMockForGettingQueryResult($domQueryResultMock, $this->_cssClasses['productLink']);

        $modelParserMock = $this->_getParserModelMockForGettingZendDomQuery($domQueryMock, $html);

        $this->assertEquals($link, $modelParserMock->parseProductLink($html));
    }

    /**
     * Return null link from parsed search page if DOMElement is not exist
     *
     * @return void
     */
    public function testReturnsNullLinkFromParsedSearchPageIfDomElementIsNotExist()
    {
        $html = 'html';

        $domQueryResultMock = $this->_getDomQueryResultMockForGettingDomElement(null);

        $domQueryMock =
            $this->_getDomQueryMockForGettingQueryResult($domQueryResultMock, $this->_cssClasses['productLink']);

        $modelParserMock = $this->_getParserModelMockForGettingZendDomQuery($domQueryMock, $html);

        $this->assertNull($modelParserMock->parseProductLink($html));
    }

    /**
     * Return product price from parsed product page if DOMElement exists
     *
     * @return void
     */
    public function testReturnsProductPriceFromParsedProductPageIfDomElementExists()
    {
        $price = 'price';
        $html = 'html';

        $domElement = new DOMElement('name');
        $domElement->nodeValue = $price;

        $domQueryResultMock = $this->_getDomQueryResultMockForGettingDomElement($domElement);

        $domQueryMock =
            $this->_getDomQueryMockForGettingQueryResult($domQueryResultMock, $this->_cssClasses['productPrice']);

        $modelParserMock = $this->_getParserModelMockForGettingZendDomQuery($domQueryMock, $html);


        $this->assertEquals($price, $modelParserMock->parseProductPrice($html));
    }

    /**
     * Return null price from parsed product page if DOMElement is not exist
     *
     * @return void
     */
    public function testReturnsNullPriceFromParsedProductPageIfDomElementIsNotExist()
    {
        $html = 'html';

        $domQueryResultMock = $this->_getDomQueryResultMockForGettingDomElement(null);

        $domQueryMock =
            $this->_getDomQueryMockForGettingQueryResult($domQueryResultMock, $this->_cssClasses['productPrice']);

        $modelParserMock = $this->_getParserModelMockForGettingZendDomQuery($domQueryMock, $html);


        $this->assertNull($modelParserMock->parseProductPrice($html));
    }

    /**
     * Return true from parsing page on captcha
     *
     * @return void
     */
    public function testReturnsTrueFromParsingPageOnCaptcha()
    {
        $html = 'html';

        $domElementMock = $this->_getDOMElementMockForGettingActionAttribute('/checkcaptcha');

        $domQueryResultMock = $this->_getDomQueryResultMockForGettingDomElement($domElementMock);

        $domQueryMock =
            $this->_getDomQueryMockForGettingQueryResult($domQueryResultMock, $this->_cssClasses['captchaForm']);

        $modelParserMock = $this->_getParserModelMockForGettingZendDomQuery($domQueryMock, $html);

        $this->assertTrue($modelParserMock->parseCheckCaptchaPage($html));
    }

    /**
     * Return false from parsing page on captcha if cation attribute value is not captcha
     *
     * @return void
     */
    public function testReturnsFalseFromParsingPageOnCaptchaIfActionAttributeValueIsNotCaptcha()
    {
        $html = 'html';

        $domElementMock = $this->_getDOMElementMockForGettingActionAttribute();

        $domQueryResultMock = $this->_getDomQueryResultMockForGettingDomElement($domElementMock);

        $domQueryMock =
            $this->_getDomQueryMockForGettingQueryResult($domQueryResultMock, $this->_cssClasses['captchaForm']);

        $modelParserMock = $this->_getParserModelMockForGettingZendDomQuery($domQueryMock, $html);

        $this->assertFalse($modelParserMock->parseCheckCaptchaPage($html));
    }

    /**
     * Return false from parsing page on captcha if cation attribute value is not captcha
     *
     * @return void
     */
    public function testReturnsFalseFromParsingPageOnCaptchaIfFormElementIsNotExist()
    {
        $html = 'html';

        $this->_mockDOMElementWithNeverInvokedGettingAttribute();

        $domQueryResultMock = $this->_getDomQueryResultMockForGettingDomElement();

        $domQueryMock =
            $this->_getDomQueryMockForGettingQueryResult($domQueryResultMock, $this->_cssClasses['captchaForm']);

        $modelParserMock = $this->_getParserModelMockForGettingZendDomQuery($domQueryMock, $html);

        $this->assertFalse($modelParserMock->parseCheckCaptchaPage($html));
    }


    /**
     * Get parser model mock for getting Zend_Dom_Query mock
     *
     * @param PHPUnit_Framework_MockObject_MockObject $zendDomQueryMock Zend_Dom_Query mock
     * @param string                                  $html             Html
     *
     * @return EcomDev_PHPUnit_Mock_Proxy
     */
    protected function _getParserModelMockForGettingZendDomQuery($zendDomQueryMock, $html)
    {
        $modelParserMock = $this->getModelMock('oggetto_yandexprices/api_parser', ['_getZendDomQuery']);

        $modelParserMock->expects($this->once())
            ->method('_getZendDomQuery')
            ->with($html)
            ->willReturn($zendDomQueryMock);

        $this->replaceByMock('model', 'oggetto_yandexprices/api_parser', $modelParserMock);

        return $modelParserMock;
    }

    /**
     * Get Zend_Dom_Query mock for getting query result
     *
     * @param PHPUnit_Framework_MockObject_MockObject $domQueryResultMock Zend_Dom_Query_Result mock
     * @param string                                  $query              Query
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getDomQueryMockForGettingQueryResult($domQueryResultMock, $query)
    {
        $domQueryMock = $this->getMockBuilder('Zend_Dom_Query')
            ->disableOriginalConstructor()
            ->setMethods(['query'])
            ->getMock();

        $domQueryMock->expects($this->once())
            ->method('query')
            ->with($query)
            ->willReturn($domQueryResultMock);

        return $domQueryMock;
    }

    /**
     * Get Zend_Dom_Query_Result mock for getting DOMElement
     *
     * @param PHPUnit_Framework_MockObject_MockObject $domElementMock DOMElement mock
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getDomQueryResultMockForGettingDomElement($domElementMock = null)
    {
        $domQueryResultMock = $this->getMockBuilder('Zend_Dom_Query_Result')
            ->disableOriginalConstructor()
            ->setMethods(['current'])
            ->getMock();

        $domQueryResultMock->expects($this->once())
            ->method('current')
            ->willReturn($domElementMock);

        return $domQueryResultMock;
    }

    /**
     * Get DOMElement mock for getting action attribute
     *
     * @param string $valueAttribute Value attribute
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getDOMElementMockForGettingActionAttribute($valueAttribute = null)
    {
        $domElementMock = $this->getMockBuilder('DOMElement')
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();

        $domElementMock->expects($this->once())
            ->method('getAttribute')
            ->willReturn($valueAttribute);

        return $domElementMock;
    }

    /**
     * Mock DOMElement for getting action attribute
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _mockDOMElementWithNeverInvokedGettingAttribute()
    {
        $domElementMock = $this->getMockBuilder('DOMElement')
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();

        $domElementMock->expects($this->never())
            ->method('getAttribute');
    }
}
