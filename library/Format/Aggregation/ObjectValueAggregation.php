<?php
/**
 * Created by PhpStorm.
 * User: Константин
 * Date: 26.06.14
 * Time: 23:31
 */

/**
 * Class ObjectValueAggr
 * @package Aggregation
 */
class Format_Aggregation_ObjectValueAggregation
{
    /**
     * Catalogue id
     *
     * @var integer
     */
    private $catalogueID;

    /**
     * Price min criteria
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
     * Brand id array
     *
     * @var array
     */
    private $brands = array();

    /**
     * Attributes array
     *
     * @var array
     */
    private $attributes = array();

    /**
     * Aggregation json or array
     *
     * @var array
     */
    private $aggregation = array();

    /**
     * Sing brands criteria
     *
     * @var boolean
     */
    private $signBrandsCriteria;

    /**
     * Set aggregation
     *
     * @param array $aggregation
     */
    public function setAggregation(array $aggregation)
    {
        $this->aggregation = $aggregation;
    }

    /**
     * Get aggregation
     *
     * @return array
     */
    public function getAggregation()
    {
        return $this->aggregation;
    }

    /**
     * Set sign brands criteria
     *
     * @param boolean $brandsCriteriaSign
     */
    public function setSignBrandsCriteria($brandsCriteriaSign)
    {
        $this->signBrandsCriteria = $brandsCriteriaSign;
    }

    /**
     * Set attributes
     *
     * @param array
     */
    public function setAttributes(array $attributes)
    {
        if (empty($attributes)) return;

        $this->attributes = new Format_AttributesIterator();
        $this->attributes->setAttributes($attributes);
    }

    /**
     * Get attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set brands
     *
     * @param array $brands
     */
    public function setBrands(array $brands)
    {
        $this->brands = $brands;
    }

    /**
     * Get brands
     *
     * @return array
     */
    public function getBrands()
    {
        return $this->brands;
    }

    /**
     * Set catalogue id
     *
     * @param int $catalogueID
     */
    public function setCatalogueID($catalogueID)
    {
        $this->catalogueID = $catalogueID;
    }

    /**
     * Get catalogue id
     *
     * @return int
     */
    public function getCatalogueID()
    {
        return $this->catalogueID;
    }

    /**
     * Set price max
     *
     * @param int $priceMax
     */
    public function setPriceMax($priceMax)
    {
        $this->priceMax = $priceMax;
    }

    /**
     * Get price max
     *
     * @return int
     */
    public function getPriceMax()
    {
        return $this->priceMax;
    }

    /**
     * Set price min
     *
     * @param int $priceMin
     */
    public function setPriceMin($priceMin)
    {
        $this->priceMin = $priceMin;
    }

    /**
     * Get price min
     *
     * @return int
     */
    public function getPriceMin()
    {
        return $this->priceMin;
    }
}