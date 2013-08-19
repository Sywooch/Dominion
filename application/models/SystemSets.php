<?php

/**
 * Получить параметро по имени
 */
class models_SystemSets extends ZendDBEntity
{
    protected $_name = 'SETINGS';

    public function getSettingValue($where)
    {
        $sql = "SELECT VALUE
          FROM {$this->_name}
          WHERE SYSTEM_NAME='{$where}'";

        return $this->_db->fetchOne($sql);
    }
}