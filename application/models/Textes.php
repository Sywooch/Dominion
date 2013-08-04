<?php
class models_Textes extends ZendDBEntity
{
    protected $_name = 'TEXTES';

    public function getTextes($file_name, $lang = 0)
    {

        if ($lang > 0) {
            $sql = "SELECT b.DESCRIPTION
            FROM {$this->_name} a INNER JOIN {$this->_name}_LANG b ON (b.other_id=a.id)
            WHERE a.SYS_NAME = '{$file_name}'
              AND b.lang_id = {$lang}";
        } else {
            $sql = "SELECT DESCRIPTION
            FROM {$this->_name}
            WHERE SYS_NAME = '{$file_name}'";
        }

        return $this->_db->fetchOne($sql);
    }

}

?>
