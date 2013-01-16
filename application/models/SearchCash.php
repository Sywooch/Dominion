<?php

class models_SearchCash extends ZendDBEntity
{

    protected $_name = 'ITEM';
    protected $_item0 = 'ITEM0';
    protected $_item1 = 'ITEM1';
    protected $_item2 = 'ITEM2';
    protected $_item7 = 'ITEM7';
    protected $_itemr = 'ITEMR';
    protected $_attribut = 'ATTRIBUT';
    protected $_attr_catalog_vis = 'ATTR_CATALOG_VIS';
    protected $_catalogue = 'CATALOGUE';
    protected $_search_cash = 'SEARCH_CASH';
    protected $_search_cash_item = 'SEARCH_CASH_ITEM';

    public function getItem()
    {
        $sql = "select ITEM_ID
               , CATALOGUE_ID
               , BRAND_ID
          from {$this->_name}
          where IS_CASHED = 0
            and STATUS = 1
          limit 1";

        return $this->_db->fetchRow($sql);
    }

    public function getVisAttr($catalogue_id)
    {
        $sql = "select A.ATTRIBUT_ID
               , A.TYPE
          from {$this->_attribut} as A
          inner join {$this->_attr_catalog_vis} ACV on (ACV.ATTRIBUT_ID = A.ATTRIBUT_ID)
          where ACV.CATALOGUE_ID = ?
            AND A.STATUS = 1
          order by A.ATTRIBUT_ID";

        return $this->_db->fetchAll($sql, $catalogue_id);
    }

    public function resetItem()
    {
        $sql = "update {$this->_name}
          set IS_CASHED = 0";

        $this->_db->query($sql);
    }

    public function update_item($search_items)
    {
        $where = '';

        if (!empty($search_items)) {
            $_search_items = implode(', ', $search_items);
            $where = " where ITEM_ID in ({$_search_items})";
        }

        $sql = "update {$this->_name}
          set IS_CASHED = 1
          {$where}";

        $this->_db->query($sql);
    }

    public function resetSearchCash()
    {
        $sql = "truncate table {$this->_search_cash}";

        $this->_db->query($sql);
    }

    public function resetSearchCashItem()
    {
        $sql = "truncate table {$this->_search_cash_item}";

        $this->_db->query($sql);
    }

    public function getItemAttrValue($params)
    {
        $table = '';

        switch ($params['attr_type']) {
            case 0:
            case 3:
            case 4:
            case 5:
            case 6:
                $table = $this->_item0;
                break;

            case 1:
                $table = $this->_item1;
                break;

            case 2:
                $table = $this->_item2;
                break;

            case 7:
                $table = $this->_item7;
                break;
        }

        $sql = "select VALUE
          from {$table}
          where ITEM_ID = {$params['item_id']}
            and ATTRIBUT_ID = {$params['attr_id']}";

        return $this->_db->fetchOne($sql);
    }

    public function getSimilarAttrItems($params, $search_items = array())
    {
        $table = '';
        $where = '';
        $value = '';
        switch ($params['attr_type']) {
            case 0:
            case 3:
            case 4:
            case 5:
            case 6:
                $table = $this->_item0;
                $value = " and A.VALUE = {$params['value']}";
                break;

            case 1:
                $table = $this->_item1;
                $value = " and A.VALUE = {$params['value']}";
                break;

            case 2:
                $table = $this->_item2;
                $value = " and A.VALUE = '{$params['value']}'";
                break;

            case 7:
                $table = $this->_item7;
                $value = " and A.VALUE = '{$params['value']}'";
                break;
        }

        if (!empty($search_items)) {
            $_search_items = implode(', ', $search_items);
            $where = " and A.ITEM_ID in ({$_search_items})";
        }

        $sql = "select A.ITEM_ID
          from {$table} A
          inner join {$this->_name} I ON (I.ITEM_ID = A.ITEM_ID)
          inner join {$this->_catalogue} C ON (C.CATALOGUE_ID = I.CATALOGUE_ID)
          where A.ATTRIBUT_ID = {$params['attr_id']}
            and I.CATALOGUE_ID = {$params['catalogue_id']}
            and I.STATUS = 1
            {$value}
            {$where}";

        return $this->_db->fetchCol($sql);
    }

    public function insert_data($table, $data)
    {
        $this->_db->insert($table, $data);
    }

    public function getCashedItems($cash, $catalogue_id, $brands)
    {
        $math = '';
        $where = '';

        if (!empty($cash)) {
            foreach ($cash as $key => $ch) {
                foreach ($ch as $attr) {
                    $_cash[$key][] = "match (SEARCH_CASH) against ('a{$key}v{$attr}')";
                }
            }
        }

        if (!empty($_cash)) {
            foreach ($_cash as $ch) {
                if (count($ch) > 1) {
                    $math.= '(' . implode(' or ', $ch) . ') and ';
                } else {
                    $math.= implode('', $ch) . ' and ';
                }
            }
        }

        if (!empty($brands)) {
            $_brands = implode(', ', $brands);
            $where = " and BRAND_ID in ({$_brands})";
        }

        $sql = "select *
          from {$this->_search_cash_item}
          use index (CATALOGUE_ID, BRAND_ID)
          where CATALOGUE_ID = {$catalogue_id}
          {$where}
          and  {$math} 1";

        return $this->_db->fetchAll($sql);
    }

    public function getCashedItemsWithPrice($item, $price)
    {
        $having = '';
        if (!empty($price['pmin'])) {
            $having.= " and result >= {$price['pmin']} ";
        }

        if (!empty($price['pmax'])) {
            $having.= " and result <= {$price['pmax']} ";
        }

        $_item = implode(', ', $item);

        $sql = "select SCI.*
               , if( I.PRICE1 >0, I.PRICE1, I.PRICE ) AS result
               , I.PRICE
               , I.PRICE1
               , I.CURRENCY_ID
               , I.IS_ACTION
          from {$this->_search_cash_item} SCI
          inner join {$this->_name} I on (I.ITEM_ID = SCI.ITEM_ID)
          where SCI.ITEM_ID in ({$_item})
          having result > 0 {$having}";

        return $this->_db->fetchAll($sql);
    }

}