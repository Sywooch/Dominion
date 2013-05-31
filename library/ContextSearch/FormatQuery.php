<?php

/**
 * Class FormatQuery
 *
 * @package library\ContextSearch
 */
class ContextSearch_FormatQuery
{
    /**
     * Type search engine
     *
     * @var string
     */
    private $search_engine;

    /**
     * Id some section
     *
     * @var integer
     */
    private $id;

    /**
     * Name section
     *
     * @var string
     */
    private $action;

    /**
     * Real string query for search
     *
     * @var string
     */
    private $string_query;

    /**
     * Config parameters
     *
     * @var array
     */
    private $config = array();

    /**
     * Data for put into index search engine
     *
     * @var array
     */
    private $data = array();

    /**
     * Constructor for get paramters to connect
     *
     * @param string       $search_engine
     * @param string       $action
     * @param null|integer $index
     *
     * @throws \Exception
     */
    public function __construct($search_engine, $action, $index = null)
    {
        if (!isset($search_engine) && !is_string($search_engine) && !isset($action))
            throw new \Exception("Error: The not correct input parameter search_engine!");
        if (isset($index))
            $this->config['index'] = $index;
        $this->search_engine = $search_engine;
        $this->action = $action;
    }

    /**
     * Setter for data
     *
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * Getter for data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**Установить индекс
     *
     * @param type $index
     */
    public function setIndex($index)
    {
        $this->config['index'] = $index;
    }

    /**Установить Запрос
     *
     * @param type $string_query
     */
    public function setQuery($string_query)
    {
        $this->string_query = $string_query;
    }

    /**Установить Id
     *
     * @param type $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**Установить type
     *
     * @param type $type
     */
    public function setType($type)
    {
        $this->config['type'] = $type;
    }

    /**Установить host
     *
     * @param type $host
     */
    public function setHost($host)
    {
        $this->config['host'] = $host;
    }

    /**Получить имя поискового движка
     *
     * @return type string
     */
    public function getSearchEngine()
    {
        return $this->search_engine;
    }

    /**Получает адрес хоста
     *
     * @return type string
     */
    public function getHost()
    {
        return $this->config['host'];
    }

    /**Получает имя индекса
     *
     * @return type string
     */
    public function getIndex()
    {
        return $this->config['index'];
    }

    /**Получает тип
     *
     * @return type
     */
    public function getType()
    {
        return $this->config['type'];
    }

    /**Получает действие (PUT, GET,POST, и тд.)
     *
     * @return type string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**Получить запрос
     *
     * @return type string
     */
    public function getQuery()
    {
        return $this->string_query;
    }

    /**Получить массив настроек
     *
     * @return type string
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Получить экземпляр класса фабрики создания бизнес логики
     *
     * @return SearchEngineFactory
     */
    public function getSearchEngineFactory()
    {
        return new ContextSearch_SearchEngineFactory();
    }
}
