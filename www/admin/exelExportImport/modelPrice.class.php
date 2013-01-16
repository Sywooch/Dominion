<?php

/**
 * Model for exporting data
 *
 * @author Ruslan Bocharov <helcy1@ya.ru>
 */
class modelPrice {

    /**
     * DB connector
     * @var type 
     */
    private $_cmf;

    /**
     * Конструктор
     * @param SCMF $cmf 
     */
    public function __construct(SCMF $cmf) {
        $this->_cmf = $cmf;
    }

    /**
     * return SCMF
     * @return SCMF 
     */
    public function getSCMF(){
        return $this->_cmf;
    }

        /**
     * Get Items for price
     * @param integer $cataloguesID
     * @return array 
     */
    public function getItems($cataloguesID) {
     
        $sql = "SELECT I.NAME
                    , I.ITEM_ID
                    , I.PRICE
                    , I.STATUS
                    , B.NAME AS BRAND_NAME
                    , day(now()) - day(DATE_INSERT) AS 'DAY_DIFF'
                FROM
                ITEM I
                , CATALOGUE C
                , BRAND B
                WHERE C.IN_ADV = 1
                AND I.CATALOGUE_ID = C.CATALOGUE_ID
                AND I.BRAND_ID = B.BRAND_ID
                AND I.STATUS =1
                AND I.CATALOGUE_ID = ?";

        return $this->_cmf->select($sql, $cataloguesID);
    }

    public function getCatalogesId($cataloId) {

        if ($cataloId < 0) {
            $sql = "select
             CASE
            WHEN CC.PARENT_ID is null THEN C.CATALOGUE_ID
            ELSE CC.CATALOGUE_ID END as CATALOGUE_ID,
              CASE
            WHEN CC.PARENT_ID is null THEN C.NAME
            ELSE CC.NAME END as NAME
              from CATALOGUE C left join CATALOGUE CC on(C.CATALOGUE_ID = CC.PARENT_ID)
              where CC.PARENT_ID is not null
              or (C.PARENT_ID = 0 and CC.PARENT_ID is null)
              #order by C.ORDERING, CC.ORDERING
                union
            select CATALOGUE_ID, NAME from CATALOGUE where PARENT_ID = 0";

            return $this->_cmf->select($sql);
        }

        $sql = "select CATALOGUE_ID, NAME
		  from CATALOGUE
		  where PARENT_ID = ?";

        $childs = $this->_cmf->select($sql, $cataloId);

        if ($childs)
            return $childs;

        $sql = "select CATALOGUE_ID, NAME from CATALOGUE where CATALOGUE_ID =?";

        return $this->_cmf->select($sql, $cataloId);
    }

    public function getCatalogName($cataloId) {
        return $this->_cmf->selectrow_array("select `NAME` from CATALOGUE where CATALOGUE_ID =?", $cataloId);
    }


    public function checkCatalog($id) {
        return $this->_cmf->selectrow_array("select count(*) Co from CATALOGUE where CATALOGUE_ID =?", $id);
    }

}