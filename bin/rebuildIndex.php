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

$elasticSearchModel = new models_ElasticSearch();
$db = $elasticSearchModel->getConnectDB();
$sql = $elasticSearchModel->getAllData();
$query = $db->query($sql);

$helperFormatData = new Format_FormatDataElastic();

$parameters = $config->toArray();
$searchEngine = $parameters['search_engine'];
$connect = new ContextSearch_ElasticSearch_Connect($searchEngine);
$connect->setAction("PUT");

$contextSearch = new ContextSearch_ContextSearchFactory();
$queryBuilder = $contextSearch->getQueryBuilderElasticSearch();

$elasticSearchPUT = $queryBuilder->createQuery($connect);

$data = array();

while ($row = $query->fetch()) {

    $data[$row['ITEM_ID']] = $row;

    if (count($data) >= 499) {
        $formatData = $helperFormatData->formatDataForElastic($data);

        $elasticSearchPUT->addDocuments($formatData);

        $data = array();
    } else {
        continue;
    }
}

if (!empty($data)) {
    $formatData = $helperFormatData->formatDataForElastic($data);

    $elasticSearchPUT->addDocuments($formatData);
}

echo "Data add to index success";




