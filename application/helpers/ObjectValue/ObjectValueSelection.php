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
     * Count attributes
     */
    const COUNT_ATTRIBUTES = 1;

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
     * Set data attributes unique
     *
     * @param array $attributesUnique
     */
    public function setDataAttributesUnique(array $attributesUnique)
    {
        $this->dataSample['attributes_unique'] = $attributesUnique;
    }

    /**
     * Set data attributes double
     *
     * @param array $attributesDouble
     */
    public function setDataAttributesDouble(array $attributesDouble)
    {
        $this->dataSample['attributes_double'] = $attributesDouble;
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
        $this->dataSample['attributes_unique']['CATALOGUE_ID'] = $catalogueID;
    }

    /**
     * Get attributes
     *
     * @return mixed
     */
    public function getDataAttributes()
    {
        return array_merge($this->dataSample['attributes_unique'], $this->dataSample['attributes_double'], $this->dataSlider);
    }

    /**
     * Isset attributes unique
     *
     * @param string $key
     * @return bool
     */
    public function issetAttributesUnique($key)
    {
        return isset($this->dataSample['attributes_unique'][$key]);
    }

    /**
     * Isset attributes unique
     *
     * @param string $key
     *
     * @return bool
     */
    public function issetAttributesDouble($key)
    {
        return isset($this->dataSample['attributes_double'][$key]);
    }

    /**
     * Get data attributes unique
     *
     * @return mixed
     */
    public function getDataAttributesUnique()
    {
        return $this->dataSample['attributes_unique'];
    }

    /**
     * Get data attributes double
     *
     * @return mixed
     */
    public function getDataAttributesDouble()
    {
        return $this->dataSample['attributes_double'];
    }

    /**
     * Get data attributes with brands
     *
     * @return array
     */
    public function getDataAttributesWithBrands()
    {
        return array_merge($this->dataSample['attributes_unique'],$this->dataSample['attributes_double'], $this->dataSample['brands'], $this->dataSlider);
    }

    /**
     * Is brands empty
     *
     * @return bool
     */
    public function isBrandsEmpty()
    {
        return empty($this->dataSample['brands']);
    }

    /**
     * Check catalogueID
     *
     * @param integer $key
     * @return bool
     */
    public function getCatalogueID($key)
    {
        return (self::CATALOGUE_ID === $key) ? $this->dataSample['attributes_unique'][$key] : false;
    }

    /**
     * Isset attributes
     *
     * @return bool
     */
    public function issetAttributes()
    {
        return count($this->dataSample['attributes_double'] + $this->dataSample['attributes_unique'] + $this->dataSlider) > self::COUNT_ATTRIBUTES;
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
     * Check is brand column
     *
     * @param integer $nameColumn
     * @param integer $value
     * @return bool
     */
    public function isBrand($nameColumn, $value)
    {
        return substr(strstr($nameColumn, "."), 1) == $value;
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