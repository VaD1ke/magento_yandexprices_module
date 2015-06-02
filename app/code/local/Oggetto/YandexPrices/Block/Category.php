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
 * Block class for viewing price from Yandex Market in category
 *
 * @category   Oggetto
 * @package    Oggetto_YandexPrices
 * @subpackage Block
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_YandexPrices_Block_Category extends Mage_Catalog_Block_Category_View
{
    /**
     * Product
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;

    /**
     * Set product
     *
     * @param Mage_Catalog_Model_Product $product Product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->_product = $product;

        return $this;
    }

    /**
     * Get price
     *
     * @return string|null
     */
    public function getPrice()
    {
        /** @var Oggetto_YandexPrices_Model_Price $modelYandexPrice */
        $modelYandexPrice = Mage::getModel('oggetto_yandexprices/price');

        $product = $this->_getProduct();

        $modelYandexPrice->load($product->getId());

        if ($modelYandexPrice->isPriceExist()) {
            return $modelYandexPrice->getPrice();
        }

        return null;
    }


    /**
     * Get product
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return $this->_product;
    }
}
