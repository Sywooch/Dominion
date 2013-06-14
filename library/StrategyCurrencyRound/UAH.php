<?php

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