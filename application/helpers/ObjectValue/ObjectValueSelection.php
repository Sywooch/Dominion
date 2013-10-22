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
     * CATALOGUE_ID
     */
    const CATALOGUE_ID = "CATALOGUE_ID";
    /**
     * Data sample
     *
     * @var array
     */
    private $dataSample = array();

    /**
     * Data slider
     *
     * @var array
     */
    private $dataSlider = array();

    /**
     * Set dataSlider
     *
     * @param string $nameColumn
     * @param integer $min
     * @param integer $max
     */
    public function setDataSlider($nameColumn, $min, $max)
    {
        $this->dataSlider[$nameColumn] = array("min" => $min, "max" => $max);
    }

    /**
     * Set data sample
     *
     * @param array $dataSample
     */
    public function setDataAttributes(array $dataSample)
    {
        $this->dataSample['attributes'] = $dataSample;
    }

    /**
     * Set data brands
     *
     * @param array $brands
     */
    public function setDataBrands(array $brands)
    {
        $this->dataSample['brands'] = $brands;
    }

    /**
     * Set catalogue id
     *
     * @param integer $catalogueID
     */
    public function setCatalogueID($catalogueID)
    {
        $this->dataSample['attributes']['CATALOGUE_ID'] = $catalogueID;
    }

    /**
     * Getter for data sample
     *
     * @return array
     */
    public function getDataAttributes()
    {
        return array_merge($this->dataSample['attributes'], $this->dataSlider);
    }

    /**
     * Get data attributes with brands
     *
     * @return array
     */
    public function getDataAttributesWithBrands()
    {
        return array_merge($this->dataSample['attributes'], $this->dataSample['brands'], $this->dataSlider);
    }

    /**
     * Check catalogueID
     *
     * @param integer $key
     * @return bool
     */
    public function getCatalogueID($key)
    {
        $rr = self::CATALOGUE_ID;
        $status = self::CATALOGUE_ID === $key;
        return (self::CATALOGUE_ID === $key) ? $this->dataSample['attributes'][$key] : false;
    }

    /**
     * Gettet min
     *
     * @param string $nameColumn
     * @return mixed
     */
    public function getDataSliderMin($nameColumn)
    {
        return isset($this->dataSlider[$nameColumn]['min']) ? $this->dataSlider[$nameColumn]['min'] : false;
    }

    /**
     * Getter data max
     *
     * @param string $nameColumn
     * @return mixed
     */
    public function getDataSliderMax($nameColumn)
    {
        return isset($this->dataSlider[$nameColumn]['max']) ? $this->dataSlider[$nameColumn]['max'] : false;
    }
}