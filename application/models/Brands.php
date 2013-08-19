<?php
class Models_Brands extends ZendDBEntity
{
    protected $_name = 'BRAND';

    function viewBrands($catalogue_id, $childs = '', $first, $last)
    {
        if (empty($catalogue_id)) {
            $sql = "SELECT B.*
             FROM BRAND B
             JOIN ITEM I ON (I.BRAND_ID = B.BRAND_ID)
             WHERE B.STATUS = 1
               AND I.STATUS = 1
               AND I.PRICE > 0
             GROUP BY B.BRAND_ID
             ORDER BY B.NAME
             LIMIT {$first}, {$last}";
        } else {
            if ($childs) $where = " and I.CATALOGUE_ID IN(" . $catalogue_id . "," . implode(',', $childs) . ")";
            else $where = " and I.CATALOGUE_ID = " . $catalogue_id;

            $sql = "SELECT B.*
             FROM BRAND B
             JOIN ITEM I ON (I.BRAND_ID = B.BRAND_ID)
             WHERE B.STATUS = 1
               AND I.STATUS = 1
               AND I.PRICE > 0
               {$where}             
             GROUP BY B.BRAND_ID
             ORDER BY B.NAME
             LIMIT {$first}, {$last}";
        }

        return $this->_db->fetchAll($sql);
    }

    function viewBrandsAjax($params)
    {
        if (empty($params['catalogue_id'])) {
            if ($params['section'] == 'index') $where = " and B.IN_INDEX = 1 ";
            else $where = " and B.IN_ALL_PAGES = 1 ";

            $sql = "SELECT B.*
             FROM BRAND B
             JOIN ITEM I ON (I.BRAND_ID = B.BRAND_ID)
             WHERE B.STATUS = 1
               {$where}
               AND I.STATUS = 1
               AND I.PRICE > 0
             GROUP BY B.BRAND_ID
             ORDER BY B.NAME
             LIMIT {$params['first']}, {$params['last']}";
        } else {
            if ($params['childs']) $where = " and I.CATALOGUE_ID IN(" . $params['catalogue_id'] . "," . implode(',', $params['childs']) . ")";
            else $where = " and I.CATALOGUE_ID = " . $params['catalogue_id'];

            $sql = "SELECT B.*
             FROM BRAND B
             JOIN ITEM I ON (I.BRAND_ID = B.BRAND_ID)
             WHERE B.STATUS = 1
               AND B.IN_ALL_PAGES = 1
               AND I.STATUS = 1
               AND I.PRICE > 0
               {$where}             
             GROUP BY B.BRAND_ID
             ORDER BY B.NAME
             LIMIT {$params['first']}, {$params['last']}";
        }

        return $this->_db->fetchAll($sql);
    }

    function viewBrandsAjaxCount($params)
    {
        if (empty($params['catalogue_id'])) {
            if ($params['section'] == 'index') $where = " and B.IN_INDEX = 1 ";
            else $where = " and B.IN_ALL_PAGES = 1 ";

            $sql = "SELECT count(DISTINCT B.BRAND_ID)
             FROM BRAND B
             JOIN ITEM I ON (I.BRAND_ID = B.BRAND_ID)
             WHERE B.STATUS = 1
               {$where}
               AND I.STATUS = 1
               AND I.PRICE > 0 ";
        } else {
            if ($params['childs']) $where = " and I.CATALOGUE_ID IN(" . $params['catalogue_id'] . "," . implode(',', $params['childs']) . ")";
            else $where = " and I.CATALOGUE_ID = " . $params['catalogue_id'];

            $sql = "SELECT count(DISTINCT B.BRAND_ID)
             FROM BRAND B
             JOIN ITEM I ON (I.BRAND_ID = B.BRAND_ID)
             WHERE B.STATUS = 1
               AND B.IN_ALL_PAGES = 1
               AND I.STATUS = 1
               AND I.PRICE > 0
               {$where}";
        }

        return $this->_db->fetchOne($sql);

    }

    function getBrandInfo($brand_id)
    {
        $sql = "SELECT *
           FROM BRAND
           WHERE BRAND_ID = {$brand_id}";

        return $this->_db->fetchRow($sql);
    }

    function getItemBrandCount($brand_id, $catalogue_id, $childs = '')
    {

        if (empty($catalogue_id)) {
            $sql = "SELECT COUNT(*)
             FROM ITEM
             WHERE BRAND_ID  = {$brand_id}
               AND STATUS = 1
               AND PRICE > 0";
        } else {
            $sql = "SELECT COUNT(*)
             FROM BRAND B
             JOIN ITEM I ON (I.BRAND_ID = B.BRAND_ID)
             WHERE B.STATUS = 1
               AND I.CATALOGUE_ID  = {$catalogue_id}
               AND I.BRAND_ID  = {$brand_id}
               AND I.STATUS = 1
               AND I.PRICE > 0";
        }

        return $this->_db->fetchOne($sql);
    }

    function getBrandByCode($code)
    {
        $sql = "SELECT BRAND_ID
           FROM BRAND
           WHERE ID_FROM_VBD = {$code}";

        return $this->_db->fetchOne($sql);
    }

    function getBrandByAltName($vendor)
    {
        $sql = "SELECT BRAND_ID
           FROM BRAND
           WHERE ALT_NAME = '{$vendor}'";

        return $this->_db->fetchOne($sql);
    }

    function getBrandByName($vendor)
    {
        $sql = "SELECT BRAND_ID
           FROM BRAND
           WHERE NAME = '{$vendor}'";

        return $this->_db->fetchOne($sql);
    }

    function insertBrand($data)
    {
        $this->_db->insert('BRAND', $data);

        return $this->_db->lastInsertId();
    }

    public function updateBrand($data, $uid)
    {
        $this->_db->update('BRAND', $data, 'BRAND_ID=' . $uid);
    }

    public function getMaxId()
    {
        $sql = "SELECT max(BRAND_ID) FROM BRAND";

        return $this->_db->fetchOne($sql);
    }

}

?>
