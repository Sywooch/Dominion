<?php

/**
 * Получить параметро по имени
 */
class models_SystemSets extends ZendDBEntity
{
    protected $_name = 'SETINGS';

    public function getSettingValue($where)
    {
        $sql = "select VALUE
          from {$this->_name}  
          where SYSTEM_NAME='{$where}'";

        return $this->_db->fetchOne($sql);
    }
}