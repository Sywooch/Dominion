<?php
require_once "CreateEnvironment.php";
require_once "LoaderFactory.php";

define("CURRENCY_PRICE", 2);

$createEnvironment = new CreateEnvironment();
$createEnvironment->setType("selection");

$loaderFactory = new LoaderFactory();

/** @var $elasticSearchPUT ContextSearch_ElasticSearch_BuildExecute_PUT */
$elasticSearchPUT = $createEnvironment->buildIndex();

$mapping = json_decode(file_get_contents(__DIR__ . "/config/mappingSelection.json"), true);
$elasticSearchPUT->createMapping($mapping);

$elasticSearchModel = $loaderFactory->getModelElasticSearch();
$queryAllItems = $elasticSearchModel->getConnectDB()->query($elasticSearchModel->getAllItemID());

$itemModel = new models_Item();
$currencyInfo = $itemModel->getCurrencyInfo(CURRENCY_PRICE);

$formatDataElastic = new Format_FormatDataElastic();
$data = array();
while ($row = $queryAllItems->fetch()) {
    $data[$row['ITEM_ID']]["PRODUCT_ID"] = (int)$row['ITEM_ID'];
    $data[$row['ITEM_ID']]['CATALOGUE_ID'] = (int)$row['CATALOGUE_ID'];
    $data[$row['ITEM_ID']]['PRICE'] = (float)Format_PriceConverter::convertUSAToUA($row["PRICE"], $currencyInfo["PRICE"]);
    $data[$row['ITEM_ID']]['BRAND_ID'] = (float)$row['BRAND_ID'];

    $data[$row['ITEM_ID']]['ATTRIBUTES'] = $elasticSearchModel->getAttributesIndex($row['ITEM_ID']);

    echo "add item element " . $row['ITEM_ID'] . " catalogue_id- " . $row["CATALOGUE_ID"] . "\r\n\n";

    if (count($data) != $createEnvironment->getLimit()) {
        continue;
    }

    $elasticSearchPUT->addDocuments($data);
    $data = array();
}

if (count($data)) {
    $elasticSearchPUT->addDocuments($data);
}

echo "Data add to index success";

