<?php

/**
 * Helper для получения товаров удовл. параметрам подбора
 */
class Helpers_ItemSelection extends App_Controller_Helper_HelperAbstract
{

    private $items_result;

    public function getItemSelection($attr)
    {
        $decart = array();

        if (!empty($attr)) {
            foreach ($attr as $atr => $values) {
                foreach ($values as $val) {
                    $decart = $this->getDecart($atr, $val, $decart);
                }
            }
        }

        $this->getCashedItems($decart);
    }

    /**
     * Получить все товары из поискового кеша удовл. параметрам подбора
     *
     * @param array $decart
     */
    private function getCashedItems($decart)
    {
        $SearchCash = new models_SearchCash();
        $Registration = new models_Registration();

        $cash = array_shift($decart);

        $_cash = array_map('trim', explode(' ', $cash));
        $_attrib = $this->getAttribArray($_cash);

        $this->items_result = $SearchCash->getCashedItems($_attrib,
                                                          $this->params['catalogue_id'],
                                                          $this->params['brands']);
        $this->items_result_wb = $SearchCash->getCashedItems($_attrib,
                                                             $this->params['catalogue_id'],
                                                             0);

        if (!empty($this->params['pmin']) || !empty($this->params['pmax'])) {
            $items = $this->getItemsResultId();
            $price['pmin'] = $this->params['pmin'];
            $price['pmax'] = $this->params['pmax'];

            $_items_result = $SearchCash->getCashedItemsWithPrice($items, $price);

            $user_data = Zend_Auth::getInstance()->getIdentity();
            if (!empty($user_data)) {
                $shopuser_discounts_id = $Registration->getShopuserDiscountsId($user_data['user_id']);

                if (!empty($shopuser_discounts_id) && !empty($_items_result)) {
                    $_items_result = $this->getAuthCashedItemsWithPrice($_items_result);
                }
            }

            $this->items_result = $_items_result;
        }
    }

    /**
     * Составить список всех уникальных атрибутов и брендов для активации
     *
     * @param array $items
     * @return array
     */
    public function getAttributsForActive()
    {
        $SearchCash = new models_SearchCash();

        $_brands = array();
        $_attrib_temp = array();
        $_attrib = array();

        if (!empty($this->items_result_wb)) {
            foreach ($this->items_result_wb as $itm) {
                $_brands[] = $itm['BRAND_ID'];
            }
        }

        $_brands = array_unique($_brands);

        $items_result = $SearchCash->getCashedItems(null,
                                                    $this->params['catalogue_id'],
                                                    $_brands);

        if (!empty($this->items_result)) {
            foreach ($this->items_result as $itm) {
                $_search_cash = array_map('trim',
                                          explode(' ', $itm['SEARCH_CASH']));

                $_attrib_temp = array_merge($_attrib_temp, $_search_cash);
            }

            $_attrib_temp = array_unique($_attrib_temp);

            $_attrib = $this->getAttribArray($_attrib_temp);
        }


//    $_attrib = array_unique($_attrib);

        return array($_brands, $_attrib);
    }

    private function getAttribArray($attrib_temp)
    {
        $_attrib = array();
        if (!empty($attrib_temp)) {
            foreach ($attrib_temp as $attr) {
                if (preg_match('/a(\w+)v(\w+)/', $attr, $m)) {
                    $_attrib[$m[1]][] = $m[2];
                }
            }
        }

        return $_attrib;
    }

    /**
     * Найти Декартово произведение всех входящих атрибутов
     *
     * @param integer $atr
     * @param integer $val
     * @param string $decart
     * @return string
     */
    private function getDecart($atr, $val, $decart)
    {
        $_decart = '';
        $_result = 'a' . $atr . 'v' . $val;
        $max = 0;
        if (!empty($decart)) {
            foreach ($decart as $key => $dec_val) {
                if (strlen($dec_val . $_result) > $max) {
                    unset($decart[$key]);
                    $_decart = $dec_val . ' ' . $_result;
                    $max = strlen($dec_val . $_result);
                }
            }

            $decart[] = trim($_decart);
        }

        if (strlen($_result) > $max) {
            $decart[] = $_result;
        }

        return $decart;
    }

    public function getItemsResultId()
    {
        $_result = array();
        if (!empty($this->items_result)) {
            foreach ($this->items_result as $itm) {
                $_result[] = $itm['ITEM_ID'];
            }
        }

        return $_result;
    }

    public function getItemsResultCount()
    {
        return count($this->items_result);
    }

    private function getAuthCashedItemsWithPrice($items)
    {
        $Item = new models_Item();

        $params['currency'] = $this->params['currency_id'];
        $helperLoader = Zend_Controller_Action_HelperBroker::getStaticHelper('HelperLoader');
        $ct_helper = $helperLoader->loadHelper('Cart', $params);
        $ct_helper->setModel($Item);

        $curr_info = $Item->getCurrencyInfo($this->params['currency_id']);

        $result = array();
        foreach ($items as $view) {
            list($new_price, $new_price1) = $Item->recountPrice($view['PRICE'],
                                                                $view['PRICE1'],
                                                                $view['CURRENCY_ID'],
                                                                $this->params['currency_id'],
                                                                $curr_info['PRICE']);

            if ($this->params['currency_id'] > 1) {
                $view['iprice'] = round($new_price, 1);
                $view['iprice1'] = round($new_price1, 1);
            } else {
                $view['iprice'] = round($new_price);
                $view['iprice1'] = round($new_price1);
            }

            $view = $ct_helper->recountPrice($view);

            $result_price = !empty($view['iprice1']) ? $view['iprice1'] : $view['iprice'];

            if (!empty($this->params['nat_pmin']) && !empty($this->params['nat_pmax'])) {
                if ($this->params['nat_pmax'] >= $result_price && $result_price >= $this->params['nat_pmin'])
                    $result[] = $view;
            }
            elseif (!empty($this->params['nat_pmin']) && empty($this->params['nat_pmax'])) {
                if ($this->params['nat_pmin'] <= $result_price)
                    $result[] = $view;
            }
            elseif (empty($this->params['nat_pmin']) && !empty($this->params['nat_pmax'])) {
                if ($this->params['nat_pmax'] >= $result_price)
                    $result[] = $view;
            }
        }

        return $result;
    }

}