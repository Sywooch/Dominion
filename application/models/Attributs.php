<?php

class models_Attributs extends ZendDBEntity
{
    protected $_name = 'ATTRIBUT';

    public function getAttributes($catalogueId = 0, $tableName = 'ATTR_CATALOG_LINK')
    {
        $exclude = '';
//     if (!empty($at)){
//       preg_match_all('/a(\d+)v/', $at, $match);
//       $ff = implode(",", $match[1]);
//       $exclude.= " and ACV.ATTRIBUT_ID not in ($ff) ";
//     }

        $sql = "SELECT DISTINCT A. * , U.NAME AS U_NAME
            FROM ATTRIBUT A                 
                 JOIN {$tableName} ACV USING (ATTRIBUT_ID)
                 JOIN CATALOGUE C USING (CATALOGUE_ID)
                 JOIN CAT_ITEM CA USING (CATALOGUE_ID)
                 JOIN ITEM I USING (ITEM_ID)
                 JOIN ITEM0 I0 USING (ITEM_ID)
                 LEFT JOIN UNIT U ON (U.UNIT_ID=A.UNIT_ID)
            WHERE C.CATALOGUE_ID = {$catalogueId}
                  {$exclude}
              AND A.STATUS = 1
              AND I.STATUS = 1
              AND C.STATUS = 1
              AND I0.ATTRIBUT_ID = A.ATTRIBUT_ID
              AND (A.TYPE = 3 OR
                   A.TYPE = 6 OR
                  (A.TYPE = 0 AND
                  A.IS_RANGEABLE = 1) OR
                  (A.TYPE = 1 AND
                  A.IS_RANGEABLE = 1))
             ORDER BY  A.ORDERING ";

        return $this->_db->fetchAll($sql);
    }

    /**
     * Get attributes is range view
     *
     * @param integer $catalogueId
     * @return array
     */
    public function getAttributesIsRangeView($catalogueId)
    {
        $sql = "SELECT
        A.ATTRIBUT_ID,
        AL.NAME
      FROM  ITEM I
        JOIN ITEM0 I1 USING (ITEM_ID)
        JOIN ATTRIBUT A USING (ATTRIBUT_ID)
        JOIN ATTRIBUT_LIST AL USING (ATTRIBUT_ID)
      WHERE I1.VALUE = AL.ATTRIBUT_LIST_ID
      AND I.CATALOGUE_ID = {$catalogueId}
      AND A.IS_RANGE_VIEW = 1
    union
    SELECT
        A.ATTRIBUT_ID,
        AL.NAME
      FROM ITEM I
        JOIN ITEM2 I2 USING (ITEM_ID)
        JOIN ATTRIBUT A USING (ATTRIBUT_ID)
        JOIN ATTRIBUT_LIST AL USING (ATTRIBUT_ID)
      WHERE  I.CATALOGUE_ID = {$catalogueId} AND I2.VALUE = AL.ATTRIBUT_LIST_ID
      AND A.IS_RANGE_VIEW = 1
      UNION
      SELECT
        A.ATTRIBUT_ID,
        AL.NAME
      FROM  ITEM I
        JOIN ITEM0 I0 USING (ITEM_ID)
        JOIN ATTRIBUT A USING (ATTRIBUT_ID)
        JOIN ATTRIBUT_LIST AL USING (ATTRIBUT_ID)
      WHERE I0.VALUE = AL.ATTRIBUT_LIST_ID
      AND I.CATALOGUE_ID = {$catalogueId}
      AND A.IS_RANGE_VIEW = 1
";

        return $this->_db->fetchAll($sql);
    }

    public function getItemsAttributes($id, $tableName = 'ATTR_CATALOG_LINK')
    {
        $sql = "SELECT DISTINCT A.*
                  ,U.NAME AS U_NAME
            FROM ATTRIBUT A
            JOIN {$tableName} ACV USING (ATTRIBUT_ID)
            JOIN CATALOGUE C USING (CATALOGUE_ID)
            JOIN CAT_ITEM CA USING (CATALOGUE_ID)
            JOIN ITEM I USING (ITEM_ID)
            JOIN ITEM0 I0 USING (ITEM_ID)
            LEFT JOIN UNIT U ON (U.UNIT_ID=A.UNIT_ID)
            WHERE I.ITEM_ID = {$id}
              AND A.STATUS=1
             AND (A.TYPE = 3 OR
                 A.TYPE = 6 OR
                (A.TYPE = 0 AND
                A.IS_RANGEABLE = 1) OR
                (A.TYPE = 1 AND
                A.IS_RANGEABLE = 1))
            ORDER BY A.ORDERING";

        return $this->_db->fetchAll($sql);
    }

    public function getAttrXml($id, $iid)
    {

        $sql = "SELECT A.NAME AS A_NAME
                     ,U.NAME AS U_NAME
                     ,A.TYPE
                     ,A.TITLE
                     ,A.IS_RANGEABLE
               FROM ATTRIBUT A
               LEFT JOIN UNIT U ON (A.UNIT_ID=U.UNIT_ID)
               WHERE A.ATTRIBUT_ID=?";

        $attr_info = $this->_db->fetchRow($sql, array($id));


        if ($attr_info['TYPE'] == 3) {
            $attr_info['RNAME'] = $this->_db->fetchOne("select NAME from ATTRIBUT_LIST where ATTRIBUT_ID='" . $id . "' and ATTRIBUT_LIST_ID='" . $iid . "'");
        } else {
            $attr_info['RNAME'] = $this->_db->fetchOne("select ALTER_VALUE from ATTRIBUT where ATTRIBUT_ID='" . $id . "'");
            if ($attr_info['RNAME'] == '') $attr_info['RNAME'] = 1;
        }

        return $attr_info;
    }

    public function getDopparam($attribut_id, $item_id)
    {
        $sql = "SELECT TYPE,
                     IS_RANGEABLE 
              FROM ATTRIBUT
              WHERE STATUS=1
                AND ATTRIBUT_ID=?";

        $row = $this->_db->fetchRow($sql, $attribut_id);
        $TYPE = $row['TYPE'];
        $IS_RANGEABLE = $row['IS_RANGEABLE'];

        $sel = '';
        if (($TYPE == 5) || ($TYPE == 6)) $sel = 'attr5';
        elseif ($TYPE == 2) {
            $TABLE = 'ATTRIBUT_LIST';
            $sel = 'attrl';
        } elseif ($TYPE == 3) {
            $TABLE = 'ATTRIBUT_LIST';
            $sel = 'attr';
        } elseif ($TYPE == 0 && !$IS_RANGEABLE) {
            $TABLE = 'ATTRIBUT_LIST';
            $sel = 'attr0';
        } elseif ($TYPE == 1 && !$IS_RANGEABLE) {
            $TABLE = 'ATTRIBUT_LIST';
            $sel = 'attr1';
        } elseif ($TYPE < 2 && $IS_RANGEABLE) {
            $TABLE = 'RANGE_LIST';
            $sel = 'attrr';
        }

        $_item_id = implode(', ', $item_id);

        $this->_db->query('DROP TABLE IF EXISTS t100');
        if ($sel == 'attr') {
            $this->_db->query('create temporary table t100 select distinct R.VALUE from ITEM0 R where R.ATTRIBUT_ID=' . $attribut_id . ' and R.VALUE<>"" and R.ITEM_ID in (' . $_item_id . ') group by R.VALUE');
        } elseif ($sel == 'attrr') {
            $this->_db->query('create temporary table t100 select R.RANGE_LIST_ID from ITEMR R where R.ATTRIBUT_ID=' . $attribut_id . ' and R.ITEM_ID in (' . $_item_id . ') group by R.RANGE_LIST_ID');
        } elseif ($sel == 'attr0') {
            $this->_db->query('create temporary table t100 select distinct R.VALUE from ITEM0 where R.ATTRIBUT_ID=' . $attribut_id . ' and R.VALUE<>"" and R.ITEM_ID in (' . $_item_id . ') group by R.VALUE');
        } elseif ($sel == 'attrl') {
            $this->_db->query('create temporary table t100 select R.VALUE from ITEM2 R where R.ATTRIBUT_ID=' . $attribut_id . ' and R.VALUE<>"" and R.ITEM_ID in (' . $_item_id . ') group by R.VALUE');
        } elseif ($sel == 'attr5') {
            $this->_db->query('create temporary table t100 select distinct R.VALUE from ITEM0 R where R.ATTRIBUT_ID=' . $attribut_id . ' and R.ITEM_ID in (' . $_item_id . ')');
        }

        $SELS = array(
            'attrr' => "SELECT R.RANGE_LIST_ID AS id,R.NAME AS val FROM RANGE_LIST R INNER JOIN t100 S ON (S.RANGE_LIST_ID=R.RANGE_LIST_ID) ORDER BY R.NAME",
            'attr' => "select R.ATTRIBUT_LIST_ID as id,R.NAME as val from ATTRIBUT_LIST R inner join t100 S on (S.VALUE=R.ATTRIBUT_LIST_ID) where R.ATTRIBUT_ID='" . $attribut_id . "' order by R.NAME",
            'attr0' => "SELECT VALUE AS id,VALUE AS val FROM t100 ORDER BY VALUE",
            'attrl' => "select R.ATTRIBUT_LIST_ID as id,S.VALUE as val from ATTRIBUT_LIST R inner join t100 S on (S.VALUE=R.NAME) where R.ATTRIBUT_ID='" . $attribut_id . "' order by S.VALUE",
            'attr5' => "SELECT VALUE AS id,VALUE AS val FROM t100 ORDER BY VALUE DESC"
        );


        $sql = $SELS[$sel];

        return $this->_db->fetchAll($sql);
    }

    public function getRangeValues($id)
    {
        $sql = "SELECT MIN
                , MAX 
           FROM RANGE_LIST
           WHERE RANGE_LIST_ID = {$id}";

        return $this->_db->fetchRow($sql);
    }

    public function getAttributInfo($id)
    {
        $sql = "SELECT *
           FROM ATTRIBUT
           WHERE ATTRIBUT_ID = ?";

        return $this->_db->fetchRow($sql, $id);
    }

    public function getRangeName($id)
    {
        $sql = "SELECT NAME
           FROM RANGE_LIST
           WHERE RANGE_LIST_ID = {$id}";

        return $this->_db->fetchOne($sql);
    }

    function getAttributByCode($code)
    {
        $sql = "SELECT ATTRIBUT_ID
           FROM ATTRIBUT
           WHERE ID_FROM_VBD = ?";

        $ff = $this->_db->fetchOne($sql, $code);

//     $profiler = $this->_db->getProfiler();
//     $query = $profiler->getLastQueryProfile();
//     echo $query->getQuery()."\r\n";

        return $ff;
    }

    function getAttributByName($name, $type)
    {
        $sql = "SELECT ATTRIBUT_ID
           FROM ATTRIBUT
           WHERE NAME = '{$name}'
             AND TYPE = {$type}";

        return $this->_db->fetchOne($sql);
    }

    function getUniteByName($name)
    {
        $sql = "SELECT UNIT_ID
           FROM UNIT
           WHERE NAME = '{$name}'";

        return $this->_db->fetchOne($sql);
    }

    function hasAttributList($name, $attribute_id)
    {
        $sql = "SELECT ATTRIBUT_LIST_ID
           FROM ATTRIBUT_LIST
           WHERE NAME = '{$name}'
             AND ATTRIBUT_ID = {$attribute_id}";

        return $this->_db->fetchOne($sql);
    }

    function insertAttributList($data)
    {
        $this->_db->insert('ATTRIBUT_LIST', $data);

        return $this->_db->lastInsertId();
    }

    function insertAttribut($data)
    {
        $sql = "INSERT INTO ATTRIBUT
           SET ID_FROM_VBD = {$data['ID_FROM_VBD']}
              ,NAME = '{$data['NAME']}'
              ,ATTRIBUT_GROUP_ID = {$data['ATTRIBUT_GROUP_ID']}
              ,TYPE = {$data['TYPE']}
              ,UNIT_ID = {$data['UNIT_ID']}
              ,STATUS = 1";

//     echo $sql." \r\n <br>";

        $this->_db->query($sql);

//     $this->_db->insert('ATTRIBUT', $data);

        return $this->_db->lastInsertId();
    }

    public function updateAttribut($data, $uid)
    {
        $sql = "UPDATE ATTRIBUT
           SET ID_FROM_VBD = {$data['ID_FROM_VBD']}
              ,NAME = '{$data['NAME']}'
              ,TYPE = {$data['TYPE']}
           WHERE ATTRIBUT_ID={$uid}";


//     echo $sql." \r\n <br>";

        $this->_db->query($sql);

//     $this->_db->update('ATTRIBUT', $data, 'ATTRIBUT_ID='.$uid);
    }

    public function getMaxId()
    {
        $sql = "SELECT max(ATTRIBUT_ID) FROM ATTRIBUT";

        return $this->_db->fetchOne($sql);
    }

    public function getAttributType($id)
    {
        $sql = "SELECT TYPE FROM ATTRIBUT WHERE ATTRIBUT_ID = ?";

        return $this->_db->fetchOne($sql, $id);
    }

    public function getMinAttrRange($params)
    {
        $attrType = $this->getAttributType($params['attribut_id']);
        $value = null;
        switch ($attrType) {
            case 0: // 0-Int
                $sql = 'select VALUE
                  from ITEM0 
                  where ITEM_ID in (' . implode(',', $params['items']) . ')
                    and ATTRIBUT_ID = ' . $params['attribut_id'];

                if (!empty($params['min']))
                    $sql .= ' and ' . $params['min'] . ' <= VALUE';

                if (!empty($params['max']))
                    $sql .= ' and ' . $params['max'] . ' >= VALUE';

                $sql .= ' order by VALUE asc limit 1';

                $_result = $this->_db->fetchRow($sql);
                $value = $_result['VALUE'];
                break;

            case 1: // 1-double
                $sql = 'select VALUE
                  from ITEM1 
                  where ITEM_ID in (' . implode(',', $params['items']) . ')
                    and ATTRIBUT_ID = ' . $params['attribut_id'];

                if (!empty($params['min']))
                    $sql .= ' and ' . $params['min'] . ' <= VALUE';

                if (!empty($params['max']))
                    $sql .= ' and ' . $params['max'] . ' >= VALUE';

                $sql .= ' order by VALUE asc limit 1';

                $_result = $this->_db->fetchRow($sql);
                $value = $_result['VALUE'];
                break;

            case 3: // 3-список
                $sql = 'select I.VALUE
                  from ITEM0 I
                     , ATTRIBUT_LIST A 
                  where I.ITEM_ID in (' . implode(',', $params['items']) . ')
                    and I.VALUE = A.ATTRIBUT_LIST_ID
                    and I.ATTRIBUT_ID = ' . $params['attribut_id'];

                if (!empty($params['min']))
                    $sql .= ' and ' . $params['min'] . ' <= A.NAME';

                if (!empty($params['max']))
                    $sql .= ' and ' . $params['max'] . ' >= A.NAME';

                $sql .= ' order by A.NAME asc limit 1';

                $_result = $this->_db->fetchRow($sql);
                $value = $_result['VALUE'];
                break;
        }

        return $value;
    }

    public function getMaxAttrRange($params)
    {
        $attrType = $this->getAttributType($params['attribut_id']);
        $value = null;
        switch ($attrType) {
            case 0: // 0-Int
                $sql = 'select VALUE
                  from ITEM0 
                  where ITEM_ID in (' . implode(',', $params['items']) . ')
                    and ATTRIBUT_ID = ' . $params['attribut_id'];

                if (!empty($params['min']))
                    $sql .= ' and ' . $params['min'] . ' <= VALUE';

                if (!empty($params['max']))
                    $sql .= ' and ' . $params['max'] . ' >= VALUE';

                $sql .= ' order by VALUE desc limit 1';

                $_result = $this->_db->fetchRow($sql);
                $value = $_result['VALUE'];
                break;

            case 1: // 1-double
                $sql = 'select VALUE
                  from ITEM1 
                  where ITEM_ID in (' . implode(',', $params['items']) . ')
                    and ATTRIBUT_ID = ' . $params['attribut_id'];

                if (!empty($params['min']))
                    $sql .= ' and ' . $params['min'] . ' <= VALUE';

                if (!empty($params['max']))
                    $sql .= ' and ' . $params['max'] . ' >= VALUE';

                $sql .= ' order by VALUE desc limit 1';

                $_result = $this->_db->fetchRow($sql);
                $value = $_result['VALUE'];
                break;

            case 3: // 3-список
                $sql = 'select I.VALUE
                  from ITEM0 I
                     , ATTRIBUT_LIST A 
                  where I.ITEM_ID in (' . implode(',', $params['items']) . ')
                    and I.VALUE = A.ATTRIBUT_LIST_ID
                    and I.ATTRIBUT_ID = ' . $params['attribut_id'];

                if (!empty($params['min']))
                    $sql .= ' and ' . $params['min'] . ' <= A.NAME';

                if (!empty($params['max']))
                    $sql .= ' and ' . $params['max'] . ' >= A.NAME';

                $sql .= ' order by A.NAME desc limit 1';

                $_result = $this->_db->fetchRow($sql);
                $value = $_result['VALUE'];
                break;
        }

        return $value;
    }

    public function _getAllRangeForAttr($params)
    {
        $attrType = $this->getAttributType($params['attribut_id']);
        $value = array();
        switch ($attrType) {
            case 0: // 0-Int
                $sql = 'select VALUE
                  from ITEM0 
                  where ITEM_ID in (' . implode(',', $params['items']) . ')
                    and ATTRIBUT_ID = ' . $params['attribut_id'];

                if (!empty($params['min']))
                    $sql .= ' and ' . $params['min'] . ' <= VALUE';

                if (!empty($params['max']))
                    $sql .= ' and ' . $params['max'] . ' >= VALUE';

                break;

            case 1: // 1-double
                $sql = 'select VALUE
                  from ITEM1 
                  where ITEM_ID in (' . implode(',', $params['items']) . ')
                    and ATTRIBUT_ID = ' . $params['attribut_id'];

                if (!empty($params['min']))
                    $sql .= ' and ' . $params['min'] . ' <= VALUE';

                if (!empty($params['max']))
                    $sql .= ' and ' . $params['max'] . ' >= VALUE';

                break;

            case 3: // 3-список
                $sql = "SELECT NAME
                FROM ATTRIBUT_LIST
                WHERE ATTRIBUT_LIST_ID = ?";

                $_min = $this->_db->fetchOne($sql, $params['min']);
                $_max = $this->_db->fetchOne($sql, $params['max']);


                $sql = 'select distinct I.VALUE
                  from ITEM0 I
                     , ATTRIBUT_LIST A 
                  where I.ITEM_ID in (' . implode(',', $params['items']) . ')
                    and I.VALUE = A.ATTRIBUT_LIST_ID
                    and I.ATTRIBUT_ID = ' . $params['attribut_id'];

                if (!empty($_min))
                    $sql .= ' and ' . $_min . ' <= A.NAME';

                if (!empty($_max))
                    $sql .= ' and ' . $_max . ' >= A.NAME';

                break;
        }

        return $this->_db->fetchCol($sql);
    }

    public function getAllRangeForAttr($attr_val, $items)
    {
        $att_id = 0;
        if (preg_match('/(\d+)v(.+)/', $attr_val, $m)) {
            $att_id = $m[1];
            $_val = explode('-', $m[2]);

            $params['min'] = $_val[0];
            $params['max'] = $_val[1];
            $params['items'] = $items;
            $params['attribut_id'] = $att_id;

            $values = $this->_getAllRangeForAttr($params);
        }

        return array($att_id, $values);
    }

    public function getAllAttrForSelection($params)
    {
        $attr_array = explode('a', $params['at']);
        foreach ($attr_array as $attr_val) {
            if (strpos($attr_val, '-') !== false) {
                list($att_id, $values) = $this->getAllRangeForAttr($attr_val, $params['items']);
                $attr[$att_id] = $values;
            } else {
                if (preg_match('/(\w+)v(\w+)/', $attr_val, $m)) {
                    $attr[$m[1]][] = $m[2];
                }
            }
        }
        ksort($attr);

        return $attr;
    }
}