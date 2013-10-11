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
}