<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 11.10.13
 * Time: 23:00
 * To change this template use File | Settings | File Templates.
 */

class Format_ConvertDataElasticSelection
{

    const CURRENCY_ID = 2;
    const TYPE_ATTRIBUTE_STRING = 2;
    const NAME_ATRIBUTES_UNIQUE = "attributes_unique";
    const NAME_ATTRIBUTES_DOUBLE = "attributes_double";

    /**
     * Convert string to array for selection of ElasticSearch
     *
     * @param $parameters
     * @return array
     */
    public static function getArrayAttributes($parameters)
    {
        preg_match_all("/([a-z]\d+|b\d+)/", $parameters, $result);

        $resultFormat[self::NAME_ATRIBUTES_UNIQUE] = array();
        $resultFormat[self::NAME_ATTRIBUTES_DOUBLE] = array();
        $resultFormat['brands'] = array();
        foreach ($result[0] as $key => $value) {

            if (!strstr($value, "v") && !strstr($value, "a")) {
                $value = substr($value, 1, strlen($value));
                $resultFormat['brands'][]["ATTRIBUTES." . $value] = $value;

                continue;
            } else if (strstr($value, "a")) continue;

            $preKey = $result[0][$key - 1];

            $nameKey = count(array_keys($result[0], $preKey)) > 1 ? self::NAME_ATTRIBUTES_DOUBLE : self::NAME_ATRIBUTES_UNIQUE;

            $resultFormat[$nameKey][]["ATTRIBUTES." . substr($preKey, 1, strlen($preKey)) . ".VALUE"] = substr($value, 1, strlen($value));
        }

        return $resultFormat;
    }

    /**
     * Get format recount price
     *
     * @param integer $minPrice
     * @param integer $maxPrice
     * @param string $real_currency
     * @param Helpers_ItemSelectionPrice $isp_helper
     *
     * @return array
     */
    public static function getFormatRecountPrice($minPrice, $maxPrice, $real_currency, Helpers_ItemSelectionPrice $isp_helper)
    {
        return $isp_helper->recountPrice(
            array(
                "min_price" => $minPrice, "max_price" => $maxPrice
            ),
            array(
                "currency_id" => self::CURRENCY_ID, "real_currency_id" => $real_currency)
        );
    }

    /**
     * Convert data to return in ajax
     *
     * @param array $dataAttributesResult
     * @param array $dataBrandsWithAttributesResult
     * @return mixed
     */
    public static function getFormatResultData(array $dataAttributesResult, array $dataBrandsWithAttributesResult)
    {
        function array_recursive_unique(&$value, $key)
        {
            $value = array_unique($value);
        }

        $formatDataBrands = self::formatResult($dataBrandsWithAttributesResult);
        $formatDataAttributes = self::formatResult($dataAttributesResult);

        if (!empty($formatDataAttributes['brands']) && !empty($formatDataBrands)) $formatDataBrands['brands'] = $formatDataAttributes['brands'];

        $formatData['brands'] = array_unique($formatDataAttributes['brands']);
        $formatData['attrib'] = !empty($formatDataBrands['attrib']) ? $formatDataBrands['attrib'] : $formatDataAttributes['attrib'];
        array_walk($formatData['attrib'], "array_recursive_unique");
        $formatData['brands_count'] = count($formatData['brands']);
        $formatData['attrib_count'] = count($formatData['attrib']);
        $formatData['items_count'] = count(empty($dataBrandsWithAttributesResult) ? $dataAttributesResult : $dataBrandsWithAttributesResult);

        return $formatData;
    }

    /**
     * Get items
     *
     * @param array $attributes
     * @return array
     */
    public static function getItems(array $attributes)
    {
        $resultData = array();
        foreach ($attributes as $value) {
            $resultData[] = $value["ITEM_ID"];
        }

        return $resultData;
    }

    /**
     * Format Data
     *
     * @param array $data
     * @return mixed
     */
    private static function formatResult(array $data)
    {
        $formatData['attrib'] = array();
        $formatData['brands'] = array();
        foreach ($data as $value) {
            unset($value['ATTRIBUTES']['price']);

            foreach ($value['ATTRIBUTES'] as $key => $val) {
                if ($val["TYPE"] == self::TYPE_ATTRIBUTE_STRING) continue;

                if ($key == $val) {
                    $formatData['brands'][] = $val;

                    continue;
                }

                $formatData['attrib'][$key][] = $val['VALUE'];
            }
        }

        return $formatData;
    }

    /**
     * Convert to valid format data array with attributes
     *
     * @param array $attributes
     *
     * @return array
     */
    static public function formatDataRange(array $attributes)
    {
        $attributesFormat = array();

        foreach ($attributes as $value) {
            $attributesFormat[$value["ATTRIBUT_ID"]][] = Format_ConvertDataElasticSelection::getInt($value["NAME"]);
        }

        return self::getAttributesLine(Format_ConvertDataElasticSelection::getMaxMinValue($attributesFormat));
    }

    /**
     * Format attributes range
     *
     * @param array $attributesRange
     *
     * @return array
     */
    static public function formatAttributesRange(array $attributesRange)
    {
        $attributesFormat = array();

        foreach ($attributesRange as $key => $value) {
            $attributesFormat["ATTRIBUTES." . $key . ".VALUE"] = $value;
        }

        return $attributesFormat;
    }


    /**
     * Get attributes line
     *
     * @param array $attributesMinMax
     * @return array
     */
    static private function getAttributesLine(array $attributesMinMax)
    {
        if (empty($attributesMinMax)) return $attributesMinMax;

        foreach ($attributesMinMax as $key => $value) {
            $attributesMinMax[$key]["left_side"] = round($value["min"] * 100 / 130);
            $attributesMinMax[$key]["right_side"] = round($value["max"] * 110 / 100);
        }

        return $attributesMinMax;
    }

    /**
     * Get integer
     *
     * @param string $value
     * @return integer
     */
    private static function getInt($value)
    {
        preg_match_all("/(\d+)|(\d+\.\d+)(?=\s[A-Za-zA-Яа-я]+)/", $value, $match);

        return array_shift($match[0]);
    }

    /**
     * Get max min value
     *
     * @param array $attributes
     * @return array
     */
    private static function getMaxMinValue(array $attributes)
    {
        $attributesMinMax = array();
        foreach ($attributes as $key => $value) {
            sort($attributes[$key]);
            $attributesMinMax[$key]["min"] = array_shift($attributes[$key]);
            $attributesMinMax[$key]["max"] = array_pop($attributes[$key]);
        }

        return $attributesMinMax;
    }
}