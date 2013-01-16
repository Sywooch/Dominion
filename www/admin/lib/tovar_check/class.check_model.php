<?php
  class checkModel{
    
    /**
    * put your comment there...
    * 
    * @var SCMF
    */
    private $_cmf;
    
    function __construct(SCMF $cmf){
      $this->_cmf = $cmf;      
    }
    
    public function getSettings($system_name){
      $sql="select VALUE
            from SETINGS
            where SYSTEM_NAME = ?";
            
      return $this->_cmf->selectrow_array($sql, $system_name);
    }
    
    public function getBuyerName($zakaz_id){
      $sql="select concat(SURNAME, ' ', NAME)
            from ZAKAZ
            where ZAKAZ_ID = ?";
            
      return $this->_cmf->selectrow_array($sql, $zakaz_id);
    }
    
    public function getZakazInfo($zakaz_id){
      $sql="select *
                  , date_format(DELIVERYDATA, '%d') as day 
                  , date_format(DELIVERYDATA, '%m') as month 
                  , date_format(DELIVERYDATA, '%Y') as year 
            from ZAKAZ
            where ZAKAZ_ID = ?";
            
      $sth = $this->_cmf->execute($sql, $zakaz_id);
      
      return mysql_fetch_array($sth, MYSQL_ASSOC);
    }
    
    public function getZakazItem($zakaz_id){
      $sql="select I.NAME
                 , I.ARTICLE
                 , I.TYPENAME
                 , B.NAME as BRAND_NAME
                 , I.ARTICLE
                 , Z.PRICE                
                 , Z.QUANTITY                
                 , Z.CURRENCY_ID
           from ZAKAZ_ITEM  Z
           join ITEM I using (ITEM_ID)
           join BRAND B on  (B.BRAND_ID = I.BRAND_ID)
           where Z.ZAKAZ_ID = {$zakaz_id}";
           
      return $this->_cmf->select($sql);
    }
    
    public function getDefaultCurrency(){
      $sth = $this->_cmf->execute('select * from CURRENCY where CURRENCY_ID = 1');
      
      return mysql_fetch_array($sth, MYSQL_ASSOC);                                    
    }
    
    public function getCurrencyRate($CURRENCY_ID){
      return $this->_cmf->selectrow_array("select PRICE from CURRENCY where CURRENCY_ID=?", $CURRENCY_ID);
    }
  }
?>