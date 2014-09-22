<?php
use Symfony\Component\Console\Helper\ProgressBar;

class ContextSearch_RebuildProducts
{
    /**
     * @param ProgressBar                     $progress
     * @param ContextSearch_LoaderFactory     $loaderFactory
     * @param ContextSearch_CreateEnvironment $createEnvironment
     */
    static function run($progress, $loaderFactory, $createEnvironment)
    {
        $createEnvironment->setType("products");
        $elasticSearchPUT = $createEnvironment->buildIndex();

        $formatDataElastic = new Format_FormatDataElastic();

        $elasticSearchModel = $loaderFactory->getModelElasticSearch();
        $query = $elasticSearchModel->getConnectDB()->query($elasticSearchModel->getAllData());
        $data = array();
        while ($row = $query->fetch()) {
            $data[$row['ITEM_ID']] = $row;

            if (count($data) != $createEnvironment->getLimit()) continue;

            /** @var $elasticSearchPUT ContextSearch_ElasticSearch_BuildExecute_PUT */
            $elasticSearchPUT->addDocuments($formatDataElastic->formatDataForElastic($data));

            $data = array();
            $progress->setMessage('Task in progress...');
            $progress->advance($createEnvironment->getLimit());
        }

        if (count($data)) $elasticSearchPUT->addDocuments($formatDataElastic->formatDataForElastic($data));
    }
} 