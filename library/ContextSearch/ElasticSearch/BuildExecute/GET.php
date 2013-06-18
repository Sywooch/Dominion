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
     *
     * @param ContextSerch_ElasticSearch_Connect $connect
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
        $this->queryBuilder['query']['bool']['must'] = $formatData->getQueryString();
        $this->queryBuilder['filter'] = $formatData->getPrefix();
        $this->queryBuilder[] = $formatData->getFrom();
        $this->queryBuilder[] = $formatData->getSize();

        return $this;
    }

    /**
     * Execute GET query
     */
    public function execute()
    {
        $jsonParse = json_encode($this->queryBuilder);

        $query = new Query($jsonParse);
        $search = new Search(new Client);

        $result = $search->addIndex("goods")->search($query);

        return $result;
    }
}