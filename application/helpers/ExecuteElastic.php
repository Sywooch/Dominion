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
     * Run elastic GET
     *
     * @param $parameters
     * @param $term
     * @param string $formatResult
     * @param int $size
     *
     * @return mixed
     */
    public function runElasticGET($parameters, $term, $formatResult = "convertToArray", $size = 10)
    {
        $connect = new ContextSearch_ElasticSearch_Connect($parameters);
        $connect->setAction("GET");

        $formatQuery = new ContextSearch_ElasticSearch_FormatQuery();

        $formatQuery->setValue($term);
        $formatQuery->setMatchAll();
        $formatQuery->setFields($connect->getFields());
        $formatQuery->setSize($size);

        $contextSearch = new ContextSearch_ContextSearchFactory();
        $queryBuilder = $contextSearch->getQueryBuilderElasticSearch();

        $elasticSearchGET = $queryBuilder->createQuery($connect);

        $results = $elasticSearchGET->buildFilter($formatQuery);

        if (empty($results)) {
            $formatQuery->clearQuery();

            $data = array("_all" => $term);
            $formatQuery->setBool();
            $formatQuery->setMust();
            $formatQuery->setSize($size);
            $formatQuery->setQueryString($data);

            $results = $elasticSearchGET->buildQuery($formatQuery)->execute();
        }

        return $elasticSearchGET->$formatResult($results);
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

        $formatDataElastic->formatPrices($PriceObjectValue);

        return $PriceObjectValue->getItems();
    }

}