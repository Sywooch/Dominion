<?php
require_once "CreateEnvironment.php";
require_once "LoaderFactory.php";

$loaderFactory = new LoaderFactory();

$jsonQuery = '{
    "query": {
        "filtered": {
            "query": {
                "term": {
                    "CATALOGUE_ID": 21
                }
            },
            "filter": {
                "and": [
                    {
                        "term": {
                            "ATTRIBUTES.2542.VALUE": 18239
                        }
                    },
                    {
                        "range":{
                        "ATTRIBUTES.price":{
                            "gt":450,
                            "lt": 5000
                        }
                        }
                    },
                    {
                    "or":[
                        {
                            "term":{"ATTRIBUTES.725":725}
                        },
                        {
                            "term":{"ATTRIBUTES.37":37}
                        }
                    ]
                    }
                ]
            }
        }
    },
    "from": 0,
    "size": 69
}';
$builder = $loaderFactory->getBuilder($jsonQuery);
$query = $loaderFactory->getQuery($builder);

$search = $loaderFactory->getSearch();
$search->setOption("from", 0);
$search->setOption("size", 77);
$resultSet = $search->addIndex("dominion")->addType("selection")->search($query);

$rr = 0;