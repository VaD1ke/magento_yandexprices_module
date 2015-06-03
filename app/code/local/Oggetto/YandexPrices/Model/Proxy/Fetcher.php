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
 * IP fetcher for proxy
 *
 * @category   Oggetto
 * @package    Oggetto_YandexPrices
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_YandexPrices_Model_Proxy_Fetcher
{
    /**
     * Path to proxy IP text file
     *
     * @var string
     */
    protected $_path;

    /**
     * Init object
     */
    public function __construct()
    {
        $this->_path = Mage::getBaseDir() . DS . 'app' . DS . 'code' . DS . 'local'
            . DS . 'Oggetto' . DS . 'YandexPrices' . DS . 'files' . DS . 'proxy.txt';
    }

    /**
     * Get proxy array
     *
     * @return array
     */
    public function getProxyArray()
    {
        $file = $this->_getArrayFromFile($this->_getPath());

        $proxyArray = [];
        foreach ($file as $proxy) {
            $currentProxy = explode(':', $proxy);
            $proxyArray[] = [
                'ip'   => $currentProxy[0],
                'port' => $currentProxy[1]
            ];
        }

        return $proxyArray;
    }

    /**
     * Get data array from file
     *
     * @param string $path Path to file
     * @return array
     */
    protected function _getArrayFromFile($path)
    {
        return file($path, FILE_IGNORE_NEW_LINES);
    }

    /**
     * Get path to file with proxy
     *
     * @return string
     */
    protected function _getPath()
    {
        return $this->_path;
    }
}
