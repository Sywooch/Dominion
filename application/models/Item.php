<?php

class models_Item extends ZendDBEntity
{

    protected $_name = 'ITEM';
    public $sql_log = array();

    public function getCurrencyInfo($currency)
    {
        $q = "SELECT PRICE,
                 SNAME
          FROM CURRENCY
          WHERE CURRENCY_ID=?";

        return $this->_db->fetchRow($q, $currency);
    }

    /**
     * Получить путь к базовым картинкам
     *
     * @return Zend_Db_Statement_Interface
     */
    public function getAllImageBase()
    {
        return $this->_db->query(
            "SELECT I.ITEM_ID, I.BASE_IMAGE, IMAGE3
                FROM ITEM I
                WHERE I.BASE_IMAGE <> ''
                AND I.IMAGE0 IS NULL");
    }


    /**
     * Обновить ITEM - любое поле
     *
     * @param array $data  Данные которые сетим
     * @param array $where Данные для where
     */
    public function updateGlobalItem($data, $where)
    {
        $this->_db->update('ITEM', $data, $where);
    }

    /**
     * Model for get by currency extend information
     *
     * @param integer $currencyID
     *
     * @return mixed
     */
    public function getExtendCurrencyInfo($currencyID)
    {
        $sql = "SELECT PRICE, SNAME, SYSTEM_NAME FROM CURRENCY WHERE CURRENCY_ID=?";

        return $this->_db->fetchRow($sql, $currencyID);
    }

    public function getCurrencyName($currency)
    {
        $q = "SELECT SNAME
                  FROM CURRENCY
              WHERE CURRENCY_ID=?";

        return $this->_db->fetchOne($q, $currency);
    }

    public function getCatalogItemCount($params)
    {
        $where = '';

        if (!empty($params['catalogue_id'])) {
            $where = " and CATALOGUE_ID = {$params['catalogue_id']} ";
        }

        $sql = "SELECT count(*)
            FROM {$this->_name}
            WHERE PRICE > 0
              AND STATUS = 1
            {$where}";

        return $this->_db->fetchOne($sql);
    }

    public function getPricesLineItem($params)
    {
        $where = '';
        $limit = '';

        if (!empty($params['catalogue_id'])) {
            $where = " and CATALOGUE_ID = {$params['catalogue_id']} ";
        }

        if (!empty($params['start']) && !empty($params['limit'])) {
            $limit = " limit {$params['start']}, {$params['limit']} ";
        }

        $sql = "SELECT ITEM_ID
                  ,PRICE
                  ,PRICE1
            FROM {$this->_name}
            WHERE PRICE > 0
              AND STATUS = 1
            {$where}
            ORDER BY PRICE
            {$limit}";

        return $this->_db->fetchRow($sql);
    }

    public function getSimilarTempTable($id, $not_in)
    {
        $sql = "SELECT DISTINCT CI.ITEM_ID
              FROM CAT_ITEM CI
              INNER JOIN ITEM I ON (CI.ITEM_ID=I.ITEM_ID) WHERE 1 ";

        if (!empty($id)) {
            $sql .= " and CI.CATALOGUE_ID IN ({$id})";
        }
        if (!empty($not_in)) {
            $sql .= " and CI.ITEM_ID <> {$not_in}";
        }
        $sql .= " and I.STATUS=1 and I.PRICE > 0 order by I.NAME";

        return $this->_db->fetchCol($sql);
    }

    public function initTempShortAttrTable($PARAM, $childs)
    {
        $result = array();
        $childs_ = implode("','", $childs);
        foreach ($PARAM as $attr => $val) {
            $sql = "SELECT TYPE,IS_RANGEABLE FROM ATTRIBUT WHERE STATUS=1 AND ATTRIBUT_ID={$attr}";
            $row = $this->_db->fetchRow($sql);

            $TYPE = $row['TYPE'];
            $IS_RANGEABLE = $row['IS_RANGEABLE'];

            $value = $PARAM[$attr];

            if ($TYPE < 2 && $IS_RANGEABLE) {
                $query = "SELECT ATTRIBUT_ID
                FROM ITEMR
                WHERE ATTRIBUT_ID = ?
                  AND RANGE_LIST_ID = ?
                  AND ITEM_ID IN ('{$childs_}')";
            } elseif ($TYPE == 0) {
                $query = "SELECT ATTRIBUT_ID
                FROM ITEM0
                WHERE ATTRIBUT_ID = ?
                  AND VALUE = ?
                  AND ITEM_ID IN ('{$childs_}')";
            } elseif ($TYPE == 2) {
                $query = "SELECT ATTRIBUT_ID
                FROM ITEM2
                WHERE ATTRIBUT_ID = ?
                  AND VALUE = ?
                  AND ITEM_ID IN ('{$childs_}')";
            } elseif ($TYPE == 3 || $TYPE == 4) {
                $query = "SELECT IO.ATTRIBUT_ID
                FROM ITEM0 IO
                JOIN ATTRIBUT_LIST AL ON (AL.ATTRIBUT_LIST_ID = IO.VALUE)
                WHERE IO.ATTRIBUT_ID = ?
                  AND AL.NAME = ?
                  AND IO.ITEM_ID IN ('{$childs_}')";
            } elseif ($TYPE > 4) {
                if ($value == 0) {
                    $query = "SELECT ATTRIBUT_ID
                  FROM ITEM0
                  WHERE ATTRIBUT_ID = ?
                    AND (VALUE=? OR VALUE=2)
                    AND ITEM_ID IN ('{$childs_}')";
                } else {
                    $query = "SELECT ATTRIBUT_ID
                  FROM ITEM0
                  WHERE ATTRIBUT_ID = ?
                    AND (VALUE=? OR VALUE=1)
                    AND ITEM_ID IN ('{$childs_}')";
                }
            }

            $result[$attr] = $this->_db->fetchOne($query, array($attr, $value));
        }

        return $result;
    }

    public function getItemItemAjax($id)
    {
        $sql = "SELECT I.ITEM_ID
                ,I.CURRENCY_ID
                ,I.PRICE
                ,I.PRICE1
                ,I.STATUS
          FROM ITEM_ITEM II
          JOIN ITEM I ON (I.ITEM_ID = II.ITEM_ITEM_ID)
          WHERE II.ITEM_ID={$id}
            AND II.STATUS = 1";

        return $this->_db->fetchAll($sql);
    }

    public function getItemItem($id)
    {
        $sql = "SELECT ITEM_ITEM_ID
          FROM ITEM_ITEM
          WHERE ITEM_ID={$id}
            AND STATUS = 1";

        return $this->_db->fetchCol($sql);
    }

    /**
     * Метод для получения информации о товаре
     * @access   public
     *
     * @param    integer $id
     *
     * @return   array
     */
    public function getItemInfo($id)
    {
        $sql = "SELECT I.ITEM_ID
                  ,I.CATALOGUE_ID
                  ,I.NAME
                  ,I.CATNAME
                  ,I.TYPENAME
                  ,I.ARTICLE
                  ,I.CURRENCY_ID
                  ,I.PRICE
                  ,I.PRICE1
                  ,I.PURCHASE_PRICE
                  ,I.IMAGE1
                  ,I.IMAGE2
                  ,I.IMAGE3
                  ,I.DESCRIPTION
                  ,I.SEO_BOTTOM
                  ,I.STATUS

                  ,I.WARRANTY_ID
                  ,I.DELIVERY_ID
                  ,I.CREDIT_ID

                  ,I.IS_ACTION

                  ,D.IMAGE AS DISCOUNTS_IMAGE
                  ,B.NAME AS BRAND_NAME
                  ,B.URL AS BRAND_URL

                  ,W.DESCRIPTION AS WARRANTY_DESCRIPTION
                  ,DL.DESCRIPTION AS DELIVERY_DESCRIPTION
                  ,CR.DESCRIPTION AS CREDIT_DESCRIPTION

                  ,C.REALCATNAME AS CATALOGUE_REALCATNAME
                  ,C.NAME AS CATALOGUE_NAME
                  ,CRN.SNAME
            FROM ITEM I
            LEFT JOIN DISCOUNTS D ON (D.DISCOUNT_ID = I.DISCOUNT_ID)
            LEFT JOIN BRAND B ON (B.BRAND_ID = I.BRAND_ID)
            LEFT JOIN WARRANTY W ON (W.WARRANTY_ID=I.WARRANTY_ID)
            LEFT JOIN DELIVERY DL ON (DL.DELIVERY_ID=I.DELIVERY_ID)
            LEFT JOIN CREDIT CR ON (CR.CREDIT_ID=I.CREDIT_ID)
            LEFT JOIN CATALOGUE C ON (C.CATALOGUE_ID = I.CATALOGUE_ID)
            LEFT JOIN CURRENCY CRN ON (CRN.CURRENCY_ID = I.CURRENCY_ID)
            WHERE I.ITEM_ID=?";


        return $this->_db->fetchRow($sql, $id);
    }

    public function getItemChpuInfo($id)
    {
        $sql = "SELECT I.ITEM_ID
                  ,I.CATNAME
                  ,C.REALCATNAME AS CATALOGUE_REALCATNAME
            FROM ITEM I
            LEFT JOIN CATALOGUE C ON (C.CATALOGUE_ID = I.CATALOGUE_ID)
            WHERE I.ITEM_ID=?";

        return $this->_db->fetchRow($sql, $id);
    }

    public function getItemLognText($id)
    {
        $sql = "SELECT XML
          FROM XMLS
          WHERE TYPE=3
            AND XMLS_ID=?";

        return $this->_db->fetchOne($sql, $id);
    }

    public function getItemInfoAjax($id)
    {
        $sql = "SELECT ITEM_ID
                  ,CURRENCY_ID
                  ,PRICE
                  ,PRICE1
            FROM ITEM
            WHERE ITEM_ID=?";

        return $this->_db->fetchRow($sql, $id);
    }

    public function getCatItems($params, $lang = 0)
    {
        $where = '';
        $asc = 'asc';
        $order_by = 'I.NAME';

        if (!empty($params['order_by'])) {
            $asc = $params['asc'];
            $order_by = $params['order_by'];
        }

        if (!empty($params['brand_id']) && is_array($params['brand_id'])) {
            $_brand = implode(", ", $params['brand_id']);

            $where .= " and I.BRAND_ID IN ({$_brand}) ";
        } elseif (!empty($params['brand_id']) && !is_array($params['brand_id'])) {
            $where .= " and I.BRAND_ID = {$params['brand_id']} ";
        }

        if (!empty($params['catalogue_id'])) {
            $where .= " and I.CATALOGUE_ID = {$params['catalogue_id']} ";
        }

        if (!empty($params['items_id'])) {
            $_items = implode(", ", $params['items_id']);

            $where .= " and I.ITEM_ID IN ({$_items}) ";
        }

        $sql = "SELECT I.ITEM_ID
                ,I.CATALOGUE_ID
                ,I.NAME
                ,I.ARTICLE
                ,I.CURRENCY_ID
                ,I.PRICE
                ,I.PRICE1
                ,I.IMAGE1
                ,I.IMAGE2
                ,I.IMAGE3
                ,I.DESCRIPTION
                ,I.SEO_BOTTOM
                ,I.CATNAME
                ,I.IS_ACTION
                ,I.STATUS
                ,D.IMAGE AS DISCOUNTS_IMAGE
                ,B.NAME AS BRAND_NAME
                ,C.REALCATNAME AS CATALOGUE_REALCATNAME
                ,CR.SNAME
          FROM ITEM I
          LEFT JOIN CATALOGUE C ON (C.CATALOGUE_ID = I.CATALOGUE_ID)
          LEFT JOIN CURRENCY CR ON (CR.CURRENCY_ID = I.CURRENCY_ID)
          LEFT JOIN DISCOUNTS D ON (D.DISCOUNT_ID = I.DISCOUNT_ID)
          LEFT JOIN BRAND B ON (B.BRAND_ID = I.BRAND_ID)
          WHERE I.PRICE > 0
            {$where}
          ORDER BY {$order_by} {$asc}
          LIMIT {$params['start']},{$params['per_page']}";

        return $this->_db->fetchAll($sql);
    }

    public function getCatalItems(
        $catalogue_id,
        $item_id,
        $startSelect = 0,
        $perPage = 0
    )
    {

        $_catalogue_id = implode(", ", $catalogue_id);
        $sql = "SELECT I.ITEM_ID
                ,I.CATALOGUE_ID
                ,I.NAME
                ,I.ARTICLE
                ,I.CURRENCY_ID
                ,I.PRICE
                ,I.PRICE1
                ,I.IMAGE1
                ,I.IMAGE2
                ,I.SEO_BOTTOM
                ,I.IS_ACTION
                ,D.IMAGE AS DISCOUNTS_IMAGE
                ,B.NAME AS BRAND_NAME
          FROM ITEM I
          LEFT JOIN DISCOUNTS D ON (D.DISCOUNT_ID = I.DISCOUNT_ID)
          LEFT JOIN BRAND B ON (B.BRAND_ID = I.BRAND_ID)
          WHERE I.STATUS=1
            AND I.ITEM_ID <> {$item_id}
            AND I.PRICE > 0
            AND I.CATALOGUE_ID IN ({$_catalogue_id})
          ORDER BY I.NAME";

        if (!empty($perPage)) {
            $sql .= " limit {$startSelect},{$perPage}";
        }

        return $this->_db->fetchAll($sql);
    }

    public function getMinMaxPrice($catalogue_id)
    {

        $_catalogue_id = implode(", ", $catalogue_id);

        $sql = "SELECT ITEM_ID
          FROM ITEM
          WHERE STATUS=1
            AND PRICE > 0
            AND CATALOGUE_ID IN ({$_catalogue_id})";

        return $this->_db->fetchCol($sql);
    }

    public function getCatalogItemsID(
        $catalogue_id,
        $item_id = 0,
        $limit = false,
        $start = 0
    )
    {

        $_catalogue_id = implode(", ", $catalogue_id);
        $where = '';

        if (!empty($item_id)) {
            $where = 'and ITEM_ID <> ' . $item_id;
        }

        $sql = "SELECT ITEM_ID
          FROM ITEM
          WHERE STATUS=1
            AND PRICE > 0
            AND CATALOGUE_ID IN ({$_catalogue_id})
            {$where}";

        if (!empty($limit) && !empty($start)) {
            $sql .= " limit {$start}, {$limit}";
        } elseif ($limit) {
            $sql .= " limit {$limit}";
        }

        return $this->_db->fetchCol($sql);
    }

    public function getSimilarItemsCount($table, $it_id = 0)
    {
        $sql = "SELECT count(TT.ITEM_ID)
          FROM {$table} TT
              ,ITEM I
          WHERE I.ITEM_ID = TT.ITEM_ID
            AND I.STATUS=1
            AND I.ITEM_ID <> {$it_id}
            AND I.PRICE > 0";

        if (!empty($it_id)) {
            $sql .= " and I.ITEM_ID != {$it_id}";
        }

        return $this->_db->fetchOne($sql);
    }

    public function getSimilarItems(
        $table,
        $it_id = 0,
        $limit = false,
        $start = 0
    )
    {
        $sql = "SELECT TT.ITEM_ID
          FROM {$table} TT
              ,ITEM I
          WHERE I.ITEM_ID = TT.ITEM_ID
            AND I.STATUS=1
            AND I.PRICE > 0";

        if (!empty($it_id)) {
            $sql .= " and I.ITEM_ID <> {$it_id}";
        }

        $sql .= " order by I.PRICE";

        if ($limit) {
            $sql .= " limit {$start}, {$limit}";
        }

        return $this->_db->fetchCol($sql);
    }

    /**
     * Метод для получения ИД каталога для товара
     * @access   public
     *
     * @param    integer $id
     *
     * @return   integer $catalog
     */
    public function getItemCatalog($id)
    {
        $sql = "SELECT CATALOGUE_ID FROM ITEM WHERE ITEM_ID = ?";

        return $this->_db->fetchOne($sql, $id);
    }

    public function getItemName($id)
    {
        $sql = "SELECT NAME FROM ITEM WHERE ITEM_ID=?";

        return $this->_db->fetchOne($sql, $id);
    }

    public function getCartItemName($id)
    {
        $sql = "SELECT concat(B.NAME,' ',I.NAME)
          FROM ITEM I
          LEFT JOIN BRAND B ON (B.BRAND_ID = I.BRAND_ID)
          WHERE I.ITEM_ID=?";

        return $this->_db->fetchOne($sql, $id);
    }

    public function getItemBreadcrumbName($id)
    {
        $sql = "SELECT I.TYPENAME
                ,I.NAME
                ,B.NAME AS BRAND_NAME
          FROM ITEM I
          LEFT JOIN BRAND B ON (B.BRAND_ID = I.BRAND_ID)
          WHERE I.ITEM_ID=?";

        return $this->_db->fetchRow($sql, $id);
    }

    public function getCountItemResponses($id)
    {
        $sql = "SELECT count(*)
             FROM RESPONSES
             WHERE ITEM_ID={$id}
               AND HIDE=1
             ORDER BY DATA DESC";

        return $this->_db->fetchOne($sql);
    }

    public function getItemResponses($id)
    {
        $sql = "SELECT *
                   ,DATE_FORMAT(DATA,'%d/%m/%Y %H:%i') AS date
             FROM RESPONSES
             WHERE ITEM_ID=?
               AND HIDE=1
             ORDER BY DATA DESC";

        return $this->_db->fetchAll($sql, array($id));
    }

    public function insertResponseData($data)
    {
        $this->_db->insert('RESPONSES', $data);
    }

    public function recountPrice(
        $price,
        $price1,
        $currency_id,
        $currency,
        $curr_price
    )
    {
        if ($currency_id != $currency) {
            //Валюта товара не соответствует выбранной,нужен пересчет
            $q = "SELECT PRICE FROM CURRENCY WHERE CURRENCY_ID=?";
            $cprice = $this->_db->fetchOne($q, $currency_id);

            if ($currency_id > 1 && $curr_price > 1) {
                //Цена не в гривне,пересчет в гривны
                $bprice = $price * $curr_price;
                $bprice1 = $price1 * $curr_price;

                //Из гривен перевoдим в выбранную валюту
                if ($currency > 1) {
                    $new_price = round($bprice / $cprice);
                    $new_price1 = round($bprice1 / $cprice);
                } else {
                    $new_price = round($bprice / $cprice);
                    $new_price1 = round($bprice1 / $cprice);
                }
            } elseif ($currency_id > 1 && $curr_price <= 1) {
                //Цена не в гривне,пересчет в гривны
                $bprice = $price * $cprice;
                $bprice1 = $price1 * $cprice;

                //Из гривен перевoдим в выбранную валюту
                if ($currency > 1) {
                    $new_price = round($bprice / $curr_price);
                    $new_price1 = round($bprice1 / $curr_price);
                } else {
                    $new_price = round($bprice / $curr_price);
                    $new_price1 = round($bprice1 / $curr_price);
                }
            } else {
                //Стоимость товара в гривнах
                if ($currency > 1) {
                    $new_price = round($price / $curr_price);
                    $new_price1 = round($price1 / $curr_price);
                } else {
                    $new_price = round($price / $curr_price);
                    $new_price1 = round($price1 / $curr_price);
                }
            }
        } else {
            //Не пересчитывать
            $new_price = $price;
            $new_price1 = $price1;
        }

        return array($new_price, $new_price1);
    }

    public function recountPriceSc(
        $price,
        $item_currency_id,
        $current_currency_id,
        $curr_price
    )
    {
        if ($item_currency_id != $current_currency_id) {
            //Валюта товара не соответствует выбранной,нужен пересчет
            $q = "SELECT PRICE FROM CURRENCY WHERE CURRENCY_ID=?";
            $cprice = $this->_db->fetchOne($q, $item_currency_id);
            if ($item_currency_id > 1) {
                //Цена не в гривне,пересчет в гривны
                $bprice = $price * $cprice;

                //Из гривен перевoдим в выбранную валюту
                if ($current_currency_id > 1) {
                    $new_price = round($bprice / $curr_price, 1);
                } else {
                    $new_price = round($bprice / $curr_price);
                }
            } else {
                //Стоимость товара в гривнах
                if ($current_currency_id > 1) {
                    $new_price = round($price / $curr_price, 1);
                } else {
                    $new_price = round($price / $curr_price);
                }
            }
        } else {
            //Не пересчитывать
            $new_price = $price;
        }

        return $new_price;
    }

    public function getAttributes(
        $catalogueId = 0,
        $tableName = 'ATTR_CATALOG_LINK',
        $limit = '',
        $show = 0
    )
    {
        if (!$catalogueId) {
            return;
        }

        $where = '';
        if ($limit) {
            $restrict = ' limit 0,' . $limit;
        } else {
            $restrict = '';
        }

        if ($show == 1) {
            $where .= " and A.SHOW_='1'";
        }

        $sql = "select ACL.ATTRIBUT_ID
                 , A.NAME
                 , A.TITLE
                 , A.TYPE
                 , A.UNIT_ID
                 , A.NOT_CARD
                 , A.IS_RANGEABLE
                 , A.MULTIPLE_
                 , if(A.VIEW_ATTRIBUT_GROUP_ID is null, 1000000, A.VIEW_ATTRIBUT_GROUP_ID) as VIEW_ATTRIBUT_GROUP_ID
                 , if(VAG.NAME is null, 'Другие характеристики', VAG.NAME) as VAG_NAME
                 , if(VAG.ORDERING is null, 999, VAG.ORDERING) as vag_order
            from " . $tableName . " as ACL
               , ATTRIBUT as A
            left join VIEW_ATTRIBUT_GROUP as VAG on (VAG.VIEW_ATTRIBUT_GROUP_ID=A.VIEW_ATTRIBUT_GROUP_ID)
            where A.ATTRIBUT_ID=ACL.ATTRIBUT_ID
              and ACL.CATALOGUE_ID='" . $catalogueId . "'
              and ACL.CATALOGUE_ID='" . $catalogueId . "'
              and A.STATUS=1 " . $where . "
              order by vag_order, A.ORDERING " . $restrict;

        $attributes = $this->_db->fetchAll($sql);

        for ($i = 0; $i < count($attributes); $i++) {
            if ($attributes[$i]['UNIT_ID']) {
                $q = "SELECT NAME FROM UNIT WHERE UNIT_ID=?";
                $unit_name = $this->_db->fetchOne($q, $attributes[$i]['UNIT_ID']);
                $attributes[$i]['UNIT_NAME'] = $unit_name;
            } else {
                $attributes[$i]['UNIT_NAME'] = '';
            }
        }

        return $attributes;
    }

    public function getAttributesDescription(
        $catalogueId = 0,
        $tableName = 'ATTR_CATALOG_LINK'
    )
    {
        if (!$catalogueId) {
            return;
        }

        $sql = "select ACL.ATTRIBUT_ID
                 , ACL.IN_PODBOR
                 , A.NAME
                 , A.TITLE
                 , A.TYPE
                 , A.UNIT_ID
                 , A.NOT_CARD
                 , A.IS_RANGEABLE
                 , if(A.VIEW_ATTRIBUT_GROUP_ID is null, 1000000, A.VIEW_ATTRIBUT_GROUP_ID) as VIEW_ATTRIBUT_GROUP_ID
                 , if(VAG.NAME is null, 'Другие характеристики', VAG.NAME) as VAG_NAME
                 , if(VAG.ORDERING is null, 999, VAG.ORDERING) as vag_order
                 , A.MULTIPLE_
            from " . $tableName . " as ACL
                 , ATTRIBUT as A
            left join VIEW_ATTRIBUT_GROUP as VAG on (VAG.VIEW_ATTRIBUT_GROUP_ID=A.VIEW_ATTRIBUT_GROUP_ID)
            where A.ATTRIBUT_ID=ACL.ATTRIBUT_ID
              and ACL.CATALOGUE_ID='" . $catalogueId . "'
              and A.STATUS=1
              and ACL.IN_PODBOR > 0
              order by A.ORDERING";

        $attributes = $this->_db->fetchAll($sql);

        for ($i = 0; $i < count($attributes); $i++) {
            if ($attributes[$i]['UNIT_ID']) {
                $q = "SELECT NAME FROM UNIT WHERE UNIT_ID=?";
                $unit_name = $this->_db->fetchOne($q, $attributes[$i]['UNIT_ID']);
                $attributes[$i]['UNIT_NAME'] = $unit_name;
            } else {
                $attributes[$i]['UNIT_NAME'] = '';
            }
        }

        return $attributes;
    }

    public function getItemAttributes(
        $attributeList,
        $itemId,
        $catId = '',
        $limit = 0
    )
    {
        $itemAttribute = $attributeList;
        $attribs = array();
        $cnt = 0;

        for ($j = 0; $j < count($itemAttribute); $j++) {
            $attrType = $itemAttribute[$j]['TYPE'];
            $multiple = $itemAttribute[$j]['MULTIPLE_'];
            $value = '';
            $val = '';
            $image = '';

            switch ($attrType) {
                case 0: // 0-Int
                    $value = $this->_db->fetchOne(
                        'SELECT VALUE FROM ITEM0 WHERE ITEM_ID=? AND ATTRIBUT_ID=?',
                        array($itemId, $itemAttribute[$j]['ATTRIBUT_ID'])
                    );
                    if ($itemAttribute[$j]['IS_RANGEABLE'] == 1) {
                        $val = $this->_db->fetchOne(
                            'SELECT RANGE_LIST_ID FROM ITEMR WHERE ITEM_ID=? AND ATTRIBUT_ID=?',
                            array($itemId, $itemAttribute[$j]['ATTRIBUT_ID'])
                        );
                    } else {
                        $val = $value;
                    }
                    break;

                case 1: // 1-double
                    $value = $this->_db->fetchOne(
                        'SELECT VALUE FROM ITEM1 WHERE ITEM_ID=? AND ATTRIBUT_ID=?',
                        array($itemId, $itemAttribute[$j]['ATTRIBUT_ID'])
                    );
                    if ($itemAttribute[$j]['IS_RANGEABLE'] == 1) {
                        $val = $this->_db->fetchOne(
                            'SELECT RANGE_LIST_ID FROM ITEMR WHERE ITEM_ID=? AND ATTRIBUT_ID=?',
                            array($itemId, $itemAttribute[$j]['ATTRIBUT_ID'])
                        );
                    } else {
                        $val = $value;
                    }
                    break;

                case 2: // 2-varchar
                    $value = $this->_db->fetchOne(
                        'SELECT VALUE FROM ITEM2 WHERE ITEM_ID=? AND ATTRIBUT_ID=?',
                        array($itemId, $itemAttribute[$j]['ATTRIBUT_ID'])
                    );
                    break;

                case 3: // 3-список
                    if ($multiple == 1) {
                        $value = '';
                        $values = $this->_db->fetchAll(
                            'SELECT A.NAME FROM ITEM0 I, ATTRIBUT_LIST A WHERE I.VALUE=A.ATTRIBUT_LIST_ID AND I.ITEM_ID=? AND I.ATTRIBUT_ID=?',
                            array($itemId, $itemAttribute[$j]['ATTRIBUT_ID'])
                        );
                        if ($values) {
                            $arr = array();
                            foreach ($values as $v) {
                                $arr[] = $v['NAME'];
                            }
                            $value = implode("; ", $arr);
                        }
                    } else {
                        $value = $this->_db->fetchOne(
                            'SELECT A.NAME FROM ITEM0 I, ATTRIBUT_LIST A WHERE I.VALUE=A.ATTRIBUT_LIST_ID AND I.ITEM_ID=? AND I.ATTRIBUT_ID=?',
                            array($itemId, $itemAttribute[$j]['ATTRIBUT_ID'])
                        );
                    }
                    $val = $this->_db->fetchOne(
                        'SELECT VALUE FROM ITEM0 I WHERE ITEM_ID=? AND ATTRIBUT_ID=?',
                        array($itemId, $itemAttribute[$j]['ATTRIBUT_ID'])
                    );

                    //echo 'select A.NAME from ITEM0 I, ATTRIBUT_LIST A where I.ATTRIBUT_ID=A.ATTRIBUT_ID and I.ITEM_ID='.$itemId.' and I.ATTRIBUT_ID='.$itemAttribute[$j]['ATTRIBUT_ID'].'<br>';
                    break;
                case 4: // 4-список с картинкой и текстом
                    $data = $this->_db->fetchRow(
                        'SELECT A.NAME,A.IMAGE FROM ITEM0 I, ATTRIBUT_LIST A WHERE I.VALUE=A.ATTRIBUT_LIST_ID AND I.ITEM_ID=? AND I.ATTRIBUT_ID=?',
                        array($itemId, $itemAttribute[$j]['ATTRIBUT_ID'])
                    );
                    $value = $data['NAME'];
                    $image = $data['IMAGE'];
                    break;

                case 5: // 5-чекбокс
                case 6: // 6-чекбокс с тремя состояниями (да,нет,не знаю)
                    $value = $this->_db->fetchOne(
                        'SELECT VALUE FROM ITEM0 WHERE ITEM_ID=? AND ATTRIBUT_ID=?',
                        array($itemId, $itemAttribute[$j]['ATTRIBUT_ID'])
                    );
                    if (empty($value)) {
                        $val = 'Нет';
                    } else {
                        $val = $this->_db->fetchOne(
                            'SELECT ALTER_VALUE FROM ATTRIBUT WHERE ATTRIBUT_ID=?',
                            array($itemAttribute[$j]['ATTRIBUT_ID'])
                        );
                    }
                    break;

                case 7: // краткое описание(64 к)
                    $value = $this->_db->fetchOne(
                        'SELECT VALUE FROM ITEM7 WHERE ITEM_ID=? AND ATTRIBUT_ID=?',
                        array($itemId, $itemAttribute[$j]['ATTRIBUT_ID'])
                    );
                    break;
            }

            if (!strchr($value, 'важно') && $value != '') {
                if ($itemAttribute[$j]['TYPE'] != 6 || ($itemAttribute[$j]['TYPE'] == 6 && $value == 1)) {
                    $attribs[$cnt]['attribut_id'] = $itemAttribute[$j]['ATTRIBUT_ID'];
                    $attribs[$cnt]['is_rangeable'] = $itemAttribute[$j]['IS_RANGEABLE'];
                    $attribs[$cnt]['not_card'] = $itemAttribute[$j]['NOT_CARD'];
                    $attribs[$cnt]['name'] = $itemAttribute[$j]['NAME'];
                    $attribs[$cnt]['type'] = $itemAttribute[$j]['TYPE'];
                    $attribs[$cnt]['unit_name'] = $itemAttribute[$j]['UNIT_NAME'];
                    $attribs[$cnt]['value'] = $value;
                    $attribs[$cnt]['image'] = $image;
                    $attribs[$cnt]['val'] = $val;

                    $attribs[$cnt]['view_attribut_group_id'] = $itemAttribute[$j]['VIEW_ATTRIBUT_GROUP_ID'];
                    $attribs[$cnt]['vag_name'] = $itemAttribute[$j]['VAG_NAME'];

                    if (isset($itemAttribute[$j]['IN_PODBOR'])) {
                        $attribs[$cnt]['in_podbor'] = $itemAttribute[$j]['IN_PODBOR'];
                    }
                    $cnt++;
                }
            }
            if ($limit > 0) {
                if ($cnt > $limit) {
                    break;
                }
            }
        }

        return $attribs;
    }

    public function getItemShortAttrib($attributeList, $itemId)
    {
        $itemAttribute = $attributeList;
        $attribs = array();

        for ($j = 0; $j < count($itemAttribute); $j++) {
            $attrType = $itemAttribute[$j]['TYPE'];
            $multiple = $itemAttribute[$j]['MULTIPLE_'];
            $value = '';
            $val = '';

            switch ($attrType) {
                case 0: // 0-Int
                    if ($itemAttribute[$j]['IS_RANGEABLE'] == 1) {
                        $value = $this->_db->fetchOne(
                            'SELECT RANGE_LIST_ID FROM ITEMR WHERE ITEM_ID=? AND ATTRIBUT_ID=?',
                            array($itemId, $itemAttribute[$j]['ATTRIBUT_ID'])
                        );
                    } else {
                        $value = $this->_db->fetchOne(
                            'SELECT VALUE FROM ITEM0 WHERE ITEM_ID=? AND ATTRIBUT_ID=?',
                            array($itemId, $itemAttribute[$j]['ATTRIBUT_ID'])
                        );
                    }
                    break;

                case 1: // 1-double
                    if ($itemAttribute[$j]['IS_RANGEABLE'] == 1) {
                        $value = $this->_db->fetchOne(
                            'SELECT RANGE_LIST_ID FROM ITEMR WHERE ITEM_ID=? AND ATTRIBUT_ID=?',
                            array($itemId, $itemAttribute[$j]['ATTRIBUT_ID'])
                        );
                    } else {
                        $value = $this->_db->fetchOne(
                            'SELECT VALUE FROM ITEM1 WHERE ITEM_ID=? AND ATTRIBUT_ID=?',
                            array($itemId, $itemAttribute[$j]['ATTRIBUT_ID'])
                        );
                    }
                    break;

                case 2: // 2-varchar
                    $value = $this->_db->fetchOne(
                        'SELECT VALUE FROM ITEM2 WHERE ITEM_ID=? AND ATTRIBUT_ID=?',
                        array($itemId, $itemAttribute[$j]['ATTRIBUT_ID'])
                    );
                    break;

                case 3: // 3-список
                    if ($multiple == 1) {
                        $value = '';
                        $values = $this->_db->fetchAll(
                            'SELECT A.NAME FROM ITEM0 I, ATTRIBUT_LIST A WHERE I.VALUE=A.ATTRIBUT_LIST_ID AND I.ITEM_ID=? AND I.ATTRIBUT_ID=?',
                            array($itemId, $itemAttribute[$j]['ATTRIBUT_ID'])
                        );
                        if ($values) {
                            $arr = array();
                            foreach ($values as $v) {
                                $arr[] = $v['NAME'];
                            }
                            $value = implode("; ", $arr);
                        }
                    } else {
                        $value = $this->_db->fetchOne(
                            'SELECT A.NAME FROM ITEM0 I, ATTRIBUT_LIST A WHERE I.VALUE=A.ATTRIBUT_LIST_ID AND I.ITEM_ID=? AND I.ATTRIBUT_ID=?',
                            array($itemId, $itemAttribute[$j]['ATTRIBUT_ID'])
                        );
                    }
                    $val = $this->_db->fetchOne(
                        'SELECT VALUE FROM ITEM0 I WHERE ITEM_ID=? AND ATTRIBUT_ID=?',
                        array($itemId, $itemAttribute[$j]['ATTRIBUT_ID'])
                    );

                    //echo 'select A.NAME from ITEM0 I, ATTRIBUT_LIST A where I.ATTRIBUT_ID=A.ATTRIBUT_ID and I.ITEM_ID='.$itemId.' and I.ATTRIBUT_ID='.$itemAttribute[$j]['ATTRIBUT_ID'].'<br>';
                    break;
                case 4: // 4-список с картинкой и текстом
                    $data = $this->_db->fetchRow(
                        'SELECT A.NAME,A.IMAGE FROM ITEM0 I, ATTRIBUT_LIST A WHERE I.VALUE=A.ATTRIBUT_LIST_ID AND I.ITEM_ID=? AND I.ATTRIBUT_ID=?',
                        array($itemId, $itemAttribute[$j]['ATTRIBUT_ID'])
                    );
                    $value = $data['NAME'];
                    break;

                case 5: // 5-чекбокс
                case 6: // 6-чекбокс с тремя состояниями (да,нет,не знаю)
                    $value = $this->_db->fetchOne(
                        'SELECT VALUE FROM ITEM0 WHERE ITEM_ID=? AND ATTRIBUT_ID=?',
                        array($itemId, $itemAttribute[$j]['ATTRIBUT_ID'])
                    );
                    $val = $this->_db->fetchOne(
                        'SELECT ALTER_VALUE FROM ATTRIBUT WHERE ATTRIBUT_ID=?',
                        array($itemAttribute[$j]['ATTRIBUT_ID'])
                    );
                    break;
            }

            if (!strchr($value, 'важно') && $value != '') {
                if ($itemAttribute[$j]['TYPE'] != 6 || ($itemAttribute[$j]['TYPE'] == 6 && $value == 1)) {
                    $attribs[$itemAttribute[$j]['ATTRIBUT_ID']] = $value;
                }
            }
        }

        return $attribs;
    }

    public function getItmsList($id, $value)
    {
        $sql = "select I.ITEM_ID,
                   I.NAME
            from ITEM I
            inner join ITEM0 I0 on I0.ITEM_ID=I.ITEM_ID
            where I.ITEM_ID<>'" . $id . "'
              and I0.VALUE='" . $value . "'
            order by RAND() limit 0,10";

        return $this->_db->fetchAll($sql);
    }

    public function insertZakaz($data)
    {
        $this->_db->insert('ZAKAZ', $data);
        $this->updateSequence('ZAKAZ');

        return $this->lastInsertId('ZAKAZ');
    }

    public function insertOrder($data)
    {
        $sql = "INSERT INTO ZAKAZ_ITEM
          SET ZAKAZ_ID = {$data['ZAKAZ_ID']}
            , CATALOGUE_ID = {$data['CATALOGUE_ID']}
            , NAME = '{$data['NAME']}'
            , ITEM_ID = {$data['ITEM_ID']}
            , PRICE = {$data['PRICE']}
            , ITEM_PRICE = {$data['ITEM_PRICE']}
            , ITEM_CURRENCY = '{$data['ITEM_CURRENCY']} '
            , QUANTITY = {$data['QUANTITY']}
            , COST  = {$data['COST']}
            , CURRENCY_ID = {$data['CURRENCY_ID']} ";

        $this->_db->query($sql);

        $this->updateSequence('ZAKAZ_ITEM');

        return $this->lastInsertId('ZAKAZ_ITEM');
    }

    public function getItemMeta($id)
    {
        $sql = "SELECT I.CATALOGUE_ID
                  ,B.NAME AS BRAND_NAME
                  ,I.ITEM_ID
                  ,I.NAME
                  ,I.TYPENAME
                  ,I.TITLE
                  ,I.DESC_META
                  ,I.KEYWORD_META
            FROM ITEM I
            LEFT JOIN BRAND B ON (B.BRAND_ID = I.BRAND_ID)
            WHERE I.ITEM_ID=?";

        return $this->_db->fetchRow($sql, $id);
    }

    public function getItemsForMeta($is_new, $gen_catalog_id = 0)
    {
        $where = " 1 ";
        if ($is_new) {
            $where .= " and TITLE = ''
               and DESC_META = ''
               and KEYWORD_META = ''";
        }
        if (!empty($gen_catalog_id)) {
            $where .= " and CATALOGUE_ID = {$gen_catalog_id}";
        }
        $sql = "SELECT CATALOGUE_ID
                  ,TYPENAME
                  ,BRAND_ID
                  ,ITEM_ID
                  ,NAME
            FROM ITEM I
            WHERE {$where}";

        return $this->_db->fetchAll($sql);
    }

    function getBrandName($id)
    {
        $sql = "SELECT NAME
           FROM BRAND
           WHERE BRAND_ID = ?";

        return $this->_db->fetchOne($sql, $id);
    }

    public function getCatSubItemTitle($id)
    {
        $sql = "SELECT SUB_ITEM_TITLE
               , PARENT_ID
               , CATALOGUE_ID
          FROM CATALOGUE
          WHERE CATALOGUE_ID = ?";

        return $this->_db->fetchRow($sql, $id);
    }

    public function getAllModels($cid)
    {
        $ids_str = implode(',', $cid);

        $sql = "SELECT B.BRAND_ID
               , B.NAME
          FROM ITEM I
              ,BRAND B
          WHERE I.CATALOGUE_ID IN ({$ids_str})
            AND I.BRAND_ID = B.BRAND_ID
            AND I.PRICE > 0
            AND I.STATUS=1
            AND B.STATUS=1
          GROUP BY B.BRAND_ID
          ORDER BY B.NAME";

        return $this->_db->fetchAll($sql);
    }

    public function getItemsSearch()
    {
        $sql = "SELECT I.*
                  ,C.NAME AS CNAME
                  ,C.REALCATNAME AS CATALOGUE_REALCATNAME
                  ,B.NAME AS BRAND_NAME
            FROM ITEM I
            JOIN CATALOGUE C ON (C.CATALOGUE_ID=I.CATALOGUE_ID)
            JOIN BRAND B ON (B.BRAND_ID=I.BRAND_ID)
            WHERE I.STATUS = 1
              AND I.PRICE  > 0";

        return $this->_db->fetchAll($sql);
    }

    /** метод для получения массива списочных значений атрибутов товара
     *  Используется для занесения этих значений в поисковый индекс
     */
    public function getItemSearchAttrs($itemID)
    {
        $sql = "SELECT
              AL.NAME
            FROM
              ATTRIBUT_LIST AL
              JOIN ITEM0 I0
                ON (AL.ATTRIBUT_ID = I0.ATTRIBUT_ID
              AND AL.ATTRIBUT_LIST_ID = I0.VALUE AND I0.ITEM_ID = ?)
              JOIN ATTRIBUT A ON (AL.ATTRIBUT_ID = A.ATTRIBUT_ID)";

        return $this->_db->fetchAll($sql, $itemID);
    }

    public function getItemPhotos($id)
    {
        $sql = "SELECT *
            FROM ITEM_PHOTO
            WHERE ITEM_ID = ?
              AND STATUS=1";

        return $this->_db->fetchAll($sql, $id);
    }

    public function getItemMedia($id)
    {
        $sql = "SELECT *
            FROM ITEM_MEDIA
            WHERE ITEM_ID = ?
              AND STATUS=1";

        return $this->_db->fetchAll($sql, $id);
    }

    public function getAllItemId()
    {
        $sql = "SELECT ITEM_ID
                 , CATALOGUE_ID
            FROM ITEM";

        return $this->_db->fetchAll($sql);
    }

    public function updateItem($id, $description)
    {
        $sql = "UPDATE ITEM
            SET DESCRIPTION = '{$description}'
            WHERE ITEM_ID = {$id}";

        return $this->_db->query($sql);
    }

    function getItemByCode($code)
    {
        $sql = "SELECT ITEM_ID
           FROM ITEM
           WHERE ARTICLE = '{$code}'";

//     echo __CLASS__."==".__METHOD__."==".$sql."\r\n";

        return $this->_db->fetchOne($sql);
    }

    function getWarrantyByCode($code)
    {
        $sql = "SELECT WARRANTY_ID
           FROM WARRANTY
           WHERE ID_FROM_VBD = {$code}";

        return $this->_db->fetchOne($sql);
    }

    function getDeliveryByCode($code)
    {
        $sql = "SELECT DELIVERY_ID
           FROM DELIVERY
           WHERE ID_FROM_VBD = '{$code}'";

        return $this->_db->fetchOne($sql);
    }

    function getItemByName($name, $categoryId)
    {
        $sql = "SELECT ITEM_ID
           FROM ITEM
           WHERE NAME = '{$name}'
             AND CATALOGUE_ID = {$categoryId}";

//     echo __CLASS__."==".__METHOD__."==".$sql."\r\n";

        return $this->_db->fetchOne($sql);
    }

    function insertItem($data)
    {
        $this->_db->insert('ITEM', $data);
        $last_id = $this->_db->lastInsertId();

        $this->updateSequence('ITEM');

        return $last_id;
    }

    function insertCatItem($data)
    {
        $this->_db->insert('CAT_ITEM', $data);
    }

    public function updateItemImport($data, $uid)
    {
        $this->_db->update('ITEM', $data, 'ITEM_ID=' . $uid);
    }

    public function deactiveAllItems()
    {
        $data['STATUS'] = 0;
        $this->_db->update('ITEM', $data, 'STATUS > 0');
    }

    function updateSequence($name)
    {
        $sql = "UPDATE SEQUENCES
           SET ID = ID + 1
           WHERE NAME = '{$name}'";

        $this->_db->query($sql);
    }

    function lastInsertId($name)
    {
        $sql = "SELECT ID
           FROM SEQUENCES
           WHERE NAME = '{$name}'";

        $res = $this->_db->fetchOne($sql);

        return $res;
    }

    function insertItemFotos($data)
    {
        $this->_db->insert('ITEM_PHOTO', $data);
        $this->updateSequence('ITEM_PHOTO');

        return $this->_db->lastInsertId();
    }

    function updateItemFotos($data, $uid)
    {
        $this->_db->update('ITEM_PHOTO', $data, 'ITEM_ITEM_ID=' . $uid);

        return $this->_db->lastInsertId();
    }

    public function hasAttrCatalogLink($catalogue_id, $attribut_id)
    {
        $sql = "SELECT count(*)
           FROM ATTR_CATALOG_LINK
           WHERE CATALOGUE_ID = {$catalogue_id}
             AND ATTRIBUT_ID = {$attribut_id}";

        return $this->_db->fetchOne($sql);
    }

    public function hasItemN($table, $data)
    {
        $sql = "SELECT ITEM_ID
           FROM {$table}
           WHERE ATTRIBUT_ID = {$data['ATTRIBUT_ID']}
             AND ITEM_ID = {$data['ITEM_ID']}";

//     echo __CLASS__."==".__METHOD__."==".$sql."\r\n";

        return $this->_db->fetchOne($sql);
    }

    public function insertItemN($table, $data)
    {
        $this->_db->insert($table, $data);
    }

    public function insertAttrCatalogLink($data)
    {
        $this->_db->insert('ATTR_CATALOG_LINK', $data);
    }

    public function insertItemItem($data)
    {
        $this->_db->insert('ITEM_ITEM', $data);
        $this->updateSequence('ITEM_ITEM');
    }

    public function getMaxId()
    {
        $sql = "SELECT max(ITEM_ID) FROM ITEM";

        return $this->_db->fetchOne($sql);
    }

    public function itemHasImage($id)
    {
        $sql = "SELECT count(1)
           FROM ITEM
           WHERE IMAGE1 LIKE '%#%'
             AND IMAGE2 LIKE '%#%'
             AND IMAGE3 LIKE '%#%'
             AND ITEM_ID = {$id}";

        $res = $this->_db->fetchOne($sql);

        return !empty($res) ? true : false;
    }

    public function itemHasItemImage($name, $id)
    {
        $sql = "SELECT ITEM_ITEM_ID
           FROM ITEM_PHOTO
           WHERE ITEM_ID = {$id}
             AND NAME = '{$name}'";

        $res = $this->_db->fetchOne($sql);

        return !empty($res) ? true : false;
    }

    public function getBaseItemImage()
    {
        $sql = "SELECT ITEM_ID
                     , BASE_IMAGE
                FROM ITEM
                WHERE NEED_RESIZE = 1";

//     echo __CLASS__."==".__METHOD__."==".$sql."\r\n";

        return $this->_db->fetchAll($sql);
    }

    public function getBaseItemPhotos($item_id)
    {
        $sql = "SELECT ITEM_ITEM_ID
                , NAME
           FROM ITEM_PHOTO
           WHERE ITEM_ID = {$item_id}";

        return $this->_db->fetchAll($sql);
    }

    public function getSiteMapItems()
    {
        $sql = "SELECT I.ITEM_ID
                  ,I.CATNAME
                  ,C.REALCATNAME AS CATALOGUE_REALCATNAME
            FROM ITEM I
            LEFT JOIN CATALOGUE C ON (C.CATALOGUE_ID = I.CATALOGUE_ID)
            WHERE I.STATUS=1";

        return $this->_db->fetchAll($sql);
    }

    public function truncateItemItem()
    {
        $sql = "truncate table ITEM_ITEM";

        $this->_db->query($sql);
    }

    public function hasItemReserved($data)
    {
        $sql = "SELECT count(*)
            FROM ITEM_REQUEST
            WHERE ITEM_ID = {$data['ITEM_ID']}
              AND EMAIL = '{$data['EMAIL']}'";

        $res = $this->_db->fetchOne($sql);

        return !empty($res) ? true : false;
    }

    public function getReservedItems()
    {
        $sql = "SELECT I.ITEM_ID
                  ,I.NAME
                  ,I.CATNAME
                  ,I.TYPENAME
                  ,B.NAME AS BRAND_NAME
                  ,C.REALCATNAME AS CATALOGUE_REALCATNAME

                  ,IR.EMAIL
            FROM ITEM I
            INNER JOIN ITEM_REQUEST IR ON (IR.ITEM_ID = I.ITEM_ID)
            LEFT JOIN BRAND B ON (B.BRAND_ID = I.BRAND_ID)
            LEFT JOIN CATALOGUE C ON (C.CATALOGUE_ID = I.CATALOGUE_ID)
            WHERE I.STATUS = 1
              AND I.PRICE > 0
              AND IR.STATUS = 0";

        return $this->_db->fetchAll($sql);
    }

    public function updateReservedData($items_id)
    {
        $sql = "UPDATE ITEM_REQUEST
            SET STATUS = 1
            WHERE ITEM_ID IN ({$items_id})";

        $this->_db->query($sql);
    }

    public function getPayments()
    {
        $sql = "SELECT *
          FROM PAYMENT
          WHERE STATUS = 1
          ORDER BY ORDERING";

        return $this->_db->fetchAll($sql);
    }

    public function getUserOrders()
    {
        $sql = "SELECT ZAKAZ_ID
                     , USER_ID
                FROM ZAKAZ
                WHERE STATUS = 3
                  AND USER_ID IS NOT null
                  ";

        return $this->_db->fetchAll($sql);
    }

    public function getUserOrderSumm($id)
    {
        $sql = "SELECT sum(COST)
                FROM ZAKAZ_ITEM
                WHERE ZAKAZ_ID = ?";

        return $this->_db->fetchOne($sql, $id);
    }

    public function getUserDiscountId($summ)
    {
        $sql = "SELECT SHOPUSER_DISCOUNTS_ID
          FROM SHOPUSER_DISCOUNTS
          WHERE MIN <= {$summ}
            AND MAX >= {$summ}
          ORDER BY ORDERING ASC";

        return $this->_db->fetchOne($sql);
    }

    public function getUserDiscountImages($id)
    {
        $sql = "SELECT IMAGE1
               , IMAGE2
          FROM SHOPUSER_DISCOUNTS
          WHERE SHOPUSER_DISCOUNTS_ID = ?";

        return $this->_db->fetchRow($sql, $id);
    }

    public function getUserDiscountRang($shopuser_discounts_id, $summ)
    {
        $sql = "SELECT DISCOUNT_SUMM
          FROM SHOPUSER_DISCOUNTS_RANGE_LIST
          WHERE MIN <= {$summ}
            AND MAX >= {$summ}
            AND SHOPUSER_DISCOUNTS_ID = {$shopuser_discounts_id}";

        return $this->_db->fetchOne($sql);
    }

    public function getSelectItemSimplePrice($params)
    {
        $where = '';

        if (!empty($params['brand_id']) && is_array($params['brand_id'])) {
            $_brand = implode(", ", $params['brand_id']);

            $where .= " and BRAND_ID IN ({$_brand}) ";
        } elseif (!empty($params['brand_id']) && !is_array($params['brand_id'])) {
            $where .= " and BRAND_ID = {$params['brand_id']} ";
        }

        if (!empty($params['catalogue_id'])) {
            $where .= " and CATALOGUE_ID = {$params['catalogue_id']} ";
        }

        if (!empty($params['items_id'])) {
            $_items = implode(", ", $params['items_id']);

            $where .= " and ITEM_ID IN ({$_items}) ";
        }

        $sql = "SELECT min(if(PRICE1 > 0, PRICE1, PRICE)) AS min_price
               , max(if(PRICE1 > 0, PRICE1, PRICE)) AS max_price
          FROM ITEM
          WHERE STATUS = 1
            AND PRICE > 0
            {$where}";

        return $this->_db->fetchRow($sql);
    }

    public function getSelectItemAuthPrice($catalogue_id)
    {

        $sql = "SELECT PRICE
                ,PRICE1
                ,CURRENCY_ID
                ,IS_ACTION
          FROM ITEM
          WHERE STATUS=1
            AND PRICE > 0
            AND CATALOGUE_ID = {$catalogue_id}
          ORDER BY PRICE";

        return $this->_db->fetchAll($sql);
    }

    public function getItemByImage($image)
    {
        $sql = "SELECT count(1)
          FROM ITEM
          WHERE IMAGE1 LIKE '{$image}#%'
             OR IMAGE2 LIKE '{$image}#%'
             OR IMAGE3 LIKE '{$image}#%'";

        $res = $this->_db->fetchOne($sql);

        return !empty($res) ? true : false;
    }

    public function getItemPhotoByImage($image)
    {
        $sql = "SELECT count(1)
          FROM ITEM_PHOTO
          WHERE IMAGE1 LIKE '{$image}#%'
             OR IMAGE2 LIKE '{$image}#%'";

        $res = $this->_db->fetchOne($sql);

        return !empty($res) ? true : false;
    }

    public function getItemInfoForUrl($id)
    {
        $sql = "SELECT I.ITEM_ID
                  ,I.NAME
                  ,I.CATNAME
                  ,C.REALCATNAME AS CATALOGUE_REALCATNAME
                  ,C.NAME AS CATALOGUE_NAME

            FROM ITEM I
            LEFT JOIN CATALOGUE C ON (C.CATALOGUE_ID = I.CATALOGUE_ID)
            WHERE I.ITEM_ID=?";


        return $this->_db->fetchRow($sql, $id);
    }

    public function getSearchItemCash($id)
    {
        $sql = "SELECT SEARCH_CASH
          FROM SEARCH_CASH_ITEM
          WHERE ITEM_ID = ?";

        return $this->_db->fetchOne($sql, $id);
    }

    public function getSearchItems(
        $item_id,
        $catalogue_id,
        $item_cash_arr,
        $temp_item_cash
    )
    {
        $math = '';
        if ($item_cash_arr) {
            foreach ($item_cash_arr as $val) {
                $math .= "match (SEARCH_CASH) against ('{$val}') and ";
            }
        }

        if ($temp_item_cash) {
            foreach ($temp_item_cash as $val) {
                $ch[] = "match (SEARCH_CASH) against ('{$val}')";
            }
            $math .= '(' . implode(' or ', $ch) . ') and ';
        }

        $sql = "SELECT ITEM_ID
          FROM SEARCH_CASH_ITEM
          WHERE {$math} 1
            AND CATALOGUE_ID = {$catalogue_id}
            AND ITEM_ID <> {$item_id}";

        return $this->_db->fetchCol($sql);
    }

}