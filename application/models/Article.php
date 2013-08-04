<?php
class models_Article extends ZendDBEntity
{
    protected $_name = 'ARTICLE';

    public function getArticleIndexCount($amount)
    {
        $sql = "SELECT count(*)
          FROM {$this->_name}
          WHERE STATUS = 1
          ORDER BY DATA DESC
          LIMIT {$amount}";

        return $this->_db->fetchOne($sql);
    }

    public function getArticleCount()
    {
        $sql = "SELECT count(*)
          FROM {$this->_name}
          WHERE STATUS = 1";

        return $this->_db->fetchOne($sql);
    }

    public function getArticleIndex($amount)
    {
        $sql = "SELECT *
          FROM {$this->_name}
          WHERE STATUS = 1
          ORDER BY DATA DESC
          LIMIT {$amount}";

        return $this->_db->fetchAll($sql);
    }

    public function getArticleGroups($lang = 0)
    {
        if ($lang > 0) $article_groups = $this->_db->fetchAll("SELECT A.ARTICLE_GROUP_ID, B.NAME FROM {$this->_name}_GROUP A INNER JOIN ARTICLE_GROUP_LANGS B ON B.ARTICLE_GROUP_ID=A.ARTICLE_GROUP_ID WHERE B.CMF_LANG_ID=? ORDER BY B.NAME", $lang);
        else $article_groups = $this->_db->fetchAll("SELECT ARTICLE_GROUP_ID, NAME FROM {$this->_name}_GROUP ORDER BY NAME");
        for ($i = 0; $i < sizeof($article_groups); $i++) {
            $count = $this->_db->fetchOne("SELECT COUNT(*) FROM {$this->_name} WHERE ARTICLE_GROUP_ID=?", $article_groups[$i]['ARTICLE_GROUP_ID']);
            $article_groups[$i]['cnt'] = $count;
        }

        return $article_groups;
    }

    public function getArticles($startSelect, $pageSize, $lang_id = 0)
    {

        $sql = "SELECT ARTICLE_ID
                 , ARTICLE_GROUP_ID
                 , NAME
                 , CATNAME
                 , DATE_FORMAT(DATA,'%d.%m.%Y') AS date
                 , DESCRIPTION
                 , IMAGE1 
            FROM {$this->_name}
            WHERE STATUS=1
            ORDER BY ARTICLE_ID DESC
            LIMIT {$startSelect}, {$pageSize}";

        return $this->_db->fetchAll($sql);
    }

    public function getArticleSingle($id)
    {

        $sql = "SELECT ARTICLE_ID
               , NAME
               , DATE_FORMAT(DATA,'%d.%m.%y') AS date
          FROM {$this->_name}
          WHERE ARTICLE_ID=?";

        $result = $this->_db->fetchRow($sql, $id);

        if ($result) {
            $xml = $this->_db->fetchOne("SELECT XML FROM XMLS WHERE TYPE=8 AND XMLS_ID=?", $id);
            if ($xml) $result['LONG_TEXT'] = $xml;
            else $result['LONG_TEXT'] = '';
        }

        return $result;
    }

    public function getPopUpText($data, $id)
    {
        $table = key($data);

        $sql = "SELECT LONG_TEXT
          FROM {$table}
          WHERE {$data[$table]}=?";

        return $this->_db->fetchOne($sql, array($id));
    }

    public function getSiteMapArticle()
    {
        $sql = "SELECT CATNAME
          FROM {$this->_name}
          WHERE STATUS = 1
          ORDER BY DATA DESC";

        return $this->_db->fetchCol($sql);
    }

    public function getArticleName($id)
    {
        $sql = "SELECT NAME
          FROM {$this->_name}
          WHERE ARTICLE_ID = ?";

        return $this->_db->fetchOne($sql, $id);
    }

    public function getArticleId($catname)
    {

        $sql = "SELECT ARTICLE_ID
          FROM {$this->_name}
          WHERE CATNAME = ?";

        return $this->_db->fetchOne($sql, $catname);
    }
}

?>