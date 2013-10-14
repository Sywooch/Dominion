<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 06.10.13
 * Time: 16:52
 * To change this template use File | Settings | File Templates.
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
    public function setDataSample(array $dataSample)
    {
        $this->dataSample = $dataSample;
    }

    /**
     * Set catalogue id
     *
     * @param integer $catalogueID
     */
    public function setCatalogueID($catalogueID)
    {
        $this->dataSample['CATALOGUE_ID'] = $catalogueID;
    }

    /**
     * Getter for data sample
     *
     * @return array
     */
    public function getDataSample()
    {
       return array_merge($this->dataSample, $this->dataSlider);
    }

    /**
     * Check catalogueID
     *
     * @param integer $key
     * @return bool
     */
    public function getCatalogueID($key)
    {
        return (self::CATALOGUE_ID === $key) ? $this->dataSample[$key] : false;
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