<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 07.06.13
 * Time: 14:20
 * To change this template use File | Settings | File Templates.
 */
/**
 * Interface for main round of some strategy of currency
 *
 * Class StrategyRoundInterface
 */
interface StrategyCurrencyRound_StrategyRoundInterface
{
    /**
     * Strategy round
     *
     * @param array   $item
     * @param integer $newPrice
     * @param integer $oldPrice
     *
     * @return mixed
     */

    public function roundCurrency(array $item, $newPrice, $oldPrice);
}