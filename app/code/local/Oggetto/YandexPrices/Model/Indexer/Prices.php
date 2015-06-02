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
 * Indexer model
 *
 * @category   Oggetto
 * @package    Oggetto_YandexPrices
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */

class Oggetto_YandexPrices_Model_Indexer_Prices extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Matched entities
     * @var array
     */
    protected $_matchedEntities = [
        Mage_Catalog_Model_Product::ENTITY => [
            Mage_Index_Model_Event::TYPE_REINDEX,
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION
        ],
    ];

    /**
     * Init object
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('oggetto_yandexprices/indexer_prices');
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('oggetto_yandexprices')->__('Yandex Market Prices');
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('oggetto_yandexprices')->__('Indexes prices from Yandex Market');
    }


    /**
     * Register event
     *
     * @param Mage_Index_Model_Event $event Event
     * @return void
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        /* @var $entity Mage_Catalog_Model_Product */
        $entity = $event->getDataObject();

        if ($id = $entity->getId()) {
            $event->setData('product_id', $id);
        } elseif ($ids = $entity->getProductsIds()) {
            $event->setData('product_ids', $ids);
        }
    }

    /**
     * Process event
     *
     * @param Mage_Index_Model_Event $event Event
     * @return void
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        if ($event->getData('product_id') || $event->getData('product_ids')) {
            $this->callEventHandler($event);
        }
    }
}
