<?php
use Symfony\Component\Console\Helper\ProgressBar;

class ContextSearch_RebuildFilters
{
    const CURRENCY_PRICE = 2;

    /**
     * @param ProgressBar                     $progress
     * @param ContextSearch_LoaderFactory     $loaderFactory
     * @param ContextSearch_CreateEnvironment $createEnvironment
     */
    static function run($progress, $loaderFactory, $createEnvironment)
    {

        $createEnvironment->setType("selection");

        /** @var $elasticSearchPUT ContextSearch_ElasticSearch_BuildExecute_PUT */
        $elasticSearchPUT = $createEnvironment->buildIndex();

        $mapping = json_decode(file_get_contents(__DIR__ . "/../../application/configs/mappingSelection.json"), true);
        $elasticSearchPUT->createMapping($mapping);


        $elasticSearchModel = $loaderFactory->getModelElasticSearch();
        $queryAllItems = $elasticSearchModel->getConnectDB()->query($elasticSearchModel->getAllItemID());

        $itemModel = new models_Item();
        $currencyInfo = $itemModel->getCurrencyInfo(self::CURRENCY_PRICE);

//        $formatDataElastic = new Format_FormatDataElastic();
        $data = array();
        while ($row = $queryAllItems->fetch()) {
            $data[$row['ITEM_ID']]["PRODUCT_ID"] = (int)$row['ITEM_ID'];
            $data[$row['ITEM_ID']]['CATALOGUE_ID'] = (int)$row['CATALOGUE_ID'];
            $data[$row['ITEM_ID']]['PRICE'] = (float)Format_PriceConverter::convertUSAToUA($row["PRICE"], $currencyInfo["PRICE"]);
            $data[$row['ITEM_ID']]['BRAND_ID'] = (float)$row['BRAND_ID'];

            $data[$row['ITEM_ID']]['ATTRIBUTES'] = $elasticSearchModel->getAttributesIndex($row['ITEM_ID']);

//            echo "add item element " . $row['ITEM_ID'] . " catalogue_id- " . $row["CATALOGUE_ID"] . "\r\n\n";

            if (count($data) != $createEnvironment->getLimit()) {
                continue;
            }

            $elasticSearchPUT->addDocuments($data);
            $data = array();
            $progress->setMessage('Task in progress...');
            $progress->advance($createEnvironment->getLimit());
        }

        if (count($data)) {
            $elasticSearchPUT->addDocuments($data);
        }
    }
} 