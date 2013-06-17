<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 17.06.13
 * Time: 17:02
 * To change this template use File | Settings | File Templates.
 */

/**
 * CLass for create business logic for builder query
 *
 * Class ContextSearch_ContextSearchBuilder
 */
class ContextSearch_ContextSearchBuilder
{
    /**
     * Create Query Builder Elastic search
     *
     * @return ContextSearch_ElasticSearch_QueryBuilder
     */
    public function getQueryBuilderElasticSearch()
    {
        return new ContextSearch_ElasticSearch_QueryBuilder();
    }
}