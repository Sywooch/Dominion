<?php
/**
* Модель БД импортера атрибутов
* 
* Syomik Dmitriy <dsemik@gmail.com>
*/
class AttributImportModel
{
    /**
    * Модель БД
    * 
    * @var AttributImportModel
    */
    private $_db;

    /**
    * Конструктор
    * 
    * @param SCMF $db
    * 
    * @return AttributImportModel
    */
    public function __construct(SCMF $db)
    {
        $this->_db = $db;
    }

    /**
    * put your comment there...
    * 
    * @param int $catalogId
    * 
    * @return array
    */
    public function getItems($catalogId)
    {
        $sql="select ITEM_ID
                   , CATALOGUE_ID
                   , CC_XML
              from ITEM
              where CC_XML is not NULL";

        if (!empty($catalogId)) {
            $sql.=' and CATALOGUE_ID = '.$catalogId;
        }

        return $this->_db->select($sql);
    }
    
    public function updateItem($itemId) 
    {
        $sql = "update ITEM
                set CC_XML = null
                where ITEM_ID = {$itemId}";

        $this->_db->execute($sql);
    }

    /**
    * Узнать ID группы атрибутов VIEW
    * 
    * @param string $name имя группы
    * 
    * @return string
    */
    public function getViewAttrGroupId($name)
    {
        $sql="select VIEW_ATTRIBUT_GROUP_ID
              from VIEW_ATTRIBUT_GROUP
              where NAME = '{$name}'";

        $viewAttrGroupId =  $this->_db->selectrow_array($sql);
        if (empty($viewAttrGroupId)) {
            $viewAttrGroupId = $this->insertViewAttrGroupId($name);
        }

        return $viewAttrGroupId;
    }

    /**
    * Доабвить группу атрибутов VIEW
    * 
    * @param mixed $name
    * 
    * @return int
    */
    public function insertViewAttrGroupId($name)
    {
        $_ordering = $this->_db->selectrow_array('select max(ORDERING) from VIEW_ATTRIBUT_GROUP');
        $_ordering++;
        $id = $this->_db->GetSequence('VIEW_ATTRIBUT_GROUP');
        $this->_db->execute('insert into VIEW_ATTRIBUT_GROUP (VIEW_ATTRIBUT_GROUP_ID,NAME,ORDERING) values (?,?,?)', $id, stripslashes($name), $_ordering);

        return $id;
    }

    /**
    * Получить имя категории товаров
    * 
    * @param string $catalogueId
    * 
    * @return string
    */
    public function getCatalogueName($catalogueId)
    {
        $sql="select NAME
              from CATALOGUE
              where CATALOGUE_ID = {$catalogueId}";

        return  $this->_db->selectrow_array($sql);
    }

    /**
    * Получить имя атрибута
    * 
    * @param string $attrId
    * 
    * @return string
    */
    public function getAttributeName($attrId)
    {
        $sql="select NAME
              from ATTRIBUT
              where ATTRIBUT_ID = {$attrId}";

        return  $this->_db->selectrow_array($sql);
    }

    /**
    * Узнать ID группы атрибутов
    * 
    * @param string $name имя группы
    * 
    * @return string
    */
    public function getAttrGroupId($name)
    {
        $sql="select ATTRIBUT_GROUP_ID
              from ATTRIBUT_GROUP
              where NAME = '{$name}'";

        $attrGroupId =  $this->_db->selectrow_array($sql);
        if (empty($attrGroupId)) {
            $attrGroupId = $this->insertAttrGroupId($name);
        }

        return $attrGroupId;
    }

    /**
    * Доабвить группу атрибутов
    * 
    * @param mixed $name
    * 
    * @return int
    */
    public function insertAttrGroupId($name)
    {
        $_ordering = $this->_db->selectrow_array('select max(ORDERING) from ATTRIBUT_GROUP');
        $_ordering++;
        $id = $this->_db->GetSequence('ATTRIBUT_GROUP');
        $this->_db->execute('insert into ATTRIBUT_GROUP (ATTRIBUT_GROUP_ID,NAME,ORDERING) values (?,?,?)', $id, stripslashes($name), $_ordering);

        return $id;
    }

    /**
    * Получить ID атрибута
    * 
    * @param string $name        имя атрибута
    * @param string $attrGroupId ID группы атриюутов
    * 
    * @return int
    */
    public function getAttrId($name, $attrGroupId)
    {
        $sql="select ATTRIBUT_ID
                   , TYPE
              from ATTRIBUT
              where NAME = '{$name}'
                and ATTRIBUT_GROUP_ID = {$attrGroupId}";

        return $this->_db->selectrow_array($sql);
    }

    /**
    * Добавить атрибут
    * 
    * @param array $data
    * 
    * @return int
    */
    public function insertAttr($data)
    {
        $_ordering = $this->_db->selectrow_array('select max(ORDERING) from ATTRIBUT where ATTRIBUT_GROUP_ID = '.$data['ATTRIBUT_GROUP_ID']);
        $_ordering++;
        $id = $this->_db->GetSequence('ATTRIBUT');
        $this->_db->execute('insert into ATTRIBUT (ATTRIBUT_ID,ATTRIBUT_GROUP_ID,NAME,VIEW_ATTRIBUT_GROUP_ID,ORDERING) values (?,?,?,?,?)', $id, $data['ATTRIBUT_GROUP_ID'], stripslashes($data['NAME']), $data['VIEW_ATTRIBUT_GROUP_ID'], $_ordering);

        return $id;
    }

    /**
    * Добавить значение во временную табшлицу
    * 
    * @param array $data
    */
    public function insertItemTemp($data)
    {
        $this->_db->execute('insert into ITEM_TEMP (ITEM_ID,ATTRIBUT_ID,VALUE) values (?,?,?)', $data['ITEM_ID'], $data['ATTRIBUT_ID'], $data['VALUE']);
    }

    public function Spravotchnik($id, $sql)
    {
        return $this->_db->Spravotchnik($id, $sql);
    }

    /**
    * put your comment there...
    * 
    * @param int $id ID атрибута
    * 
    * @return array
    */
    public function getAttributInfo($id)
    {
        $sql="select *
              from ATTRIBUT
              where ATTRIBUT_ID = {$id}
                and TYPE is not NULL";

        return $this->_db->select($sql);
    }

    /**
    * Проверяем есть ли у товара значения для джанного атрибута
    * 
    * @param mixed $table
    * @param mixed $attrData
    * 
    * @return boolean
    */
    public function isHasAttrValue($table, $attrData)
    {
        $sql = "select count(*)
                from {$table}
                where ITEM_ID = {$attrData['itemId']}
                  and ATTRIBUT_ID = {$attrData['attrId']}
                  and VALUE = '{$attrData['value']}'";

        $result = $this->_db->selectrow_array($sql);

        return !empty($result) ? true:false;
    }
    
    /**
    * Редактировать значение атрибута для товара
    * 
    * @param string $table    имя таблицы
    * @param array  $attrData данные
    */
    public function editAttr($table, $attrData)
    {
        $sql = "delete from {$table}
                where ITEM_ID = {$attrData['itemId']}
                  and ATTRIBUT_ID = {$attrData['attrId']}";

        $this->_db->execute($sql);

        if (mysql_error()) {
            echo $sql."<br>";
            echo mysql_error();
        }

        $sql = "insert into {$table}
                set VALUE = '{$attrData['value']}'
                   ,ITEM_ID = {$attrData['itemId']}
                   ,ATTRIBUT_ID = {$attrData['attrId']}";

        $this->_db->execute($sql);

        if (mysql_error()) {
            echo $sql."<br>";
            echo mysql_error();
        }
    }

    /**
    * Обновление атрибута
    * 
    * @param array $updateAttr
    */
    public function updateAttribut($updateAttr)
    {
        $sql = "update ATTRIBUT
                set ATTRIBUT_GROUP_ID = {$updateAttr['attrGrpId']}
                  , TYPE = {$updateAttr['type']}
                  , UNIT_ID = {$updateAttr['unitId']}
                  , VIEW_ATTRIBUT_GROUP_ID = {$updateAttr['viewAttrGrpId']}
                  , STATUS = 1
                where ATTRIBUT_ID = {$updateAttr['attrId']}";

        $this->_db->execute($sql);
    }

    /**
    * Взять данные из временной таблицы значений атрибутов
    * 
    * @param int $id
    * 
    * @return array
    */
    public function getItemTempData($id) 
    {
        $sql="select *
              from ITEM_TEMP
              where ATTRIBUT_ID = {$id}";

        return $this->_db->select($sql);
    }

    public function insertItemAttr($table, $attrData)
    {
        $sql = "insert into {$table}
                set ITEM_ID = '{$attrData['ITEM_ID']}'
                   ,ATTRIBUT_ID = '{$attrData['ATTRIBUT_ID']}'
                   ,VALUE = '{$attrData['VALUE']}'";

        $this->_db->execute($sql);
    }

    /**
    * put your comment there...
    * 
    * @param mixed $attrId
    * @param mixed $value
    */
    public function getAttrListId($attrId, $value)
    {
        $sql="select ATTRIBUT_LIST_ID
              from ATTRIBUT_LIST
              where NAME = '{$value}'
                and ATTRIBUT_ID = {$attrId}";

        $attrListId =  $this->_db->selectrow_array($sql);
        if (empty($attrListId)) {
            $attrListId = $this->insertAttrListId($value, $attrId);
        }

        return $attrListId;
    }

    /**
    * Доабвить группу атрибутов
    * 
    * @param mixed $name
    * 
    * @return int
    */
    public function insertAttrListId($value, $attrId)
    {
        $id = $this->_db->GetSequence('ATTRIBUT_LIST');

        $value = strip_tags($value);
        $value = addslashes($value);

        $sql = "insert into ATTRIBUT_LIST
                set ATTRIBUT_LIST_ID = {$id}
                   ,ATTRIBUT_ID = {$attrId}
                   ,NAME = '{$value}'";

        $this->_db->execute($sql);
        if (mysql_error()) {
            echo mysql_error()."<br>\r\n";
            echo $sql."<br>\r\n";
        }

        return $id;
    }

    public function insertAttrCatalogLink($attrCatLink)
    {
        $sql="select count(*) as cnt
              from ATTR_CATALOG_LINK
              where CATALOGUE_ID = {$attrCatLink['CATALOGUE_ID']}
                and ATTRIBUT_ID = {$attrCatLink['ATTRIBUT_ID']}";

        $result =  $this->_db->selectrow_array($sql);
        if (empty($result)) {
            $sql = "insert into ATTR_CATALOG_LINK
                    set CATALOGUE_ID = {$attrCatLink['CATALOGUE_ID']}
                       ,ATTRIBUT_ID = {$attrCatLink['ATTRIBUT_ID']}";

            $this->_db->execute($sql);
        }
    }

    
    public function deleteTempAttrValue($attrId)
    {
        $sql = "delete from ITEM_TEMP where ATTRIBUT_ID = {$attrId}";

        $this->_db->execute($sql);
    }
    
    public function getTempItemsAttrValues($attrId) 
    {
        $sql = 'select I.NAME
                      ,IT.VALUE
                from  ITEM_TEMP IT
                inner join ITEM I on (I.ITEM_ID = IT.ITEM_ID)
                where IT.ATTRIBUT_ID = '.$attrId;

        return $this->_db->select($sql);
    }

    public function getNotTypeAttr($catalogId)
    {
        $sql = 'select A.*
                from ATTRIBUT A
                inner join ATTR_CATALOG_LINK ACL on (ACL.ATTRIBUT_ID = A.ATTRIBUT_ID)
                where A.TYPE is null ';

        if (!empty($catalogId)) {
            $sql.=' and ACL.CATALOGUE_ID = '.$catalogId;
        }

        return $this->_db->select($sql);
    }

    public function setSequence()
    {
        $sql = 'select *
                from SEQUENCES';

        $sequence = $this->_db->select($sql);
        if (!empty($sequence)) {
            foreach ($sequence as $view) {
                $nameIdCol = $view['NAME'].'_ID';
                $nameTable = $view['NAME'];

                $sql = "select max({$nameIdCol})
                        from {$nameTable}";

                $maxId = $this->_db->selectrow_array($sql);
                if (!empty($maxId)) {
                    $sql = "update SEQUENCES
                            set ID = {$maxId}
                            where NAME = '{$nameTable}'";

                    $this->_db->execute($sql);
                }
            }
        }
    }
}
?>