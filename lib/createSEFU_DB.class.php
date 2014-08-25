<?php

/**
 * Класс createSEFU_DB является моделью данных для CreateSEFU
 *
 * @author Администратор
 */
class createSEFU_DB
{
    /**
     * Свойство $db является объектом связи с БД
     */
    private $db;

    public function  __construct()
    {
        $this->db = SCMF_extension::singleton();
    }

    /**
     * Возвращает массив данных о каталоге (CATALOGUE_ID, PARENT_ID, NAME, REALCATNAME, SPECIAL_URL)
     */
    public function getCatData($id)
    {
        $sql = "select PARENT_ID, NAME, REALCATNAME
                from CATALOGUE
                and CATALOGUE_ID = $id";

        return $this->db->selectRow($sql);
    }

    /**
     * Возвращает массив ID каталогов
     *
     * @return array
     */
    public function getCats()
    {
        $sql = "select CATALOGUE_ID from CATALOGUE ORDER BY PARENT_ID, ORDERING";

        return $this->db->selectIntArray($sql);
    }

    public function getCatsBrands($id)
    {
        $sql = "select BRAND_ID from CAT_BRAND where CATALOGUE_ID = {$id}";

        return $this->db->selectIntArray($sql);
    }

    public function getItemsCount($id)
    {
        $sql = "select count(*) as amnt
              from ITEM 
              where CATALOGUE_ID={$id} 
                and STATUS=1";

        return $this->db->selectRow($sql);
    }

    /**
     * Возвращает массив ID карточек товара
     */
    public function getItems()
    {
        $sql = "select ITEM_ID, CATALOGUE_ID from ITEM";

        return $this->db->selectRowArray($sql);
    }

    /**
     * Возвращает массив названий каталогов, состоящий из названия ъ
     * каталога $catID и всех его родительских каталогов
     *
     * @param int $catID
     *
     * @return array
     */
    public function getCatParentName($catID)
    {
        $parents = $this->getParents($catID); // массив id родителей для $catID
        $nameArr = array(); // массив названий каталогов
        foreach ($parents as $id) {
            $nameArr[] = $this->getCatName($id);
        }
        $cyrURL = implode("/", $nameArr); // инициализируем путь, состоящий из названий каталогов (на кириллице)
        return $cyrURL;
    }

    public function getCatName($id)
    {
        $sql = "select NAME
            from CATALOGUE 
            where CATALOGUE_ID = $id";

        return $this->db->selectString($sql);
    }

    public function getBrandName($id)
    {
        $sql = "select NAME
            from BRAND 
            where BRAND_ID = $id";

        return $this->db->selectString($sql);
    }

    /**
     * Возвращает массив ID родителей каталога $catID, включая сам каталог
     *
     * @param int $catID
     *
     * @return array
     */
    public function getParents($catID)
    {
        $pathIDs = array();
        $sql = 'select PARENT_ID from CATALOGUE where CATALOGUE_ID=?';
        while ($ap = $this->db->selectRow($sql, $catID)) {
            array_push($pathIDs, $catID);
            $catID = $ap['PARENT_ID'];
        }

        $pathIDs = array_reverse($pathIDs);

        return $pathIDs;
    }

    /**
     * Возвращает массив данных о карточке товара $itemID
     *
     * @param int $itemID
     *
     * @return array
     */
    public function getItemInfo($itemID)
    {
        $sql = "select * from ITEM where ITEM_ID = $itemID";

        return $this->db->selectRow($sql);
    }

    /**
     * Возвращает реальный ("админский" - неизменный) путь для каталога $catID
     *
     * @param int $catID
     *
     * @return string
     */
    public function getRealURLCat($catID)
    {
        $sql = "select REALCATNAME from CATALOGUE where CATALOGUE_ID = ?";

        return $this->db->selectString($sql, $catID);
    }

    /**
     * Добавляет новое либо изменяет существующее соотношение урл сайта -- ЧПУ-урл.
     *
     * @param string $siteURL урл сайта
     * @param string $sefURL  ЧПУ
     *
     * @return int id сохраненной записи
     */
    public function saveSiteSEFRelation($siteURL, $sefURL)
    {
        $sql = "insert into SEF_SITE_URL set SITE_URL = ?, SEF_URL = ?, DATE = now()";
        $this->db->execute($sql, $siteURL, $sefURL);
        $sql = "select SEF_SITE_URL_ID from SEF_SITE_URL where SITE_URL = ?";

        return $this->db->selectInt($sql, $siteURL);
    }

    /**
     * Возвращает ID записи в таблице SEF_SITE_URL по SITE_URL или SEF_URL (оба поля являются уникальными).
     *
     * @param string $siteURL
     * @param string $sefURL
     *
     * @return mixed ID записи, если передан хотя бы 1 из параметров, и false в противном случае
     */
    public function getIdSefSite($siteURL = "", $sefURL = "")
    {
        if ($siteURL) {
            $siteURL = preg_replace("/\/$/", "", $siteURL); // удаляем последний слэш
            $sql = "select SEF_SITE_URL_ID from SEF_SITE_URL where SITE_URL rlike '^$siteURL.?$'";
        }
        if ($sefURL) {
            $sefURL = preg_replace("/\/$/", "", $sefURL); // удаляем последний слэш
            // заменяем "(" на "[(]" и ")" на "[)]" соответственно, т.к. скобка - это спец.символ в регулярках
            $sefURL = str_replace("(", "[(]", $sefURL);
            $sefURL = str_replace(")", "[)]", $sefURL);
            $sql = "select SEF_SITE_URL_ID from SEF_SITE_URL where SEF_URL rlike '^$sefURL.?$'";
        }

//        echo "$sql<br/>";
        return $this->db->selectInt($sql);
    }

    /**
     * Возвращает все поля таблицы SEF_SITE_URL для $id.
     *
     * @param int $id
     *
     * @return array
     */
    public function getSEF_SiteRelationInfo($id)
    {
        $sql = "select * from SEF_SITE_URL where SEF_SITE_URL_ID = ?";

        return $this->db->selectRow($sql, $id);
    }

    /**
     * Обновление ЧПУ-урла и даты для урла сайта с id $idSefSiteRelation.
     *
     * @param int    $idSefSiteRelation id записи в таблице SEF_SITE_URL
     * @param string $sefURL            ЧПУ-урл для страницы каталога
     */
    public function updateSEF_SiteRelation($idSefSiteRelation, $sefURL)
    {
        $sql = "update SEF_SITE_URL
                    set SEF_URL = ?,
                        DATE = now()
                  where SEF_SITE_URL_ID = ?";
        $this->db->execute($sql, $sefURL, $idSefSiteRelation);
    }

    /**
     * Добавляет урл к списку старых урлов, если такой урл еще не существует.
     *
     * @param int    $idSefSiteRelation id родительской записи
     * @param string $url               добавляемый урл
     */
    public function addOldURL($idSefSiteRelation, $url)
    {
        $url1 = preg_replace("/\/$/", "", $url); // удаляем последний слэш
        $isExists = $this->db->selectInt("select 1 from OLD_SEF_URL where NAME rlike '^$url1.?$'");
        if (!$isExists) {
            $sql = "insert into OLD_SEF_URL
                      set NAME = '{$url}',
                          SEF_SITE_URL_ID = '{$idSefSiteRelation}',
                          DATE = now()";

            $this->db->execute($sql);
        }
    }

    /**
     * Возвращает спец.урл для каталога или REALCATNAME при его отсутствии.
     *
     * @param int $catID id каталога
     *
     * @return string урл
     */
    public function getCatCurURL($catID)
    {
        $sql = "select concat('/','" . SEFU_PREFIX_CAT_URL_FOR_ADMIN . "',REALCATNAME) as name
                from CATALOGUE
                where CATALOGUE_ID = ?";

        return $this->db->selectString($sql, $catID);
    }

    /**
     * Возвращает ЧПУ-урл, соответствующий старому урлу.
     *
     * @param string $oldURL старый урл
     *
     * @return string ЧПУ-урл
     */
    public function getSefURLbyOldURL($oldURL)
    {
        $oldURL = preg_replace("/\/$/", "", $oldURL); // удаляем последний слэш
        $oldURL = str_replace("&amp;", "&", $oldURL);
        $oldURL = str_replace("&", "&amp;", $oldURL);
        $sql = "select S.SEF_URL
                  from OLD_SEF_URL O join SEF_SITE_URL S using (SEF_SITE_URL_ID)
                  where O.NAME rlike '^$oldURL.?$'";
//        echo $sql."<br/>";
        $resultURL = $this->db->selectString($sql);
        $resultURL = str_replace('&amp;', '&', $resultURL);

        return $resultURL;
    }

    /**
     * Возвращает урл сайта, соответствующий ЧПУ-урлу.
     *
     * @param string $oldURL старый урл
     *
     * @return string урл сайта
     */
    public function getSiteURLbySEFU($sefURL)
    {
        $sefURL = preg_replace("/\/$/", "", $sefURL); // удаляем последний слэш
        $sefURL = str_replace('&amp;', '&', $sefURL);
        $sefURL = str_replace('&', '&amp;', $sefURL);
        $sefURLDecode = urldecode($sefURL);
        $sefURL = mysql_escape_string($sefURL);
        $sefURLDecode = mysql_escape_string($sefURLDecode);
        $sql = "select SITE_URL from SEF_SITE_URL where SEF_URL rlike '^{$sefURL}.?$' or SEF_URL rlike '^$sefURLDecode.?$'";
//        echo $sql;die;
        $res = $this->db->selectString($sql);

        return $res;
    }

    /**
     * Возвращает массив ID статей
     */
    public function getArticles()
    {
        $sql = "select ARTICLE_ID from ARTICLE";

        return $this->db->selectIntArray($sql);
    }

    /**
     * Возвращает заголовок статьи для $articleID
     *
     * @param int $articleID
     */
    public function getNameArticle($articleID)
    {
        $sql = "select NAME from ARTICLE where ARTICLE_ID = ?";

        return $this->db->selectString($sql, $articleID);
    }

    public function deleteOldNotNeedUrl($siteURL)
    {
        $sql = "select count(1) from OLD_SEF_URL where NAME = '{$siteURL}'";
        $res = $this->db->selectInt($sql);

        if (!empty($res)) {
            $sql = "delete from OLD_SEF_URL where NAME = '{$siteURL}'";
            $this->db->execute($sql);

            return true;
        }


        return false;
    }

    /**
     * Возвращает массив ID статей
     *
     * @return array
     */
    public function getNews()
    {
        $sql = "select NEWS_ID from NEWS";

        return $this->db->selectIntArray($sql);
    }

    /**
     * Возвращает массив с информацией о новости
     *
     * @param int $newsID
     *
     * @return array
     */
    public function getNewsInfo($newsID)
    {
        $sql = "select *, DATE_FORMAT(DATA, '%Y-%m-%d') as dateFormat
                from NEWS where NEWS_ID = ?";

        return $this->db->selectRow($sql, $newsID);
    }

    /**
     * Возвращает массив id страниц сайта
     *
     * @return array
     */
    public function getDocs()
    {
        $sql = "select ANOTHER_PAGES_ID from ANOTHER_PAGES";

        return $this->db->selectIntArray($sql);
    }

    /**
     * Возвращает массив данных о странице сайта $docID
     *
     * @param int $docID
     *
     * @return array
     */
    public function getDocInfo($docID)
    {
        $sql = "select * from ANOTHER_PAGES where ANOTHER_PAGES_ID = ?";

        return $this->db->selectRow($sql, $docID);
    }

    /**
     * Возвращает название страницы сайта $docID
     *
     * @param int $docID
     *
     * @return string
     */
    public function getDocName($docID)
    {
        $sql = "select NAME from ANOTHER_PAGES where ANOTHER_PAGES_ID = ?";

        return $this->db->selectString($sql, $docID);
    }

    public function getDocIdByUrl($url)
    {
        $sql = "select ANOTHER_PAGES_ID from ANOTHER_PAGES where URL = ? and STATUS = 1";

        return $this->db->selectString($sql, $url);
    }

    public function getDocRealCat($docID)
    {
        $sql = "select REALCATNAME from ANOTHER_PAGES where ANOTHER_PAGES_ID = ?";

        return $this->db->selectString($sql, $docID);
    }

    /**
     * Возвращает урлы для 301 редиректа, полученные из htaccess
     *
     * @return array
     */
    public function getSEFUfromHtaccess()
    {
        $sql = "select URL1, URL2 from temp";

        return $this->db->selectRowArray($sql);
    }

    public function getOldURLInfo($url)
    {
        $url = rtrim($url, "/"); // удаляем последний слэш
        $sql = "select * from OLD_SEF_URL where NAME rlike '^$url.?$'";

//        echo $sql."<br/>";
        return $this->db->selectRow($sql);
    }

    public function getSefUrlByID($id)
    {
        return $this->db->selectString("select SEF_URL from SEF_SITE_URL where SEF_SITE_URL_ID = ?", $id);
    }
}