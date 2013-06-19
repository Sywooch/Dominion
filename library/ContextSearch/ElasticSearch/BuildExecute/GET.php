<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 17.06.13
 * Time: 17:05
 * To change this template use File | Settings | File Templates.
 */
use Elastica\Query;
use Elastica\Search;
use Elastica\Client;
use Elastica\Query\Builder;
use Elastica\Filter\Prefix;
use Elastica\ResultSet;

/**
 * ContextSearch BuildExecuteGet
 *
 * Class ContextSearch_ElasticSearch_BuildExecute_GET
 */
class ContextSearch_ElasticSearch_BuildExecute_GET extends ContextSearch_ElasticSearch_BuildExecute_QueryAbstract
{

    /**
     * Format Query Builder
     *
     * @var array
     */
    private $queryBuilder = array();

    /**
     * Search object
     *
     * @var object
     */
    private $querySearch;


    /**
     * Constructor for connect
     */
    public function __construct(ContextSearch_ElasticSearch_Connect $connect)
    {
        parent::__construct($connect);
    }


    /**
     * Build Query
     *
     * @param ContextSearch_ElasticSearch_FormatQuery $formatData
     *
     * @return $this
     */
    public function buildQuery(ContextSearch_ElasticSearch_FormatQuery $formatData)
    {
        $this->queryBuilder = $formatData->getQueryString();
        $this->queryBuilder['from'] = $formatData->getFrom();
        $this->queryBuilder['size'] = $formatData->getSize();

        $jsonParse = json_encode($this->queryBuilder);
        $builder = new Builder($jsonParse);

        $this->querySearch = new Query($builder);


        return $this;

    }

    /**
     * Filter builder query
     *
     * @param ContextSearch_ElasticSearch_FormatQuery $formatData
     *
     * @return \Elastica\ResultSet|mixed
     */
    public function buildFilter(ContextSearch_ElasticSearch_FormatQuery $formatData)
    {
        $fields = $formatData->getFields();
        $this->querySearch = new Prefix();
        $this->querySearch->setPrefix($formatData->getValue());
        foreach ($fields as $field) {
            $this->querySearch->setField($field);
            $response = $this->execute();
            $results = $response->getResults();
            if (!empty($results)) {

                return $response;
            }
        }

        return null;
    }

    public function buildCount(ContextSearch_ElasticSearch_FormatQuery $formatData)
    {

    }

    /**
     * Execute GET query
     */
    public function execute()
    {
        $search = new Search(new Client);

        $result = $search->addIndex($this->parameters->getIndex())->addType($this->parameters->getType())->search($this->querySearch);

        return $result;
    }

    /**
     * Convert to array
     *
     * @param ResultSet $resultSet
     *
     * @return array|void
     */
    public function convertToArray(ResultSet $resultSet)
    {
        $formatArr = array();
        foreach ($resultSet->getResults() as $result) {
            $formatArr[] = $result->getData();
        }

        return $formatArr;
    }

    /**
     * Convert to jSON
     *
     * @param ResultSet $resultSet
     *
     * @return string
     */
    public function convertToJson(ResultSet $resultSet)
    {
        return json_encode($resultSet->getResults());
    }

}