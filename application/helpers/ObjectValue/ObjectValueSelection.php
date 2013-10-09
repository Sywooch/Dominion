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
     * Scroll data
     *
     * @var array
     */
    private $dataSlider = array();

    /**
     * Data sample
     *
     * @var array
     */
    private $dataSample = array();

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
     * Getter for dataSlider
     *
     * @return array
     */
    public function getDataSlider()
    {
        return $this->dataSlider;
    }

    /**
     * Getter for data sample
     *
     * @return array
     */
    public function getDataSample()
    {
        return $this->dataSample;
    }

    /**
     * Gettet min
     *
     * @param string $nameColumn
     * @return mixed
     */
    public function getDataSliderMin($nameColumn)
    {
        return $this->dataSlider[$nameColumn]['min'];
    }

    /**
     * Getter data max
     *
     * @param string $nameColumn
     * @return mixed
     */
    public function getDataSliderMax($nameColumn)
    {
        return $this->dataSlider[$nameColumn]['max'];
    }
}