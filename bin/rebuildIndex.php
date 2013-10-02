#!/usr/bin/env php
<?php

require_once "CreateEnvironment.php";
require_once "LoaderFactory.php";

$createEnvironment = new CreateEnvironment();
$createEnvironment->setType("products");

$loaderFactory = new LoaderFactory();
$elasticSearchModel = $loaderFactory->getModelElasticSearch();

$createEnvironment->buildIndex(
    $elasticSearchModel->getConnectDB()->query($elasticSearchModel->getAllData()),
    new Format_FormatDataElastic()
);

echo "Data add to index success";




