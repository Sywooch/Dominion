<?php
namespace library\ContextSearch\Elastic;

use library\ContextSearch\Elastic\ElasticSearchModel;
use library\ContextSearch\FormatQuery;

/**
 * Class ElasticSearchFactory
 *
 * @package library\ContextSearch\Elastic
 */
class ElasticSearchFactory
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
     * Конструктор cоединение с ElasticSearch
     *
     * @param FormatQuery $format_query
     */
    public function __construct(FormatQuery $format_query)
    {
        $this->format_query = $format_query;
        $this->elastica_client = new \Elastica_Client($format_query->getConfig());
        $this->elastic_search_model = new ElasticSearchModel($this->elastica_client, $format_query->getIndex());
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
        $elastica_query = new \Elastica_Query();

        return $elastica_query;
    }

    /**
     * Получить обьект Document
     *
     * @return \Elastica_Document
     */
    public function getDocument()
    {
        $elastica_doc = new \Elastica_Document();

        return $elastica_doc;
    }

    /**
     * Получить обьект Query String
     *
     * @return \Elastica_Query_QueryString
     */
    public function getQueryString()
    {
        $elastica_query_string = new \Elastica_Query_QueryString();

        return $elastica_query_string;
    }

    /**
     * Получить обьект Facet
     *
     * @return \Elastica_Facet_Terms
     */
    public function getFacets()
    {
        $elastica_facets = new \Elastica_Facet_Terms($this->format_query->getIndex());

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
}
