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
     * Index object
     *
     * @var \Elastica\Index
     */
    private $index;

    /**
     * Object elastica client
     *
     * @var \Elastica\Client
     */
    private static $elasticaClient;

    /**
     * Connect parameters
     */
    protected static $parameters;

    /**
     * Connect to Elastic Search
     *
     * @param ContextSearch_ElasticSearch_Connect $connect
     */
    public function __construct(ContextSearch_ElasticSearch_Connect $connect)
    {
        if (empty(self::$elasticaClient)) {
            self::$elasticaClient = new \Elastica\Client($connect->getConfig());
            $this->index = self::$elasticaClient->getIndex($connect->getIndex());
            self::$parameters = $connect;
        }
    }

    /**
     * Getter config index
     *
     * @return ContextSerch_ElasticSearch_Connect|null
     */
    protected function getIndex()
    {
        return $this->getIndex();
    }

    /**
     * Getter config type
     *
     * @return \Elastica\Type
     */
    protected function getType()
    {
        return $this->index->getType(self::$parameters->getType());
    }

    /**
     * Get parameters
     *
     * @return ContextSearch_ElasticSearch_Connect
     */
    protected function getParameters()
    {
        return self::$parameters;
    }

    /**
     * Get host
     *
     * @return string
     */
    protected function getHost()
    {
        return self::$parameters->getHost();
    }

    /**
     * Abstract method for few classes
     *
     * @return mixed
     */
    abstract public function execute();
}