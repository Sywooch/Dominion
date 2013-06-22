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
     * Get model from itemsPrices
     *
     * @param array $itemsId
     *
     * @return array
     */
    public function getItemsForPrices(array $itemsId)
    {
        $_items = implode(", ", $itemsId);

        $where = "";
        $where .= " and I.ITEM_ID IN ({$_items}) ";

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
          where
            I.PRICE > 0 " . $where;

        return $this->_db->fetchAll($sql);
    }
}