<?php
class models_Cart extends ZendDBEntity
{
   protected $_name = 'ITEM';
     
   public function getPaymentInfo($id){
     $sql="select *
           from PAYMENT 
           where PAYMENT_ID = ?";
           
     return $this->_db->fetchRow($sql, (int)$id);
   }
   
   public function selectZakazName($id){
     $sql="select NAME
           from ZAKAZ
           where ZAKAZ_ID = {$id}";
           
     return $this->_db->fetchOne($sql);
   } 
   
   public function selectZakaz($id){
     $sql="select *
           from ZAKAZ_ITEM
           where ZAKAZ_ID = {$id}";
           
     return $this->_db->fetchAll($sql);
   }
   
   public function curIDWMZ(){
     $sql="select CURRENCY_ID
           from CURRENCY
           where SYSTEM_NAME = 'WMZ'";
           
     return $this->_db->fetchOne($sql);
   }
   
   public function setStatusZakaz($status, $id){
     $sql="update ZAKAZ
           set STATUS = {$status}
           where ZAKAZ_ID = {$id}";
           
     $this->_db->query($sql);
   }
   
   public function getUserDiscountId($user_id){
      $sql="select SHOPUSER_DISCOUNTS_ID
            from SHOPUSER 
            where USER_ID = {$user_id}";
            
      return $this->_db->fetchOne($sql);
    }
    
    public function getDiscountData($discounts_id){
      $sql="select *
            from SHOPUSER_DISCOUNTS 
            where SHOPUSER_DISCOUNTS_ID = {$discounts_id}";
            
      return $this->_db->fetchRow($sql);
    }
    
    public function getNextDiscountId($ordering){
      $sql="select SHOPUSER_DISCOUNTS_ID
            from SHOPUSER_DISCOUNTS 
            where ORDERING > {$ordering}
            order by ORDERING
            limit 1";
            
      return $this->_db->fetchRow($sql);
    }
   
   public function getUserOrderSumm($id){
    $sql="select sum(ZI.COST)
          from ZAKAZ_ITEM ZI
          inner join ZAKAZ Z ON (Z.ZAKAZ_ID = ZI.ZAKAZ_ID)
          where Z.USER_ID = ?";
         
    return $this->_db->fetchOne($sql, $id);
  }
   
}
