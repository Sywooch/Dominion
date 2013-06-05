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
class Helpers_FormatDataElastic
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
        $formatArray = array();
        foreach ($items as $item) {
            $item['URL'] = $item['REALCATNAME'] . $item['ITEM_ID'] . "-" . $item['CATNAME'] . "/";
            $item['MAIN'] = $item['TYPENAME'] . " " . $item['BRAND'] . " " . $item['NAME_PRODUCT'];
            unset($item['REALCATNAME'], $item['ITEM_ID'], $item['CATNAME']);
            $formatArray[] = $item;
        }

        return $formatArray;
    }

    /**
     * Format data for show in result search
     *
     * @param array|Json $dataResult
     *
     * @return array
     */
    public function formatDataForResultQuery($dataResult)
    {
        if (!is_array($dataResult)) {
            $dataResult = json_decode($dataResult);
        }
        $goods = array();
        foreach ($dataResult as $key => $data) {
            $goods[$key]['name'] = $data['TYPENAME'];
            $goods[$key]['brand'] = $data['BRAND'];
            $goods[$key]['name_product'] = $data['NAME_PRODUCT'];
            $image = explode("#", $goods['IMAGE1']);
            $goods[$key]['price'] = $data['PRICE'];
            $goods[$key]['image'] = array(
                'url' => $image[0],
                'width' => $image[1],
                'height' => $image[2]
            );

            $goods[$key] = $this->replaceValue($goods[$key], null, "");
            $goods[$key]['image'] = $this->replaceValue($goods[$key]['image'], null, "");
        }

        return $goods;
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