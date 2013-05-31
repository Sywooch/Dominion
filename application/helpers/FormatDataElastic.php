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
            unset($item['REALCATNAME'], $item['ITEM_ID'], $item['CATNAME']);
            $formatArray[] = $item;
        }

        return $formatArray;
    }

}