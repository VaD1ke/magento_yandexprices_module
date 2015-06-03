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
 * Parser for Yandex Market
 *
 * @category   Oggetto
 * @package    Oggetto_YandexPrices
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_YandexPrices_Model_Api_Parser
{
    /**
     * Css classes in html document
     *
     * @var array
     */
    protected $_cssClasses;

    /**
     * Init object
     */
    public function __construct()
    {
        $this->_cssClasses = [
            'productLink'  => '.b-minicards__r a',
            'productPrice' => '.b-price',
            'captchaForm'  => '.form__inner'
        ];
    }

    /**
     * Parse search page for getting link to product
     *
     * @param string $html Html to parse
     * @return string|null
     */
    public function getProductLink($html)
    {
        $dom = $this->_getZendDomQuery($html);

        $query = $this->_cssClasses['productLink'];

        if ($results = $dom->query($query)->current()) {
            $link = $results->getAttribute('href');
            return $link;
        }
        return null;
    }

    /**
     * Parse product page for getting price
     *
     * @param string $html Html to parse
     * @return string|null
     */
    public function getProductPrice($html)
    {
        $dom = $this->_getZendDomQuery($html);

        $query = $this->_cssClasses['productPrice'];

        if ($results = $dom->query($query)->current()) {
            $price = $results->nodeValue;

            return $price;
        }
        return null;
    }

    /**
     * Parse Check captcha page
     *
     * @param string $html Html
     * @return bool
     */
    public function isCaptchaPage($html)
    {
        $dom = $this->_getZendDomQuery($html);
        $query = $this->_cssClasses['captchaForm'];

        if ($results = $dom->query($query)->current()) {
            if ($results->getAttribute('action') == '/checkcaptcha') {
                return true;
            }
        }

        return false;
    }


    /**
     * Get Zend_Dom_Query object
     *
     * @param string $html Html
     * @return Zend_Dom_Query
     */
    protected function _getZendDomQuery($html)
    {
        return new Zend_Dom_Query($html);
    }
}
