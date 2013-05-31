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
class Helpers_FormatDataElastic extends App_Controller_Helper_HelperAbstract
{
    /**
     * Formating array for elastic search
     *
     * @return array
     */
    public function formatDataForElastic()
    {
        $elasticSearchModel = new models_ElasticSearch();
        $items = $elasticSearchModel->getProducts();
        $formatArray = array();
        foreach ($items as $item) {
            $item['URL'] = $item['REALCATNAME'] . $item['ITEM_ID'] . "-" . $item['CATNAME'] . "/";
            unset($item['REALCATNAME'], $item['ITEM_ID'], $item['CATNAME']);
            $formatArray[] = $item;
        }

        return $formatArray;
    }

}