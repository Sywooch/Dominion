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

    /**
     * Convert string to array for selection of ElasticSearch
     *
     * @param $parameters
     * @return array
     */
    public static function getArrayAttributes($parameters)
    {
        preg_match_all("/([a-z]\d+|b\d+)/", $parameters, $result);

        $resultFormat['attributes'] = array();
        $resultFormat['brands'] = array();
        foreach ($result[0] as $key => $value) {

            if (!strstr($value, "v") && !strstr($value, "a")) {
                $value = substr($value, 1, strlen($value));
                $resultFormat['brands'][]["ATTRIBUTES." . $value] = $value;

                continue;
            } else if (strstr($value, "a")) continue;

            $preKey = $result[0][$key - 1];
            $resultFormat['attributes'][]["ATTRIBUTES." . substr($preKey, 1, strlen($preKey)) . ".VALUE"] = substr($value, 1, strlen($value));
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
}