#!/usr/bin/env php
<?php

require_once "CreateEnvironment.php";
require_once "LoaderFactory.php";

$createEnvironment = new CreateEnvironment();
$createEnvironment->setType("products");

$loaderFactory = new LoaderFactory();

$elasticSearchPUT = $createEnvironment->buildIndex();

$formatDataElastic = new Format_FormatDataElastic();

$elasticSearchModel = $loaderFactory->getModelElasticSearch();
$query = $elasticSearchModel->getConnectDB()->query($elasticSearchModel->getAllData());
$data = array();
while ($row = $query->fetch()) {
    $data[$row['ITEM_ID']] = $row;

    echo "add item element " . $row['ITEM_ID'] . " - " . $row["NAME_PRODUCT"] . "\r\n\n";

    if (count($data) != $createEnvironment->getLimit()) continue;

    /** @var $elasticSearchPUT ContextSearch_ElasticSearch_BuildExecute_PUT */
    $elasticSearchPUT->addDocuments($formatDataElastic->formatDataForElastic($data));

    $data = array();
}

if (count($data)) $elasticSearchPUT->addDocuments($formatDataElastic->formatDataForElastic($data));

echo "Data add to index success";