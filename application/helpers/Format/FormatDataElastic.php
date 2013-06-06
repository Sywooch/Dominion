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
class Helpers_Format_FormatDataElastic extends App_Controller_Helper_HelperAbstract
{
    /**
     * Object value for save state format array for calc price in
     * discount and send to result search query of elastica
     *
     * @var object
     */
    private $pricesObjectValue;

    /**
     * Setter for PriceObjectValue
     *
     * @param helpers_Format_PricesObjectValue $pricesObjectValue
     */
    public function setPricesObjectValue(helpers_Format_PricesObjectValue $pricesObjectValue)
    {
        /** @var $pricesObjectValue helpers_PricesObjectValue */
        $this->pricesObjectValue = $pricesObjectValue;
    }

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
            unset($item['REALCATNAME'], $item['CATNAME']);
            $formatArray[] = $item;
        }

        return $formatArray;
    }

    /**
     * Format data for show in result search
     *
     * @return array
     */
    public function formatDataForResultQuery()
    {
        $dataResult = $this->pricesObjectValue->getData();
        $goods = array();
        foreach ($dataResult as $key => $data) {
            $unit = $this->pricesObjectValue->getItem($key, "UNIT");
            $price = $this->pricesObjectValue->getItem($key, "iprice");
            $goods[$key]['name'] = $data['TYPENAME'];
            $goods[$key]['brand'] = $data['BRAND'];
            $goods[$key]['name_product'] = $data['NAME_PRODUCT'];
            $image = explode("#", $goods['IMAGE1']);
            $goods[$key]['price'] = $price . " " . $unit;
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
     * Execute format and calculate logic prices
     *
     * @throws Exception
     */
    public function formatPrices()
    {
        if (empty($this->pricesObjectValue)) {
            throw new Exception("Error: price object value is null, class:" . __CLASS__ . ", line: " . __LINE__);
        }

        $items = $this->getDataItems($this->pricesObjectValue->getData());
        $recount = $this->pricesObjectValue->getRecount();
        $recount->setItemModel($this->pricesObjectValue->getModelsItem());
        $recount->setCurrency($this->pricesObjectValue->getCurrency());

        foreach ($items as $item) {
            $recountItem = $recount->calcRecount($item);
            $roundItem = $recount->calcRound($recountItem);
            $discountItem = $this->pricesObjectValue->getDiscount()->calcDiscount($roundItem);
            $this->pricesObjectValue->setItem($discountItem);
        }
    }

    /**
     * Get from model items
     *
     * @param array $dataResult
     *
     * @return array
     */
    private function getDataItems(array $dataResult)
    {
        $elasticSearch = new models_ElasticSearch();
        $itemsId = array();
        foreach ($dataResult as $data) {
            $itemsId[] = $data['ITEM_ID'];
        }

        $items = $elasticSearch->getItemsForPrices($itemsId);

        return $items;
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