<?php
require_once "CreateEnvironment.php";
require_once "LoaderFactory.php";

$loaderFactory = new LoaderFactory();

$jsonQuery = '{
    "query": {
        "bool": {
            "must": [
                {
                    "term": {
                        "CATALOGUE_ID": "23"
                    }
                },
                {
                    "range": {
                        "ATTRIBUTES.price": {
                            "gt": "1027",
                            "lt": "4427"
                        }
                    }
                }
            ]
        }
    }
}';

$builder = $loaderFactory->getBuilder($jsonQuery);
$query = $loaderFactory->getQuery($builder);

$search = $loaderFactory->getSearch();

$resultSet = $search->addIndex("dominion")->addType("selection")->search($query);

$rr = 0;