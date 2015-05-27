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
 * Indexer Prices model test class
 *
 * @category   Oggetto
 * @package    Oggetto_YandexPrices
 * @subpackage Test
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_YandexPrices_Test_Model_Indexer_Prices extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Model indexer prices
     *
     * @var Oggetto_YandexPrices_Model_Indexer_Prices
     */
    protected $_model = null;

    /**
     * Set up initial variables
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_model = Mage::getModel('oggetto_yandexprices/indexer_prices');
    }

    /**
     * Check resource model name
     *
     * @return void
     */
    public function testChecksResourceModelName()
    {
        $this->assertEquals('oggetto_yandexprices/indexer_prices', $this->_model->getResourceName());
    }

}
