<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Константин
 * Date: 31.05.13
 * Time: 12:31
 * To change this template use File | Settings | File Templates.
 */

/**
 * Class model get data for put to elastic search in index
 *
 * Class GetElasticSearch
 */
class models_ElasticSearch extends ZendDBEntity
{
    /**
     * Model get all information from data about Products
     *
     * @return array
     */
    public function getProducts()
    {
        $sql = "SELECT
                  I.ITEM_ID,
                  I.TYPENAME,
                  I.NAME AS NAME_PRODUCT,
                  B.NAME AS BRAND,
                  I.ARTICLE,
                  I.CATNAME,
                  I.PRICE,
                  I.IMAGE1,
                  C.REALCATNAME
                FROM ITEM I LEFT JOIN BRAND B USING (BRAND_ID)
                LEFT JOIN CATALOGUE C USING(CATALOGUE_ID)";

        return $this->_db->fetchAll($sql);
    }

    /**
     * Get all data from database about item or products
     *
     * @param bool $count Get count of this query
     *
     * @return array
     */
    public function getAllData($count = false)
    {

        $sql = "SELECT ";

        $sql .= $count ? "count(*)" : "I.ITEM_ID,
                  I.CATALOGUE_ID
                  ,I.NAME AS NAME_PRODUCT
                  ,I.CATNAME
                  ,I.TYPENAME
                  ,I.ARTICLE
                  ,I.IMAGE0
                  ,B.NAME AS BRAND
                  ,C.REALCATNAME
                  ,C.NAME AS CATALOGUE_NAME";

        $sql .= ' FROM ITEM I
                    LEFT JOIN BRAND B ON (B.BRAND_ID = I.BRAND_ID)
                    LEFT JOIN CATALOGUE C ON (C.CATALOGUE_ID = I.CATALOGUE_ID)';

        return $sql;
    }


    /**
     * get data for format in search
     *
     * @param array $itemsId
     *
     * @return array
     */
    public function getItemsForPrices(array $itemsId)
    {
        $itemsId = implode(", ", $itemsId);
        $where = "";
        $where .= " and I.ITEM_ID IN ({$itemsId}) ";

        $sql = "select I.ITEM_ID,
                  I.CATALOGUE_ID
                  ,I.NAME AS NAME_PRODUCT
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
                  ,B.NAME as BRAND
                  ,B.URL as BRAND_URL

                  ,W.DESCRIPTION as WARRANTY_DESCRIPTION
                  ,DL.DESCRIPTION as DELIVERY_DESCRIPTION
                  ,CR.DESCRIPTION as CREDIT_DESCRIPTION

                  ,C.REALCATNAME
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
            where 1
            #I.PRICE > 0
            " . $where;

        return $this->_db->fetchAll($sql);
    }

    /**
     * Get all items id
     *
     * @return string
     */
    public function getAllItemID()
    {
        return "SELECT i.ITEM_ID, i.CATALOGUE_ID, i.PRICE, i.BRAND_ID FROM ITEM i WHERE i.STATUS = 1 AND i.PRICE > 0";
    }

    /**
     * Get attributes for Elastic index
     *
     * @param $itemID Item ID
     *
     * @return array
     */
    public function getAttributesIndex($itemID)
    {
        $sql = "SELECT
                  a.ATTRIBUT_ID,
                  a.IS_RANGE_VIEW,
                  a.NAME,
                  a.TYPE,
                  i0.VALUE
                FROM ATTRIBUT a
                  JOIN ITEM0 i0 USING (ATTRIBUT_ID)
                WHERE i0.ITEM_ID = ?
                AND (a.IS_RANGE_VIEW = 0 OR a.IS_RANGE_VIEW IS NULL)
                UNION
                SELECT
                  a.ATTRIBUT_ID,
                  a.IS_RANGE_VIEW,
                  a.NAME,
                  a.TYPE,
                  i1.VALUE
                FROM ATTRIBUT a
                  JOIN ITEM1 i1 USING (ATTRIBUT_ID)
                WHERE i1.ITEM_ID = ?
                AND (a.IS_RANGE_VIEW = 0 OR a.IS_RANGE_VIEW IS NULL)
                  UNION
                  SELECT
                      a.ATTRIBUT_ID,
                      a.IS_RANGE_VIEW,
                      a.NAME,
                      a.TYPE,
                      i.VALUE
                    FROM ATTRIBUT a
                      JOIN ITEM0 i USING (ATTRIBUT_ID)
                    WHERE i.ITEM_ID = ?
                    AND a.IS_RANGE_VIEW = 1
                    UNION
                    SELECT
                      a.ATTRIBUT_ID,
                      a.IS_RANGE_VIEW,
                      a.NAME,
                      a.TYPE,
                      i.VALUE
                    FROM ATTRIBUT a
                      JOIN ITEM1 i USING (ATTRIBUT_ID)
                    WHERE i.ITEM_ID = ?
                    AND a.IS_RANGE_VIEW = 1;
                  ";

        return array_map(function ($result) {
                $el['ATTRIBUT_ID'] = (int)$result['ATTRIBUT_ID'];
                $el['NAME'] = $result['NAME'];
                $el['TYPE'] = (int)$result['TYPE'];
                $el["VALUE"] = $result['VALUE'];
                $el['IS_RANGE_VIEW'] = (bool)$result['IS_RANGE_VIEW'];

                if ($el['TYPE'] == 1 || $el['IS_RANGE_VIEW']) {
                    $el['FLOAT_VALUE'] = $valueAttribute = (float)Format_ConvertDataElasticSelection::getInt($result['VALUE']);
                } else {
                    $el['INT_VALUE'] = $valueAttribute = (int)$result['VALUE'];
                }

                $el[$result["ATTRIBUT_ID"]] = $valueAttribute;

                return $el;

            }, $this->_db->fetchAll($sql, array($itemID, $itemID, $itemID, $itemID))
        );
    }

    /**
     * Get connect DB
     *
     * @return mixed|null|Zend_Db_Adapter_Abstract
     */
    public function getConnectDB()
    {
        return $this->_db;
    }
}