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
class ElasticsearchController extends App_Controller_Frontend_Action
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
        $term = $this->_getParam("term");

        if (empty($term)) {
            $this->_helper->json($term);
        }
        $parameters = $this->config->toArray();
//        $connect = new ContextSearch_ElasticSearch_Connect($parameters['search_engine']);
//        $connect->setAction("GET");
//
//        $formatQuery = new ContextSearch_ElasticSearch_FormatQuery();
//        $data = array("_all" => $term);
//        $formatQuery->setQueryString($data);
//
//        $contextSearch = new ContextSearch_ContextSearchFactory();
//        $queryBuilder = $contextSearch->getQueryBuilderElasticSearch();
//        $elasticSearchGET = $queryBuilder->createQuery($connect);
//
//        $resultArray = $queryObject->convertToArray();

        $helpersElasticExecute = $this->_helper->helperLoader("ExecuteElastic");
        $results = $helpersElasticExecute->runElasticGET($parameters, $term);

        if (empty($resultArray)) {
            $this->_helper->json($results);
        }
        $PriceObjectValue = new Format_PricesObjectValue();

//        $PriceObjectValue = $this->_helper->helperLoader("Format_PricesObjectValue");
        $PriceObjectValue->setRecount($this->_helper->helperLoader("Prices_Recount"));
        $PriceObjectValue->setDiscount($this->_helper->helperLoader("Prices_Discount"));
        $PriceObjectValue->setData($resultArray);
        $PriceObjectValue->setCurrency($this->currency);

        $formatDataElastic = new Format_FormatDataElastic();

//        $helperFormatData = $this->_helper->helperLoader("Format_FormatDataElastic");


        $formatData = $formatDataElastic->formatDataForResultQuery($PriceObjectValue);

        $this->_helper->json($formatData);
    }
}