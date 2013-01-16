<?php
class models_News extends ZendDBEntity{
  protected $_name = 'NEWS';
  
  public function getNewsIndexCount($amount){
    $sql="select count(*)
          from {$this->_name}
          where STATUS = 1
          order by DATA desc
          limit {$amount}";
          
    return $this->_db->fetchOne($sql); 
  }
  
  public function getNewsCount(){
    $sql="select count(*)
          from {$this->_name}
          where STATUS = 1";
          
    return $this->_db->fetchOne($sql); 
  }
  
  public function getNewsIndex($amount){
    $sql="select *
          from {$this->_name}
          where STATUS = 1
          order by DATA desc
          limit {$amount}";
          
    return $this->_db->fetchAll($sql); 
  }
  
  public function getNews($startSelect, $pageSize){    
    $sql = "select  NEWS_ID
                  , NAME
                  , CATNAME
                  , DATE_FORMAT(DATA,'%d.%m.%Y') as date
                  , DESCRIPTION
                  , IMAGE1 
            from {$this->_name} 
            where STATUS=1 
            order by NEWS_ID desc 
            limit {$startSelect}, {$pageSize}";
            
    return $this->_db->fetchAll($sql);
  }
  
  public function getNewsSingle($id){
    $sql="select NEWS_ID
               , NAME
               , DATE_FORMAT(DATA,'%d.%m.%y') as date 
          from {$this->_name} 
          where NEWS_ID=?";
          
    $result = $this->_db->fetchRow($sql ,$id);
          
    if($result){
      $xml = $this->_db->fetchOne("select XML from XMLS where TYPE=1 and XMLS_ID=?",$id);
      if($xml) $result['LONG_TEXT'] = $xml;
      else $result['LONG_TEXT'] = '';
    }
     
    return $result;
  }
  
  public function getNewsName($id){
    $sql="select NAME
          from {$this->_name} 
          where NEWS_ID=?";
     
    return $this->_db->fetchOne($sql ,array($id));
  }
  
  public function getNewsId($catname){
    
    $sql="select NEWS_ID
          from {$this->_name} 
          where CATNAME = ?";
     
    return $this->_db->fetchOne($sql ,$catname);
  }
    
  public function getSiteMapNews(){
    $sql="select CATNAME
          from {$this->_name}
          where STATUS = 1
          order by DATA desc";
          
    return $this->_db->fetchCol($sql); 
  }
  
}
?>
