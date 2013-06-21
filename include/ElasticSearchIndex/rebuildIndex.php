<?php

require_once __DIR__ . "/../../application/configs/config.php";

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

$loader->registerPrefixes(
    array(
        'ContextSearch_' => LIBRARY_PATH,
        "Helpers_" => APPLICATION_PATH,
        "models_" => APPLICATION_PATH,
        "Format_" => LIBRARY_PATH
    )
);
$config = new Zend_Config_Ini(APPLICATION_PATH . "/configs/application.ini", "production");
Zend_Registry::set("config", $config);
$loader->register();

$elasticSearchModel = new models_ElasticSearch();
$data = $elasticSearchModel->getProducts();

$helperFormatData = new Format_FormatDataElastic();

$formatData = $helperFormatData->formatDataForElastic($data);
$parameters = $config->toArray();
$connect = new ContextSearch_ElasticSearch_Connect($parameters['search_engine']);
$connect->setAction("PUT");
$contextSearch = new ContextSearch_ContextSearchFactory();
$queryBuilder = $contextSearch->getQueryBuilderElasticSearch();
$elasticSearchPUT = $queryBuilder->createQuery($connect);

$elasticSearchPUT->addDocuments($formatData);

echo "Data add to index success";




