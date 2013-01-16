<?php
class models_Registration extends ZendDBEntity{
  protected $_name = 'SHOPUSER';   
  
  public function insertRegData($data){     
    $this->_db->insert('SHOPUSER', $data);
  } 
  
  public function updateRegData($data, $uid){     
    $this->_db->update('SHOPUSER', $data, 'USER_ID='.$uid);
  }
  
  public function resetUsersDiscount(){
    $sql="update SHOPUSER
          set SHOPUSER_DISCOUNTS_ID = 0";
          
    $this->_db->query($sql);
  }
  
  public function checkLogin($login){  
    $sql="select *
          from SHOPUSER
          where EMAIL = '{$login}'";
                        
    return  $this->_db->fetchRow($sql);
  }
  
   public function sigin($login, $passwd){     
    $sql="select *
          from SHOPUSER
          where EMAIL = '{$login}'
            and PASSWORD = '{$passwd}'
            and STATUS=1";
                        
    return  $this->_db->fetchRow($sql);
  }
  
  public function sigin_id($id){     
    $sql="select *
          from SHOPUSER
          where USER_ID = {$id}";
                        
    return  $this->_db->fetchRow($sql);
  }
  
  public function getShopuserDiscountsId($id){     
    $sql="select SHOPUSER_DISCOUNTS_ID
          from SHOPUSER
          where USER_ID = {$id}";
                        
    return  $this->_db->fetchOne($sql);
  }
  
  public function userData($id){      
    $sql="select SURNAME
               , NAME
               , PRIVATEINFO
               , TELMOB
               , TELMOBT1
               , EMAIL
          from SHOPUSER
          where USER_ID = {$id}";
                        
    return  $this->_db->fetchRow($sql);
  }
  
  public function check_email($email){     
    $sql="select EMAIL
               , PASSWORD
          from SHOPUSER
          where EMAIL = '{$email}'";
                        
    return  $this->_db->fetchRow($sql);
  }
  
  public function getOrdersCount($uid){
    $sql="select count(*)
          from ZAKAZ
          where USER_ID = {$uid}";
          
    return  $this->_db->fetchOne($sql);
  }
  
  public function getOrders($uid){
    $sql="select Z.*
               , ZS.NAME as ZS_NAME
               , ZS.COLOR as ZS_COLOR
               , DATE_FORMAT(DATA,'%d.%m.%Y') as date
          from ZAKAZ Z
          left join ZAKAZSTATUS ZS on (ZS.ZAKAZSTATUS_ID = Z.STATUS)
          where Z.USER_ID = {$uid}
          order by Z.DATA";
          
    return  $this->_db->fetchAll($sql);
  }
  
  public function getZakazItems($id){
    $sql="select ZI.*
               , I.IMAGE1
               , I.CATNAME
               , C.REALCATNAME as CATALOGUE_REALCATNAME
               , CR.SNAME
               , B.NAME as BRAND_NAME
          from ZAKAZ_ITEM ZI
          join ITEM I on (ZI.ITEM_ID = I.ITEM_ID)
          join BRAND B on (B.BRAND_ID = I.BRAND_ID)
          join CURRENCY CR on (CR.CURRENCY_ID = ZI.CURRENCY_ID)
          join CATALOGUE C on (C.CATALOGUE_ID = I.CATALOGUE_ID)
          where ZAKAZ_ID = {$id}";
    
    return  $this->_db->fetchAll($sql);
  }
}
?>
