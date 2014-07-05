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
     * @var Result
     */
    private $resultSet;

    /**
     * Aggregation builder
     *
     * @var Format_Aggregation_Builder
     */
    private $aggregationBuilder;

    /**
     * Brands and attributes constant
     */
    const BRANDS = "brands";
    const ATTRIBUTES = "attributes";
    const ITEMS = "item_id";
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

        $queryJson = $aggregationBuilder->buildQueryAggregation($aggregationObjectValue);

        $this->elasticSearchGET->buildQueryAggregation($queryJson);
        $this->elasticSearchGET->setSize(0);

        $resultData = $this->elasticSearchGET->execute();
    }
    /**
     * Format data selection
     *
     * @param array $dataAttributes
     * @param ContextSearch_ElasticSearch_FormatFilter $filterFormat
     * @param Helpers_ObjectValue_ObjectValueSelection $objectValueSelection
     *
     * @return ContextSearch_ElasticSearch_FormatFilter
     */
    private function formatDataSelect(array $dataAttributes, ContextSearch_ElasticSearch_FormatFilter $filterFormat, Helpers_ObjectValue_ObjectValueSelection $objectValueSelection)
    {
        foreach ($dataAttributes as $key => $value) {
            if ($objectValueSelection->getDataSliderMin($key)) {
                $filterFormat->addFilterRange(
                    $key,
                    $objectValueSelection->getDataSliderMin($key),
                    $objectValueSelection->getDataSliderMax($key)
                );

                continue;
            } else if ($objectValueSelection->getCatalogueID($key)) {
                $filterFormat->addQueryTerm($key, $value);

            }

            if (!is_array($value)) {
                continue;
            }

            foreach ($value as $subKey => $val) {
                if ($objectValueSelection->isBrand($subKey, $val)) {
                    if (!$objectValueSelection->issetAttributes()) {
                        $filterFormat->addFilterTerm($subKey, $val, ContextSearch_ElasticSearch_FormatFilter::BOOL_OR);
                    } else {
                        $filterFormat->addFilterTermChild($subKey, $val);
                    }

                    continue;
                }

                $filterFormat->addFilterTermChild(
                    $subKey,
                    $val,
                    ContextSearch_ElasticSearch_FormatFilter::BOOL_AND,
                    ContextSearch_ElasticSearch_FormatFilter::BOOL_AND,
                    $objectValueSelection->issetAttributesDouble($subKey) ? ContextSearch_ElasticSearch_FormatFilter::BOOL_OR : null
                );
            }
        }

        return $filterFormat;
    }

    /**
     * Execute elastic
     *
     * @param ContextSearch_ElasticSearch_FormatFilter $filterFormat
     * @return mixed
     */
    private function executeElastic(ContextSearch_ElasticSearch_FormatFilter $filterFormat)
    {
        $filterFormat->setFrom(0);
        $filterFormat->setSize($this->elasticSearchGET->getTotalHits($this->elasticSearchGET->buildQueryFilter($filterFormat)->execute()));

        return $this->elasticSearchGET->buildQuery($filterFormat)->execute();
    }

    /**
     * Get count elements
     *
     * @return mixed
     */
    public function getCountElements()
    {
        return $this->elasticSearchGET->getTotalHits($this->resultSet);
    }

    /**
     * Get brands
     *
     * @return array
     */
    public function getBrands()
    {
        return $this->getElements(self::BRANDS);
    }

    /**
     * Get attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->getElements(self::ATTRIBUTES);
    }

    /**
     * Get items id
     *
     * @return array
     */
    public function getItemsID()
    {
        return $this->getElements(self::ITEMS);
    }

    /**
     * Get elements
     *
     * @param string $modified
     * @return mixed
     */
    private function getElements($modified)
    {
        if (!isset($this->resultSet[$modified])) return array();

        return $this->elasticSearchGET->convertToArray($this->resultSet[$modified]);
    }
}