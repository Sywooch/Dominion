<?php

class search_cash_item {

    private $_db;
    private $_search_cash;
    private $_prev_attr = 0;

    function __construct() {
        Zend_Loader::loadClass('models_SearchCash');

        $this->_db = new models_SearchCash();
    }

    /**
     * Стар работы скрипта
     *
     */
    public function buildCash() {
        $this->resetItem();
        $this->resetSearchCash();

        $item_data = true;

        while ($item_data) {
            $item_data = $this->_db->getItem();
            if (!empty($item_data)) {
                $this->processingItem($item_data);
            }
        }
    }

    /**
     * Обнуление товаров в исходное состояние
     */
    public function resetItem() {
        $this->_db->resetItem();
    }

    /**
     * Обнуление таблицы кеша
     *
     */
    public function resetSearchCash() {
        $this->_db->resetSearchCashItem();
    }

    /**
     * Создание кеша товара
     */
    private function processingItem($item_data) {

        $this->_search_cash = array();
        $attributs = $this->_db->getVisAttr($item_data['CATALOGUE_ID']);

        if (!empty($attributs)) {
            foreach ($attributs as $attr) {

                switch ($attr['TYPE']) {
                    case 0:
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                        $this->processingItemZero($item_data, $attr);
                        break;

                    case 1:
                        $this->processingItemZero($item_data, $attr);
                        break;

//            case 2:
//              $this->processingItemZero($item_data, $attr);
//            break;
//            case 7:
//              $this->processingItemZero($item_data, $attr);
//            break;
                }

                $this->_prev_attr = $attr['ATTRIBUT_ID'];
            }
        }

//      if(isset($this->_search_cash[$item_data['ITEM_ID']])){
        $insert_data['ITEM_ID'] = $item_data['ITEM_ID'];
        $insert_data['SEARCH_CASH'] = isset($this->_search_cash[$item_data['ITEM_ID']]) ? trim($this->_search_cash[$item_data['ITEM_ID']]) : '';
        $insert_data['CATALOGUE_ID'] = $item_data['CATALOGUE_ID'];
        $insert_data['BRAND_ID'] = $item_data['BRAND_ID'];

        $this->_db->insert_data('SEARCH_CASH_ITEM', $insert_data);
//      }

        $this->_db->update_item(array($item_data['ITEM_ID']));
    }

    /**
     * Обработка атрибутов таблицы ITEM0
     *
     */
    private function processingItemZero($item_data, $attribut_data) {
        $params['attr_type'] = $attribut_data['TYPE'];
        $params['item_id'] = $item_data['ITEM_ID'];
        $params['attr_id'] = $attribut_data['ATTRIBUT_ID'];
        $params['catalogue_id'] = $item_data['CATALOGUE_ID'];

        $value = $this->_db->getItemAttrValue($params);

        if (!empty($value)) {
            $params['value'] = $value;

            if (!empty($this->_prev_attr) && !empty($this->_search_cash[$item_data['ITEM_ID']])) {
                $prev_attr = $this->_search_cash[$item_data['ITEM_ID']];

                $this->_search_cash[$item_data['ITEM_ID']] = $prev_attr . 'a' . $attribut_data['ATTRIBUT_ID'] . 'v' . $value . ' ';
            } else {
                $this->_search_cash[$item_data['ITEM_ID']] = 'a' . $attribut_data['ATTRIBUT_ID'] . 'v' . $value . ' ';
            }
        }
    }

}