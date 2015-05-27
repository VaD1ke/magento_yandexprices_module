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
    }

    /**
     * Fetch price from Yandex Market
     *
     * @param string $productName Product name
     * @return null|string
     */
    public function fetchPriceFromMarket($productName)
    {
        $linkToProduct = $this->searchProduct($productName);
        $price = $this->getProductPrice($linkToProduct);

        return $price;
    }

    /**
     * Search product in Yandex Market
     *
     * @param string $productName Product Name
     * @return string|null
     */
    public function searchProduct($productName)
    {
        /** @var Zend_Http_Client $client */
        $client = $this->_getHttpClient();
        $client->resetParameters(true);
        $client->setParameterGet([
            'cvredirect' => '1',
            'text'       => $productName
        ]);

        $response = $client->request(Varien_Http_Client::GET);

        if ($response->getStatus() == 200) {
            /** @var Oggetto_YandexPrices_Model_Api_Parser $parser */
            $parser = Mage::getModel('oggetto_yandexprices/api_parser');

            $link = $parser->parseProductLink($response->getBody());

            return $link;
        }
        return null;
    }

    /**
     * Get product price
     *
     * @param string $url Product URL
     * @return string|null
     */
    public function getProductPrice($url)
    {
        $client = $this->_getHttpClient($url);

        $response = $client->request(Varien_Http_Client::GET);

        if ($response->getStatus() == 200) {
            /** @var Oggetto_YandexPrices_Model_Api_Parser $parser */
            $parser = Mage::getModel('oggetto_yandexprices/api_parser');

            $price = $parser->parseProductPrice($response->getBody());

            return $price;
        }
        return null;
    }

    /**
     * Get http client with url
     *
     * @param string $url URL
     * @return Zend_Http_Client
     */
    protected function _getHttpClient($url = null)
    {
        if (is_null($url)) {
            $this->_httpClient = new Varien_Http_Client($this->_url);
        } else {
            $this->_httpClient = new Varien_Http_Client($url);
        }
        return $this->_httpClient;
    }
}
