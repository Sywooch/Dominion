<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 03.10.13
 * Time: 1:38
 * To change this template use File | Settings | File Templates.
 */

use Symfony\Component\ClassLoader\UniversalClassLoader;

/**
 * Class LoaderFactory
 */
class LoaderFactory
{
    /**
     * Initialize
     */
    public function __construct()
    {
        $loader = new UniversalClassLoader();
        $loader->registerPrefixes(
            array(
                'ContextSearch_' => LIBRARY_PATH,
                "Helpers_" => APPLICATION_PATH,
                "models_" => APPLICATION_PATH,
                "Format_" => LIBRARY_PATH
            )
        );
        $loader->register();
    }

    /**
     * Get ModelsElasticSearch
     *
     * @return models_ElasticSearch
     */
    public function getModelElasticSearch()
    {
        return new models_ElasticSearch();
    }

    /**
     * Get config
     *
     * @param string $path
     * @param string $nameSection
     * @return Zend_Config_Ini
     */
    public function getConfig($path, $nameSection)
    {
        return new Zend_Config_Ini($path, $nameSection);
    }

    /**
     * Parameters
     *
     * @param array $parameters
     * @return ContextSearch_ElasticSearch_Connect
     */
    public function getSearchEngineConnect(array $parameters)
    {
        return new ContextSearch_ElasticSearch_Connect($parameters);
    }

    /**
     * Get context search factory
     *
     * @return ContextSearch_ContextSearchFactory
     */
    public function getContextSearchFactory()
    {
        return new ContextSearch_ContextSearchFactory();
    }

    /**
     * Builder
     *
     * @return \Elastica\Query\Builder
     */
    public function getBuilder($jsonQuery)
    {
        return new \Elastica\Query\Builder($jsonQuery);
    }

    /**
     * Query
     *
     * @return \Elastica\Query
     */
    public function getQuery(\Elastica\Query\Builder $builder)
    {
        return new \Elastica\Query($builder->toArray());
    }

    /**
     * Get search
     *
     * @return \Elastica\Search
     */
    public function getSearch()
    {
        return new \Elastica\Search(new \Elastica\Client());
    }
}