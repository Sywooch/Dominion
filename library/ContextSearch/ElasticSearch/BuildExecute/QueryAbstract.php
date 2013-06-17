<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 17.06.13
 * Time: 17:54
 * To change this template use File | Settings | File Templates.
 */

abstract class QueryAbstract
{
    /**
     * Status Singleton
     *
     * @var null
     */
    private static $connect = null;

    /**
     * Connect to Elastic Search
     *
     * @param ContextSerch_ElasticSearch_Connect $connect
     */
    public function __construct(ContextSerch_ElasticSearch_Connect $connect)
    {
        if (empty(self::$connect)) {
            self::$connect = $connect;
        }
    }

    /**
     * Get Connect for child class
     *
     * @return ContextSerch_ElasticSearch_Connect|null
     */
    protected function getConnect()
    {
        return self::$connect;
    }

    /**
     * Abstract method for few classes
     *
     * @return mixed
     */
    abstract public function execute();
}