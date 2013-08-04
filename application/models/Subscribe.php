<?php
class models_Subscribe extends ZendDBEntity
{
    protected $_name = 'MESSAGES';

    public function get_settings($param)
    {
        $sql = "SELECT VALUE
          FROM SETINGS
          WHERE SYSTEM_NAME = '{$param}'";

        return $this->_db->fetchOne($sql);
    }

    public function get_subscribe_tasks()
    {
        $sql = "SELECT MS.MESSAGES_ID
               , MS.NAME
               , MS.SUBSCRIBE_CLIENT_GROUP_ID
               , MS.TEXT
          FROM MESSAGES MS JOIN SUBSCRIBE_CLIENT_GROUP SCG USING (SUBSCRIBE_CLIENT_GROUP_ID)
          WHERE MS.STATE_ =0
            AND MS.ACTION = 0";

        return $this->_db->fetchAll($sql);
    }

    public function get_subscribe_users($messages_id, $subscribe_limit)
    {
        $sql = "SELECT if(L.SURNAME IS null, L.NAME, concat(L.SURNAME,' ',L.NAME)) AS NAME
               , L.EMAIL
               , L.USER_ID
          FROM SHOPUSER L
          JOIN MESSAGES_SHOPUSER ML ON (ML.USER_ID = L.USER_ID)
          WHERE ML.MESSAGES_ID = {$messages_id}
            AND ML.WAS_SEND = 0
          LIMIT {$subscribe_limit}";

        return $this->_db->fetchAll($sql);
    }

    public function update_messages($mess_id)
    {
        $sql = "UPDATE MESSAGES
          SET STATE_= 1
            , ACTION = 1
          WHERE MESSAGES_ID={$mess_id}";

        $this->_db->query($sql);
    }

    public function update_user($messages_id, $listener_id)
    {
        $sql = "UPDATE MESSAGES_SHOPUSER
          SET WAS_SEND = 1
          WHERE MESSAGES_ID = {$messages_id}
            AND USER_ID = {$listener_id}";

        $this->_db->query($sql);
    }

}

?>
