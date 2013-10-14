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

    /**
     * Convert string to array for selection of ElasticSearch
     *
     * @param string $attributes
     * @param string $brands
     * @return array
     */
    public static function getArrayAttributes($attributes, $brands)
    {
        preg_match_all("/[a-z]\d+/", $attributes, $result);
        preg_match_all('/b(\d+)/', $brands, $resultBrands);

        $resultFormat = array();
        $parseArray = array_merge($result[0], $resultBrands[1]);
        foreach ($parseArray as $key => $value) {

            if (!strstr($value, "v") && !strstr($value, "a")) {
                $resultFormat[]["ATTRIBUTES." . $value] = $value;

                continue;
            } else if (strstr($value, "a")) continue;

            $preKey = $parseArray[$key - 1];
            $resultFormat[]["ATTRIBUTES." . substr($preKey, 1, strlen($preKey))] = substr($value, 1, strlen($value));
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
    public static function getFormatRecountPrice($minPrice, $maxPrice, $real_currency,Helpers_ItemSelectionPrice $isp_helper)
    {
        return $isp_helper->recountPrice(
            array(
                "min_price" => $minPrice, "max_price" => $maxPrice
            ),
            array(
                "currency_id" => self::CURRENCY_ID, "real_currency_id" => $real_currency)
        );
    }
}