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
 * Proxy fetcher model test class
 *
 * @category   Oggetto
 * @package    Oggetto_YandexPrices
 * @subpackage Test
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_YandexPrices_Test_Model_Proxy_Fetcher extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Return proxy array from file
     *
     * @return void
     */
    public function testReturnsProxyArrayFromFile()
    {
        $path = 'path';

        $arrayFromFile = [
            'ip1:port1',
            'ip2:port2'
        ];

        $modelFetcherMock = $this->getModelMock('oggetto_yandexprices/proxy_fetcher', [
            '_getArrayFromFile', '_getPath'
        ]);

        $modelFetcherMock->expects($this->once())
            ->method('_getPath')
            ->willReturn($path);

        $modelFetcherMock->expects($this->once())
            ->method('_getArrayFromFile')
            ->with($path)
            ->willReturn($arrayFromFile);

        $this->replaceByMock('model', 'oggetto_yandexprices/proxy_fetcher', $modelFetcherMock);

        $this->assertEquals($this->expected()->getProxyArray(), $modelFetcherMock->getProxyArray());
    }
}
