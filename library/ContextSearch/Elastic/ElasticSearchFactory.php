<?php

use Elastica\Client;
use Elastica\Query;
use Elastica\Document;
use Elastica\Query\QueryString;
use Elastica\Facet\Terms;
use Elastica\Query\Match;
use Elastica\Filter\Prefix;

/**
 * Class ElasticSearchFactory
 *
 * @package library\ContextSearch\Elastic
 */
class ContextSearch_Elastic_ElasticSearchFactory
{
    /**
     * Object Elastica client
     *
     * @var \Elastica_Client
     */
    private $elastica_client;

    /**
     * Object FormatQuery
     *
     * @var FormatQuery
     */
    private $format_query;

    /**
     * Object elastic search model
     *
     * @var ElasticSearchModel
     */
    private $elastic_search_model;

    /**
     * Constructor for create format query
     *
     * @param ContextSearch_FormatQuery $format_query
     */
    public function __construct(ContextSearch_FormatQuery $format_query)
    {
        $this->format_query = $format_query;
        $this->elastica_client = new Client($format_query->getConfig());
        $this->elastic_search_model = new ContextSearch_Elastic_ElasticSearchModel($this->elastica_client, $format_query->getIndex());
    }

    /**
     * Получить обьект ElasticaClient
     *
     * @return \Elastica_Client
     */
    public function getElasticaClient()
    {
        return $this->elastica_client;
    }

    /**
     * Получить обьект Elastica_query
     *
     * @return \Elastica_Query
     */
    public function getElasticaQuery()
    {
        $elastica_query = new Query();

        return $elastica_query;
    }

    /**
     * Получить обьект Document
     *
     * @return \Elastica_Document
     */
    public function getDocument()
    {
        $elastica_doc = new Document();

        return $elastica_doc;
    }

    /**
     * Получить обьект Query String
     *
     * @return \Elastica_Query_QueryString
     */
    public function getQueryString()
    {
        $elastica_query_string = new QueryString();

        return $elastica_query_string;
    }

    /**
     * Получить обьект Facet
     *
     * @return \Elastica_Facet_Terms
     */
    public function getFacets()
    {
        $elastica_facets = new Terms($this->format_query->getIndex());

        return $elastica_facets;
    }

    /**
     * Получить экземпляр класса \ElasticSearchModel
     *
     * @return ElasticSearchModel
     */
    public function getElasticSearchModel()
    {
        return $this->elastic_search_model;
    }

    /**
     * Create object for execute elastic query with prefix
     *
     * @return Prefix
     */
    public function getMatch()
    {
        return new Match();
    }

    /**
     * Format execute search
     *
     * @return Prefix
     */
    public function getQueryPrefix()
    {
        return new Prefix();
    }
}
