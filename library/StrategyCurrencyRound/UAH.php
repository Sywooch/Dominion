<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 07.06.13
 * Time: 14:33
 * To change this template use File | Settings | File Templates.
 */
/**
 * Class for round currency in USD
 *
 * Class UAH
 */
class StrategyCurrencyRound_UAH implements StrategyCurrencyRound_StrategyRoundInterface
{
    /**
     * Round currency UAH
     *
     * @param array   $item
     * @param integer $newPrice
     * @param integer $oldPrice
     *
     * @return array|mixed
     */
    public function roundCurrency(array $item, $newPrice, $oldPrice)
    {
        $item['iprice'] = round($newPrice);
        $item['iprice1'] = round($oldPrice);

        return $item;
    }
}