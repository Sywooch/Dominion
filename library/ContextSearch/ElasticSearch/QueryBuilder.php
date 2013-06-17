<?php
/**
 * Class for create query builder logic
 *
 * Class ContextSearch_ElasticSearch_QueryBuilder
 */
class ContextSearch_ElasticSearch_QueryBuilder implements QueryBuilderInterface
{
    /**
     * Registry to create elastic search
     *
     * @param ContextSerch_ElasticSearch_Connect $connect
     *
     * @return mixed|string
     */
    public function createQuery(ContextSerch_ElasticSearch_Connect $connect)
    {
        $nameClass = "ContextSearch_ElasticSearch_BuildExecute_" . $connect->getAction();

        return new $nameClass($connect);
    }
}