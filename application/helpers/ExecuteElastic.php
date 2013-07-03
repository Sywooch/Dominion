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

        $formatQuery = new ContextSearch_ElasticSearch_FormatQuery();

        $formatQuery->setValue($term);
        $formatQuery->setMatchAll();
        $formatQuery->setFields($connect->getFields());
        $formatQuery->setSize($size);
        $formatQuery->setFrom($from);

        $contextSearch = new ContextSearch_ContextSearchFactory();
        $queryBuilder = $contextSearch->getQueryBuilderElasticSearch();

        $elasticSearchGET = $queryBuilder->createQuery($connect);

        $results = $elasticSearchGET->buildFilter($formatQuery);

        if (empty($results)) {
            $currentClass = $this;

            $results = $currentClass->$nameMethod($formatQuery, $elasticSearchGET, $term, $from, $size);
        }

        return $elasticSearchGET->$formatResult($results);
    }

    /**
     * Execute Query
     *
     * @param ContextSearch_ElasticSearch_FormatQuery $formatQuery
     * @param ContextSearch_ElasticSearch_BuildExecute_GET $elasticSearchGET
     * @param string $term
     * @param string $from
     * @param integer $size
     * @return \Elastica\ResultSet
     */
    private function executeQuery(
        ContextSearch_ElasticSearch_FormatQuery $formatQuery,
        ContextSearch_ElasticSearch_BuildExecute_GET $elasticSearchGET,
        $term,
        $from,
        $size
    )
    {
        $formatQuery->clearQuery();

        $data = array("_all" => $term);
        $formatQuery->setBool();
        $formatQuery->setMust();
        $formatQuery->setFrom($from);
        $formatQuery->setSize($size);
        $formatQuery->setQueryString($data);

        return $elasticSearchGET->buildQuery($formatQuery)->execute();
    }

    /**
     * Execute match
     *
     * @param ContextSearch_ElasticSearch_FormatQuery $formatQuery
     * @param ContextSearch_ElasticSearch_BuildExecute_GET $elasticSearchGET
     * @param $term
     * @param null| integer $from
     * @param null| integer $size
     * @return \Elastica\ResultSet
     */
    private function executeMatch(
        ContextSearch_ElasticSearch_FormatQuery $formatQuery,
        ContextSearch_ElasticSearch_BuildExecute_GET $elasticSearchGET,
        $term,
        $from = null,
        $size = null
    )
    {
        $formatQuery->clearQuery();

        $formatQuery->setMatch($this->parameters['main_field'], $term);
        $formatQuery->setFrom($from);
        $formatQuery->setSize($size);

        return $elasticSearchGET->buildQuery($formatQuery)->execute();
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
    public function executeFormatData(
        $items,
        $currencyStrategy,
        Helpers_Prices_Recount $recount,
        Helpers_Prices_Discount $discount,
        $formatItem = false)
    {
        $PriceObjectValue = new Format_PricesObjectValue();

        $PriceObjectValue->setRecount($recount);
        $PriceObjectValue->setDiscount($discount);
        $PriceObjectValue->setData($items);
        $PriceObjectValue->setCurrency($currencyStrategy);

        $formatDataElastic = new Format_FormatDataElastic();

        if ($formatItem) {

            return $formatDataElastic->formatDataForResultQuery($PriceObjectValue);
        }

        return $formatDataElastic->formatDataForSearchQuery($PriceObjectValue);
    }
}