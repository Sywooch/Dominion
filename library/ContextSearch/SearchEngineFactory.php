<?php
/**
 * Class SearchEngineFactory
 *
 * @package lib\ContextSearch
 */
class ContextSearch_SearchEngineFactory
{

    /**
     * Получить Бизнес логику ElasticSearch
     *
     * @return Execute
     */
    public function getElasticSearch()
    {
        return new ContextSearch_Elastic_Execute();
    }
}
