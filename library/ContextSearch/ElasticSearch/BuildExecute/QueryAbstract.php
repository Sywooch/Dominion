<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 17.06.13
 * Time: 17:54
 * To change this template use File | Settings | File Templates.
 */
use Elastica\Client;

abstract class ContextSearch_ElasticSearch_BuildExecute_QueryAbstract
{
    /**
     * Status Singleton
     *
     * @var null
     */
    private static $connect = array();

    /**
     * Connect parameters
     */
    protected $parameters = array();

    /**
     * Connect to Elastic Search
     *
     * @param ContextSearch_ElasticSearch_Connect $connect
     */
    public function __construct(ContextSearch_ElasticSearch_Connect $connect)
    {
        if (empty(self::$connect)) {
            $elasticaClient = new \Elastica\Client($connect->getConfig());
            self::$connect['index'] = $elasticaClient->getIndex($connect->getIndex());
            self::$connect['type'] = self::$connect['index']->getType($connect->getType());
            $this->parameters = $connect;
        }
    }

    /**
     * Getter config index
     *
     * @return ContextSerch_ElasticSearch_Connect|null
     */
    protected function getIndex()
    {
        return self::$connect['index'];
    }

    /**
     * Getter config type
     *
     * @return mixed
     */
    protected function getType()
    {
        return self::$connect['type'];
    }

    /**
     * Abstract method for few classes
     *
     * @return mixed
     */
    abstract public function execute();
}