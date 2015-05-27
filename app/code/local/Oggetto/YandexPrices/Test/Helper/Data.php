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
 * @package    Oggetto_Yandex Prices
 * @copyright  Copyright (C) 2015 Oggetto Web (http://oggettoweb.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper data test class
 *
 * @category   Oggetto
 * @package    Oggetto_YandexPrices
 * @subpackage Test
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_YandexPrices_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Helper Data
     *
     * @var Oggetto_YandexPrices_Helper_Data
     */
    protected $_helper;

    /**
     * Set Up initial variables
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_helper = Mage::helper('oggetto_yandexprices');
    }

    public function testReturnsNumbersFromString()
    {
        $testString = 'h21j5 l4f';
        $testNumber = '2154';

        $this->assertEquals($testNumber, $this->_helper->getNumbersFromString($testString));
    }
}
