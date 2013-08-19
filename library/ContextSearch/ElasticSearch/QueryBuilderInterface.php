<?php
/**
 * Interface for describe logic function class QueryBuilder
 *
 * Class QueryBuilderInterface
 */
interface ContextSearch_ElasticSearch_QueryBuilderInterface
{
    /**
     * Create query
     *
     * @param ContextSerch_ElasticSearch_Connect $connect
     *
     * @return mixed
     */
    public function createQuery(ContextSearch_ElasticSearch_Connect $connect);
}