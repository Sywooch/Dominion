<?php
class Models_Brands extends ZendDBEntity{
   protected $_name = 'BRAND';
   
   function viewBrands($catalogue_id, $childs='', $first, $last){
     if(empty($catalogue_id)){
       $sql="select B.*
             from BRAND B
             join ITEM I on (I.BRAND_ID = B.BRAND_ID)
             where B.STATUS = 1
               and I.STATUS = 1 
               and I.PRICE > 0                          
             group by B.BRAND_ID
             order by B.NAME
             limit {$first}, {$last}";
     }
     else{
       if($childs) $where = " and I.CATALOGUE_ID IN(".$catalogue_id.",".implode(',',$childs).")";
       else $where = " and I.CATALOGUE_ID = ".$catalogue_id;
       
       $sql="select B.*
             from BRAND B
             join ITEM I on (I.BRAND_ID = B.BRAND_ID)
             where B.STATUS = 1
               and I.STATUS = 1 
               and I.PRICE > 0 
               {$where}             
             group by B.BRAND_ID
             order by B.NAME
             limit {$first}, {$last}";
     }
     
     return $this->_db->fetchAll($sql);
   }
   
   function viewBrandsAjax($params){
     if(empty($params['catalogue_id'])){
       if($params['section']=='index') $where = " and B.IN_INDEX = 1 ";
       else $where = " and B.IN_ALL_PAGES = 1 ";
       
       $sql="select B.*
             from BRAND B
             join ITEM I on (I.BRAND_ID = B.BRAND_ID)
             where B.STATUS = 1
               {$where}
               and I.STATUS = 1 
               and I.PRICE > 0                          
             group by B.BRAND_ID
             order by B.NAME
             limit {$params['first']}, {$params['last']}";             
     }
     else{
       if($params['childs']) $where = " and I.CATALOGUE_ID IN(".$params['catalogue_id'].",".implode(',',$params['childs']).")";
       else $where = " and I.CATALOGUE_ID = ".$params['catalogue_id'];
       
       $sql="select B.*
             from BRAND B
             join ITEM I on (I.BRAND_ID = B.BRAND_ID)
             where B.STATUS = 1
               and B.IN_ALL_PAGES = 1
               and I.STATUS = 1 
               and I.PRICE > 0 
               {$where}             
             group by B.BRAND_ID
             order by B.NAME
             limit {$params['first']}, {$params['last']}";
     }
     
     return $this->_db->fetchAll($sql);
   }
   
   function viewBrandsAjaxCount($params){
     if(empty($params['catalogue_id'])){
       if($params['section']=='index') $where = " and B.IN_INDEX = 1 ";
       else $where = " and B.IN_ALL_PAGES = 1 ";
       
       $sql="select count(distinct B.BRAND_ID)
             from BRAND B
             join ITEM I on (I.BRAND_ID = B.BRAND_ID)
             where B.STATUS = 1
               {$where}
               and I.STATUS = 1 
               and I.PRICE > 0 ";
     }
     else{
       if($params['childs']) $where = " and I.CATALOGUE_ID IN(".$params['catalogue_id'].",".implode(',',$params['childs']).")";
       else $where = " and I.CATALOGUE_ID = ".$params['catalogue_id'];
       
       $sql="select count(distinct B.BRAND_ID)
             from BRAND B
             join ITEM I on (I.BRAND_ID = B.BRAND_ID)
             where B.STATUS = 1
               and B.IN_ALL_PAGES = 1
               and I.STATUS = 1 
               and I.PRICE > 0 
               {$where}";
     }
             
     return $this->_db->fetchOne($sql);
     
   }
   
   function getBrandInfo($brand_id){ 
     $sql="select *
           from BRAND
           where BRAND_ID = {$brand_id}";
           
     return $this->_db->fetchRow($sql);  
   }
   
   function getItemBrandCount($brand_id, $catalogue_id, $childs=''){
     
     if(empty($catalogue_id)){
       $sql="select COUNT(*)
             from ITEM
             where BRAND_ID  = {$brand_id} 
               and STATUS = 1 
               and PRICE > 0";
     }
     else{
       $sql="select COUNT(*)
             from BRAND B
             join ITEM I on (I.BRAND_ID = B.BRAND_ID)
             where B.STATUS = 1
               and I.CATALOGUE_ID  = {$catalogue_id} 
               and I.BRAND_ID  = {$brand_id} 
               and I.STATUS = 1 
               and I.PRICE > 0";
     }
     
     return $this->_db->fetchOne($sql);
   }
   
   function getBrandByCode($code){ 
     $sql="select BRAND_ID
           from BRAND
           where ID_FROM_VBD = {$code}";
           
     return $this->_db->fetchOne($sql);  
   }
   
   function getBrandByAltName($vendor){ 
     $sql="select BRAND_ID
           from BRAND
           where ALT_NAME = '{$vendor}'";
           
     return $this->_db->fetchOne($sql);  
   }
   
   function getBrandByName($vendor){ 
     $sql="select BRAND_ID
           from BRAND
           where NAME = '{$vendor}'";
           
     return $this->_db->fetchOne($sql);  
   }
   
   function insertBrand($data){ 
     $this->_db->insert('BRAND', $data);
           
     return $this->_db->lastInsertId();
   }
   
   public function updateBrand($data, $uid){     
     $this->_db->update('BRAND', $data, 'BRAND_ID='.$uid);
   }
   
   public function getMaxId(){
      $sql = "select max(BRAND_ID) from BRAND";
      return $this->_db->fetchOne($sql);
   }
   
}
?>
