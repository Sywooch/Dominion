<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 30.05.13
 * Time: 16:15
 * To change this template use File | Settings | File Templates.
 */

/**
 * Controller for execute Elastic Search
 *
 * Class ElasticSearchController
 */
class ElasticSearchController extends App_Controller_Frontend_Action
{
    /**
     * Config parameters
     *
     * @var objects
     */
    private $config;

    /**
     * Initialize system controller for get request
     */
    public function init()
    {
        parent::init();

        $this->config = Zend_Registry::get("config");

        if (empty($this->config)) {
            throw new Exception("Error: configuration is not include class: " . __CLASS__ . ", line: " . __LINE__);
        }
    }

    /**
     * Action for execute elastic search
     */
    public function indexAction()
    {
        $formatQuery = new ContextSearch_FormatQuery(
            $this->config->search_engine->name,
            $this->_getParam("event"),
            $this->config->search_engine->index
        );

        $formatQuery->setHost($this->config->search_engine->host);
        $formatQuery->setType($this->config->search_engine->type_products);
        $formatQuery->setQuery($this->_getParam("term"));
        $formatQuery->setNameFields($this->config->search_engine->name_fields->toArray());

        $queryObject = new ContextSearch_Query();
        $queryObject->execQuery($formatQuery);

        $resultArray = $queryObject->convertToArray();

        $helperPriceObjectValue = $this->_helper->helperLoader("PricesObjectValue");
        $helperPriceObjectValue->setRecount($this->_helper->helperLoader("Prices_Recount"));
        $helperPriceObjectValue->setDiscount($this->_helper->helperLoader("Prices_Discount"));
        $helperPriceObjectValue->setData($resultArray);
        $helperPriceObjectValue->setCurrency($this->currency);

        $helperFormatData = $this->_helper->helperLoader("FormatDataElastic");
        $helperFormatData->setPricesObjectValue($helperPriceObjectValue);

        $helperFormatData->formatPrices();

        $formatData = $helperFormatData->formatDataForResultQuery($resultArray);

        $this->getResponse()->setHeader("Content-Type", "text/html")
            ->setBody(json_encode($formatData))
            ->sendResponse();

        exit();
    }
}