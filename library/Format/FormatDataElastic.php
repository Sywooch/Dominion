<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 31.05.13
 * Time: 13:43
 * To change this template use File | Settings | File Templates.
 */
/**
 * Business logic for generate url and format array to put in index elastic search
 *
 * Class Helpers_FormatDataElastic
 */
class Format_FormatDataElastic
{
    /**
     * Formating array for elastic search
     *
     * @param array $items
     *
     * @return array
     */
    public function formatDataForElastic(array $items)
    {
        foreach ($items as $key => $item) {
            $items[$key]['MAIN'] = $item['TYPENAME'] . " " . $item['BRAND'] . " " . $item['NAME_PRODUCT'];

            $items[$key]['MAIN_ALTERNATIVE'] = "{$item['TYPENAME']} {$item['BRAND']} " . str_replace(" ", "", $item['NAME_PRODUCT']);
            $items[$key]['URL'] = $item['REALCATNAME'] . $item['ITEM_ID'] . "-" . $item['CATNAME'] . "/";
            unset($items[$key]['REALCATNAME'], $items[$key]['CATNAME']);
        }

        return $items;
    }

    /**
     * Format Data for build index
     *
     * @param array $attributes
     * @param       $price
     * @param       $brandId
     *
     * @return array
     */
    public function formatDataForSelection(array $attributes, $price, $brandId)
    {
        if (empty($attributes)) {
            return $attributes;
        }

        $formatArray = array();
        foreach ($attributes as $value) {
            $formatArray[$value['ATTRIBUT_ID']]['VALUE'] = $value["IS_RANGE_VIEW"] ? (int)Format_ConvertDataElasticSelection::getInt($value["VALUE"]) : $value["VALUE"];
            $formatArray[$value['ATTRIBUT_ID']]['TYPE'] = $value['TYPE'];
            $formatArray[$value['ATTRIBUT_ID']]['IS_RANGE_VIEW'] = $value["IS_RANGE_VIEW"];
        }
        $formatArray['price'] = round($price, 1);
        $formatArray[$brandId] = $brandId;

        return $formatArray;
    }

    /**
     * Format data for show in result search
     *
     * @param Format_PricesObjectValue $pricesObjectValue
     *
     * @return array
     */
    public function formatDataForResultQuery(Format_PricesObjectValue $pricesObjectValue)
    {
        $resultData = $pricesObjectValue->getData();
        $this->formatPrices($pricesObjectValue);
        $goods = array();

        foreach ($resultData as $key => $data) {
            $price = $pricesObjectValue->getItem($data['ITEM_ID'], "DISCOUNT_PRICE");
            $unit = $pricesObjectValue->getItem($data['ITEM_ID'], "UNIT");
            $goods[$key]['name'] = $data['TYPENAME'];
            $goods[$key]['brand'] = $data['BRAND'];
            $goods[$key]['name_product'] = $data['NAME_PRODUCT'];

            $data['IMAGE0'] = !empty($data['IMAGE0']) ? $data['IMAGE0'] : '##';
            $image = explode("#", $data['IMAGE0']);

            $goods[$key]['price'] = $price . " " . $unit;

            $goods[$key]['url'] = $data['URL'];
            $goods[$key]['image'] = array(
                'url' => $image[0],
                'width' => $image[1],
                'height' => $image[2]
            );

            $goods[$key]['value'] = "{$data['TYPENAME']} {$data['BRAND']} {$data['NAME_PRODUCT']}";
            $goods[$key] = $this->replaceValue($goods[$key], null, "");
            $goods[$key]['image'] = $this->replaceValue($goods[$key]['image'], null, "");
        }

        return $goods;
    }

    /**
     * Format data for search
     *
     * @param Format_PricesObjectValue $pricesObjectValue
     *
     * @return array
     */
    public function formatDataForSearchQuery(Format_PricesObjectValue $pricesObjectValue)
    {
        $dataResult = $pricesObjectValue->getData();
        $this->formatPrices($pricesObjectValue);

        $items = $pricesObjectValue->getItems();

        foreach ($dataResult as $data) {
            $items[$data['ITEM_ID']]['URL'] = $data['URL'];
        }

        return $items;
    }

    /**
     * Execute format and calculate logic prices
     *
     * @param Format_PricesObjectValue $pricesObjectValue
     */
    public function formatPrices(Format_PricesObjectValue $pricesObjectValue)
    {
        $items = $this->getDataItems($pricesObjectValue->getData());
        foreach ($items as $item) {
            $recount = $pricesObjectValue->getRecount();
            $recount->setItemModel($pricesObjectValue->getModelsItem());
            $recount->setCurrency($pricesObjectValue->getCurrency());

            $recountItem = $recount->calcRecount($item);

            $nameStrategyRound = "StrategyCurrencyRound_" . $recount->getNameRoundStrategy();
            $strategyRound = new $nameStrategyRound();
            $roundItem = $strategyRound->roundCurrency($recountItem, $recountItem['NEW_PRICE'], $recountItem['OLD_PRICE']);

            $item = $pricesObjectValue->getDiscount()->calcDiscount($roundItem);

            $pricesObjectValue->setItem($item, $item['ITEM_ID']);
        }
    }

    /**
     * Get from model items
     *
     * @param array $dataResult
     *
     * @return array
     */
    private function getDataItems($dataResult)
    {
        $elasticSearch = new models_ElasticSearch();
        $itemsId = array();
        foreach ($dataResult as $data) {
            $itemsId[] = $data['ITEM_ID'];
        }

        return $elasticSearch->getItemsForPrices($itemsId);
    }

    /**
     * Generate and replace values in array
     *
     * @param array $massive
     * @param mixed $value
     * @param mixed $replaceValue
     *
     * @return array
     */
    private function replaceValue(array $massive, $value, $replaceValue)
    {
        $valueSearch = array_keys($massive, $value);

        if (!empty($valueSearch)) {
            foreach ($valueSearch as $value) {
                $massive[$value] = $replaceValue;
            }
        }

        return $massive;
    }


}