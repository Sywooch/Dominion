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
     * Format attributes range
     *
     * @var array
     */
    private $formatAttributesRange = array();

    /**
     * Format attributes checked
     *
     * @var array
     */
    private $formatAttributesChecked = array();

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
            $tmpArray = array();
            $tmpArray['ATTRIBUT_ID'] = $value["ATTRIBUT_ID"] ? (int)Format_ConvertDataElasticSelection::getInt($value["ATTRIBUT_ID"]) : $value["ATTRIBUT_ID"];

            $tmpArray['VALUE'] = $value["IS_RANGE_VIEW"] ? (int)Format_ConvertDataElasticSelection::getInt($value["VALUE"]) : $value["VALUE"];
            $tmpArray['TYPE'] = $value['TYPE'];
            $tmpArray['IS_RANGE_VIEW'] = $value["IS_RANGE_VIEW"];

            if (is_float($value["VALUE"]) || is_int($value["VALUE"])) {
                $h = 3;
            }


            $formatArray[] = $tmpArray;
        }
//        $formatArray['price'] = round($price, 1);
//        $formatArray[$brandId] = $brandId;

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

            $data['IMAGE3'] = !empty($data['IMAGE3']) ? $data['IMAGE3'] : '##';
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
            /** @var $recount Helpers_Prices_Recount */
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

    /**
     * Parse range attributes
     *
     * @param string $attributesRange
     *
     * @return boolean
     */
    public function parseRangeAttributes($attributesRange)
    {
        if (empty($attributesRange)) return false;

        preg_match_all("/(?<=[a-z])(\d+)(?:[a-z])(\d+)-(\d+)/", $attributesRange, $match);

        array_shift($match);
        list($attributes, $min, $max) = $match;
        foreach ($attributes as $key => $value) {
            $this->formatAttributesRange[$attributes[$key]]["id"] = $attributes[$key];
            $this->formatAttributesRange[$attributes[$key]]["is_range"] = true;
            $this->formatAttributesRange[$attributes[$key]]["value"]["from"] = $min[$key];
            $this->formatAttributesRange[$attributes[$key]]["value"]["to"] = $max[$key];
        }

        return true;
    }

    /**
     * Parse attributes checked
     *
     * @param string $attributesChecked
     *
     * @return boolean
     */
    public function parseAttributesChecked($attributesChecked)
    {
        if (empty($attributesChecked)) return false;

        preg_match_all("/(?<=[a-z])(\d+)(?:[a-z])(\d+)/", $attributesChecked, $match);

        array_shift($match);
        list($attributesId, $values) = $match;
        foreach ($attributesId as $key => $value) {
            $this->formatAttributesChecked[$value]["id"] = $value;
            $this->formatAttributesChecked[$value]["is_range"] = false;
            $this->formatAttributesChecked[$value]["value"][] = $values[$key];
        }

        return true;
    }

    /**
     * Get attributes format aggregation
     *
     * @return array
     */
    public function getAttributesFormatAggregation()
    {
        return array_merge_recursive($this->formatAttributesRange, $this->formatAttributesChecked);
    }
}