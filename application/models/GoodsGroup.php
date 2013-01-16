<?php
class Models_GoodsGroup extends ZendDBEntity{
  protected $_name = 'GOODS_GROUP';
  
  public function getFrontGoodsGroup(){
    $sql="select *
          from GOODS_GROUP
          where STATUS = 1
            and IN_FRONT = 1
          order by ORDERING";
          
    return $this->_db->fetchAll($sql);
  }
  
  public function getGoodsGroup(){
    $sql="select *
          from GOODS_GROUP
          where STATUS = 1
          order by ORDERING";
          
    return $this->_db->fetchAll($sql);
  }
  
  public function getGoodsItemCount($id){
    $sql="select count(*)
          from ITEM I
          join GOODS_GROUP_ITEM_LINK GGIL on (GGIL.ITEM_ID = I.ITEM_ID)
          where GGIL.GOODS_GROUP_ID = {$id}
            and GGIL.STATUS=1
            and I.STATUS=1";
    
    return $this->_db->fetchOne($sql);
  }
  
  public function getGoodsItem($id, $limit=0){        
    $sql="select I.ITEM_ID 
               , I.NAME
               , I.TYPENAME
               , I.PRICE
               , I.CURRENCY_ID
               , I.CATALOGUE_ID
               , I.PRICE1               
               , B.NAME as BRAND_NAME
               , C.NAME as CATALOGUE_NAME
               , CR.SNAME
               , GGIL.IMAGE as GGIL_IMAGE
               , D.IMAGE as DISCOUNTS_IMAGE
               , D.IMAGE1 as DISCOUNTS_IMAGE_BIG
               , GGIL.IMAGE
          from ITEM I
          join GOODS_GROUP_ITEM_LINK GGIL on (GGIL.ITEM_ID = I.ITEM_ID)
          left join BRAND B on (B.BRAND_ID = I.BRAND_ID)
          join CATALOGUE C on (C.CATALOGUE_ID = I.CATALOGUE_ID)
          join CURRENCY CR on (CR.CURRENCY_ID = I.CURRENCY_ID)
          left join DISCOUNTS D on (D.DISCOUNT_ID = I.DISCOUNT_ID)
          where GGIL.GOODS_GROUP_ID = {$id}
            and GGIL.STATUS=1
            and I.STATUS=1
          order by GGIL.GOODS_GROUP_ID";
          
    if(!empty($limit))$sql.=" limit {$limit}"; 
    
    return $this->_db->fetchAll($sql);
  }
  
   public function getGoodsItemAjax($id, $limit=0){        
    $sql="select I.ITEM_ID 
               , I.PRICE
               , I.PRICE1
               , I.CURRENCY_ID
          from ITEM I
          join GOODS_GROUP_ITEM_LINK GGIL on (GGIL.ITEM_ID = I.ITEM_ID)
          where GGIL.GOODS_GROUP_ID = {$id}
            and GGIL.STATUS=1
            and I.STATUS=1
          order by GGIL.GOODS_GROUP_ID";
          
    if(!empty($limit))$sql.=" limit {$limit}";
    
    return $this->_db->fetchAll($sql);
  }
  
  public function getGroupIDIndent($id){     
    $sql="select GOODS_GROUP_ID
          from GOODS_GROUP 
          where IMPORT_IDENT = '{$id}'";
    
    return $this->_db->fetchOne($sql);
  }
  
  public function getGroupIDIndentXml($id){     
    $sql="select GOODS_GROUP_ID
          from GOODS_GROUP 
          where IMPORT_IDENT_XML = '{$id}'";
    
    return $this->_db->fetchOne($sql);
  }
  
  public function getGroupsProper($id){     
    $sql="select *                 
          from GOODS_GROUP
          where GOODS_GROUP_ID = {$id}";
    
    return $this->_db->fetchRow($sql);
  }
  
  public function insertItemToGoodGroup($data){  
    $this->_db->insert('GOODS_GROUP_ITEM_LINK', $data);
  }
  
  public function deleteOldRecored($id){     
    $sql="delete                 
          from GOODS_GROUP_ITEM_LINK
          where GOODS_GROUP_ID = {$id}";
          
    $this->_db->query($sql);
  }
  
  public function updateItemToGoodGroup($data, $goods_group_id, $item_id){     
    $sql="update GOODS_GROUP_ITEM_LINK
          set IMAGE = '{$data['IMAGE']}'
          where GOODS_GROUP_ID = {$goods_group_id}
            and ITEM_ID = {$item_id}";
          
    $this->_db->query($sql);
  }
  
  public function getBaseGoodsGroupImage($groups){
    $where = '';
    if(is_array($groups)){
      $where = "and GG.IMPORT_IDENT in ('".implode("','", $groups)."')";
    }
    else{
      $where = "and GG.IMPORT_IDENT = '{$groups}'";
    }
    $sql="select GGIL.GOODS_GROUP_ID
               , I.ITEM_ID
               , I.BASE_IMAGE
          from GOODS_GROUP_ITEM_LINK GGIL
             , GOODS_GROUP GG
             , ITEM I
          where GGIL.GOODS_GROUP_ID = GG.GOODS_GROUP_ID
            and I.ITEM_ID = GGIL.ITEM_ID
            and length(I.BASE_IMAGE) > 0
          {$where}";
   
    return $this->_db->fetchAll($sql);
 }
    
}
?>