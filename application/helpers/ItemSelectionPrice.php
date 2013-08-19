<?php
/**
 * Helper для расчета минимальной и максимальной цен для подбора
 * с учетом системы накопительных скидок для авторизированных покупателей
 */

class Helpers_ItemSelectionPrice extends App_Controller_Helper_HelperAbstract
{

    /**
     * Найти min max цену для подбора
     *
     * @param array $params
     */
    public function getPrices($params)
    {
        $Item = new models_Item();
        $Registration = new models_Registration();

        $shopuser_discounts_id = 0;

        $result['min_price'] = 0;
        $result['max_price'] = 0;

        $user_data = Zend_Auth::getInstance()->getIdentity();
        if (!empty($user_data)) {
            $shopuser_discounts_id = $Registration->getShopuserDiscountsId($user_data['user_id']);
        }

        if (!empty($shopuser_discounts_id)) {
            $params['user_id'] = $user_data['user_id'];

            $result = $this->getSelectItemAuthPrice($params);
        } else {
            $result = $Item->getSelectItemSimplePrice($params);

            $result['min_price_origin'] = $result['min_price'];
            $result['max_price_origin'] = $result['max_price'];

            list($result['min_price'], $result['max_price']) = $this->recountPrice($result, $params);
        }

        return $result;
    }

    /**
     * Формирует XML для вывода линии ценовых промежутков
     *
     * @param array $params
     * @param int   $periods
     * @param int   $krat
     */


    public function getPricesLine($params)
    {
        $Item = new models_Item();

        if ($params['max_price'] - $params['min_price'] > 0) {
            $count = $Item->getCatalogItemCount($params);

            if (!empty($count) && $count > 2) {
                $left_side = round($params['min_price'] * 100 / 130);
                $right_side = round($params['max_price'] * 110 / 100);

                $this->domXml->create_element('price_line', '', 2);
                $this->domXml->set_attribute(array('step' => 1));

                $this->domXml->create_element('price_from', 0);
                $this->domXml->create_element('price', $left_side);
                $this->domXml->go_to_parent();

                $half = ceil($count / 2);

                /*===============================================================================*/
                $params['start'] = ceil($half * 40 / 100);
                $params['limit'] = 1;
                $left_item = $Item->getPricesLineItem($params);

                $price['min_price'] = $left_item['PRICE'];
                $price['max_price'] = $left_item['PRICE1'];

                list($left_min_price, $left_max_price) = $this->recountPrice($price, $params);

                $left_min_price = ceil($left_min_price / 10);
                $left_min_price = $left_min_price * 10;

                $step_left = round(($left_min_price - $params['min_price']) / 25);

                $this->domXml->create_element('price_line', '', 2);
                $this->domXml->set_attribute(array('step' => $step_left));

                $this->domXml->create_element('price_from', $left_side);
                $this->domXml->create_element('price', $left_min_price);
                $this->domXml->create_element('style', 'left');
                $this->domXml->go_to_parent();


                /*===============================================================================*/
                $params['start'] = $half;
                $params['limit'] = 1;
                $middle_item = $Item->getPricesLineItem($params);

                $price['min_price'] = $middle_item['PRICE'];
                $price['max_price'] = $middle_item['PRICE1'];

                list($middle_min_price, $middle_max_price) = $this->recountPrice($price, $params);

                $middle_min_price = ceil($middle_min_price / 10);
                $middle_min_price = $middle_min_price * 10;

                $step_middle = round(($middle_min_price - $left_min_price) / 46);

                $this->domXml->create_element('price_line', '', 2);
                $this->domXml->set_attribute(array('step' => $step_middle));

                $this->domXml->create_element('price_from', $left_min_price);
                $this->domXml->create_element('price', $middle_min_price);
                $this->domXml->create_element('style', 'middle');
                $this->domXml->go_to_parent();

                /*===============================================================================*/
                $params['start'] = $half + ceil($half * 40 / 100);
                $params['limit'] = 1;
                $right_item = $Item->getPricesLineItem($params);

                $price['min_price'] = $right_item['PRICE'];
                $price['max_price'] = $right_item['PRICE1'];

                list($right_min_price, $right_max_price) = $this->recountPrice($price, $params);

                $right_min_price = ceil($right_min_price / 10);
                $right_min_price = $right_min_price * 10;

                $step_right = round(($right_min_price - $middle_min_price) / 46);

                $this->domXml->create_element('price_line', '', 2);
                $this->domXml->set_attribute(array('step' => $step_right));

                $this->domXml->create_element('price_from', $middle_min_price);
                $this->domXml->create_element('price', $right_min_price);
                $this->domXml->create_element('style', 'right');
                $this->domXml->go_to_parent();

                /*===============================================================================*/

                $step_right = round(($params['max_price'] - $right_min_price) / 25);

                $this->domXml->create_element('price_line', '', 2);
                $this->domXml->set_attribute(array('step' => $step_right));

                $this->domXml->create_element('price_from', $right_min_price);
                $this->domXml->create_element('price', $right_side);
                $this->domXml->go_to_parent();
            }
        }
    }

    /**
     * Приведение цены к национальной валюте
     *
     * @param array $price
     * @param array $params
     *
     * @return array
     */

    public function recountPrice($price, $params)
    {
        $Item = new models_Item();

        $curr_info = $Item->getCurrencyInfo($params['currency_id']);

        list($min_price, $max_price) = $Item->recountPrice($price['min_price'], $price['max_price'], $params['real_currency_id'], $params['currency_id'], $curr_info['PRICE']);

        return array($min_price, $max_price);
    }

    private function getSelectItemAuthPrice($params)
    {
        $Item = new models_Item();

        $params['currency'] = $params['currency_id'];
        $helperLoader = Zend_Controller_Action_HelperBroker::getStaticHelper('HelperLoader');
        $ct_helper = $helperLoader->loadHelper('Cart', $params);
        $ct_helper->setModel($Item);

        $result['min_price'] = 0;
        $result['max_price'] = 0;

        $min_price = 9999999;
        $min_price_origin = 0;

        $curr_info = $Item->getCurrencyInfo($params['currency_id']);

        $items = $Item->getSelectItemAuthPrice($params['catalogue_id']);
        if (!empty($items)) {
            foreach ($items as $view) {
                list($new_price, $new_price1) = $Item->recountPrice($view['PRICE'], $view['PRICE1'], $view['CURRENCY_ID'], $params['currency_id'], $curr_info['PRICE']);

                if ($params['currency_id'] > 1) {
                    $view['iprice'] = round($new_price, 1);
                    $view['iprice1'] = round($new_price1, 1);
                } else {
                    $view['iprice'] = round($new_price);
                    $view['iprice1'] = round($new_price1);
                }

                $view = $ct_helper->recountPrice($view);

                $result_price = !empty($view['iprice1']) ? $view['iprice1'] : $view['iprice'];
                $min_price_origin_result = !empty($view['PRICE1']) ? $view['PRICE1'] : $view['PRICE'];

                if ($min_price > $result_price) {
                    $min_price = $result_price;
                    $min_price_origin = $min_price_origin_result;
                }
                if ($result['max_price'] < $result_price) {
                    $result['max_price'] = $result_price;
                    $result['max_price_origin'] = $min_price_origin_result;
                }
            }

            $result['min_price'] = $min_price;
            $result['min_price_origin'] = $min_price_origin;
        }

        return $result;
    }
}