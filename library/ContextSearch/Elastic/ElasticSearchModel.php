<?php

namespace library\ContextSearch\Elastic;
/**
 * Class ElasticSearchModel
 *
 * @package library\ContextSearch\Elastic
 */
class ElasticSearchModel
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
    function __construct(\Elastica_Client $elastica_client, $config_index)
    {
        if (!isset($elastica_client) || !isset($config_index) || !is_string($config_index))
            throw new \Exception("Error:The input parameters is not correct");
        $this->index = $elastica_client->getIndex($config_index);
    }

    /**
     * Заполнить данные в индекс ElasticSearch
     *
     * @param \Elastica_Document $elastica_doc
     * @param \GetElasticSearch  $elastic_searchSQL
     * @param  string            $type
     *
     * @throws \Exception
     */
    public function putData(\Elastica_Document $elastica_doc, \GetElasticSearch $elastic_searchSQL, $type)
    {
        if (!isset($type) || !is_string($type) || !isset($elastica_doc) || !isset($elastic_searchSQL))
            throw new \Exception("Error: input parameters doesn't correct");
        $main_type = $this->index->getType($type);
        $list = $this->getDataFromSQL($elastic_searchSQL, $type);
        if (!isset($list))
            throw new \Exception("Error: The variable 'list' is null");
        for ($i = 0; $i < count($list); $i++) {
            $elastica_doc->setId($i);
            $elastica_doc->setData($list[$i]);
            $main_type->addDocument($elastica_doc);
        }
    }

    /**
     * Получить Данные с модели SQL
     *
     * @param \GetElasticSearch $elastic_searchSQL
     * @param string            $type
     *
     * @return mixed
     * @throws \Exception
     */
    private function getDataFromSQL(\GetElasticSearch $elastic_searchSQL, $type)
    {
        if (!isset($type) || !is_string($type))
            throw new \Exception("Error:The input parameters does not correct type");
        $name_method = self::NAME_METHOD . ucfirst($type);
        if (!method_exists($elastic_searchSQL, $name_method))
            throw new \Exception('Error: The methods not exists to current class');
        $list = $elastic_searchSQL->$name_method();

        return $list;
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
    public function searchInElastic(\Elastica_Query $elastica_query, \Elastica_Query_QueryString $elastica_query_string, $query)
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
    public function facetsData(\Elastica_Facet_Terms $elastica_facet, \Elastica_Query $elastica_query, $config_name, array $fields)
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