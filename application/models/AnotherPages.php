<?php

class models_AnotherPages extends ZendDBEntity
{

    protected $_name = 'ANOTHER_PAGES';

    public function insertSomeData($table, $data)
    {
        $this->_db->insert($table, $data);

        return $this->_db->lastInsertId();
    }

    public function updateSomeData($table, $data, $uid)
    {
        $this->_db->update($table, $data, 'id=' . $uid);
    }

    public function getTree($parentId = 0)
    {
        $sql = 'SELECT ANOTHER_PAGES_ID,
                      PARENT_ID,
                      NAME,
                      CATNAME,
                      REALCATNAME,
                      URL,
                      IS_NEW_WIN,
                      SHOW_NEAR_CATALOGUE_MENU
               FROM ANOTHER_PAGES
               WHERE PARENT_ID=?
                 AND STATUS=1
               ORDER BY ORDER_ ASC';

        $menu = $this->_db->fetchAll($sql, $parentId);

        for ($i = 0; $i < count($menu); $i++) {
            $children = $this->getTree($menu[$i]['ANOTHER_PAGES_ID']);

            if ($children) {
                $menu[$i]['menu_children'] = $children;
            }
        }

        return $menu;
    }

    public function getPageTemplate($id)
    {
        if (!is_numeric($id)) {
            $pid = $this->getDocId($id);
        } else {
            $pid = $id;
        }

        $template = $this->_db->fetchOne("select TEMPLATE from ANOTHER_PAGES where ANOTHER_PAGES_ID = '" . $pid . "'");

        list($name, $ext) = explode(".", $template);

        return $name;
    }

    public function getDocId($id)
    {
        $docId = $this->_db->fetchOne("select ANOTHER_PAGES_ID from ANOTHER_PAGES where REALCATNAME = '/" . $id . "/'");

        return $docId;
    }

    public function getPageId($id)
    {
        $docId = $this->_db->fetchOne("select ANOTHER_PAGES_ID from ANOTHER_PAGES where URL LIKE '%" . $id . "%'");

        return $docId;
    }

    public function getDocByUrl($id)
    {
        return $this->_db->fetchOne("select ANOTHER_PAGES_ID from ANOTHER_PAGES where URL = '" . $id . "'");
    }

    public function getDocName($id)
    {
        return $this->_db->fetchOne(
            "SELECT NAME FROM ANOTHER_PAGES WHERE ANOTHER_PAGES_ID = ?",
            $id
        );
    }

    public function getDocInfo($id)
    {
        $sql = "SELECT ANOTHER_PAGES_ID,
                        PARENT_ID,
                        NAME,
                        REALCATNAME,
                        URL,
                        TITLE,
                        DESCRIPTION,
                        KEYWORDS
                 FROM ANOTHER_PAGES
                 WHERE ANOTHER_PAGES_ID=?";

        return $this->_db->fetchRow($sql, $id);
    }

    public function getDocMetaInfo($id)
    {
        $sql = "SELECT TITLE,
                        DESCRIPTION,
                        KEYWORDS
                 FROM ANOTHER_PAGES
                 WHERE ANOTHER_PAGES_ID=?";

        return $this->_db->fetchRow($sql, $id);
    }

    public function getPageInfo($id, $lang)
    {
        $sql = "select ANOTHER_PAGES_ID,
                      PARENT_ID,
                      NAME,
                      REALCATNAME,
                      URL,
                      TITLE,
                      DESCRIPTION,
                      KEYWORDS
               from ANOTHER_PAGES
               where URL LIKE '%" . $id . "%'";

        $info = $this->_db->fetchRow($sql);

        //XML
        $xml = $this->_db->fetchOne(
            "SELECT XML FROM XMLS WHERE TYPE=0 AND XMLS_ID=?",
            array($id)
        );

        if ($xml) {
            $xml = str_replace("../images", "/images", $xml);
            $info['text'] = $xml;
        } else {
            $info['text'] = '';
        }

        return $info;
    }

    public function getPath($id)
    {
        $path = array();
        if ($id == 0) {
            return $path;
        }
        $parents = $this->getParents($id);
        $parents = array_reverse($parents);
        if ($parents) {
            for ($i = 0; $i < sizeof($parents); $i++) {
                $docinfo = $this->getDocInfo($parents[$i]);
                $path[] = $docinfo;
            }
        }
        $docinfo2 = $this->getDocInfo($id);
        $path[] = $docinfo2;

        return $path;
    }

    public function getParents($id)
    {
        $path = array();
        $sql = "SELECT PARENT_ID FROM ANOTHER_PAGES WHERE ANOTHER_PAGES_ID=? AND STATUS=1 ORDER BY NAME";
        $parents = $this->_db->fetchAll($sql, $id);

        if (count($parents) > 0) {
            foreach ($parents as $parent) {
                if ($parent['PARENT_ID'] > 0) {
                    $path[] = $parent['PARENT_ID'];
                    $path = array_merge(
                        $path,
                        $this->getParents($parent['PARENT_ID'])
                    );
                }
            }
        }

        return $path;
    }

    public function getChildren($id)
    {
        $path = array();
        $sql = "SELECT ANOTHER_PAGES_ID FROM ANOTHER_PAGES WHERE PARENT_ID=? AND STATUS=1 ORDER BY NAME";
        $childs = $this->_db->fetchAll($sql, $id);

        return $childs;
    }

    public function getSubmenu($id, $lang)
    {
        $children = $this->getChildren($id);
        $submenu = array();
        if ($children) {
            for ($i = 0; $i < sizeof($children); $i++) {
                $info = $this->getDocInfo(
                    $children[$i]['ANOTHER_PAGES_ID'],
                    $lang
                );
                if ($info) {
                    $submenu[] = $info;
                }
            }
        }

        return $submenu;
    }

    public function getDocXml($id, $type = 0)
    {
        $sql = "SELECT XML
            FROM XMLS
            WHERE XMLS_ID=?
              AND TYPE=?";

        return $this->_db->fetchOne($sql, array($id, $type));
    }

    public function getSocials()
    {
        $sql = "SELECT *
            FROM SOCIALS
            WHERE STATUS=1
            ORDER BY ORDERING";

        return $this->_db->fetchAll($sql);
    }

    public function getSocialsOne($indent)
    {
        $sql = "SELECT *
            FROM SOCIALS
            WHERE STATUS=1
              AND INDENT= '{$indent}'
            ORDER BY ORDERING";

        return $this->_db->fetchRow($sql);
    }

    public function getSiteMapTree()
    {
        $sql = 'SELECT ANOTHER_PAGES_ID
                    , CATNAME
                    , REALCATNAME
                    , URL
               FROM ANOTHER_PAGES
               WHERE STATUS=1
                 AND PARENT_ID = 5
               ORDER BY ORDER_ ASC';

        $menu = $this->_db->fetchAll($sql);

        $pos = 2;
        for ($i = 0; $i < count($menu); $i++) {
            $menu[$i]['CATNAME'] = str_replace("/", "", $menu[$i]['CATNAME']);
        }

        return $menu;
    }

    /**
     * @param string $sefURL
     *
     * @return string
     */
    public function getSiteURLbySEFU($sefURL)
    {
        $sefURL = preg_replace("/\/$/", "", $sefURL);
        $sefURL = str_replace('&amp;', '&', $sefURL);
        $sefURL = str_replace('&', '&amp;', $sefURL);
        $sefURLDecode = urldecode($sefURL);
        $sefURL = mysql_escape_string($sefURL);
        $sefURLDecode = mysql_escape_string($sefURLDecode);

        $sql = "SELECT SITE_URL
                    FROM SEF_SITE_URL
                    WHERE SEF_URL RLIKE '^{$sefURL}.?$'
                    OR SEF_URL RLIKE '^$sefURLDecode.?$'";

        $res = $this->_db->fetchOne($sql);

        if (!$res) {
            return false;
        }

        // Если в конце нет слеша - обязательно его добавляем
        if ('/' !== substr($res, -1)) {
            $res .= "/";
        }

        return $res;
    }

    public function getSefURLbyOldURL($oldURL)
    {
        $oldURL = preg_replace("/\/$/", "", $oldURL);
        $oldURL = str_replace("&amp;", "&", $oldURL);
        $oldURL = str_replace("&", "&amp;", $oldURL);

        $sql = "SELECT S.SEF_URL
              FROM OLD_SEF_URL O JOIN SEF_SITE_URL S USING (SEF_SITE_URL_ID)
              WHERE O.NAME RLIKE '^$oldURL.?$'";

        $resultURL = $this->_db->fetchOne($sql);

        $resultURL = str_replace('&amp;', '&', $resultURL);

        return $resultURL;
    }

    public function getTranslitRules()
    {
        $result = $this->_db->fetchAll("SELECT * FROM TRANSLIT_RULE");
        $rules = array();
        if (!empty($result)) {
            foreach ($result as $view) {
                $rules[$view['SRC']] = $view['TRANSLIT'];
            }
        }

        return $rules;
    }

    public function getCatName($id)
    {
        $sql = "SELECT PARENT_ID
                  ,CATNAME
            FROM CATALOGUE
            WHERE CATALOGUE_ID=?";

        return $this->_db->fetchRow($sql, $id);
    }

    public function getRedirector($url)
    {
        $sql = "SELECT URL_TO
            FROM REDIRECTOR
            WHERE URL_FROM = '{$url}'";

        return $this->_db->fetchOne($sql);
    }

}