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
        $this->queryBuilder[] = $formatData->getFrom();
        $this->queryBuilder[] = $formatData->getSize();

        return $this;
    }

    /**
     * Filter builder query
     *
     * @param ContextSearch_ElasticSearch_FormatQuery $formatData
     */
    public function buildFilter(ContextSearch_ElasticSearch_FormatQuery $formatData)
    {
        $this->queryBuilder = $formatData->getMatchAll();
        $this->queryBuilder['filter'] = $formatData->getPrefix();
    }

    public function buildCount(ContextSearch_ElasticSearch_FormatQuery $formatData)
    {

    }

    /**
     * Execute GET query
     */
    public function execute()
    {
        $jsonParse = json_encode($this->queryBuilder);

        $builder = new Builder($jsonParse);
        $query = new Query($builder);
        $search = new Search(new Client);

        $result = $search->addIndex($this->parameters->getIndex())->addType($this->parameters->getType())->search($query);

        return $result;
    }
}