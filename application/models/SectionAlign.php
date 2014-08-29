<?php

class models_SectionAlign extends ZendDBEntity
{
    protected $_name = 'SECTION_ALIGN';

    public function GetBanns($where)
    {
        $sql = "SELECT ALIGN_ID
                ,IMAGE1
                ,TYPE
                ,ALT
                ,DESCRIPTION
                ,BANNER_CODE
                ,URL
                ,NEWWIN 
          FROM {$this->_name}
          WHERE BANN_SECTION_ID=?
            AND STATUS=1";

        return $this->_db->fetchAll($sql, array($where));
    }


    public function getBanners($align, $section)
    {
        $sql = "SELECT SECTION_ALIGN_ID,
                   IMAGE1,
                   ALT,
                   DESCRIPTION,
                   BANNER_CODE,
                   TYPE,
                   URL,
                   NEWWIN 
            FROM {$this->_name}
            WHERE ALIGN_ID={$align}
              AND BANN_SECTION_ID={$section}
              AND STATUS=1
            ORDER BY ORDERING";

        return $this->_db->fetchAll($sql);
    }

    public function getRandomBanner($align, $section, $lang = 0)
    {
        $sql = "SELECT SECTION_ALIGN_ID,
                    IMAGE1,
                    ALT,
                    DESCRIPTION,
                    BANNER_CODE,
                    TYPE,
                    URL,
                    NEWWIN 
             FROM {$this->_name}
             WHERE ALIGN_ID=?
               AND BANN_SECTION_ID=?
               AND STATUS=1
             ORDER BY RAND() LIMIT 0,1";

        $banner = $this->_db->fetchRow($sql, array($align, $section));

        $burl = '';
        if ($banner['URL'] != '' || strchr($banner['URL'], "http:")) $burl = $banner['URL'];
        else {
            if ($banner['URL'] != '') {
                if (strchr($banner['URL'], "doc")) {
                    if (substr($banner['URL'], 0, 1) != "/") $burl .= "/";
                    $burl .= $banner['URL'];
                } else {
                    if (substr($banner['URL'], 0, 1) != "/") $burl = "/doc/" . $banner['URL'];
                    else $burl = "/doc" . $banner['URL'];
                }
                if (substr($banner['URL'], -1) != "/") $burl .= "/";
            } else $burl = '';
        }
        if ($burl != '') $banner['burl'] = $burl;
        else $banner['burl'] = '';

        return $banner;
    }

    /**
     * Get section align by ban section schedule
     *
     * @return array
     */
    public function getSectionAlignByBanSectionSchedule()
    {
        $sql = "SELECT sa.DESCRIPTION FROM BANN_SECTION bs LEFT JOIN SECTION_ALIGN sa USING(BANN_SECTION_ID) WHERE bs.BANN_SECTION_ID = 2";

        return $this->_db->fetchAll($sql);
    }
}

?>