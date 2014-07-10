<?php

/**
 * Class SelectionElasticSearch
 */
class Helpers_SelectionElasticSearch extends App_Controller_Helper_HelperAbstract
{
    /**
     * Elastic search get curl
     *
     * @var ContextSearch_ElasticSearch_BuildExecute_GET
     */
    private $elasticSearchGET;

    /**
     * Result of search in elasticSearch
     *
     * @var \Elastica\ResultSet
     */
    private $resultSet;

    /**
     * Brands and attributes constant
     */
    const BRANDS = "brands";
    const ATTRIBUTES = "attributes";
    const ITEMS = "items";
    const PRICE_MIN = "price_min";
    const PRICE_MAX = "price_max";
    const POSITION_PRICES = 2;
    const POSITION_ATTRIBUTES = 0;
    const POSITION_BRANDS = 1;

    /**
     * Connect to elastic search
     *
     * @param array $parameters
     * @param $type
     */
    public function connect(array $parameters, $type)
    {
        $connect = new ContextSearch_ElasticSearch_Connect($parameters);
        $connect->setAction("GET");
        $connect->setType($type);

        $contextSearch = new ContextSearch_ContextSearchFactory();

        $this->elasticSearchGET = $contextSearch->getQueryBuilderElasticSearch()->createQuery($connect);
    }


    /**
     * Selection from elastic search
     *
     * @param Helpers_ObjectValue_ObjectValueSelection $objectValueSelection
     *
     * @return \Elastica\ResultSet|mixed
     */
    public function selection(
        Helpers_ObjectValue_ObjectValueSelection $objectValueSelection
    )
    {
        $aggregationObjectValue = new Format_Aggregation_ObjectValueAggregation();
        $aggregationObjectValue->setCatalogueID($objectValueSelection->getCatalogueID());
        $aggregationObjectValue->setAttributes($objectValueSelection->getAttributes());
        $aggregationObjectValue->setBrands($brands = $objectValueSelection->getBrands());
        $aggregationObjectValue->setPriceMin($objectValueSelection->getPriceMin());
        $aggregationObjectValue->setPriceMax($objectValueSelection->getPriceMax());
        $aggregationObjectValue->setAggregation(
            (empty($brands) || !$objectValueSelection->isCheckBrands())
                ? $objectValueSelection->getAggregationWithBrands()
                : $objectValueSelection->getAggregationWithoutBrands()
        );

        $aggregationBuilder = new Format_Aggregation_Builder($objectValueSelection->getColumns());

        $this->elasticSearchGET->buildQueryAggregation($aggregationBuilder->buildQueryAggregation($aggregationObjectValue));
        $this->elasticSearchGET->setSize(0);

        $this->resultSet = $this->elasticSearchGET->execute();
    }

    /**
     * Get count elements
     *
     * @return integer
     */
    public function getCountElements()
    {
        return $this->elasticSearchGET->getTotalHits($this->resultSet);
    }

    /**
     * Get aggregations result
     *
     * @return array
     */
    public function getAggregationsResult()
    {
        return $this->resultSet->getAggregations();
    }
}