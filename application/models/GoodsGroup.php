<?php
class Models_GoodsGroup extends ZendDBEntity
{
    protected $_name = 'GOODS_GROUP';

    public function getFrontGoodsGroup()
    {
        $sql = "SELECT *
          FROM GOODS_GROUP
          WHERE STATUS = 1
            AND IN_FRONT = 1
          ORDER BY ORDERING";

        return $this->_db->fetchAll($sql);
    }

    public function getGoodsGroup()
    {
        $sql = "SELECT *
          FROM GOODS_GROUP
          WHERE STATUS = 1
          ORDER BY ORDERING";

        return $this->_db->fetchAll($sql);
    }

    public function getGoodsItemCount($id)
    {
        $sql = "SELECT count(*)
          FROM ITEM I
          JOIN GOODS_GROUP_ITEM_LINK GGIL ON (GGIL.ITEM_ID = I.ITEM_ID)
          WHERE GGIL.GOODS_GROUP_ID = {$id}
            AND GGIL.STATUS=1
            AND I.STATUS=1";

        return $this->_db->fetchOne($sql);
    }

    public function getGoodsItem($id, $limit = 0)
    {
        $sql = "SELECT I.ITEM_ID
               , I.NAME
               , I.TYPENAME
               , I.PRICE
               , I.CURRENCY_ID
               , I.CATALOGUE_ID
               , I.PRICE1               
               , B.NAME AS BRAND_NAME
               , C.NAME AS CATALOGUE_NAME
               , CR.SNAME
               , GGIL.IMAGE AS GGIL_IMAGE
               , D.IMAGE AS DISCOUNTS_IMAGE
               , D.IMAGE1 AS DISCOUNTS_IMAGE_BIG
               , GGIL.IMAGE
          FROM ITEM I
          JOIN GOODS_GROUP_ITEM_LINK GGIL ON (GGIL.ITEM_ID = I.ITEM_ID)
          LEFT JOIN BRAND B ON (B.BRAND_ID = I.BRAND_ID)
          JOIN CATALOGUE C ON (C.CATALOGUE_ID = I.CATALOGUE_ID)
          JOIN CURRENCY CR ON (CR.CURRENCY_ID = I.CURRENCY_ID)
          LEFT JOIN DISCOUNTS D ON (D.DISCOUNT_ID = I.DISCOUNT_ID)
          WHERE GGIL.GOODS_GROUP_ID = {$id}
            AND GGIL.STATUS=1
            AND I.STATUS=1
          ORDER BY GGIL.GOODS_GROUP_ID";

        if (!empty($limit)) $sql .= " limit {$limit}";

        return $this->_db->fetchAll($sql);
    }

    public function getGoodsItemAjax($id, $limit = 0)
    {
        $sql = "SELECT I.ITEM_ID
               , I.PRICE
               , I.PRICE1
               , I.CURRENCY_ID
          FROM ITEM I
          JOIN GOODS_GROUP_ITEM_LINK GGIL ON (GGIL.ITEM_ID = I.ITEM_ID)
          WHERE GGIL.GOODS_GROUP_ID = {$id}
            AND GGIL.STATUS=1
            AND I.STATUS=1
          ORDER BY GGIL.GOODS_GROUP_ID";

        if (!empty($limit)) $sql .= " limit {$limit}";

        return $this->_db->fetchAll($sql);
    }

    public function getGroupIDIndent($id)
    {
        $sql = "SELECT GOODS_GROUP_ID
          FROM GOODS_GROUP
          WHERE IMPORT_IDENT = '{$id}'";

        return $this->_db->fetchOne($sql);
    }

    public function getGroupIDIndentXml($id)
    {
        $sql = "SELECT GOODS_GROUP_ID
          FROM GOODS_GROUP
          WHERE IMPORT_IDENT_XML = '{$id}'";

        return $this->_db->fetchOne($sql);
    }

    public function getGroupsProper($id)
    {
        $sql = "SELECT *
          FROM GOODS_GROUP
          WHERE GOODS_GROUP_ID = {$id}";

        return $this->_db->fetchRow($sql);
    }

    public function insertItemToGoodGroup($data)
    {
        $this->_db->insert('GOODS_GROUP_ITEM_LINK', $data);
    }

    public function deleteOldRecored($id)
    {
        $sql = "DELETE
          FROM GOODS_GROUP_ITEM_LINK
          WHERE GOODS_GROUP_ID = {$id}";

        $this->_db->query($sql);
    }

    public function updateItemToGoodGroup($data, $goods_group_id, $item_id)
    {
        $sql = "UPDATE GOODS_GROUP_ITEM_LINK
          SET IMAGE = '{$data['IMAGE']}'
          WHERE GOODS_GROUP_ID = {$goods_group_id}
            AND ITEM_ID = {$item_id}";

        $this->_db->query($sql);
    }

    public function getBaseGoodsGroupImage($groups)
    {
        $where = '';
        if (is_array($groups)) {
            $where = "and GG.IMPORT_IDENT in ('" . implode("','", $groups) . "')";
        } else {
            $where = "and GG.IMPORT_IDENT = '{$groups}'";
        }
        $sql = "SELECT GGIL.GOODS_GROUP_ID
               , I.ITEM_ID
               , I.BASE_IMAGE
          FROM GOODS_GROUP_ITEM_LINK GGIL
             , GOODS_GROUP GG
             , ITEM I
          WHERE GGIL.GOODS_GROUP_ID = GG.GOODS_GROUP_ID
            AND I.ITEM_ID = GGIL.ITEM_ID
            AND length(I.BASE_IMAGE) > 0
          {$where}";

        return $this->_db->fetchAll($sql);
    }

}

?>