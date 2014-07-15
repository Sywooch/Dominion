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
     * Search object
     *
     * @var object
     */
    private $querySearch;

    /**
     * From
     *
     * @var integer
     */
    private $from;

    /**
     * Size
     *
     * @var integer
     */
    private $size;

    /**
     * Constructor for connect
     */
    public function __construct(ContextSearch_ElasticSearch_Connect $connect)
    {
        parent::__construct($connect);
    }


    /**
     *  Build Query
     *
     * @param ContextSearch_ElasticSearch_FormatInterface $formatData
     *
     * @return $this
     */
    public function buildQuery(ContextSearch_ElasticSearch_FormatInterface $formatData)
    {
        $this->size = $formatData->getSize();
        $this->from = $formatData->getFrom();
        $jsonParse = json_encode($formatData->getFormatQuery());

        $builder = new Builder($jsonParse);

        $this->querySearch = new Query($builder);

        return $this;
    }

    /**
     * Build query filter
     *
     * @param ContextSearch_ElasticSearch_FormatInterface $formatData
     *
     * @return $this
     */
    public function buildQueryFilter(ContextSearch_ElasticSearch_FormatInterface $formatData)
    {
        $this->querySearch = new Query(new Builder(json_encode($formatData->getFormatQuery())));

        return $this;
    }

    /**
     * Build query aggregation
     *
     * @param string $queryJson
     */
    public function buildQueryAggregation($queryJson)
    {
        $this->querySearch = new Query(json_decode($queryJson, true));
    }

    /**
     * Set size
     *
     * @param integer $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * Set from
     *
     * @param integer $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * Filter builder query
     *
     * @param ContextSearch_ElasticSearch_FormatInterface $formatData
     *
     * @return ResultSet|mixed|null
     */
    public function buildFilter(ContextSearch_ElasticSearch_FormatInterface $formatData)
    {
        $this->querySearch = new Prefix();
        $this->querySearch->setPrefix($formatData->getValue());

        $this->size = $formatData->getSize();
        $this->from = $formatData->getFrom();

        foreach ($formatData->getFields() as $field) {
            $this->querySearch->setField($field);
            $response = $this->execute();
            $results = $response->getResults();

            if (!empty($results)) return $response;
        }

        return null;
    }

    /**
     * Execute GET query
     */
    public function execute()
    {
        $client = new Client();
        $connection = new \Elastica\Connection();
        $connection->setHost($this->getHost());

        $client->addConnection($connection);

        $search = new Search($client);
        $search->setOption("from", $this->from);
        $search->setOption("size", $this->size);

        $result = $search->addIndex($this->getParameters()->getIndex())->addType($this->getParameters()->getType())->search($this->querySearch);

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
        return json_encode($this->convertToArray($resultSet));
    }

    /**
     * Getter for total hits
     *
     * @param ResultSet $resultSet
     *
     * @return int
     */
    public function getTotalHits(ResultSet $resultSet)
    {
        return $resultSet->getTotalHits();
    }

}