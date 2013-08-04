<?php
class models_News extends ZendDBEntity
{
    protected $_name = 'NEWS';

    public function getNewsIndexCount($amount)
    {
        $sql = "SELECT count(*)
          FROM {$this->_name}
          WHERE STATUS = 1
          ORDER BY DATA DESC
          LIMIT {$amount}";

        return $this->_db->fetchOne($sql);
    }

    public function getNewsCount()
    {
        $sql = "SELECT count(*)
          FROM {$this->_name}
          WHERE STATUS = 1";

        return $this->_db->fetchOne($sql);
    }

    public function getNewsIndex($amount)
    {
        $sql = "SELECT *
          FROM {$this->_name}
          WHERE STATUS = 1
          ORDER BY DATA DESC
          LIMIT {$amount}";

        return $this->_db->fetchAll($sql);
    }

    public function getNews($startSelect, $pageSize)
    {
        $sql = "SELECT  NEWS_ID
                  , NAME
                  , CATNAME
                  , DATE_FORMAT(DATA,'%d.%m.%Y') AS date
                  , DESCRIPTION
                  , IMAGE1 
            FROM {$this->_name}
            WHERE STATUS=1
            ORDER BY NEWS_ID DESC
            LIMIT {$startSelect}, {$pageSize}";

        return $this->_db->fetchAll($sql);
    }

    public function getNewsSingle($id)
    {
        $sql = "SELECT NEWS_ID
               , NAME
               , DATE_FORMAT(DATA,'%d.%m.%y') AS date
          FROM {$this->_name}
          WHERE NEWS_ID=?";

        $result = $this->_db->fetchRow($sql, $id);

        if ($result) {
            $xml = $this->_db->fetchOne("SELECT XML FROM XMLS WHERE TYPE=1 AND XMLS_ID=?", $id);
            if ($xml) $result['LONG_TEXT'] = $xml;
            else $result['LONG_TEXT'] = '';
        }

        return $result;
    }

    public function getNewsName($id)
    {
        $sql = "SELECT NAME
          FROM {$this->_name}
          WHERE NEWS_ID=?";

        return $this->_db->fetchOne($sql, array($id));
    }

    public function getNewsId($catname)
    {

        $sql = "SELECT NEWS_ID
          FROM {$this->_name}
          WHERE CATNAME = ?";

        return $this->_db->fetchOne($sql, $catname);
    }

    public function getSiteMapNews()
    {
        $sql = "SELECT CATNAME
          FROM {$this->_name}
          WHERE STATUS = 1
          ORDER BY DATA DESC";

        return $this->_db->fetchCol($sql);
    }

}

?>
