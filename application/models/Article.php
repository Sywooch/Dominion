<?php
class models_Article extends ZendDBEntity{
  protected $_name = 'ARTICLE';
  
  public function getArticleIndexCount($amount){
    $sql="select count(*)
          from {$this->_name}
          where STATUS = 1
          order by DATA desc
          limit {$amount}";
          
    return $this->_db->fetchOne($sql); 
  }
  
  public function getArticleCount(){
    $sql="select count(*)
          from {$this->_name}
          where STATUS = 1";
          
    return $this->_db->fetchOne($sql); 
  }
  
  public function getArticleIndex($amount){
    $sql="select *
          from {$this->_name}
          where STATUS = 1
          order by DATA desc
          limit {$amount}";
          
    return $this->_db->fetchAll($sql); 
  }
  
  public function getArticleGroups($lang=0){
      if($lang > 0) $article_groups = $this->_db->fetchAll("select A.ARTICLE_GROUP_ID, B.NAME from {$this->_name}_GROUP A inner join ARTICLE_GROUP_LANGS B on B.ARTICLE_GROUP_ID=A.ARTICLE_GROUP_ID where B.CMF_LANG_ID=? order by B.NAME",$lang);
      else $article_groups = $this->_db->fetchAll("select ARTICLE_GROUP_ID, NAME from {$this->_name}_GROUP order by NAME");
      for($i=0;$i<sizeof($article_groups);$i++){
         $count = $this->_db->fetchOne("select COUNT(*) from {$this->_name} where ARTICLE_GROUP_ID=?",$article_groups[$i]['ARTICLE_GROUP_ID']);
         $article_groups[$i]['cnt'] = $count;
      }
      return $article_groups;
  }
  
  public function getArticles($startSelect, $pageSize, $lang_id = 0){
     
    $sql = "select ARTICLE_ID
                 , ARTICLE_GROUP_ID
                 , NAME
                 , CATNAME
                 , DATE_FORMAT(DATA,'%d.%m.%Y') as date
                 , DESCRIPTION
                 , IMAGE1 
            from {$this->_name} 
            where STATUS=1 
            order by ARTICLE_ID desc 
            limit {$startSelect}, {$pageSize}";
     
     return $this->_db->fetchAll($sql);
  }

  public function getArticleSingle($id){
    
    $sql="select ARTICLE_ID
               , NAME
               , DATE_FORMAT(DATA,'%d.%m.%y') as date 
          from {$this->_name} 
          where ARTICLE_ID=?";
          
    $result = $this->_db->fetchRow($sql ,$id);
          
    if($result){
      $xml = $this->_db->fetchOne("select XML from XMLS where TYPE=8 and XMLS_ID=?",$id);
      if($xml) $result['LONG_TEXT'] = $xml;
      else $result['LONG_TEXT'] = '';
    }
     
    return $result;          
  }
  
  public function getPopUpText($data, $id){
    $table = key($data);
    
    $sql="select LONG_TEXT
          from {$table} 
          where {$data[$table]}=?";
    
    return $this->_db->fetchOne($sql, array($id));
  }
  
  public function getSiteMapArticle(){
    $sql="select CATNAME
          from {$this->_name}
          where STATUS = 1
          order by DATA desc";
          
    return $this->_db->fetchCol($sql); 
  }
  
  public function getArticleName($id){
    $sql="select NAME
          from {$this->_name}
          where ARTICLE_ID = ?";
          
    return $this->_db->fetchOne($sql, $id); 
  }
  
  public function getArticleId($catname){
    
    $sql="select ARTICLE_ID
          from {$this->_name} 
          where CATNAME = ?";
     
    return $this->_db->fetchOne($sql ,$catname);
  }
}
?>