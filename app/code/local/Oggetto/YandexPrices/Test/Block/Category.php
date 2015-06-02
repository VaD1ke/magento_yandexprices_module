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
 * Block test class for displaying prices in category
 *
 * @category   Oggetto
 * @package    Oggetto_YandexPrices
 * @subpackage Block
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_YandexPrices_Test_Block_Category extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Return price by product ID from Yandex prices model
     *
     * @return void
     */
    public function testReturnsPriceByProductIdFromYandexPricesModelIfPriceExists()
    {
        $testId    = '777';
        $testPrice = '123.45';

        $modelProductMock = $this->_getProductModelMockForGettingId($testId);

        $blockCategoryMock = $this->_getCategoryBlockMockForGettingProduct($modelProductMock);


        $modelCategoryMock = $this->getModelMock('oggetto_yandexprices/price', [
            'load', 'isPriceExist', 'getPrice'
        ]);

        $modelCategoryMock->expects($this->once())
            ->method('load')
            ->with($testId)
            ->willReturnSelf();

        $modelCategoryMock->expects($this->once())
            ->method('isPriceExist')
            ->willReturn(true);

        $modelCategoryMock->expects($this->once())
            ->method('getPrice')
            ->willReturn($testPrice);

        $this->replaceByMock('model', 'oggetto_yandexprices/price', $modelCategoryMock);

        $this->assertEquals($testPrice, $blockCategoryMock->getPrice());
    }

    /**
     * Return price by product ID from Yandex prices model
     *
     * @return void
     */
    public function testReturnsPriceByProductIdFromYandexPricesModelIfPriceNotExists()
    {
        $testId = '777';

        $modelProductMock = $this->_getProductModelMockForGettingId($testId);

        $blockCategoryMock = $this->_getCategoryBlockMockForGettingProduct($modelProductMock);


        $modelCategoryMock = $this->getModelMock('oggetto_yandexprices/price', [
            'load', 'isPriceExist', 'getPrice'
        ]);

        $modelCategoryMock->expects($this->once())
            ->method('load')
            ->with($testId)
            ->willReturnSelf();

        $modelCategoryMock->expects($this->once())
            ->method('isPriceExist')
            ->willReturn(false);

        $modelCategoryMock->expects($this->never())
            ->method('getPrice');

        $this->replaceByMock('model', 'oggetto_yandexprices/price', $modelCategoryMock);

        $this->assertNull($blockCategoryMock->getPrice());
    }


    /**
     * Get catalog/product model mock for getting id
     *
     * @param string $id Product ID
     * @return EcomDev_PHPUnit_Mock_Proxy
     */
    protected function _getProductModelMockForGettingId($id)
    {
        $modelProductMock = $this->getModelMock('catalog/product', ['getId']);

        $modelProductMock->expects($this->once())
            ->method('getId')
            ->willReturn($id);

        $this->replaceByMock('model', 'catalog/product', $modelProductMock);

        return $modelProductMock;
    }

    /**
     * Get category block mock for getting product
     *
     * @param EcomDev_PHPUnit_Mock_Proxy $modelProductMock Model product mock
     * @return EcomDev_PHPUnit_Mock_Proxy
     */
    protected function _getCategoryBlockMockForGettingProduct($modelProductMock)
    {
        $blockCategoryMock = $this->getBlockMock('oggetto_yandexprices/category', ['_getProduct']);

        $blockCategoryMock->expects($this->once())
            ->method('_getProduct')
            ->willReturn($modelProductMock);

        $this->replaceByMock('block', 'oggetto_yandexprices/category', $blockCategoryMock);

        return $blockCategoryMock;
    }
}
