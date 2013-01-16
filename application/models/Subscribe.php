<?php
class models_Subscribe extends ZendDBEntity{
  protected $_name = 'MESSAGES';
  
  public function get_settings($param){
    $sql="select VALUE
          from SETINGS
          where SYSTEM_NAME = '{$param}'"; 
    
    return  $this->_db->fetchOne($sql);
  }
  
  public function get_subscribe_tasks(){
    $sql="select MS.MESSAGES_ID
               , MS.NAME
               , MS.SUBSCRIBE_CLIENT_GROUP_ID
               , MS.TEXT
          from MESSAGES MS join SUBSCRIBE_CLIENT_GROUP SCG using (SUBSCRIBE_CLIENT_GROUP_ID)      
          where MS.STATE_ =0
            and MS.ACTION = 0"; 
    
    return  $this->_db->fetchAll($sql);
  }
  
  public function get_subscribe_users($messages_id, $subscribe_limit){
    $sql="select if(L.SURNAME is null, L.NAME, concat(L.SURNAME,' ',L.NAME)) as NAME
               , L.EMAIL
               , L.USER_ID
          from SHOPUSER L
          join MESSAGES_SHOPUSER ML on (ML.USER_ID = L.USER_ID)
          where ML.MESSAGES_ID = {$messages_id}
            and ML.WAS_SEND = 0
          limit {$subscribe_limit}";
          
    return  $this->_db->fetchAll($sql);
  }
  
  public function update_messages($mess_id){
    $sql="update MESSAGES
          set STATE_= 1
            , ACTION = 1
          where MESSAGES_ID={$mess_id}";

    $this->_db->query($sql);
  }
  
  public function update_user($messages_id, $listener_id){
    $sql="update MESSAGES_SHOPUSER
          set WAS_SEND = 1
          where MESSAGES_ID = {$messages_id}
            and USER_ID = {$listener_id}";

    $this->_db->query($sql);
  }
   
}
?>
