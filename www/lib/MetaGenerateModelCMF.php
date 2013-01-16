<?php

class MetaGenerateModelCMF implements iMetaGenerateModel{
  
  /**
  * Коннетк для работы с БД
  * 
  * @var SCMF
  */
  
  private $_db;

  /**
  * put your comment there...
  * 
  * @param SCMF $db
  * @return iMetaGenerateModel
  */
  public function  __construct(SCMF $db) {
    $this->_db = $db;
  }
  
  /**
  * Получить ID всех рубрик каталога товаров
  * 
  */
  public function getCatalogsId() {
    $result = array();
    
    $sql="select CATALOGUE_ID
          from CATALOGUE";
          
    $sth = $this->_db->execute($sql);
    while(list($V_CATALOGUE_ID)=mysql_fetch_array($sth, MYSQL_NUM))
    {
      $result[] = $V_CATALOGUE_ID;
    }
    
    return $result;
  }
  
  /**
  * Получить конкретный каталог товаров
  * 
  * @param int $id
  */
  public function  getCurrentCatalog($id) {
    $sql="select TITLE
               , DESC_META
               , KEYWORD_META
               , NAME
               , PARENT_ID
          from CATALOGUE
          where CATALOGUE_ID = {$id}";          

    $sth = $this->_db->execute($sql);
    return mysql_fetch_assoc($sth);
  }
  
  /**
  * Получить ID всех товаров
  * 
  */
  public function  getItemsId() {
    $result = array();
    
    $sql="select ITEM_ID
          from ITEM";
          
    $sth = $this->_db->execute($sql);
    while(list($V_CATALOGUE_ID)=mysql_fetch_array($sth, MYSQL_NUM))
    {
      $result[] = $V_CATALOGUE_ID;
    }
    
    return $result;
  }
  
  /**
  * Получить конкретный товар
  * 
  * @param int $id
  */
  public function  getCurrentItem($id) {
    $sql="select I.TITLE
               , I.DESC_META
               , I.KEYWORD_META
               , I.NAME
               , I.TYPENAME
               , B.NAME as BRAND_NAME
          from ITEM I
          inner join BRAND B using (BRAND_ID)
          where I.ITEM_ID = {$id}";          

    $sth = $this->_db->execute($sql);
    return mysql_fetch_assoc($sth);
  }
  
  /**
  * put your comment there...
  * 
  * @param string $where
  * @return SCMF
  */
  public function getSettingValue($where){     
    $sql="select VALUE 
          from SETINGS
          where SYSTEM_NAME='{$where}'";
    
    return $this->_db->selectrow_array($sql);
  }
  
  public function getBrands($catid = 0) {
    $result = array();
    
    $sql = "SELECT B.BRAND_ID
                , B.NAME
                , B.ALT_NAME
            FROM
            BRAND B
            JOIN CATALOGUE_BRAND_VIEW CBV
            USING (BRAND_ID)
            WHERE
            1
            AND CBV.CATALOGUE_ID = {$catid}
            AND (SELECT count(*)
                FROM
                    ITEM
                WHERE
                    1
                    AND STATUS = 1
                    AND BRAND_ID = B.BRAND_ID
                    AND CATALOGUE_ID = {$catid}) > 0
            ORDER BY
            B.NAME";


    $sth = $this->_db->execute($sql);
    while($row=mysql_fetch_assoc($sth))
    {
      $result[] = $row;
    }
    
    return $result;
  }
  
  public function updateCatalogue($data, $id){
    
    $column = key($data);
    $_data = $data[$column];
     
    $sql="update CATALOGUE
          set {$column} = '{$_data}'
          where CATALOGUE_ID = {$id}";
            
    $this->_db->execute($sql);
  }
  
  public function updateItem($data, $id){
    
    $column = key($data);
    $_data = $data[$column];
     
    $sql="update ITEM
          set {$column} = '{$_data}'
          where ITEM_ID = {$id}";
            
    $this->_db->execute($sql);
  }
}