#!/usr/bin/env php

<?php
require_once __DIR__ . "/../application/configs/config.php";

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

$paramters = $config->toArray();

$connect = new ContextSearch_ElasticSearch_Connect($paramters['search_engine']);
$connect->setAction("DELETE");

$contextSearch = new ContextSearch_ContextSearchFactory();
$queryBuilder = $contextSearch->getQueryBuilderElasticSearch();
$elasticSearchDELETE = $queryBuilder->createQuery($connect);
$elasticSearchDELETE->execute();

echo "Index delete success";
