<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 07.06.13
 * Time: 14:20
 * To change this template use File | Settings | File Templates.
 */

/**
 * Class for round currency in USD
 *
 * Class USD
 */
class StrategyCurrencyRound_USD implements StrategyCurrencyRound_StrategyRoundInterface
{
    /**
     * NUMBER for round
     */
    const ROUND = 1;

    /**
     * Round currency USD
     *
     * @param array   $item
     * @param integer $newPrice
     * @param integer $oldPrice
     *
     * @return array|mixed
     */
    public function roundCurrency(array $item, $newPrice, $oldPrice)
    {
        $item['iprice'] = round($newPrice, self::ROUND);
        $item['iprice1'] = round($oldPrice, self::ROUND);

        return $item;
    }
}