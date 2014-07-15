<?php
/**
 * Created by PhpStorm.
 * User: Константин
 * Date: 14.07.14
 * Time: 21:55
 */

/**
 * Class PriceConverter
 */
class Format_PriceConverter
{
    /**
     * Convert usa to UA
     *
     * @param integer $currencyUSA
     * @param float $index
     *
     * @return integer
     */
    static public function convertUSAToUA($currencyUSA, $index)
    {
        return $currencyUSA * $index;
    }

    /**
     * Convert ua to usa
     *
     * @param integer $currencyUA
     * @param integer $index
     *
     * @return float
     */
    static public function convertUAToUSA($currencyUA, $index)
    {
        return $currencyUA / $index;
    }
} 