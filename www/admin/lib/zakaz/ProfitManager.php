<?php
  class ProfitManager{

    /**
    * CMF функцилоонал
    * 
    * @var SCMF
    */
    private $_cmf;

    function __construct(SCMF $cmf){
      $this->_cmf = $cmf;
    }

    public function getProfit($params){
      $sum = 0;
      $sname = '';
      $work_model = new zakaz_model($this->_cmf);
      
      $default_currency = $work_model->getDefaultCurrency();
      
      $sth = $work_model->getZakazData($params);
      if(is_resource($sth)){
        while($res = mysql_fetch_array($sth, MYSQL_ASSOC)){
          $sth2 = $work_model->getZakazItems($res['ZAKAZ_ID']);
          if(is_resource($sth2)){
            while($res2 = mysql_fetch_array($sth2, MYSQL_ASSOC)){
              
              if($default_currency['CURRENCY_ID'] != $res2['CURRENCY_ID']){
                $rate = $work_model->getCurrencyRate($res2['CURRENCY_ID']);
                
                $res2['PRICE'] = round($res2['PRICE'] * $rate);
                $res2['PURCHASE_PRICE'] = round($res2['PURCHASE_PRICE'] * $rate);
              }
              
              $sum+= ($res2['PRICE'] - $res2['PURCHASE_PRICE']) * $res2['QUANTITY'];
            }
          }
         
        }
      }
      
      return round($sum).' '.$default_currency['SNAME'];
    }
  }
?>
