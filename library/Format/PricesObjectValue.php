<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 06.06.13
 * Time: 13:45
 * To change this template use File | Settings | File Templates.
 */

/**
 * Object value for save elements in execute prepare format prices
 *
 * Class PricesObjectValue
 */
class Format_PricesObjectValue
{
    /**
     * Items array for save elements prices
     *
     * @var array
     */
    private $items = array();

    /**
     * Data for change prices
     *
     * @var array
     */
    private $data = array();

    /**
     * Recount object strategy recount prices
     *
     * @var Helpers_Prices_Recount
     */
    private $recount;

    /**
     * Discount object strategy discount prices
     *
     * @var object
     */
    private $discount;

    /**
     * Type currency of country
     *
     * @var integer
     */
    private $currencyID;

    /**
     * Set item in array items
     *
     * @param mixed $item
     * @param       $key
     */
    public function setItem($item, $key)
    {
        $this->items[$key] = $item;
    }

    public function setAllItems(array $items)
    {
        $this->items = $items;
    }

    /**
     * Setter for data
     *
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Setter for Recount
     *
     * @param Helpers_Prices_Recount $recount
     */
    public function setRecount(Helpers_Prices_Recount $recount)
    {
        $this->recount = $recount;
    }

    /**
     * Getter for Recount
     *
     * @return object
     */
    public function getRecount()
    {
        return $this->recount;
    }

    /**
     * Setter for discount
     *
     * @param Helpers_Prices_Discount $discount
     */
    public function setDiscount(Helpers_Prices_Discount $discount)
    {
        $this->discount = $discount;
    }

    /**
     * Getter for discount
     *
     * @return object
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Setter for currency
     *
     * @param integer $currencyID
     */
    public function setCurrency($currencyID)
    {
        $this->currencyID = $currencyID;
    }

    /**
     * Getter for currency
     *
     * @return int
     */
    public function getCurrency()
    {
        return $this->currencyID;
    }

    /**
     * Getter for data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get in items elements
     *
     * @param integer $valueID
     * @param string $findElement
     *
     * @return mixed
     */
    public function getItem($valueID, $findElement)
    {
        $key = null;

        foreach ($this->items as $key => $item) {
            $subKey = array_search($valueID, $item);

            if (!empty($subKey)) break;
        }

        return $this->items[$key][$findElement];
    }

    /**
     * Return Object ModelsItem
     *
     * @return models_Item
     */
    public function getModelsItem()
    {
        return new models_Item();
    }

    /**
     * Getter for items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

}