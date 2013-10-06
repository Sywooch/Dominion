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
     * Build Query
     *
     * @param ContextSearch_ElasticSearch_FormatQuery $formatData
     *
     * @return $this
     */
    public function buildQuery(ContextSearch_ElasticSearch_FormatQuery $formatData)
    {
        $this->size = $formatData->getSize();
        $this->from = $formatData->getFrom();
        $jsonParse = json_encode($formatData->getFormatQuery());

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

        $this->size = $formatData->getSize();
        $this->from = $formatData->getFrom();

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

    /**
     * Execute GET query
     */
    public function execute()
    {
        $search = new Search(new Client);
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