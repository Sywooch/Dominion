<?php
/**
 * Interface for describe logic function class QueryBuilder
 *
 * Class QueryBuilderInterface
 */
interface QueryBuilderInterface
{
    /**
     * Create query
     *
     * @param ContextSerch_ElasticSearch_Connect $connect
     *
     * @return mixed
     */
    public function createQuery(ContextSerch_ElasticSearch_Connect $connect);
}