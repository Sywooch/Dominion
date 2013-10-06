<?php

/**
 * Class SelectionElasticSearch
 */
class SelectionElasticSearch extends App_Controller_Helper_HelperAbstract
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
     * Set connect to elastic search
     *
     * @param array $parameters
     * @param string $type
     */
    public function __construct(array $parameters, $type)
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
     * @param ObjectValueSelection $objectValueSelection
     * @throws Exception
     */
    public function selection(ObjectValueSelection $objectValueSelection)
    {
        $filterFormat = new ContextSearch_ElasticSearch_FormatFilter();
        $filterFormat->setBool("must");

        if ($dataSlider = $objectValueSelection->getDataSlider()) {
            foreach ($dataSlider as $key => $value) {
                $filterFormat->setFromTo($key, $objectValueSelection->getDataSliderMin($key), $objectValueSelection->getDataSliderMax($key));
            }
        }

        if ($dataSample = $objectValueSelection->getDataSample()) {
            foreach ($dataSample as $key => $value) {
                $filterFormat->setTerms($key, $value);
            }
        }

        if (empty($dataSample) && empty($dataSlider)) {
            throw new Exception("Error, data sample and dataslider are empty");
        }

        $resultQuery = $filterFormat->buildQuery();

        $this->resultSet = $this->elasticSearchGET->buildQuery($resultQuery)->execute();
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