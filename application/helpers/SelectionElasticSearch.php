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
        $filterFormat = new ContextSearch_ElasticSearch_FormatFilter();
        $filterFormat->setBool("should");

        $dataSlider = $objectValueSelection->getDataSlider();
        $dataSample = $objectValueSelection->getDataSample();

        if (empty($dataSample) && empty($dataSlider)) {
            throw new Exception("Error, data sample and dataslider are empty");
        }

        foreach ($dataSlider as $key => $value) {
            $filterFormat->setFromTo($key, $objectValueSelection->getDataSliderMin($key), $objectValueSelection->getDataSliderMax($key));
        }

        foreach ($dataSample as $value) {
            foreach ($value as $key => $val) {
                $filterFormat->setTerms($key, $val);
            }
        }

        $filterFormat->setFrom(0);
        $filterFormat->setSize($this->elasticSearchGET->getTotalHits($this->elasticSearchGET->buildQueryFilter($filterFormat)->execute()));

        $this->resultSet = $this->elasticSearchGET->buildQuery($filterFormat)->execute();
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
     * @return mixed
     */
    public function getElements()
    {
        return $this->elasticSearchGET->convertToArray($this->resultSet);
    }
}