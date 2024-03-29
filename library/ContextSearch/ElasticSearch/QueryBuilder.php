<?php
/**
 * Class for create query builder logic
 *
 * Class ContextSearch_ElasticSearch_QueryBuilder
 */
class ContextSearch_ElasticSearch_QueryBuilder implements ContextSearch_ElasticSearch_QueryBuilderInterface
{
    /**
     * Registry to create elastic search
     *
     * @param ContextSearch_ElasticSearch_Connect $connect
     *
     * @return mixed
     */
    public function createQuery(ContextSearch_ElasticSearch_Connect $connect)
    {
        $nameClass = "ContextSearch_ElasticSearch_BuildExecute_" . $connect->getAction();

        return new $nameClass($connect);
    }
}