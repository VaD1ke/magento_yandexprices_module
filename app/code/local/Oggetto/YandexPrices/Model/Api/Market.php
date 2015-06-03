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
 * Api for Yandex Market
 *
 * @category   Oggetto
 * @package    Oggetto_YandexPrices
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_YandexPrices_Model_Api_Market
{
    /**
     * Yandex Market mobile version URL
     * @var string
     */
    protected $_url;
    /**
     * User agent
     *
     * @var string
     */
    protected $_userAgent;
    /**
     * Http client for Yandex Market
     * @var Varien_Http_Client
     */
    protected $_httpClient;

    /**
     * Init object
     */
    public function __construct()
    {
        $this->_url = 'http://m.market.yandex.ru/search.xml';
        $this->_userAgent = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:38.0) Gecko/20100101 Firefox/38.0';
    }

    /**
     * Fetch price from Yandex Market
     *
     * @param string $productName Product name
     * @param bool   $withProxy   With proxy
     * @param int    $index       Proxy array index
     *
     * @return null|string
     */
    public function fetchPriceFromMarket($productName, $withProxy = false, $index = 0)
    {
        $price = null;
        if (!$withProxy) {
            $linkToProduct = $this->searchProduct($productName);
            $price = $this->getProductPrice($linkToProduct);
        } else {
            $config = $this->getConfig($index);

            if (!is_null($config)) {
                try {
                    $linkToProduct = $this->searchProduct($productName, $config);
                    $price = $this->getProductPrice($linkToProduct, $config);
                } catch (Oggetto_YandexPrices_Model_Exception_CaptchaInputRequirement $e) {
                    $price = $this->_callFetchPriceFromMarketForRecursion($productName, ++$index);
                }
            }
        }

        return $price;
    }

    /**
     * Search product in Yandex Market
     *
     * @param string $productName Product Name
     * @param array  $httpConfig  Http config
     *
     * @return string|null
     * @throws Oggetto_YandexPrices_Model_Exception_CaptchaInputRequirement
     */
    public function searchProduct($productName, $httpConfig = null)
    {
        /** @var Zend_Http_Client $client */
        $client = $this->_getHttpClient(null, $httpConfig);
        $client->resetParameters(true);
        $client->setParameterGet([
            'cvredirect' => '1',
            'text'       => $productName
        ]);

        $response = $client->request(Varien_Http_Client::GET);

        if ($response->getStatus() == 200) {
            /** @var Oggetto_YandexPrices_Model_Api_Parser $parser */
            $parser = Mage::getModel('oggetto_yandexprices/api_parser');

            $body = $response->getBody();
            if ($parser->isCaptchaPage($body)) {
                throw new Oggetto_YandexPrices_Model_Exception_CaptchaInputRequirement();
            }
            $link = $parser->getProductLink($body);

            return $link;
        }
        return null;
    }

    /**
     * Get product price
     *
     * @param string $url        Product URL
     * @param array  $httpConfig Http config
     *
     * @return string|null
     * @throws Oggetto_YandexPrices_Model_Exception_CaptchaInputRequirement
     */
    public function getProductPrice($url, $httpConfig = null)
    {
        $client = $this->_getHttpClient($url, $httpConfig);

        $response = $client->request(Varien_Http_Client::GET);

        if ($response->getStatus() == 200) {
            /** @var Oggetto_YandexPrices_Model_Api_Parser $parser */
            $parser = Mage::getModel('oggetto_yandexprices/api_parser');

            $body = $response->getBody();
            if ($parser->isCaptchaPage($body)) {
                throw new Oggetto_YandexPrices_Model_Exception_CaptchaInputRequirement();
            }

            $price = $parser->getProductPrice($body);

            return $price;
        }
        return null;
    }

    /**
     * Get config for proxy
     *
     * @param int $index Index in proxy array
     *
     * @return array|null
     */
    public function getConfig($index = 0)
    {
        /** @var Oggetto_YandexPrices_Model_Proxy_Fetcher $fetcher */
        $fetcher = Mage::getModel('oggetto_yandexprices/proxy_fetcher');

        $proxyArray = $fetcher->getProxyArray();

        if (array_key_exists($index, $proxyArray)) {
            $ip = $proxyArray[$index]['ip'];
            $port = $proxyArray[$index]['port'];

            return $this->_getConfigForProxy($ip, $port);
        }

        return null;
    }


    /**
     * Get http client with url
     *
     * @param string $url    URL
     * @param array  $config Config
     * @return Zend_Http_Client
     */
    protected function _getHttpClient($url = null, $config = null)
    {
        if (is_null($url)) {
            $this->_httpClient = new Varien_Http_Client($this->_url, $config);

        } else {
            $this->_httpClient = new Varien_Http_Client($url, $config);
        }
        return $this->_httpClient;
    }

    /**
     * Return array config for proxy
     *
     * @param string $ip   Proxy IP
     * @param string $port Proxy port
     *
     * @return array
     */
    protected function _getConfigForProxy($ip, $port)
    {
        return [
            'useragent'  => $this->_userAgent,
            'adapter'    => 'Zend_Http_Client_Adapter_Proxy',
            'proxy_host' => $ip,
            'proxy_port' => $port,
        ];
    }

    /**
     * Call fetchPriceFromYandexMarket() for recursion
     *
     * @param string $productName Product name
     * @param int    $index       Index
     *
     * @return null|string
     */
    protected function _callFetchPriceFromMarketForRecursion($productName, $index)
    {
        return $this->fetchPriceFromMarket($productName, true, $index);
    }
}
