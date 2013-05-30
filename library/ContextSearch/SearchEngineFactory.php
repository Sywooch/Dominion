<?php

namespace library\ContextSearch;

use library\ContextSearch\Elastic\Execute;

/**
 * Class SearchEngineFactory
 *
 * @package lib\ContextSearch
 */
class SearchEngineFactory
{

    /**
     * Получить Бизнес логику ElasticSearch
     *
     * @return Elastic\Execute
     */
    public function getElasticSearch()
    {
        return new Execute();
    }
}
