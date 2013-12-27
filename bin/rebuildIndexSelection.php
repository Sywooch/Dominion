<?php
require_once "CreateEnvironment.php";
require_once "LoaderFactory.php";

$createEnvironment = new CreateEnvironment();
$createEnvironment->setType("selection");

$loaderFactory = new LoaderFactory();

$elasticSearchPUT = $createEnvironment->buildIndex();

$elasticSearchModel = $loaderFactory->getModelElasticSearch();
$queryAllItems = $elasticSearchModel->getConnectDB()->query($elasticSearchModel->getAllItemID());

$formatDataElastic = new Format_FormatDataElastic();
$data = array();
while ($row = $queryAllItems->fetch()) {
    $data[$row['ITEM_ID']] = $row;

    $data[$row['ITEM_ID']]['ATTRIBUTES'] = $formatDataElastic->formatDataForSelection($elasticSearchModel->getAttributesByItemID($row['ITEM_ID']), $row['PRICE'], $row['BRAND_ID']);

    unset($data[$row['ITEM_ID']]['PRICE'], $data[$row['ITEM_ID']]['BRAND_ID']);

    echo "add item element " . $row['ITEM_ID'] . " catalogue_id- " . $row["CATALOGUE_ID"] . "\r\n\n";

    if (count($data) != $createEnvironment->getLimit()) continue;

    $elasticSearchPUT->addDocuments($data);

    $data = array();
}

if (count($data)) $elasticSearchPUT->addDocuments($data);

echo "Data add to index success";

