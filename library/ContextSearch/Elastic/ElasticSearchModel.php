<?php

use Elastica\Client;
use Elastica\Document;
use Elastica\Facet\Terms;
use Elastica\Query;
use Elastica\Query\QueryString;
use Elastica\Filter\Prefix;

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
     * Name products
     */
    const NAME_PREFIX = "products";

    /**
     * Конструктор ElasticSearch
     *
     * @param Client $elastica_client
     * @param string $config_index
     *
     * @throws Exception
     */
    public function __construct(Client $elastica_client, $config_index)
    {
        if (!isset($elastica_client) || !isset($config_index) || !is_string($config_index)) {
            throw new \Exception("Error:The input parameters is not correct");
        }

        $this->index = $elastica_client->getIndex($config_index);
    }

    /**
     * Save data in index Elastic Search
     *
     * @param Document $elastica_doc
     * @param array    $data
     * @param string   $type
     *
     * @throws Exception
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
    }

    /**
     * Execute search in elastic by query
     *
     * @param Query                $elastica_query
     * @param QueryString | Prefix $type_query
     *
     * @return \Elastica\ResultSet
     * @throws Exception
     */
    public function searchInElastic($type_query, Query $elastica_query = null)
    {
        if (!($type_query instanceof QueryString) && !($type_query instanceof Prefix)) {
            throw new Exception("Error: class does not exist correct type, class: " . __CLASS__ . ", Line: " . __LINE__);
        }
        if (!empty($elastica_query)) {
            $type_query = $elastica_query->setQuery($type_query);
        }

        return $this->index->search($type_query);
    }

    /**
     * Add facets to ElasticSearch query
     *
     * @param Terms   $elastica_facet
     * @param Query   $elastica_query
     * @param strings $config_name
     * @param array   $fields
     *
     * @throws Exception
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