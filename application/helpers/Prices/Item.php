<?php

class Helpers_Prices_Item extends App_Controller_Helper_HelperAbstract
{


    public function getCreditPrice($itemPrice){
        return $itemPrice * 1.2;
    }

    public function getItemPrice($itemId, $currencyId)
    {

        $item = $this->work_model->getItemInfo($itemId);

        $helperLoader = Zend_Controller_Action_HelperBroker::getStaticHelper('HelperLoader');

        /** @var Helpers_Prices_Recount $priceCount */
        $priceCount = $helperLoader->loadHelper('Prices_Recount');


        $priceCount->setItemModel($this->work_model);
        $priceCount->setCurrency($currencyId);

        $recountItem = $priceCount->calcRecount($item);

        $nameStrategyRound = "StrategyCurrencyRound_" . $priceCount->getNameRoundStrategy();
        $strategyRound = new $nameStrategyRound();
        $roundItem = $strategyRound->roundCurrency($recountItem, $recountItem['NEW_PRICE'], $recountItem['OLD_PRICE']);


        /** @var Helpers_Prices_Discount $priceDiscount */
        $priceDiscount = $helperLoader->loadHelper('Prices_Discount');

        $item = $priceDiscount->calcDiscount($roundItem);

        return $item['DISCOUNT_PRICE'];

//        $PriceObjectValue = new Format_PricesObjectValue();
//
//        $PriceObjectValue->setRecount($this->_helper->helperLoader("Prices_Recount"));
//        $PriceObjectValue->setDiscount($this->_helper->helperLoader("Prices_Discount"));
//
//        /**@var Helpers_Prices_Recount $recount */
//        $recount = $PriceObjectValue->getRecount();
//        $recount->setItemModel($this->work_model);
//        $recount->setCurrency($currencyId);
//
//        $recountItem = $recount->calcRecount($item);
//
//        $nameStrategyRound = "StrategyCurrencyRound_" . $recount->getNameRoundStrategy();
//        $strategyRound = new $nameStrategyRound();
//        $roundItem = $strategyRound->roundCurrency($recountItem, $recountItem['NEW_PRICE'], $recountItem['OLD_PRICE']);
//
//        return $PriceObjectValue->getDiscount()->calcDiscount($roundItem);
    }
}
