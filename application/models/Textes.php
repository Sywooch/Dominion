<?php
class models_Textes extends ZendDBEntity{
  protected $_name = 'TEXTES';
  
  public function getTextes($file_name, $lang = 0){
    
    if($lang > 0){
      $sql="select b.DESCRIPTION
            from {$this->_name} a inner join {$this->_name}_LANG b ON (b.other_id=a.id)
            where a.SYS_NAME = '{$file_name}'
              AND b.lang_id = {$lang}";  
    }
    else{
      $sql="select DESCRIPTION
            from {$this->_name}
            where SYS_NAME = '{$file_name}'";  
    }
          
    return  $this->_db->fetchOne($sql);
  }
   
}
?>
