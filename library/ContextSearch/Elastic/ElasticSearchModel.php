<?php

use Elastica\Client;
use Elastica\Document;
use Elastica\Facet\Terms;
use Elastica\Query;
use Elastica\Query\QueryString;

/**
 * Class ElasticSearchModel
 *
 * @package library\ContextSearch\Elastic
 */
class ContextSearch_Elastic_ElasticSearchModel
{
    /**
     * Index for search
     *
     * @var integer
     */
    private $index;

    /**
     * Method get
     */
    const NAME_METHOD = 'get';

    /**
     * Конструктор ElasticSearch
     *
     * @param \Elastica_Client $elastica_client
     * @param  string          $config_index
     */
    function __construct(Client $elastica_client, $config_index)
    {
        if (!isset($elastica_client) || !isset($config_index) || !is_string($config_index))
            throw new \Exception("Error:The input parameters is not correct");
        $this->index = $elastica_client->getIndex($config_index);
    }

    /**
     * Заполнить данные в индекс ElasticSearch
     *
     * @param \Elastica_Document $elastica_doc
     * @param array              $data
     * @param string             $type
     *
     * @throws \Exception
     */
    public function putData(Document $elastica_doc, array $data, $type)
    {
        if (empty($data) || empty($type)) {
            throw new \Exception("Error: data or type is empty for put in elastica, class:" . __CLASS__ . " line:" . __LINE__);
        }

        $main_type = $this->index->getType($type);
        for ($i = 0; $i < count($data); $i++) {
            $elastica_doc->setId($i);
            $elastica_doc->setData($data[$i]);
            $main_type->addDocument($elastica_doc);
        }
        $rr = 0;
    }

    /**
     * Поиск по запросу в ElasticSearch
     *
     * @param \Elastica_Query             $elastica_query
     * @param \Elastica_Query_QueryString $elastica_query_string
     * @param  string                     $query
     *
     * @return mixed
     * @throws \Exception
     */
    public function searchInElastic(Query $elastica_query, QueryString $elastica_query_string, $query)
    {
        if (!isset($query) && !is_string($query) || !isset($elastica_query) || !isset($elastica_query_string))
            throw new \Exception("Error: input parameters is null");
        $elastica_query_string->setQuery($query);
        $elastica_query->setQuery($elastica_query_string);

        return $this->index->search($elastica_query);
    }

    /**
     * Добавить facets для ElasticSearch
     *
     * @param \Elastica_Facet_Terms $elastica_facet
     * @param \Elastica_Query       $elastica_query
     * @param array                 $config_name
     * @param array                 $fields
     *
     * @throws \Exception
     */
    public function facetsData(Terms $elastica_facet, Query $elastica_query, $config_name, array $fields)
    {
        if (!isset($fields) || !isset($elastica_facet) || !isset($elastica_query) || !isset($config_name) && !is_string($config_name))
            throw new \Exception("Error:the input parameters is null");
        $elastica_facet->setName($config_name);
        $elastica_facet->setFields($fields);
        $elastica_query->addFacet($elastica_facet);
    }

    /**
     * Удалить тип с ElasticSearch
     *
     * @param string $type
     *
     * @throws \Exception
     */
    public function deleteType($type)
    {
        if (!isset($type) || !is_string($type))
            throw new \Exception("Error:The input parameters does not correct type");
        $type_delete = $this->index->getType($type);
        $type_delete->delete();
    }

}