<?php

class models_Catalogue extends ZendDBEntity
{

    protected $_name = 'CATALOGUE';

    public function getCatPath($id)
    {
        $sql = "SELECT CATALOGUE_ID
                  ,PARENT_ID
                  ,NAME
                  ,CATNAME
                  ,IF(CATNAME!='',REALCATNAME,'') AS REALCATNAME
                  ,URL
            FROM {$this->_name}
            WHERE CATALOGUE_ID=?";

        return $this->_db->fetchRow($sql, $id);
    }

    public function getTree($parentId = 0)
    {
        $sql = "SELECT CATALOGUE_ID
                    ,PARENT_ID
                    ,NAME
                    ,CATNAME
                    ,REALCATNAME
                    ,IS_INDEX
                    ,URL
                    ,IMAGE1
              FROM {$this->_name}
              WHERE PARENT_ID=?
                AND REALSTATUS=1
                AND COUNT_ > 0
              ORDER BY ORDERING";

        return $this->_db->fetchAll($sql, $parentId);
    }

    public function getAllCats()
    {
        $sql = "SELECT CATALOGUE_ID
                    ,PARENT_ID
                    ,NAME
              FROM {$this->_name}
              WHERE REALSTATUS=1
                AND COUNT_ > 0
              ORDER BY ORDERING";

        return $this->_db->fetchAll($sql);
    }

    public function itemCount($id, $brand_id = 0)
    {
        $childs = $this->getChildren($id);

        $where = '';
        if ($childs)
            $where .= " and CATALOGUE_ID IN (" . $id . "," . implode(',', $childs) . ")";
        else
            $where .= " and CATALOGUE_ID = " . $id;

        if ($brand_id)
            $where .= " and BRAND_ID = " . $brand_id;

        $sql = "select COUNT(*)
             from ITEM
             where 1 " . $where . "
               and STATUS=1
               and PRICE > 0";

        return $this->_db->fetchOne($sql);
    }

    public function getChildren($id)
    {
        $path = array();
        $sql = "SELECT CATALOGUE_ID
               FROM {$this->_name}
               WHERE PARENT_ID=?
                 AND STATUS=1
               ORDER BY ORDERING, CATALOGUE_ID";
        $childs = $this->_db->fetchAll($sql, $id);

        if (count($childs) > 0) {
            foreach ($childs as $child) {
                if ($child['CATALOGUE_ID'] > 0) {
                    $path[] = $child['CATALOGUE_ID'];
                    $path = array_merge($path, $this->getChildren($child['CATALOGUE_ID']));
                }
            }
        }

        return $path;
    }

    public function getAllParents($id, $path)
    {
        $sql = "SELECT PARENT_ID
              FROM {$this->_name}
              WHERE CATALOGUE_ID={$id}
                AND STATUS=1";

        $parent_id = $this->_db->fetchOne($sql);

        if (!empty($parent_id)) {
            $path[count($path)] = $parent_id;
            $path = $this->getAllParents($parent_id, $path);
        }

        return $path;
    }

    public function getChildCatCount($id)
    {
        $sql = "SELECT count(*)
             FROM {$this->_name} C
             WHERE PARENT_ID = {$id}
               AND REALSTATUS=1";

        return $this->_db->fetchOne($sql);
    }

    public function getChildItemCount($params)
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

        $sql = "SELECT count(*)
            FROM ITEM
            WHERE PRICE > 0 AND STATUS = 1 " .
            $where;

        return $this->_db->fetchOne($sql);
    }

    public function getCatalogueId($cat)
    {
        $sql = "select CATALOGUE_ID from {$this->_name} where REALCATNAME = '/" . $cat . "/'";

        return $this->_db->fetchOne($sql);
    }

    public function getCatName($id)
    {
        return $this->_db->fetchOne("SELECT NAME FROM {$this->_name} WHERE CATALOGUE_ID=?", array($id));
    }

    public function getParentId($id)
    {
        $sql = "SELECT PARENT_ID FROM {$this->_name} WHERE CATALOGUE_ID=?";

        return $this->_db->fetchOne($sql, $id);
    }

    public function getCatRealCat($id)
    {
        $sql = "SELECT REALCATNAME FROM {$this->_name} WHERE CATALOGUE_ID=?";

        return $this->_db->fetchOne($sql, $id);
    }

    public function getParents($id)
    {
        $sql = "SELECT PARENT_ID
                    , CATALOGUE_ID
                    , NAME
                    , REALCATNAME
               FROM {$this->_name}
               WHERE CATALOGUE_ID={$id}
                 AND STATUS=1";

        return $this->_db->fetchRow($sql);
    }

    public function getCatInfo($id)
    {
        $sql = "SELECT PARENT_ID
                     ,CATALOGUE_ID
                     ,NAME
                     ,CATNAME
                     ,REALCATNAME
                     ,URL
                     ,IMAGE1
                     ,TITLE
                     ,SUB_ITEM_TITLE
                     ,SUB_TITLE
                     ,DESC_META
                     ,KEYWORD_META
               FROM {$this->_name}
               WHERE CATALOGUE_ID=?";

        $result = $this->_db->fetchRow($sql, $id);

        if ($result) {
            $xml = $this->_db->fetchOne("SELECT XML FROM XMLS WHERE TYPE=2 AND XMLS_ID=?", $id);
            if ($xml)
                $result['LONG_TEXT'] = $xml;
            else
                $result['LONG_TEXT'] = '';
        }

        return $result;
    }

    public function getCurrencies()
    {
        $sql = "SELECT CURRENCY_ID,
                       NAME,
                       SYSTEM_NAME
                FROM CURRENCY
                ORDER BY CURRENCY_ID";

        return $this->_db->fetchAll($sql);
    }

    public function getBrands($catid = 0)
    {

//        $sql = "select B.BRAND_ID
//                     ,B.NAME
//                     ,B.ALT_NAME
//               from BRAND B
//               inner join CATALOGUE_BRAND_VIEW CBV on (CBV.BRAND_ID = B.BRAND_ID)
//               where CBV.CATALOGUE_ID = ?
//               order by B.NAME";
        // Запрос с учетом включенных товаров для данного бренда и каталога
        $sql = "SELECT B.BRAND_ID
                    , B.NAME
                    , B.ALT_NAME
                FROM
                BRAND B
                JOIN CATALOGUE_BRAND_VIEW CBV
                USING (BRAND_ID)
                WHERE
                1
                AND CBV.CATALOGUE_ID = ?
                AND (SELECT count(*)
                    FROM
                        ITEM
                    WHERE
                        1
                        AND STATUS = 1
                        AND BRAND_ID = B.BRAND_ID
                        AND CATALOGUE_ID = ?) > 0
                ORDER BY
                B.NAME";


        return $this->_db->fetchAll($sql, array($catid, $catid));
    }

    public function getCount($f, $id)
    {

    }

    public function getComparedItems()
    {
        $comp_cats = array();
        if (!empty($_SESSION['citems'])) {
            $i = 0;
            foreach ($_SESSION['citems'] as $key => $Coms) {
                if ($key > 0 && !empty($Coms)) {
                    $sql = "SELECT NAME
                    FROM {$this->_name}
                    WHERE CATALOGUE_ID = {$key}
                      AND STATUS=1";

                    $cat_name = $this->_db->fetchOne($sql);

                    if (!empty($cat_name)) {
                        $comp_cats[$i]['CATALOGUE_NAME'] = $cat_name;
                        $comp_cats[$i]['CATALOGUE_ID'] = $key;
                        $parents = $this->getParents($key);
                        if ($parents)
                            $parent_id = $parents['CATALOGUE_ID'];
                        else
                            $parent_id = $key;

                        $sql = "select I.ITEM_ID
                            ,I.NAME
                            ,B.NAME as BRAND_NAME
                      from ITEM I
                      left join BRAND B on (B.BRAND_ID = I.BRAND_ID)
                      where I.CATALOGUE_ID=?
                        and I.ITEM_ID in (" . implode(',', $Coms) . ")
                        and I.STATUS=1";

                        $comp_cats[$i]['items'] = $this->_db->fetchAll($sql, $key);

                        $i++;
                    }
                }
            }
        }

        return $comp_cats;
    }

    public function CheckEmptyItems($table, $V_ATTRIBUT_ID)
    {
        if ($V_ATTRIBUT_ID != 11111 && $V_ATTRIBUT_ID != 22222) {
            $q = "SELECT TYPE,IS_RANGEABLE FROM ATTRIBUT WHERE STATUS=1 AND ATTRIBUT_ID=?";
            $row = $this->_db->fetchRow($q, $V_ATTRIBUT_ID);
            $TYPE = $row['TYPE'];
            $IS_RANGEABLE = $row['IS_RANGEABLE'];

            $query = ''; //echo  $V_ATTRIBUT_ID."=>".$TYPE."=>". $IS_RANGEABLE ."<br>";
            if ($TYPE < 2 && $IS_RANGEABLE) {
                $query = 'select S.ITEM_ID,G.RANGE_LIST_ID from ' . $table . ' S inner join ITEMR G on S.ITEM_ID=G.ITEM_ID where G.ATTRIBUT_ID=' . $V_ATTRIBUT_ID; #group by G.RANGE_LIST_ID
            } elseif ($TYPE < 2 && !$IS_RANGEABLE) {
                $query = 'select S.ITEM_ID,G.VALUE from ' . $table . ' S inner join ITEM1 G on S.ITEM_ID=G.ITEM_ID where G.ATTRIBUT_ID=' . $V_ATTRIBUT_ID; #group by G.RANGE_LIST_ID
            } elseif ($TYPE == 0 || $TYPE == 6) {
                $query = 'select S.ITEM_ID,G.VALUE from ' . $table . ' S inner join ITEM0 G on S.ITEM_ID=G.ITEM_ID where G.ATTRIBUT_ID=' . $V_ATTRIBUT_ID . '  and G.VALUE > 0'; #group by G.VALUE
            } elseif ($TYPE == 2) {
                $query = 'select S.ITEM_ID,G.VALUE from ' . $table . ' S inner join ITEM2 G on S.ITEM_ID=G.ITEM_ID where G.ATTRIBUT_ID=' . $V_ATTRIBUT_ID . '  and G.VALUE <> ""'; #group by  G.VALUE
            } elseif ($TYPE == 3) {
                $query = 'select S.ITEM_ID,G.VALUE from ' . $table . ' S inner join ITEM0 G on S.ITEM_ID=G.ITEM_ID inner join ATTRIBUT_LIST AL on AL.ATTRIBUT_LIST_ID=G.VALUE where G.ATTRIBUT_ID=' . $V_ATTRIBUT_ID . '  and G.VALUE <> ""'; #group by G.VALUE
            }
            $count = 0; //echo $query."<br>";
            if ($query != '') {
                $itms = $this->_db->fetchAll($query);

                for ($i = 0; $i < sizeof($itms); $i++) {
                    if ($TYPE == 6 && $itms[$i]['VALUE'] == 1 || ($TYPE != 6 && $itms[$i]['VALUE'] != ''))
                        $count++;
                }

                if ($count > 0)
                    return 0;
                else
                    return 1;
            } else
                return 1;
        } else {

        }
    }

    public function getCurrencyRate()
    {
        $sql = "SELECT PRICE
             FROM CURRENCY
             WHERE SYSTEM_NAME = 'USD'";

        return $this->_db->fetchOne($sql);
    }

    public function getCurrencyId($name)
    {
        $sql = "SELECT CURRENCY_ID
             FROM CURRENCY
             WHERE SYSTEM_NAME = '{$name}'";

        return $this->_db->fetchOne($sql);
    }

    public function getPriceIndentId($indent)
    {
        $sql = "SELECT PRICE_EXPORT_ID
             FROM PRICE_EXPORT
             WHERE INDENT = '{$indent}'";

        return $this->_db->fetchOne($sql);
    }

    public function getExportCatalog($id)
    {
        $sql = "SELECT C.CATALOGUE_ID
                   ,C.PARENT_ID
                   ,C.NAME
              FROM {$this->_name} C
                 , CATALOGUE_PRICE_EXPORT CPE
                 , ITEM I
              WHERE CPE.PRICE_EXPORT_ID = {$id}
                AND CPE.CATALOGUE_ID = C.CATALOGUE_ID
                AND C.REALSTATUS=1
                AND C.COUNT_ > 0
                AND I.CATALOGUE_ID=C.CATALOGUE_ID
                AND I.STATUS=1
              GROUP BY C.CATALOGUE_ID
              ORDER BY C.PARENT_ID, C.ORDERING";

        return $this->_db->fetchAll($sql);
    }

    public function getExportItems($id)
    {
        $sql = "SELECT I.ITEM_ID
                  , CR.SYSTEM_NAME
                  , CR.PRICE
                  , I.BRAND_ID
                  , B.NAME AS BRAND_NAME
                  , IF (I.TYPENAME IS null OR I.TYPENAME='', C.TYPENAME, I.TYPENAME) AS TYPENAME
                  , if(I.PRICE1>0,I.PRICE1,I.PRICE) AS ITEM_PRICE
                  , I.PRICE1 AS ITEM_PRICE1
                  , I.IMAGE1
                  , I.IMAGE2
                  , I.CURRENCY_ID
                  , I.NAME
                  , I.CATNAME
                  , I.DESCRIPTION
                  , I.CATALOGUE_ID
                  , C.REALCATNAME AS CATALOGUE_REALCATNAME
                  
                  FROM CURRENCY CR
                     , ITEM I LEFT JOIN BRAND B ON (I.BRAND_ID=B.BRAND_ID)
                       INNER JOIN CATALOGUE C ON (C.CATALOGUE_ID=I.CATALOGUE_ID)
                  WHERE I.STATUS=1
                    AND I.PRICE>0
                    AND I.STATUS=1
                    AND I.CURRENCY_ID=CR.CURRENCY_ID
                    AND I.CATALOGUE_ID={$id}";

        return $this->_db->fetchAll($sql);
    }

    /**
     * Get Catalogs and brands List
     *
     * @param      $catalogParentId ID каталога для которго выводим спсиок товаров с брендами
     *
     * @param null $catalogId
     *
     * @return array
     */
    public function getCatalogsIncludeBrandsList($catalogId = null)
    {

        $sql = "SELECT
                  C.NAME,
                  C.IMAGE1,
                  C.REALCATNAME,
                  C.CATALOGUE_ID,
                  C.PARENT_ID,
                  GROUP_CONCAT(DISTINCT CONCAT(B.NAME, '#', B.ALT_NAME) ORDER BY B.NAME) AS BRANDS
                FROM CATALOGUE C
                  JOIN CATALOGUE_BRAND_VIEW CBV1 USING (CATALOGUE_ID)
                  JOIN BRAND B USING (BRAND_ID)
                  JOIN ITEM I
                    ON (C.CATALOGUE_ID = I.CATALOGUE_ID AND B.BRAND_ID = I.BRAND_ID)
                WHERE C.CATALOGUE_ID = ?
                AND I.STATUS = 1
                GROUP BY C.CATALOGUE_ID
                ORDER BY C.ORDERING";

        return $this->_db->fetchAll($sql, array($catalogId));

    }

    public function getCatalogsIncludeBrandsListByParent($parentId = null)
    {

        $sql = "SELECT
                  C.NAME,
                  C.IMAGE1,
                  C.REALCATNAME,
                  C.CATALOGUE_ID,
                  C.PARENT_ID,
                  GROUP_CONCAT(DISTINCT CONCAT(B.NAME, '#', B.ALT_NAME) ORDER BY B.NAME) AS BRANDS
                FROM CATALOGUE C
                  JOIN CATALOGUE_BRAND_VIEW CBV1 USING (CATALOGUE_ID)
                  JOIN BRAND B USING (BRAND_ID)
                  JOIN ITEM I
                    ON (C.CATALOGUE_ID = I.CATALOGUE_ID AND B.BRAND_ID = I.BRAND_ID)
                WHERE C.PARENT_ID = ?
                AND I.STATUS = 1
                GROUP BY C.CATALOGUE_ID
                ORDER BY C.ORDERING";

        return $this->_db->fetchAll($sql, array($parentId));

    }



    public function getTopCatalogsId($parentId){
        $sql = "SELECT c.CATALOGUE_ID, c.NAME FROM CATALOGUE c
                  WHERE c.STATUS = 1
                  AND c.PARENT_ID = ?
                  AND c.COUNT_ > 0
                  ORDER BY c.ORDERING";

        return $this->_db->fetchAll($sql, array($parentId));
    }

    public function getIndexTree($catalogueID = 0, $lang = 0)
    {
        // если $catalogueID = 0 - выводим все наименования каталогов, у которых стоит атрибут IN_INDEX = 1
        // если  указан $catalogueID - выводим его каталоги-потомки
        $where = $catalogueID ? "and C.PARENT_ID = $catalogueID" : "";
        $sql = "SELECT C.CATALOGUE_ID,
                    C.PARENT_ID,
                    C.NAME,
                    C.CATNAME,
                    C.REALCATNAME,
                    C.IS_INDEX,
                    C.URL,
                    C.IMAGE1
             FROM {$this->_name} C
             INNER JOIN ITEM I ON (I.CATALOGUE_ID = C.CATALOGUE_ID) AND I.STATUS = 1
             WHERE 1
               $where
               AND C.REALSTATUS = 1
               AND C.COUNT_ > 0
               AND I.STATUS = 1
               AND I.PRICE > 0
             GROUP BY C.CATALOGUE_ID
             ORDER BY C.ORDERING";

        return $this->_db->fetchAll($sql);
    }

    public function getCatalogueByCode($cid)
    {
        $sql = "SELECT CATALOGUE_ID FROM {$this->_name} WHERE ID_FROM_VBD=?";

        return $this->_db->fetchOne($sql, $cid);
    }

    public function getCatalogueByName($name)
    {
        $sql = "SELECT CATALOGUE_ID FROM {$this->_name} WHERE NAME='{$name}'";

        return $this->_db->fetchOne($sql);
    }

    public function insertCatalogue($data)
    {
        $this->_db->insert('CATALOGUE', $data);

        return $this->_db->lastInsertId();
    }

    public function updateCatalogue($data, $uid)
    {
        $this->_db->update('CATALOGUE', $data, 'CATALOGUE_ID=' . $uid);
    }

    public function getMaxId()
    {
        $sql = "SELECT max(CATALOGUE_ID) FROM {$this->_name}";

        return $this->_db->fetchOne($sql);
    }

    public function sequencesUpdate($name, $uid)
    {
        $sql = "UPDATE SEQUENCES
           SET ID = {$uid}
           WHERE NAME = '{$name}'";

        $this->_db->query($sql);
    }

    public function getCatByParent($uid)
    {
        $sql = "SELECT CATALOGUE_ID
           FROM {$this->_name}
           WHERE PARENT_ID={$uid}";

        return $this->_db->fetchCol($sql);
    }

    public function getItemsCountByCat($uid)
    {
        $sql = "SELECT count(*)
           FROM ITEM
           WHERE CATALOGUE_ID={$uid}";

        return $this->_db->fetchOne($sql);
    }

    public function updateCatCount($count, $uid)
    {

        $sql = "UPDATE CATALOGUE
           SET COUNT_= {$count}
           WHERE CATALOGUE_ID={$uid}";

        $this->_db->query($sql);
    }

    public function getCatalogueBanner($id)
    {
        $banners = array();
        $path[] = $id;
        $path = $this->getAllParents($id, $path);

        if (!empty($path)) {
            foreach ($path as $pid) {
                $sql = "SELECT SA.*
                  FROM SECTION_ALIGN SA
                  JOIN CATALOGUE_SECTION_ALIGN CSA ON (CSA.SECTION_ALIGN_ID = SA.SECTION_ALIGN_ID)
                  WHERE CSA.CATALOGUE_ID = {$pid}
                  ORDER BY SA.IS_ADV DESC, SA.ORDERING";

                $_banners = $this->_db->fetchAll($sql);
                if (!empty($_banners))
                    $banners = array_merge($banners, $_banners);
            }
        }

        return $banners;
    }

    public function getSiteMapCatTree()
    {
        $sql = "SELECT CATALOGUE_ID
                   ,PARENT_ID
                   ,CATNAME
                   ,REALCATNAME
                   ,URL
             FROM {$this->_name}
             WHERE REALSTATUS=1
             ORDER BY ORDERING";

        return $this->_db->fetchAll($sql);
    }

    public function getOrdering($parent_id)
    {
        $sql = "SELECT max(ORDERING)
             FROM {$this->_name}
             WHERE PARENT_ID = ?";

        $ordering = $this->_db->fetchOne($sql, $parent_id);
        $ordering++;

        return $ordering;
    }

    public function getCatalogOrdering($id)
    {
        $sql = "SELECT ORDERING
             FROM {$this->_name}
             WHERE CATALOGUE_ID = ?";

        return $this->_db->fetchOne($sql, $id);
    }

    public function trancuteCatItem()
    {
        $sql = "DELETE FROM CAT_ITEM";

        $this->_db->query($sql);
    }

    public function rebuildCatItem($tid, $id)
    {
        $sql = "INSERT INTO CAT_ITEM (CATALOGUE_ID,ITEM_ID)
             SELECT {$tid},ITEM_ID
             FROM ITEM
             WHERE CATALOGUE_ID={$id}
               AND STATUS=1";

        $this->_db->query($sql);
    }

    public function getatalogActive($id)
    {
        $path = array();
//    $path = $this->getAllParents($id, $path);
        $path[count($path)] = $id;
        if (!empty($path)) {
            foreach ($path as $cid) {
                $result = $this->getCatInfo($cid);
                if (!$result)
                    return false;
            }
        }

        return true;
    }

}