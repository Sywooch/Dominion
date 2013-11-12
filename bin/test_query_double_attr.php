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
                        "and":[
                            {
                                "or":[
                                {
                                    "term":{"ATTRIBUTES.2553.VALUE": 18285}
                                },
                                {
                                    "term":{"ATTRIBUTES.2553.VALUE": 18287}
                                }
                                ]
                            }
                        ]
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
$jsonQuery = '{"query":{"filtered":{
                "query":{"term":{"CATALOGUE_ID":"23"}},
                "filter":{
                        "and":[
                                {"and":{
                                        "or":[
                                                {"term":{"ATTRIBUTES.2509.VALUE":"18336"}},
                                                {"term":{"ATTRIBUTES.2509.VALUE":"18332"}}
                                              ]
                                        }
                                },
                                {"or":[
                                        {
                                         "term":{"ATTRIBUTES.37":"37"}
                                         }
                                         ]
                                },
                                         {"range":{"ATTRIBUTES.price":{"gt":473,"lt":4427}}}]}}}}';

$builder = $loaderFactory->getBuilder($jsonQuery);
$query = $loaderFactory->getQuery($builder);

$search = $loaderFactory->getSearch();

$resultSet = $search->addIndex("dominion")->addType("selection")->search($query);

$rr = 0;