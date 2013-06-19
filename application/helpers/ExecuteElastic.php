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
     * @param array $parameters
     * @param string $term
     */
    public function runElasticGET($parameters, $term)
    {
        $connect = new ContextSearch_ElasticSearch_Connect($parameters);
        $connect->setAction("GET");

        $formatQuery = new ContextSearch_ElasticSearch_FormatQuery();

        $formatQuery->setValue($term);
        $formatQuery->setMatchAll();
        $formatQuery->setFields($connect->getFields());

        $contextSearch = new ContextSearch_ContextSearchFactory();
        $queryBuilder = $contextSearch->getQueryBuilderElasticSearch();

        $elasticSearchGET = $queryBuilder->createQuery($connect);

        $results = $elasticSearchGET->buildFilter($formatQuery);

        if (empty($results)) {
            $data = array("_all" => $term);
            $formatQuery->setQueryString($data);
            $results = $elasticSearchGET->buildQuery($formatQuery)->execute();
        }

        return $elasticSearchGET->convertToArray($results);
    }
}