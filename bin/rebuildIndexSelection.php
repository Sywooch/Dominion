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
//    $data[$row['ITEM_ID']] = $row;

    $data[$row['ITEM_ID']]['ITEM_ID'] = (int) $row['ITEM_ID'];
    $data[$row['ITEM_ID']]['CATALOGUE_ID'] = (int) $row['CATALOGUE_ID'];
    $data[$row['ITEM_ID']]['PRICE'] = (float) $row['PRICE'];
    $data[$row['ITEM_ID']]['BRAND_ID'] = (float) $row['BRAND_ID'];
//    $data[$row['ITEM_ID']]['ATTRIBUTES'] = $formatDataElastic->formatDataForSelection(
//      $elasticSearchModel->getFloatsAttributesByItemID($row['ITEM_ID']), $row['PRICE'], $row['BRAND_ID']
//    );


    $attributes = array();

    $attributes = $elasticSearchModel->getFloatsAttributesByItemID($row['ITEM_ID']);
//    $attributes[] = $elasticSearchModel->getStringsAttributesByItemID($row['ITEM_ID']);

//    $attributes = array_merge($attributes, $elasticSearchModel->getStringsAttributesByItemID($row['ITEM_ID']));

    $attributes = array_merge($attributes, $elasticSearchModel->getIntegerAttributesByItemID($row['ITEM_ID']));

    $data[$row['ITEM_ID']]['ATTRIBUTES'] = $attributes;

//    unset($data[$row['ITEM_ID']]['PRICE'], $data[$row['ITEM_ID']]['BRAND_ID']);

    echo "add item element " . $row['ITEM_ID'] . " catalogue_id- " . $row["CATALOGUE_ID"] . "\r\n\n";

    if (count($data) != $createEnvironment->getLimit()) {
        continue;
    }

    $elasticSearchPUT->addDocuments($data);

//    exit();
    $data = array();
}

if (count($data)) {
    $elasticSearchPUT->addDocuments($data);
}

echo "Data add to index success";

