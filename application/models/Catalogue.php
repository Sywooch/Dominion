<?php

class models_Catalogue extends ZendDBEntity {

    protected $_name = 'CATALOGUE';

    public function getCatPath($id) {
        $sql = "select CATALOGUE_ID
                  ,PARENT_ID
                  ,NAME
                  ,CATNAME
                  ,IF(CATNAME!='',REALCATNAME,'') as REALCATNAME
                  ,URL
            from {$this->_name}
            where CATALOGUE_ID=?";

        return $this->_db->fetchRow($sql, $id);
    }

    public function getTree($parentId = 0) {
        $sql = "select CATALOGUE_ID
                    ,PARENT_ID
                    ,NAME
                    ,CATNAME
                    ,REALCATNAME
                    ,IS_INDEX
                    ,URL
                    ,IMAGE1
              from {$this->_name}
              where PARENT_ID=?
                and REALSTATUS=1
                and COUNT_ > 0
              order by ORDERING";

        return $this->_db->fetchAll($sql, $parentId);
    }

    public function getAllCats() {
        $sql = "select CATALOGUE_ID
                    ,PARENT_ID
                    ,NAME
              from {$this->_name}
              where REALSTATUS=1
                and COUNT_ > 0
              order by ORDERING";

        return $this->_db->fetchAll($sql);
    }

    public function itemCount($id, $brand_id = 0) {
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

    public function getChildren($id) {
        $path = array();
        $sql = "select CATALOGUE_ID
               from {$this->_name}
               where PARENT_ID=?
                 and STATUS=1
               order by ORDERING, CATALOGUE_ID";
               
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

    public function getAllParents($id, $path) {
        $sql = "select PARENT_ID
              from {$this->_name}
              where CATALOGUE_ID={$id}
                and STATUS=1";

        $parent_id = $this->_db->fetchOne($sql);

        if (!empty($parent_id)) {
            $path[count($path)] = $parent_id;
            $path = $this->getAllParents($parent_id, $path);
        }

        return $path;
    }

    public function getChildCatCount($id) {
        $sql = "select count(*)
             from {$this->_name} C
             where PARENT_ID = {$id}
               and REALSTATUS=1";

        return $this->_db->fetchOne($sql);
    }

    public function getChildItemCount($params) {
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

        $sql = "select count(*)
            from ITEM
            where STATUS = 1
              and PRICE > 0
              {$where}";

        return $this->_db->fetchOne($sql);
    }

    public function getCatalogueId($cat) {
        $sql = "select CATALOGUE_ID from {$this->_name} where REALCATNAME = '/" . $cat . "/'";
        return $this->_db->fetchOne($sql);
    }

    public function getCatName($id) {
        return $this->_db->fetchOne("select NAME from {$this->_name} where CATALOGUE_ID=?", array($id));
    }

    public function getParentId($id) {
        $sql = "select PARENT_ID from {$this->_name} where CATALOGUE_ID=?";
        return $this->_db->fetchOne($sql, $id);
    }

    public function getCatRealCat($id) {
        $sql = "select REALCATNAME from {$this->_name} where CATALOGUE_ID=?";

        return $this->_db->fetchOne($sql, $id);
    }

    public function getParents($id) {
        $sql = "select PARENT_ID
                    , CATALOGUE_ID
                    , NAME
                    , REALCATNAME
               from {$this->_name}
               where CATALOGUE_ID={$id}
                 and STATUS=1";

        return $this->_db->fetchRow($sql);
    }

    public function getCatInfo($id) {
        $sql = "select PARENT_ID
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
               from {$this->_name}
               where CATALOGUE_ID=?";

        $result = $this->_db->fetchRow($sql, $id);

        if ($result) {
            $xml = $this->_db->fetchOne("select XML from XMLS where TYPE=2 and XMLS_ID=?", $id);
            if ($xml)
                $result['LONG_TEXT'] = $xml;
            else
                $result['LONG_TEXT'] = '';
        }

        return $result;
    }

    public function getCurrencies() {
        $sql = "select CURRENCY_ID,
                       NAME,
                       SYSTEM_NAME
                from CURRENCY
                order by CURRENCY_ID";

        return $this->_db->fetchAll($sql);
    }

    public function getBrands($catid = 0) {

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

    public function getCount($f, $id) {

    }

    public function getComparedItems() {
        $comp_cats = array();
        if (!empty($_SESSION['citems'])) {
            $i = 0;
            foreach ($_SESSION['citems'] as $key => $Coms) {
                if ($key > 0 && !empty($Coms)) {
                    $sql = "select NAME
                    from {$this->_name}
                    where CATALOGUE_ID = {$key}
                      and STATUS=1";

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

    public function CheckEmptyItems($table, $V_ATTRIBUT_ID) {
        if ($V_ATTRIBUT_ID != 11111 && $V_ATTRIBUT_ID != 22222) {
            $q = "select TYPE,IS_RANGEABLE from ATTRIBUT where STATUS=1 and ATTRIBUT_ID=?";
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
            $count = 0;  //echo $query."<br>";
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
        }
        else {

        }
    }

    public function getCurrencyRate() {
        $sql = "select PRICE
             from CURRENCY
             where SYSTEM_NAME = 'USD'";

        return $this->_db->fetchOne($sql);
    }

    public function getCurrencyId($name) {
        $sql = "select CURRENCY_ID
             from CURRENCY
             where SYSTEM_NAME = '{$name}'";

        return $this->_db->fetchOne($sql);
    }

    public function getPriceIndentId($indent) {
        $sql = "select PRICE_EXPORT_ID
             from PRICE_EXPORT
             where INDENT = '{$indent}'";

        return $this->_db->fetchOne($sql);
    }

    public function getExportCatalog($id) {
        $sql = "select C.CATALOGUE_ID
                   ,C.PARENT_ID
                   ,C.NAME
              from {$this->_name} C
                 , CATALOGUE_PRICE_EXPORT CPE
                 , ITEM I
              WHERE CPE.PRICE_EXPORT_ID = {$id}
                and CPE.CATALOGUE_ID = C.CATALOGUE_ID
                and C.REALSTATUS=1
                and C.COUNT_ > 0
                and I.CATALOGUE_ID=C.CATALOGUE_ID
                and I.STATUS=1
              group by C.CATALOGUE_ID
              order by C.PARENT_ID, C.ORDERING";

        return $this->_db->fetchAll($sql);
    }

    public function getExportItems($id) {
        $sql = "select I.ITEM_ID
                  , CR.SYSTEM_NAME
                  , CR.PRICE
                  , I.BRAND_ID
                  , B.NAME as BRAND_NAME
                  , IF (I.TYPENAME is null or I.TYPENAME='', C.TYPENAME, I.TYPENAME) as TYPENAME
                  , if(I.PRICE1>0,I.PRICE1,I.PRICE) as ITEM_PRICE
                  , I.PRICE1 as ITEM_PRICE1
                  , I.IMAGE1
                  , I.CURRENCY_ID
                  , I.NAME
                  , I.CATNAME
                  , I.DESCRIPTION
                  , I.CATALOGUE_ID
                  , C.REALCATNAME as CATALOGUE_REALCATNAME
                  
                  from CURRENCY CR
                     , ITEM I left join BRAND B on (I.BRAND_ID=B.BRAND_ID)
                       inner join CATALOGUE C on (C.CATALOGUE_ID=I.CATALOGUE_ID)
                  where I.STATUS=1
                    and I.PRICE>0
                    and I.STATUS=1
                    and I.CURRENCY_ID=CR.CURRENCY_ID
                    and I.CATALOGUE_ID={$id}";

        return $this->_db->fetchAll($sql);
    }

    public function getIndexTree($catalogueID = 0, $lang = 0) {
        // если $catalogueID = 0 - выводим все наименования каталогов, у которых стоит атрибут IN_INDEX = 1
        // если  указан $catalogueID - выводим его каталоги-потомки
        $where = $catalogueID ? "and C.PARENT_ID = $catalogueID" : "";
        $sql = "select C.CATALOGUE_ID,
                    C.PARENT_ID,
                    C.NAME,
                    C.CATNAME,
                    C.REALCATNAME,
                    C.IS_INDEX,
                    C.URL,
                    C.IMAGE1
             from {$this->_name} C
             inner join ITEM I on (I.CATALOGUE_ID = C.CATALOGUE_ID) and I.STATUS = 1
             where 1
               $where
               and C.REALSTATUS = 1
               and C.COUNT_ > 0
               and I.STATUS = 1
               and I.PRICE > 0
             group by C.CATALOGUE_ID
             order by C.ORDERING";

        return $this->_db->fetchAll($sql);
    }

    public function getCatalogueByCode($cid) {
        $sql = "select CATALOGUE_ID from {$this->_name} where ID_FROM_VBD=?";
        return $this->_db->fetchOne($sql, $cid);
    }

    public function getCatalogueByName($name) {
        $sql = "select CATALOGUE_ID from {$this->_name} where NAME='{$name}'";
        return $this->_db->fetchOne($sql);
    }

    public function insertCatalogue($data) {
        $this->_db->insert('CATALOGUE', $data);

        return $this->_db->lastInsertId();
    }

    public function updateCatalogue($data, $uid) {
        $this->_db->update('CATALOGUE', $data, 'CATALOGUE_ID=' . $uid);
    }

    public function getMaxId() {
        $sql = "select max(CATALOGUE_ID) from {$this->_name}";
        return $this->_db->fetchOne($sql);
    }

    public function sequencesUpdate($name, $uid) {
        $sql = "update SEQUENCES
           set ID = {$uid}
           where NAME = '{$name}'";

        $this->_db->query($sql);
    }

    public function getCatByParent($uid) {
        $sql = "select CATALOGUE_ID
           from {$this->_name}
           where PARENT_ID={$uid}";

        return $this->_db->fetchCol($sql);
    }

    public function getItemsCountByCat($uid) {
        $sql = "select count(*)
           from ITEM
           where CATALOGUE_ID={$uid}";

        return $this->_db->fetchOne($sql);
    }

    public function updateCatCount($count, $uid) {

        $sql = "update CATALOGUE
           set COUNT_= {$count}
           where CATALOGUE_ID={$uid}";

        $this->_db->query($sql);
    }

    public function getCatalogueBanner($id) {
      $banners = array();
      $path[] = $id;
      $path = $this->getAllParents($id, $path);

      if (!empty($path)) {
        foreach ($path as $pid) {
          $sql = "select SA.*
                  from SECTION_ALIGN SA
                  join CATALOGUE_SECTION_ALIGN CSA on (CSA.SECTION_ALIGN_ID = SA.SECTION_ALIGN_ID)
                  where CSA.CATALOGUE_ID = {$pid}
                  order by SA.IS_ADV desc, SA.ORDERING";

          $_banners = $this->_db->fetchAll($sql);
          if (!empty($_banners))
              $banners = array_merge($banners, $_banners);
        }
      }

      return $banners;
    }

    public function getSiteMapCatTree() {
        $sql = "select CATALOGUE_ID
                   ,PARENT_ID
                   ,CATNAME
                   ,REALCATNAME
                   ,URL
             from {$this->_name}
             where REALSTATUS=1
             order by ORDERING";

        return $this->_db->fetchAll($sql);
    }

    public function getOrdering($parent_id) {
        $sql = "select max(ORDERING)
             from {$this->_name}
             where PARENT_ID = ?";

        $ordering = $this->_db->fetchOne($sql, $parent_id);
        $ordering++;

        return $ordering;
    }

    public function getCatalogOrdering($id) {
        $sql = "select ORDERING
             from {$this->_name}
             where CATALOGUE_ID = ?";

        return $this->_db->fetchOne($sql, $id);
    }

    public function trancuteCatItem() {
        $sql = "delete from CAT_ITEM";

        $this->_db->query($sql);
    }

    public function rebuildCatItem($tid, $id) {
        $sql = "insert into CAT_ITEM (CATALOGUE_ID,ITEM_ID)
             select {$tid},ITEM_ID
             from ITEM
             where CATALOGUE_ID={$id}
               and STATUS=1";

        $this->_db->query($sql);
    }

    public function getatalogActive($id) {
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

?>
