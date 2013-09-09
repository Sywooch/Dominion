#!/usr/bin/env php

<?php

require_once __DIR__ . "/../application/configs/config.php";

use Symfony\Component\ClassLoader\UniversalClassLoader;

define("LIMIT_DOCUMENTS", 500);

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
$db = $elasticSearchModel->getConnectDB();
$sql = $elasticSearchModel->getAllData();
$query = $db->query($sql);

$helperFormatData = new Format_FormatDataElastic();

$parameters = $config->toArray();
$connect = new ContextSearch_ElasticSearch_Connect($parameters['search_engine']);

$connect->setAction("PUT");

$contextSearch = new ContextSearch_ContextSearchFactory();
$queryBuilder = $contextSearch->getQueryBuilderElasticSearch();

$elasticSearchPUT = $queryBuilder->createQuery($connect);

$data = array();

while ($row = $query->fetch()) {
    $data[$row['ITEM_ID']] = $row;

    echo "add item element " . $row['ITEM_ID'] . " - " . $row["NAME_PRODUCT"] . "\r\n\n";
    if (count($data) != LIMIT_DOCUMENTS) {

        continue;
    }

    $elasticSearchPUT->addDocuments($helperFormatData->formatDataForElastic($data));

    $data = array();
}

if (count($data)) {
    $elasticSearchPUT->addDocuments($helperFormatData->formatDataForElastic($data));
}

echo "Data add to index success";




