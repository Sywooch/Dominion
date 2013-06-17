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

$formatQuery = new ContextSearch_FormatQuery(
    $config->search_engine->name,
    "DELETE",
    $config->search_engine->index
);

$formatQuery->setHost($config->search_engine->host);
$formatQuery->setType($config->search_engine->type);

$queryObject = new ContextSearch_Query();
$queryObject->execQuery($formatQuery);

echo "Index delete success";