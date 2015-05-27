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
 * Config test class for config.xml
 *
 * @category   Oggetto
 * @package    Oggetto_YandexPrices
 * @subpackage Test
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_YandexPrices_Test_Config_Config extends EcomDev_PHPUnit_Test_Case_Config
{
    /**
     * Test setup resources on definition and existence
     *
     * @return void
     */
    public function testChecksSetupResourcesDefinedAndExists()
    {
        $this->assertSetupResourceDefined();
        $this->assertSetupResourceExists();
    }

    /**
     * Test class aliases for Model, Resource and Helper
     *
     * @return void
     */
    public function testChecksClassAliasesForModelResourceAndHelper()
    {
        $this->assertModelAlias('oggetto_faq/questions', 'Oggetto_Faq_Model_Questions');
        $this->assertResourceModelAlias('oggetto_faq/questions', 'Oggetto_Faq_Model_Resource_Questions');
        $this->assertHelperAlias('oggetto_faq', 'Oggetto_Faq_Helper_Data');
    }

    /**
     * Test config node for Block class
     *
     * @return void
     */
    public function testChecksConfigNodeValueOfBlockClass()
    {
        $this->assertConfigNodeValue('global/blocks/oggetto_faq/class', 'Oggetto_Faq_Block');
    }

    /**
     * Test codePool and version of module
     *
     * @return void
     */
    public function testChecksModuleCodePoolAndVersion()
    {
        $this->assertModuleCodePool('local', 'oggetto_faq');
        $this->assertModuleVersion('0.1.0');
    }

    /**
     * Test layout file on definition and existence
     *
     * @return void
     */
    public function testChecksFrontendLayoutDefinedAndExists()
    {
        $this->assertLayoutFileDefined('frontend', 'oggetto_faq.xml');
        $this->assertLayoutFileExists('frontend', 'oggetto_faq.xml');
    }

    /**
     * Test alias for questions table
     *
     * @return void
     */
    public function testChecksQuestionsTableAlias()
    {
        $this->assertTableAlias('oggetto_faq/table_questions', 'oggetto_faq_questions');
    }
}
