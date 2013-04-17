<?php

class models_Item extends ZendDBEntity
{

    protected $_name = 'ITEM';
    public $sql_log = array();

    public function getCurrencyInfo($currency)
    {
        $q = "select PRICE,
                 SNAME
          from CURRENCY
          where CURRENCY_ID=?";

        return $this->_db->fetchRow($q, $currency);
    }

    public function getCurrencyName($currency)
    {
        $q = "select SNAME
          from CURRENCY
          where CURRENCY_ID=?";

        return $this->_db->fetchOne($q, $currency);
    }

    public function getCatalogItemCount($params)
    {
        $where = '';

        if (!empty($params['catalogue_id'])) {
            $where = " and CATALOGUE_ID = {$params['catalogue_id']} ";
        }

        $sql = "select count(*)
            from {$this->_name}
            where PRICE > 0
              and STATUS = 1
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

        $sql = "select ITEM_ID
                  ,PRICE
                  ,PRICE1
            from {$this->_name}
            where PRICE > 0
              and STATUS = 1
            {$where}
            order by PRICE
            {$limit}";

        return $this->_db->fetchRow($sql);
    }

    public function getSimilarTempTable($id, $not_in)
    {
        $sql = "select distinct CI.ITEM_ID
              from CAT_ITEM CI
              inner join ITEM I on (CI.ITEM_ID=I.ITEM_ID) where 1 ";

        if (!empty($id))
            $sql .=" and CI.CATALOGUE_ID IN ({$id})";
        if (!empty($not_in))
            $sql .=" and CI.ITEM_ID <> {$not_in}";
        $sql .= " and I.STATUS=1 and I.PRICE > 0 order by I.NAME";

        return $this->_db->fetchCol($sql);
    }

    public function initTempShortAttrTable($PARAM, $childs)
    {
        $result = array();
        $childs_ = implode("','", $childs);
        foreach ($PARAM as $attr => $val) {
            $sql = "select TYPE,IS_RANGEABLE from ATTRIBUT where STATUS=1 and ATTRIBUT_ID={$attr}";
            $row = $this->_db->fetchRow($sql);

            $TYPE = $row['TYPE'];
            $IS_RANGEABLE = $row['IS_RANGEABLE'];

            $value = $PARAM[$attr];

            if ($TYPE < 2 && $IS_RANGEABLE) {
                $query = "select ATTRIBUT_ID
                from ITEMR
                where ATTRIBUT_ID = ?
                  and RANGE_LIST_ID = ?
                  and ITEM_ID in ('{$childs_}')";
            } elseif ($TYPE == 0) {
                $query = "select ATTRIBUT_ID
                from ITEM0
                where ATTRIBUT_ID = ?
                  and VALUE = ?
                  and ITEM_ID in ('{$childs_}')";
            } elseif ($TYPE == 2) {
                $query = "select ATTRIBUT_ID
                from ITEM2
                where ATTRIBUT_ID = ?
                  and VALUE = ?
                  and ITEM_ID in ('{$childs_}')";
            } elseif ($TYPE == 3 || $TYPE == 4) {
                $query = "select IO.ATTRIBUT_ID
                from ITEM0 IO
                join ATTRIBUT_LIST AL on (AL.ATTRIBUT_LIST_ID = IO.VALUE)
                where IO.ATTRIBUT_ID = ?
                  and AL.NAME = ?
                  and IO.ITEM_ID in ('{$childs_}')";
            } elseif ($TYPE > 4) {
                if ($value == 0) {
                    $query = "select ATTRIBUT_ID
                  from ITEM0
                  where ATTRIBUT_ID = ?
                    and (VALUE=? or VALUE=2)
                    and ITEM_ID in ('{$childs_}')";
                } else {
                    $query = "select ATTRIBUT_ID
                  from ITEM0
                  where ATTRIBUT_ID = ?
                    and (VALUE=? or VALUE=1)
                    and ITEM_ID in ('{$childs_}')";
                }
            }

            $result[$attr] = $this->_db->fetchOne($query, array($attr, $value));
        }
        return $result;
    }

    public function getItemItemAjax($id)
    {
        $sql = "select I.ITEM_ID
                ,I.CURRENCY_ID
                ,I.PRICE
                ,I.PRICE1
                ,I.STATUS
          from ITEM_ITEM II
          join ITEM I on (I.ITEM_ID = II.ITEM_ITEM_ID)
          where II.ITEM_ID={$id}
            and II.STATUS = 1";

        return $this->_db->fetchAll($sql);
    }

    public function getItemItem($id)
    {
        $sql = "select ITEM_ITEM_ID
          from ITEM_ITEM
          where ITEM_ID={$id}
            and STATUS = 1";

        return $this->_db->fetchCol($sql);
    }

    /**
     * Метод для получения информации о товаре
     * @access   public
     * @param    integer $id
     * @return   array
     */
    public function getItemInfo($id)
    {
        $sql = "select I.ITEM_ID
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

                  ,D.IMAGE as DISCOUNTS_IMAGE
                  ,B.NAME as BRAND_NAME
                  ,B.URL as BRAND_URL

                  ,W.DESCRIPTION as WARRANTY_DESCRIPTION
                  ,DL.DESCRIPTION as DELIVERY_DESCRIPTION
                  ,CR.DESCRIPTION as CREDIT_DESCRIPTION

                  ,C.REALCATNAME as CATALOGUE_REALCATNAME
                  ,C.NAME as CATALOGUE_NAME
                  ,CRN.SNAME
            from ITEM I
            left join DISCOUNTS D on (D.DISCOUNT_ID = I.DISCOUNT_ID)
            left join BRAND B on (B.BRAND_ID = I.BRAND_ID)
            left join WARRANTY W on (W.WARRANTY_ID=I.WARRANTY_ID)
            left join DELIVERY DL on (DL.DELIVERY_ID=I.DELIVERY_ID)
            left join CREDIT CR on (CR.CREDIT_ID=I.CREDIT_ID)
            left join CATALOGUE C on (C.CATALOGUE_ID = I.CATALOGUE_ID)
            left join CURRENCY CRN on (CRN.CURRENCY_ID = I.CURRENCY_ID)
            where I.ITEM_ID=?";



        return $this->_db->fetchRow($sql, $id);
    }

    public function getItemChpuInfo($id)
    {
        $sql = "select I.ITEM_ID
                  ,I.CATNAME
                  ,C.REALCATNAME as CATALOGUE_REALCATNAME
            from ITEM I
            left join CATALOGUE C on (C.CATALOGUE_ID = I.CATALOGUE_ID)
            where I.ITEM_ID=?";

        return $this->_db->fetchRow($sql, $id);
    }

    public function getItemLognText($id)
    {
        $sql = "select XML
          from XMLS
          where TYPE=3
            and XMLS_ID=?";

        return $this->_db->fetchOne($sql, $id);
    }

    public function getItemInfoAjax($id)
    {
        $sql = "select ITEM_ID
                  ,CURRENCY_ID
                  ,PRICE
                  ,PRICE1
            from ITEM
            where ITEM_ID=?";

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

            $where.=" and I.BRAND_ID IN ({$_brand}) ";
        } elseif (!empty($params['brand_id']) && !is_array($params['brand_id'])) {
            $where.=" and I.BRAND_ID = {$params['brand_id']} ";
        }

        if (!empty($params['catalogue_id'])) {
            $where.=" and I.CATALOGUE_ID = {$params['catalogue_id']} ";
        }

        if (!empty($params['items_id'])) {
            $_items = implode(", ", $params['items_id']);

            $where.=" and I.ITEM_ID IN ({$_items}) ";
        }

        $sql = "select I.ITEM_ID
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
                ,D.IMAGE as DISCOUNTS_IMAGE
                ,B.NAME as BRAND_NAME
                ,C.REALCATNAME as CATALOGUE_REALCATNAME
                ,CR.SNAME
          from ITEM I
          left join CATALOGUE C on (C.CATALOGUE_ID = I.CATALOGUE_ID)
          left join CURRENCY CR on (CR.CURRENCY_ID = I.CURRENCY_ID)
          left join DISCOUNTS D on (D.DISCOUNT_ID = I.DISCOUNT_ID)
          left join BRAND B on (B.BRAND_ID = I.BRAND_ID)
          where I.STATUS=1
            and I.PRICE > 0
            {$where}
          order by {$order_by} {$asc}
          LIMIT {$params['start']},{$params['per_page']}";

        return $this->_db->fetchAll($sql);
    }

    public function getCatalItems($catalogue_id, $item_id, $startSelect = 0,
        $perPage = 0)
    {

        $_catalogue_id = implode(", ", $catalogue_id);
        $sql = "select I.ITEM_ID
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
                ,D.IMAGE as DISCOUNTS_IMAGE
                ,B.NAME as BRAND_NAME
          from ITEM I
          left join DISCOUNTS D on (D.DISCOUNT_ID = I.DISCOUNT_ID)
          left join BRAND B on (B.BRAND_ID = I.BRAND_ID)
          where I.STATUS=1
            and I.ITEM_ID <> {$item_id}
            and I.PRICE > 0
            and I.CATALOGUE_ID IN ({$_catalogue_id})
          order by I.NAME";

        if (!empty($perPage))
            $sql.=" limit {$startSelect},{$perPage}";

        return $this->_db->fetchAll($sql);
    }

    public function getMinMaxPrice($catalogue_id)
    {

        $_catalogue_id = implode(", ", $catalogue_id);

        $sql = "select ITEM_ID
          from ITEM
          where STATUS=1
            and PRICE > 0
            and CATALOGUE_ID IN ({$_catalogue_id})";

        return $this->_db->fetchCol($sql);
    }

    public function getCatalogItemsID($catalogue_id, $item_id = 0,
        $limit = false, $start = 0)
    {

        $_catalogue_id = implode(", ", $catalogue_id);
        $where = '';

        if (!empty($item_id)) {
            $where = 'and ITEM_ID <> ' . $item_id;
        }

        $sql = "select ITEM_ID
          from ITEM
          where STATUS=1
            and PRICE > 0
            and CATALOGUE_ID IN ({$_catalogue_id})
            {$where}";

        if (!empty($limit) && !empty($start)) {
            $sql .= " limit {$start}, {$limit}";
        } elseif ($limit)
            $sql .= " limit {$limit}";

        return $this->_db->fetchCol($sql);
    }

    public function getSimilarItemsCount($table, $it_id = 0)
    {
        $sql = "select count(TT.ITEM_ID)
          from {$table} TT
              ,ITEM I
          where I.ITEM_ID = TT.ITEM_ID
            and I.STATUS=1
            and I.ITEM_ID <> {$it_id}
            and I.PRICE > 0";

        if (!empty($it_id))
            $sql.=" and I.ITEM_ID != {$it_id}";

        return $this->_db->fetchOne($sql);
    }

    public function getSimilarItems($table, $it_id = 0, $limit = false,
        $start = 0)
    {
        $sql = "select TT.ITEM_ID
          from {$table} TT
              ,ITEM I
          where I.ITEM_ID = TT.ITEM_ID
            and I.STATUS=1
            and I.PRICE > 0";

        if (!empty($it_id))
            $sql.=" and I.ITEM_ID <> {$it_id}";

        $sql.=" order by I.PRICE";

        if ($limit)
            $sql .= " limit {$start}, {$limit}";

        return $this->_db->fetchCol($sql);
    }

    /**
     * Метод для получения ИД каталога для товара
     * @access   public
     * @param    integer $id
     * @return   integer $catalog
     */
    public function getItemCatalog($id)
    {
        $sql = "select CATALOGUE_ID from ITEM where ITEM_ID = ?";

        return $this->_db->fetchOne($sql, $id);
    }

    public function getItemName($id)
    {
        $sql = "select NAME from ITEM where ITEM_ID=?";
        return $this->_db->fetchOne($sql, $id);
    }

    public function getCartItemName($id)
    {
        $sql = "select concat(B.NAME,' ',I.NAME)
          from ITEM I
          left join BRAND B on (B.BRAND_ID = I.BRAND_ID)
          where I.ITEM_ID=?";

        return $this->_db->fetchOne($sql, $id);
    }

    public function getItemBreadcrumbName($id)
    {
        $sql = "select I.TYPENAME
                ,I.NAME
                ,B.NAME as BRAND_NAME
          from ITEM I
          left join BRAND B on (B.BRAND_ID = I.BRAND_ID)
          where I.ITEM_ID=?";

        return $this->_db->fetchRow($sql, $id);
    }

    public function getCountItemResponses($id)
    {
        $sql = "select count(*)
             from RESPONSES
             where ITEM_ID={$id}
               and HIDE=1
             order by DATA desc";

        return $this->_db->fetchOne($sql);
    }

    public function getItemResponses($id)
    {
        $sql = "select *
                   ,DATE_FORMAT(DATA,'%d/%m/%Y %H:%i') as date
             from RESPONSES
             where ITEM_ID=?
               and HIDE=1
             order by DATA desc";

        return $this->_db->fetchAll($sql, array($id));
    }

    public function insertResponseData($data)
    {
        $this->_db->insert('RESPONSES', $data);
    }

    public function recountPrice($price, $price1, $currency_id, $currency,
        $curr_price)
    {
        if ($currency_id != $currency) {
            //Валюта товара не соответствует выбранной,нужен пересчет
            $q = "select PRICE from CURRENCY where CURRENCY_ID=?";
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

    public function recountPriceSc($price, $item_currency_id,
        $current_currency_id, $curr_price)
    {
        if ($item_currency_id != $current_currency_id) {
            //Валюта товара не соответствует выбранной,нужен пересчет
            $q = "select PRICE from CURRENCY where CURRENCY_ID=?";
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

    public function getAttributes($catalogueId = 0,
        $tableName = 'ATTR_CATALOG_LINK', $limit = '', $show = 0)
    {
        if (!$catalogueId)
            return;

        $where = '';
        if ($limit)
            $restrict = ' limit 0,' . $limit;
        else
            $restrict = '';

        if ($show == 1)
            $where .=" and A.SHOW_='1'";

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
                $q = "select NAME from UNIT where UNIT_ID=?";
                $unit_name = $this->_db->fetchOne($q, $attributes[$i]['UNIT_ID']);
                $attributes[$i]['UNIT_NAME'] = $unit_name;
            }
            else
                $attributes[$i]['UNIT_NAME'] = '';
        }

        return $attributes;
    }

    public function getAttributesDescription($catalogueId = 0,
        $tableName = 'ATTR_CATALOG_LINK')
    {
        if (!$catalogueId)
            return;

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
                $q = "select NAME from UNIT where UNIT_ID=?";
                $unit_name = $this->_db->fetchOne($q, $attributes[$i]['UNIT_ID']);
                $attributes[$i]['UNIT_NAME'] = $unit_name;
            }
            else
                $attributes[$i]['UNIT_NAME'] = '';
        }

        return $attributes;
    }

    public function getItemAttributes($attributeList, $itemId, $catId = '',
        $limit = 0)
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
                    $value = $this->_db->fetchOne('select VALUE from ITEM0 where ITEM_ID=? and ATTRIBUT_ID=?',
                                                  array($itemId, $itemAttribute[$j]['ATTRIBUT_ID']));
                    if ($itemAttribute[$j]['IS_RANGEABLE'] == 1)
                        $val = $this->_db->fetchOne('select RANGE_LIST_ID from ITEMR where ITEM_ID=? and ATTRIBUT_ID=?',
                                                    array($itemId, $itemAttribute[$j]['ATTRIBUT_ID']));
                    else
                        $val = $value;
                    break;

                case 1: // 1-double
                    $value = $this->_db->fetchOne('select VALUE from ITEM1 where ITEM_ID=? and ATTRIBUT_ID=?',
                                                  array($itemId, $itemAttribute[$j]['ATTRIBUT_ID']));
                    if ($itemAttribute[$j]['IS_RANGEABLE'] == 1)
                        $val = $this->_db->fetchOne('select RANGE_LIST_ID from ITEMR where ITEM_ID=? and ATTRIBUT_ID=?',
                                                    array($itemId, $itemAttribute[$j]['ATTRIBUT_ID']));
                    else
                        $val = $value;
                    break;

                case 2: // 2-varchar
                    $value = $this->_db->fetchOne('select VALUE from ITEM2 where ITEM_ID=? and ATTRIBUT_ID=?',
                                                  array($itemId, $itemAttribute[$j]['ATTRIBUT_ID']));
                    break;

                case 3: // 3-список
                    if ($multiple == 1) {
                        $value = '';
                        $values = $this->_db->fetchAll('select A.NAME from ITEM0 I, ATTRIBUT_LIST A where I.VALUE=A.ATTRIBUT_LIST_ID and I.ITEM_ID=? and I.ATTRIBUT_ID=?',
                                                       array($itemId, $itemAttribute[$j]['ATTRIBUT_ID']));
                        if ($values) {
                            $arr = array();
                            foreach ($values as $v) {
                                $arr[] = $v['NAME'];
                            }
                            $value = implode("; ", $arr);
                        }
                    } else {
                        $value = $this->_db->fetchOne('select A.NAME from ITEM0 I, ATTRIBUT_LIST A where I.VALUE=A.ATTRIBUT_LIST_ID and I.ITEM_ID=? and I.ATTRIBUT_ID=?',
                                                      array($itemId, $itemAttribute[$j]['ATTRIBUT_ID']));
                    }
                    $val = $this->_db->fetchOne('select VALUE from ITEM0 I where ITEM_ID=? and ATTRIBUT_ID=?',
                                                array($itemId, $itemAttribute[$j]['ATTRIBUT_ID']));

                    //echo 'select A.NAME from ITEM0 I, ATTRIBUT_LIST A where I.ATTRIBUT_ID=A.ATTRIBUT_ID and I.ITEM_ID='.$itemId.' and I.ATTRIBUT_ID='.$itemAttribute[$j]['ATTRIBUT_ID'].'<br>';
                    break;
                case 4: // 4-список с картинкой и текстом
                    $data = $this->_db->fetchRow('select A.NAME,A.IMAGE from ITEM0 I, ATTRIBUT_LIST A where I.VALUE=A.ATTRIBUT_LIST_ID and I.ITEM_ID=? and I.ATTRIBUT_ID=?',
                                                 array($itemId, $itemAttribute[$j]['ATTRIBUT_ID']));
                    $value = $data['NAME'];
                    $image = $data['IMAGE'];
                    break;

                case 5: // 5-чекбокс
                case 6: // 6-чекбокс с тремя состояниями (да,нет,не знаю)
                    $value = $this->_db->fetchOne('select VALUE from ITEM0 where ITEM_ID=? and ATTRIBUT_ID=?',
                                                  array($itemId, $itemAttribute[$j]['ATTRIBUT_ID']));
                    if (empty($value))
                        $val = 'Нет';
                    else
                        $val = $this->_db->fetchOne('select ALTER_VALUE from ATTRIBUT where ATTRIBUT_ID=?',
                                                    array($itemAttribute[$j]['ATTRIBUT_ID']));
                    break;

                case 7: // краткое описание(64 к)
                    $value = $this->_db->fetchOne('select VALUE from ITEM7 where ITEM_ID=? and ATTRIBUT_ID=?',
                                                  array($itemId, $itemAttribute[$j]['ATTRIBUT_ID']));
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
                if ($cnt > $limit)
                    break;
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
                    if ($itemAttribute[$j]['IS_RANGEABLE'] == 1)
                        $value = $this->_db->fetchOne('select RANGE_LIST_ID from ITEMR where ITEM_ID=? and ATTRIBUT_ID=?',
                                                      array($itemId, $itemAttribute[$j]['ATTRIBUT_ID']));
                    else
                        $value = $this->_db->fetchOne('select VALUE from ITEM0 where ITEM_ID=? and ATTRIBUT_ID=?',
                                                      array($itemId, $itemAttribute[$j]['ATTRIBUT_ID']));
                    break;

                case 1: // 1-double
                    if ($itemAttribute[$j]['IS_RANGEABLE'] == 1)
                        $value = $this->_db->fetchOne('select RANGE_LIST_ID from ITEMR where ITEM_ID=? and ATTRIBUT_ID=?',
                                                      array($itemId, $itemAttribute[$j]['ATTRIBUT_ID']));
                    else
                        $value = $this->_db->fetchOne('select VALUE from ITEM1 where ITEM_ID=? and ATTRIBUT_ID=?',
                                                      array($itemId, $itemAttribute[$j]['ATTRIBUT_ID']));
                    break;

                case 2: // 2-varchar
                    $value = $this->_db->fetchOne('select VALUE from ITEM2 where ITEM_ID=? and ATTRIBUT_ID=?',
                                                  array($itemId, $itemAttribute[$j]['ATTRIBUT_ID']));
                    break;

                case 3: // 3-список
                    if ($multiple == 1) {
                        $value = '';
                        $values = $this->_db->fetchAll('select A.NAME from ITEM0 I, ATTRIBUT_LIST A where I.VALUE=A.ATTRIBUT_LIST_ID and I.ITEM_ID=? and I.ATTRIBUT_ID=?',
                                                       array($itemId, $itemAttribute[$j]['ATTRIBUT_ID']));
                        if ($values) {
                            $arr = array();
                            foreach ($values as $v) {
                                $arr[] = $v['NAME'];
                            }
                            $value = implode("; ", $arr);
                        }
                    } else {
                        $value = $this->_db->fetchOne('select A.NAME from ITEM0 I, ATTRIBUT_LIST A where I.VALUE=A.ATTRIBUT_LIST_ID and I.ITEM_ID=? and I.ATTRIBUT_ID=?',
                                                      array($itemId, $itemAttribute[$j]['ATTRIBUT_ID']));
                    }
                    $val = $this->_db->fetchOne('select VALUE from ITEM0 I where ITEM_ID=? and ATTRIBUT_ID=?',
                                                array($itemId, $itemAttribute[$j]['ATTRIBUT_ID']));

                    //echo 'select A.NAME from ITEM0 I, ATTRIBUT_LIST A where I.ATTRIBUT_ID=A.ATTRIBUT_ID and I.ITEM_ID='.$itemId.' and I.ATTRIBUT_ID='.$itemAttribute[$j]['ATTRIBUT_ID'].'<br>';
                    break;
                case 4: // 4-список с картинкой и текстом
                    $data = $this->_db->fetchRow('select A.NAME,A.IMAGE from ITEM0 I, ATTRIBUT_LIST A where I.VALUE=A.ATTRIBUT_LIST_ID and I.ITEM_ID=? and I.ATTRIBUT_ID=?',
                                                 array($itemId, $itemAttribute[$j]['ATTRIBUT_ID']));
                    $value = $data['NAME'];
                    break;

                case 5: // 5-чекбокс
                case 6: // 6-чекбокс с тремя состояниями (да,нет,не знаю)
                    $value = $this->_db->fetchOne('select VALUE from ITEM0 where ITEM_ID=? and ATTRIBUT_ID=?',
                                                  array($itemId, $itemAttribute[$j]['ATTRIBUT_ID']));
                    $val = $this->_db->fetchOne('select ALTER_VALUE from ATTRIBUT where ATTRIBUT_ID=?',
                                                array($itemAttribute[$j]['ATTRIBUT_ID']));
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
        $sql = "insert into ZAKAZ_ITEM
          set ZAKAZ_ID = {$data['ZAKAZ_ID']}
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
        $sql = "select I.CATALOGUE_ID
                  ,B.NAME as BRAND_NAME
                  ,I.ITEM_ID
                  ,I.NAME
                  ,I.TYPENAME
                  ,I.TITLE
                  ,I.DESC_META
                  ,I.KEYWORD_META
            from ITEM I
            left join BRAND B on (B.BRAND_ID = I.BRAND_ID)
            where I.ITEM_ID=?";

        return $this->_db->fetchRow($sql, $id);
    }

    public function getItemsForMeta($is_new, $gen_catalog_id = 0)
    {
        $where = " 1 ";
        if ($is_new) {
            $where.= " and TITLE = ''
               and DESC_META = ''
               and KEYWORD_META = ''";
        }
        if (!empty($gen_catalog_id)) {
            $where.= " and CATALOGUE_ID = {$gen_catalog_id}";
        }
        $sql = "select CATALOGUE_ID
                  ,TYPENAME
                  ,BRAND_ID
                  ,ITEM_ID
                  ,NAME
            from ITEM I
            where {$where}";

        return $this->_db->fetchAll($sql);
    }

    function getBrandName($id)
    {
        $sql = "select NAME
           from BRAND
           where BRAND_ID = ?";

        return $this->_db->fetchOne($sql, $id);
    }

    public function getCatSubItemTitle($id)
    {
        $sql = "select SUB_ITEM_TITLE
               , PARENT_ID
               , CATALOGUE_ID
          from CATALOGUE
          where CATALOGUE_ID = ?";

        return $this->_db->fetchRow($sql, $id);
    }

    public function getAllModels($cid)
    {
        $ids_str = implode(',', $cid);

        $sql = "select B.BRAND_ID
               , B.NAME
          from ITEM I
              ,BRAND B
          where I.CATALOGUE_ID IN ({$ids_str})
            and I.BRAND_ID = B.BRAND_ID
            and I.PRICE > 0
            and I.STATUS=1
            and B.STATUS=1
          group by B.BRAND_ID
          order by B.NAME";

        return $this->_db->fetchAll($sql);
    }

    public function getItemsSearch()
    {
        $sql = "select I.*
                  ,C.NAME as CNAME
                  ,C.REALCATNAME as CATALOGUE_REALCATNAME
                  ,B.NAME as BRAND_NAME
            from ITEM I
            join CATALOGUE C on (C.CATALOGUE_ID=I.CATALOGUE_ID)
            join BRAND B on (B.BRAND_ID=I.BRAND_ID)
            where I.STATUS = 1
              and I.PRICE  > 0";

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
              join ATTRIBUT A on (AL.ATTRIBUT_ID = A.ATTRIBUT_ID)";
        return $this->_db->fetchAll($sql, $itemID);
    }

    public function getItemPhotos($id)
    {
        $sql = "select *
            from ITEM_PHOTO
            where ITEM_ID = ?
              and STATUS=1";

        return $this->_db->fetchAll($sql, $id);
    }

    public function getItemMedia($id)
    {
        $sql = "select *
            from ITEM_MEDIA
            where ITEM_ID = ?
              and STATUS=1";

        return $this->_db->fetchAll($sql, $id);
    }

    public function getAllItemId()
    {
        $sql = "select ITEM_ID
                 , CATALOGUE_ID
            from ITEM";

        return $this->_db->fetchAll($sql);
    }

    public function updateItem($id, $description)
    {
        $sql = "update ITEM
            set DESCRIPTION = '{$description}'
            where ITEM_ID = {$id}";

        return $this->_db->query($sql);
    }

    function getItemByCode($code)
    {
        $sql = "select ITEM_ID
           from ITEM
           where ARTICLE = '{$code}'";

//     echo __CLASS__."==".__METHOD__."==".$sql."\r\n";

        return $this->_db->fetchOne($sql);
    }

    function getWarrantyByCode($code)
    {
        $sql = "select WARRANTY_ID
           from WARRANTY
           where ID_FROM_VBD = {$code}";

        return $this->_db->fetchOne($sql);
    }

    function getDeliveryByCode($code)
    {
        $sql = "select DELIVERY_ID
           from DELIVERY
           where ID_FROM_VBD = '{$code}'";

        return $this->_db->fetchOne($sql);
    }

    function getItemByName($name, $categoryId)
    {
        $sql = "select ITEM_ID
           from ITEM
           where NAME = '{$name}'
             and CATALOGUE_ID = {$categoryId}";

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
        $sql = "update SEQUENCES
           set ID = ID + 1
           where NAME = '{$name}'";

        $this->_db->query($sql);
    }

    function lastInsertId($name)
    {
        $sql = "select ID
           from SEQUENCES
           where NAME = '{$name}'";

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
        $sql = "select count(*)
           from ATTR_CATALOG_LINK
           where CATALOGUE_ID = {$catalogue_id}
             and ATTRIBUT_ID = {$attribut_id}";

        return $this->_db->fetchOne($sql);
    }

    public function hasItemN($table, $data)
    {
        $sql = "select ITEM_ID
           from {$table}
           where ATTRIBUT_ID = {$data['ATTRIBUT_ID']}
             and ITEM_ID = {$data['ITEM_ID']}";

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
        $sql = "select max(ITEM_ID) from ITEM";
        return $this->_db->fetchOne($sql);
    }

    public function itemHasImage($id)
    {
        $sql = "select count(1)
           from ITEM
           where IMAGE1 like '%#%'
             and IMAGE2 like '%#%'
             and IMAGE3 like '%#%'
             and ITEM_ID = {$id}";

        $res = $this->_db->fetchOne($sql);

        return !empty($res) ? true : false;
    }

    public function itemHasItemImage($name, $id)
    {
        $sql = "select ITEM_ITEM_ID
           from ITEM_PHOTO
           where ITEM_ID = {$id}
             and NAME = '{$name}'";

        $res = $this->_db->fetchOne($sql);

        return !empty($res) ? true : false;
    }

    public function getBaseItemImage()
    {
        $sql = "select ITEM_ID
                     , BASE_IMAGE
                from ITEM
                where NEED_RESIZE = 1";

//     echo __CLASS__."==".__METHOD__."==".$sql."\r\n";

        return $this->_db->fetchAll($sql);
    }

    public function getBaseItemPhotos($item_id)
    {
        $sql = "select ITEM_ITEM_ID
                , NAME
           from ITEM_PHOTO
           where ITEM_ID = {$item_id}";

        return $this->_db->fetchAll($sql);
    }

    public function getSiteMapItems()
    {
        $sql = "select I.ITEM_ID
                  ,I.CATNAME
                  ,C.REALCATNAME as CATALOGUE_REALCATNAME
            from ITEM I
            left join CATALOGUE C on (C.CATALOGUE_ID = I.CATALOGUE_ID)
            where I.STATUS=1";

        return $this->_db->fetchAll($sql);
    }

    public function truncateItemItem()
    {
        $sql = "truncate table ITEM_ITEM";

        $this->_db->query($sql);
    }

    public function hasItemReserved($data)
    {
        $sql = "select count(*)
            from ITEM_REQUEST
            where ITEM_ID = {$data['ITEM_ID']}
              and EMAIL = '{$data['EMAIL']}'";

        $res = $this->_db->fetchOne($sql);

        return !empty($res) ? true : false;
    }

    public function getReservedItems()
    {
        $sql = "select I.ITEM_ID
                  ,I.NAME
                  ,I.CATNAME
                  ,I.TYPENAME
                  ,B.NAME as BRAND_NAME
                  ,C.REALCATNAME as CATALOGUE_REALCATNAME

                  ,IR.EMAIL
            from ITEM I
            inner join ITEM_REQUEST IR on (IR.ITEM_ID = I.ITEM_ID)
            left join BRAND B on (B.BRAND_ID = I.BRAND_ID)
            left join CATALOGUE C on (C.CATALOGUE_ID = I.CATALOGUE_ID)
            where I.STATUS = 1
              and I.PRICE > 0
              and IR.STATUS = 0";

        return $this->_db->fetchAll($sql);
    }

    public function updateReservedData($items_id)
    {
        $sql = "update ITEM_REQUEST
            set STATUS = 1
            where ITEM_ID in ({$items_id})";

        $this->_db->query($sql);
    }

    public function getPayments()
    {
        $sql = "select *
          from PAYMENT
          where STATUS = 1
          order by ORDERING";

        return $this->_db->fetchAll($sql);
    }

    public function getUserOrders()
    {
        $sql = "select ZAKAZ_ID
                     , USER_ID
                from ZAKAZ
                where STATUS = 3
                  and USER_ID is not null
                  and USER_ID > 0";

        return $this->_db->fetchAll($sql);
    }

    public function getUserOrderSumm($id)
    {
        $sql = "select sum(COST)
                from ZAKAZ_ITEM
                where ZAKAZ_ID = ?";

        return $this->_db->fetchOne($sql, $id);
    }

    public function getUserDiscountId($summ)
    {
        $sql = "select SHOPUSER_DISCOUNTS_ID
          from SHOPUSER_DISCOUNTS
          where MIN <= {$summ}
            and MAX >= {$summ}
          order by ORDERING asc";

        return $this->_db->fetchOne($sql);
    }

    public function getUserDiscountImages($id)
    {
        $sql = "select IMAGE1
               , IMAGE2
          from SHOPUSER_DISCOUNTS
          where SHOPUSER_DISCOUNTS_ID = ?";

        return $this->_db->fetchRow($sql, $id);
    }

    public function getUserDiscountRang($shopuser_discounts_id, $summ)
    {
        $sql = "select DISCOUNT_SUMM
          from SHOPUSER_DISCOUNTS_RANGE_LIST
          where MIN <= {$summ}
            and MAX >= {$summ}
            and SHOPUSER_DISCOUNTS_ID = {$shopuser_discounts_id}";

        return $this->_db->fetchOne($sql);
    }

    public function getSelectItemSimplePrice($params)
    {
        $where = '';

        if (!empty($params['brand_id']) && is_array($params['brand_id'])) {
            $_brand = implode(", ", $params['brand_id']);

            $where.=" and BRAND_ID IN ({$_brand}) ";
        } elseif (!empty($params['brand_id']) && !is_array($params['brand_id'])) {
            $where.=" and BRAND_ID = {$params['brand_id']} ";
        }

        if (!empty($params['catalogue_id'])) {
            $where.=" and CATALOGUE_ID = {$params['catalogue_id']} ";
        }

        if (!empty($params['items_id'])) {
            $_items = implode(", ", $params['items_id']);

            $where.=" and ITEM_ID IN ({$_items}) ";
        }

        $sql = "select min(if(PRICE1 > 0, PRICE1, PRICE)) as min_price
               , max(if(PRICE1 > 0, PRICE1, PRICE)) as max_price
          from ITEM
          where STATUS = 1
            and PRICE > 0
            {$where}";

        return $this->_db->fetchRow($sql);
    }

    public function getSelectItemAuthPrice($catalogue_id)
    {

        $sql = "select PRICE
                ,PRICE1
                ,CURRENCY_ID
                ,IS_ACTION
          from ITEM
          where STATUS=1
            and PRICE > 0
            and CATALOGUE_ID = {$catalogue_id}
          order by PRICE";

        return $this->_db->fetchAll($sql);
    }

    public function getItemByImage($image)
    {
        $sql = "select count(1)
          from ITEM
          where IMAGE1 like '{$image}#%'
             or IMAGE2 like '{$image}#%'
             or IMAGE3 like '{$image}#%'";

        $res = $this->_db->fetchOne($sql);

        return !empty($res) ? true : false;
    }

    public function getItemPhotoByImage($image)
    {
        $sql = "select count(1)
          from ITEM_PHOTO
          where IMAGE1 like '{$image}#%'
             or IMAGE2 like '{$image}#%'";

        $res = $this->_db->fetchOne($sql);

        return !empty($res) ? true : false;
    }

    public function getItemInfoForUrl($id)
    {
        $sql = "select I.ITEM_ID
                  ,I.NAME
                  ,I.CATNAME
                  ,C.REALCATNAME as CATALOGUE_REALCATNAME
                  ,C.NAME as CATALOGUE_NAME

            from ITEM I
            left join CATALOGUE C on (C.CATALOGUE_ID = I.CATALOGUE_ID)
            where I.ITEM_ID=?";



        return $this->_db->fetchRow($sql, $id);
    }

    public function getSearchItemCash($id)
    {
        $sql = "select SEARCH_CASH
          from SEARCH_CASH_ITEM
          where ITEM_ID = ?";

        return $this->_db->fetchOne($sql, $id);
    }

    public function getSearchItems($item_id, $catalogue_id, $item_cash_arr,
        $temp_item_cash)
    {
        $math = '';
        if ($item_cash_arr) {
            foreach ($item_cash_arr as $val) {
                $math.= "match (SEARCH_CASH) against ('{$val}') and ";
            }
        }

        if ($temp_item_cash) {
            foreach ($temp_item_cash as $val) {
                $ch[] = "match (SEARCH_CASH) against ('{$val}')";
            }
            $math.='(' . implode(' or ', $ch) . ') and ';
        }

        $sql = "select ITEM_ID
          from SEARCH_CASH_ITEM
          where {$math} 1
            and CATALOGUE_ID = {$catalogue_id}
            and ITEM_ID <> {$item_id}";

        return $this->_db->fetchCol($sql);
    }

}

?>