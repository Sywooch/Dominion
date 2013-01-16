<?php
  class zakaz_model{
    /**
    * CMF функцилоонал
    * 
    * @var SCMF
    */
    private $_cmf;

    function __construct(SCMF $cmf){
      $this->_cmf = $cmf;
    }
    
    public function getZakazData($params){
      return $this->_cmf->execute('select A.ZAKAZ_ID      
      from ZAKAZ A where 1'.
      (($params['SES_TELMOB'] != '') ? ' and REPLACE(REPLACE(TELMOB, \' \', \'\'), \'-\', \'\') like "%'.$params['SES_TELMOB'].'%"' : '').
      (($params['ZAKAZSTATUS_ID'] != 'all') ? ' and STATUS='.$params['ZAKAZSTATUS_ID'] : '').
      (($params['SES_CMF_USER_ID'] != 'all') ? ' and CMF_USER_ID='.$params['SES_CMF_USER_ID'] : '').
      (($params['DATE_FROM'] != '')? " and DATE(A.DATA) >= DATE(STR_TO_DATE('{$params['DATE_FROM']}', '%Y-%m-%d %H:%i'))":'').
      (($params['DATE_TO'] != '')? " and DATE(A.DATA) <= DATE(STR_TO_DATE('{$params['DATE_TO']}', '%Y-%m-%d %H:%i'))":'').''.' order by A.DATA desc');
    }
    
    public function getDefaultCurrency(){
      $sth = $this->_cmf->execute('select * from CURRENCY where CURRENCY_ID = 1');
      
      return mysql_fetch_array($sth, MYSQL_ASSOC);                                    
    }
    
    public function getCurrencyRate($CURRENCY_ID){
      return $this->_cmf->selectrow_array("select PRICE from CURRENCY where CURRENCY_ID=?", $CURRENCY_ID);
    }
    
    public function getZakazItems($V_ZAKAZ_ID){
      return $this->_cmf->execute('select PRICE
                                        , PURCHASE_PRICE
                                        , CURRENCY_ID
                                        , QUANTITY
                                   from ZAKAZ_ITEM
                                   where ZAKAZ_ID = ?', $V_ZAKAZ_ID);
                                    
    }
    
  }
?>