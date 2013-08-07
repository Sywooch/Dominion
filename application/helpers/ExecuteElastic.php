<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 18.06.13
 * Time: 18:30
 * To change this template use File | Settings | File Templates.
 */

/**
 * Class for call and execute get logic elastic search
 *
 * Class Helpers_ExecuteElastic
 */
class Helpers_ExecuteElastic extends App_Controller_Helper_HelperAbstract
{

    /**
     * Parameters for Elastic
     *
     * @var array
     */
    private $parameters = array();

    /**
     * Object Elastic Search GET
     *
     * @var ContextSearch_ElasticSearch_BuildExecute_GET
     */
    private $elasticSearchGET;

    /**
     * Set parameters
     *
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Run elastic search
     *
     * @param string $term
     * @param string $formatResult
     * @param string $nameMethod
     * @param int $from
     * @param int $size
     * @return mixed
     * @throws Exception
     */
    public function runElasticGET($term, $formatResult = "convertToArray", $nameMethod = "executeQuery", $from = 0, $size = 10)
    {
        if (empty($this->parameters)) {
            throw new Exception("Error: parameters must be not empty");
        }

        $connect = new ContextSearch_ElasticSearch_Connect($this->parameters);
        $connect->setAction("GET");

        $contextSearch = new ContextSearch_ContextSearchFactory();

        $this->elasticSearchGET = $contextSearch->getQueryBuilderElasticSearch()->createQuery($connect);

        $resultSet = $this->executePrefix($connect->getFields(), $term, $from, $size);
        $results = $resultSet->getResults();
        if (empty($results)) {
            $currentClass = $this;

            $resultSet = $currentClass->$nameMethod($term, $from, $size);
        }

        return $this->elasticSearchGET->$formatResult($resultSet);
    }

    /**
     * Set query prefixs
     *
     * @param array $fields
     * @param string $term
     * @param integer $from
     * @param integer $size
     * @return \Elastica\ResultSet|mixed
     */
    private function executePrefix(array $fields, $term, $from, $size)
    {
        $formatQuery = new ContextSearch_ElasticSearch_FormatQuery();

        $formatQuery->setShould();
        $formatQuery->setFrom($from);
        $formatQuery->setSize($size);

        $data = array();
        foreach ($fields as $field) {
            $data[$field] = $term;
        }

        $formatQuery->setPrefix($data);

        return $this->elasticSearchGET->buildQuery($formatQuery)->execute();
    }

    /**
     * Execute Query
     *
     * @param string $term
     * @param string $from
     * @param integer $size
     * @return \Elastica\ResultSet
     */
    private function executeQuery($term, $from, $size)
    {
        $formatQuery = new ContextSearch_ElasticSearch_FormatQuery();

        $formatQuery->setBool();
        $formatQuery->setShould();
        $formatQuery->setFrom($from);
        $formatQuery->setSize($size);
        $data = array();

        foreach ($this->parameters['search_fields'] as $item) {
            $data[$item] = $term;
        }

        $formatQuery->setQueryString($data);

        return $this->elasticSearchGET->buildQuery($formatQuery)->execute();
    }

    /**
     * Execute match
     *
     * @param string $term
     * @param null| integer $from
     * @param null| integer $size
     * @return \Elastica\ResultSet
     */
    private function executeMatch($term, $from = null, $size = null)
    {
        $formatQuery = new ContextSearch_ElasticSearch_FormatQuery();

        $formatQuery->setMultiMatch($this->parameters['search_fields'], $term);
        $formatQuery->setFrom($from);
        $formatQuery->setSize($size);

        return $this->elasticSearchGET->buildQuery($formatQuery)->execute();
    }

    /**
     * Business logic for execute format data
     *
     * @param  array $items
     * @param string $currencyStrategy
     * @param Helpers_Prices_Recount $recount
     * @param Helpers_Prices_Discount $discount
     * @param bool $formatItem
     *
     * @return array
     */
    public function executeFormatData($items, $currencyStrategy, Helpers_Prices_Recount $recount,
                                      Helpers_Prices_Discount $discount, $formatItem = false)
    {
        $PriceObjectValue = new Format_PricesObjectValue();

        $PriceObjectValue->setRecount($recount);
        $PriceObjectValue->setDiscount($discount);
        $PriceObjectValue->setData($items);
        $PriceObjectValue->setCurrency($currencyStrategy);

        $formatDataElastic = new Format_FormatDataElastic();

        $resultData = array();
        if (empty($items)) {
            $resultData = $items;
        } else if ($formatItem) {
            $resultData = $formatDataElastic->formatDataForResultQuery($PriceObjectValue);
        } else {
            $resultData = $formatDataElastic->formatDataForSearchQuery($PriceObjectValue);
        }

        return $resultData;
    }
}