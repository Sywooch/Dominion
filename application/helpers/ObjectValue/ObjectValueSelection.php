<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 06.10.13
 * Time: 16:52
 * To change this template use File | Settings | File Templates.
 */

/**
 * Class Helpers_ObjectValue_ObjectValueSelection
 */
class Helpers_ObjectValue_ObjectValueSelection extends App_Controller_Helper_HelperAbstract
{
    /**
     * Config
     *
     * @var array
     */
    private $columns = array();

    /**
     * Aggregation without brands
     *
     * @var array
     */
    private $aggregationWithoutBrands = array();

    /**
     * Aggregation with brands
     *
     * @var array
     */
    private $aggregationWithBrands = array();

    /**
     * Catalogue id
     *
     * @var integer
     */
    private $catalogueID;

    /**
     * Attributes
     *
     * @var array
     */
    private $attributes = array();

    /**
     * Brands
     *
     * @var array
     */
    private $brands = array();

    /**
     * Price min
     *
     * @var integer
     */
    private $priceMin;

    /**
     * Price max
     *
     * @var integer
     */
    private $priceMax;

    /**
     * Check brands
     *
     * @var boolean
     */
    private $checkBrands;

    /**
     * @param array $aggregationWithBrands
     */
    public function setAggregationWithBrands(array $aggregationWithBrands)
    {
        $this->aggregationWithBrands = $aggregationWithBrands;
    }

    /**
     * Set check brands
     *
     * @param bool $checkBrands
     */
    public function setCheckBrands($checkBrands)
    {
        $this->checkBrands = $checkBrands;
    }

    /**
     * Get check brands
     *
     * @return bool
     */
    public function isCheckBrands()
    {
        return $this->checkBrands;
    }

    /**
     * @return array
     */
    public function getAggregationWithBrands()
    {
        return $this->aggregationWithBrands;
    }

    /**
     * @param array $columns
     */
    public function setColumns(array $columns = array())
    {
        $this->columns = $columns;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param array $aggregationWithoutBrands
     */
    public function setAggregationWithoutBrands(array $aggregationWithoutBrands)
    {
        $this->aggregationWithoutBrands = $aggregationWithoutBrands;
    }

    /**
     * @return array
     */
    public function getAggregationWithoutBrands()
    {
        return $this->aggregationWithoutBrands;
    }

    /**
     * @param int $catalogueID
     */
    public function setCatalogueID($catalogueID)
    {
        $this->catalogueID = $catalogueID;
    }

    /**
     * @return int
     */
    public function getCatalogueID()
    {
        return $this->catalogueID;
    }

    /**
     * @param array $brands
     */
    public function setBrands(array $brands = array())
    {
        $this->brands = $brands;
    }

    /**
     * @return array
     */
    public function getBrands()
    {
        return $this->brands;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes = array())
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param int $priceMax
     */
    public function setPriceMax($priceMax = null)
    {
        $this->priceMax = $priceMax;
    }

    /**
     * @return int
     */
    public function getPriceMax()
    {
        return $this->priceMax;
    }

    /**
     * @param int $priceMin
     */
    public function setPriceMin($priceMin = null)
    {
        $this->priceMin = $priceMin;
    }

    /**
     * @return int
     */
    public function getPriceMin()
    {
        return $this->priceMin;
    }

}