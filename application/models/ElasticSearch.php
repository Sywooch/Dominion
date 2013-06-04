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
}