<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 05.06.13
 * Time: 14:50
 * To change this template use File | Settings | File Templates.
 */
/**
 * Class do recount price for show correct unit in national currency
 *
 * Class Helpers_RecountPrice
 */
class Helpers_Prices_Recount extends App_Controller_Helper_HelperAbstract
{
    /**
     * Currency information for strategy recount
     *
     * @var array
     */
    private static $currInfo = array();

    /**
     * Currency id
     *
     * @var integer
     */
    private $currency;

    /**
     * Create static model
     *
     * @var obejct
     */
    private static $itemModel;

    /**
     * Create once itemModel
     *
     * @param models_Item $itemModel
     */
    public function setItemModel(models_Item $itemModel)
    {
        if (empty(self::$itemModel)) {
            self::$itemModel = $itemModel;
        }
    }

    /**
     * Setter for currency
     *
     * @param integers $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * Calculate national currency
     */
    public function calcRecount($item)
    {
        $this->getCurrencyInfo();
        list($newPrices, $oldPrices) = self::$itemModel->recountPrice(
            $item['PRICE'], $item['PRICE1'], $item['CURRENCY_ID'], $this->currency, self::$currInfo['PRICE']
        );

        $item["NEW_PRICE"] = $newPrices;
        $item["OLD_PRICE"] = $oldPrices;
        $item['UNIT'] = self::$currInfo['SNAME'];

        return $item;
    }


    /**
     * Getter Currency info
     *
     * @return array
     * @throws Exception
     */
    private function getCurrencyInfo()
    {
        if (empty(self::$currInfo)) {
            if (empty(self::$itemModel)) {
                throw new Exception("Error: item model is not create, class: " . __CLASS__ . " line: " . __LINE__);
            }

            self::$currInfo = self::$itemModel->getCurrencyInfo($this->currency);
        }
    }

    /**
     * Calculate round prices
     *
     * @param mixed $item
     *
     * @return mixed
     */
    public function calcRound($item)
    {
        $item['sh_disc_img_small'] = '';
        $item['sh_disc_img_big'] = '';
        $item['has_discount'] = 0;

        if ($this->currency > 1) {
            $item['iprice'] = round($item['NEW_PRICE'], 1);
            $item['iprice1'] = round($item['OLD_PRICE'], 1);
        } else {
            $item['iprice'] = round($item['NEW_PRICE']);
            $item['iprice1'] = round($item['OLD_PRICE']);
        }

        return $item;
    }
}