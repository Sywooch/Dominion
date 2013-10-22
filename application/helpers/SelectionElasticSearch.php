<?php

/**
 * Class SelectionElasticSearch
 */
class Helpers_SelectionElasticSearch extends App_Controller_Helper_HelperAbstract
{
    /**
     * Elastic search get curl
     *
     * @var GET
     */
    private $elasticSearchGET;

    /**
     * Result of search in elasticSearch
     *
     * @var Result
     */
    private $resultSet;

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
     * Selection
     *
     * @param Helpers_ObjectValue_ObjectValueSelection $objectValueSelection
     * @throws Exception
     */
    public function selection(Helpers_ObjectValue_ObjectValueSelection $objectValueSelection)
    {
        $dataAttributes = $objectValueSelection->getDataAttributes();

        if (empty($dataAttributes)) {
            throw new Exception("Error, data sample and dataslider are empty");
        }

        $dataWithBrands = $objectValueSelection->getDataAttributesWithBrands();
        if (!empty($dataWithBrands)) {
            $filterFormat = $this->formatDataSelect($dataWithBrands, new ContextSearch_ElasticSearch_FormatFilter(), $objectValueSelection);

            $this->resultSet['brands'] = $this->executeElastic($filterFormat);
        }

        if ($this->resultSet['brands'] instanceof \Elastica\ResultSet && !$this->elasticSearchGET->getTotalHits($this->resultSet['brands'])) return;

        $filterFormat = $this->formatDataSelect($dataAttributes, new ContextSearch_ElasticSearch_FormatFilter(), $objectValueSelection);
        $this->resultSet['attributes'] = $this->executeElastic($filterFormat);
    }

    /**
     * Format data selection
     *
     * @param array $dataAttributes
     * @param ContextSearch_ElasticSearch_FormatFilter $filterFormat
     * @param Helpers_ObjectValue_ObjectValueSelection $objectValueSelection
     * @return ContextSearch_ElasticSearch_FormatFilter
     */
    private function formatDataSelect(array $dataAttributes, ContextSearch_ElasticSearch_FormatFilter $filterFormat, Helpers_ObjectValue_ObjectValueSelection $objectValueSelection)
    {
        foreach ($dataAttributes as $key => $value) {
            if ($objectValueSelection->getDataSliderMin($key)) {
                $filterFormat->setFromTo($key, $objectValueSelection->getDataSliderMin($key), $objectValueSelection->getDataSliderMax($key), "must");

                continue;
            } else if ($objectValueSelection->getCatalogueID($key)) {
                $filterFormat->setTerms($key, $value, "must");
            }

            foreach ($value as $subKey => $val) {
                $filterFormat->setTerms($subKey, $val);
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
     * Get elements
     *
     * @param string $modified
     * @return mixed
     */
    public function getElements($modified)
    {
        if (!isset($this->resultSet[$modified])) return array();

        return $this->elasticSearchGET->convertToArray($this->resultSet[$modified]);
    }
}