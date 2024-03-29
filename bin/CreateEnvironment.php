<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 02.10.13
 * Time: 23:41
 * To change this template use File | Settings | File Templates.
 */
require_once __DIR__ . "/../application/configs/config.php";
require_once "LoaderFactory.php";

/**
 * Class CreateEnvironment
 */
class CreateEnvironment
{
    /**
     * Config object from Zend application
     *
     * @var Config
     */
    private $parameters = array();

    /**
     * Type of index
     *
     * @var string
     */
    private $type;

    /**
     * Connect to elastic search
     *
     * @var ContextSearch_ElasticSearch_Connect
     */
    private $contextSearchConnect;

    /**
     * Object
     *
     * @var LoaderFactory
     */
    private $loaderFactory;

    /**
     * Object
     *
     * @var ContextSearch_ContextSearchFactory
     */
    private $contextSearchFactory;

    /**
     * Limit documents to add in index
     */
    const LIMIT_DOCUMENTS = 500;
    const PRODUCTION = "production";
    const PATH_INI = "/configs/application.ini";

    /**
     * Set type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Initialize situation
     */
    public function __construct()
    {
        $this->loaderFactory = new LoaderFactory();

        $config = $this->loaderFactory->getConfig(APPLICATION_PATH . self::PATH_INI, self::PRODUCTION);
        Zend_Registry::set("config", $config);
        $this->parameters = $config->toArray();
        $this->contextSearchConnect = $this->loaderFactory->getSearchEngineConnect($this->parameters['search_engine']);
        $this->contextSearchFactory = $this->loaderFactory->getContextSearchFactory();
    }

    /**
     * Build new index
     */
    public function buildIndex()
    {
        $this->contextSearchConnect->setType($this->type);
        $this->contextSearchConnect->setAction("PUT");

        $queryBuilder = $this->contextSearchFactory->getQueryBuilderElasticSearch();

        return $queryBuilder->createQuery($this->contextSearchConnect);
    }

    /**
     * Delete Type
     */
    public function deleteIndex()
    {
        $this->contextSearchConnect->setType($this->type);
        $this->contextSearchConnect->setAction("DELETE");

        $queryBuilder = $this->contextSearchFactory->getQueryBuilderElasticSearch();
        $elasticSearchDELETE = $queryBuilder->createQuery($this->contextSearchConnect);
        $elasticSearchDELETE->execute();
    }

    /**
     * Get limit
     *
     * @return int
     */
    public function getLimit()
    {
        return self::LIMIT_DOCUMENTS;
    }
}