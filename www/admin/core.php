<?php
$filtpath = '';
$filters = '';

require_once __DIR__ . '../../../application/configs/config.php';

error_reporting(E_ALL &~ E_DEPRECATED);
@setlocale(LC_ALL, 'ru_RU.UTF8');
@setlocale(LC_NUMERIC,"en_US");

$V_G_FONTS = array(
    "fonts/FEEDBI__.TTF",
    "fonts/frizzed.ttf",
);

class SCMF
{
    var $dbh;
    var $docroot;
    var $phpvers;
    var $ARTICLE;
    var $SRCID;
    var $CMF_LANG_ID;
    var $DEBUG;
    var $sessionCookieLifeTime;

    var $tr = array(
        "Г" => "G", "Ё" => "YO", "Є" => "E", "Ю" => "YI", "Я" => "I",
        "и" => "i", "г" => "g", "ё" => "yo", "№" => "#", "є" => "e",
        "ї" => "yi", "А" => "A", "Б" => "B", "В" => "V", "Г" => "G",
        "Д" => "D", "Е" => "E", "Ж" => "ZH", "З" => "Z", "И" => "I",
        "Й" => "J", "К" => "K", "Л" => "L", "М" => "M", "Н" => "N",
        "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T",
        "У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "TS", "Ч" => "CH",
        "Ш" => "SH", "Щ" => "SCH", "Ъ" => "'", "Ы" => "YI", "Ь" => "",
        "Э" => "E", "Ю" => "YU", "Я" => "YA", "а" => "a", "б" => "b",
        "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "zh",
        "з" => "z", "и" => "i", "й" => "j", "к" => "k", "л" => "l",
        "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
        "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h",
        "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "'",
        "ы" => "yi", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya", " " => '-',
        "…" => "-", "і" => "i", "®" => "-", "І" => "I",
        "«" => "-", "»" => "-", "(" => "", ")" => ""
    );

    //for php4 only
    function SCMF($ARTICLE = '')
    {
        SCMF::__construct($ARTICLE);
    }

    function __construct($ARTICLE = '')
    {

        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');

        $this->DEBUG = 0;
        $this->CMF_LANG_ID = (isset($_COOKIE['CMF_LANG_ID']) && $_COOKIE['CMF_LANG_ID'] > 0 ? intval($_COOKIE['CMF_LANG_ID']) : 1);
//        $this->SRCID='4294966027';
        $this->ARTICLE = $ARTICLE;
        $this->phpvers = explode('.', PHP_VERSION);
        $this->docroot = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '';
        $this->dbh = @mysql_connect(
            $config->resources->db->params->host,
            $config->resources->db->params->username,
            $config->resources->db->params->password
        );
        $this->sessionCookieLifeTime = 10800; // время жизни куки текущей сессии
        if (!$this->dbh) {
            echo "Не приконнектился.";
        }
        mysql_select_db($config->resources->db->params->dbname);

        //mysql_query('SET OPTION CHARACTER SET utf8', $this->dbh);
        mysql_query("SET character_set_server = utf8", $this->dbh);
        mysql_query("SET NAMES utf8", $this->dbh);
        mysql_query("SET CHARACTER SET utf8", $this->dbh);
        mysql_query("SET character_set_connection = utf8", $this->dbh);
        mysql_query("SET collation_connection = utf8", $this->dbh);


        //mysql_query('SET OPTION CHARACTER SET cp1251', $this->dbh);
    }

    function Close()
    {
        mysql_close($this->dbh);
    }

    function setArticle($ARTICLE)
    {
        $this->ARTICLE = $ARTICLE;
    }

    function Header()
    {
        header('Content-type: text/html; charset=utf-8');
        //header('Content-type: text/html; charset=windows-1251');
    }

    function HeaderNoCache()
    {
        header('Content-type: text/html; charset=utf-8');
        //header('Content-type: text/html; charset=windows-1251');
        header('Pragma: no-cache');
        header('Cache-Control: private, no-cache');
    }

    function GetRights()
    {
        $CMF_MD5 = $this->Param('CMF_UID');

        $CMF_MD5 = !empty($_COOKIE['CMF_UID']) ? $_COOKIE['CMF_UID'] : null;

        list($CMF_USER_ID, $NAME) = $this->selectrow_array('select CMF_USER_ID,NAME from CMF_USER where MD5_=? and STATUS=1', $CMF_MD5);
        list($CMF_SCRIPT_ID, $CMF_SCRIPT_NAME) = $this->selectrow_array('select CMF_SCRIPT_ID,NAME from CMF_SCRIPT where ARTICLE=? and STATUS=1', $this->ARTICLE);
        list($R, $W, $D) = $this->selectrow_array('select R,W,D from CMF_SCRIPT_USER where CMF_SCRIPT_ID=? and CMF_USER_ID=?', $CMF_SCRIPT_ID, $CMF_USER_ID);
        list($G_R, $G_W, $G_D) = $this->selectrow_array('select SUM(SG.R),SUM(SG.W),SUM(SG.D) from CMF_SCRIPT_GROUP SG inner join CMF_USER_GROUP UG on (SG.CMF_GROUP_ID=UG.CMF_GROUP_ID) where SG.CMF_SCRIPT_ID=? and UG.CMF_USER_ID=?', $CMF_SCRIPT_ID, $CMF_USER_ID);

        //echo  $CMF_SCRIPT_ID."=>".$CMF_USER_ID ."===>".$R."=>".$W."=>".$D."<hr>";

        $R += $G_R;
        $W += $G_W;
        $D += $G_D;
        $this->USER_ID = $CMF_USER_ID;
        $this->SCRIPT_ID = $CMF_SCRIPT_ID;
        $this->SCRIPT_NAME = $CMF_SCRIPT_NAME;
        $this->NAME = $NAME;
        $this->R = $R;
        $this->W = $W;
        $this->D = $D;
        $this->GetScriptPath();

        return ($CMF_USER_ID && $R);
    }


    function GetComparedItems($Coms = array())
    {
        $ret = '';
        if (!count($Coms) > 0) {
            $Coms = explode(';', $this->Param('Comp'));
        }
        $items = array();
        foreach ($Coms as $ind => $item) {
            $Coms[$ind] += 0;
            if ($Coms[$ind] > 0) $items[] = $item;
        }

        $Coms = $items;
        if ($Coms) {

            $sth = $this->execute('select distinct I.CATALOGUE_ID,C.NAME from ITEM I inner join CATALOGUE C on (I.CATALOGUE_ID=C.CATALOGUE_ID) where I.ITEM_ID in (' . implode(',', $Coms) . ') and I.STATUS=1');
            while (list($V_CATALOGUE_ID, $V_NAME) = mysql_fetch_array($sth, MYSQL_NUM)) {
                $ret .= "<comp_cat id='$V_CATALOGUE_ID'><name>$V_NAME</name>";
                $sth1 = $this->execute('select I.ITEM_ID,I.TYPENAME,B.NAME,I.NAME,I.PRICE from ITEM I left join BRAND B on (I.BRAND_ID=B.BRAND_ID) where I.CATALOGUE_ID=? and I.ITEM_ID in (' . implode(',', $Coms) . ') and I.STATUS=1', $V_CATALOGUE_ID); //
                while (list($VV_ITEM_ID, $VV_TYPENAME, $VV_BRAND, $VV_NAME, $VV_PRICE) = mysql_fetch_array($sth1, MYSQL_NUM)) {
                    $urname = preg_replace('/[^\w]/', '_', $this->translit($VV_BRAND . '_' . $VV_NAME));
                    $urname = preg_replace("/_{2,}/", "_", $urname);
                    $ret .= "<comp_item id='$VV_ITEM_ID'><name>$VV_TYPENAME $VV_BRAND $VV_NAME</name><price>$VV_PRICE</price><uname>$urname</uname></comp_item>";
                }
                $ret .= "</comp_cat>";
            }
        }

        return $ret;
    }

    function getParams($param)
    {
        $value = $this->selectrow_array("select VALUE from SITE_PARAM where SYSNAME=? and STATUS=1", $param);

        return $value ? $value : '';
    }

    function MakeCommonHeader($id = null)
    {
        ob_start();
        $sectionState = isset($_SESSION['closedSections'][$this->PATH_ARRAY[0]]) ? 0 : 1;
        ?>
        <html>

        <head>
            <title>Система управления сайтом - <?= $this->SCRIPT_NAME ?></title>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

            <link href="style.css" rel="stylesheet" type="text/css"/>
            <script src="admin.js"></script>

            <style type="text/css">@import url(calendar/calendar-win2k-1.css);</style>
            <script type="text/javascript" src="calendar/calendar.js"></script>
            <script type="text/javascript" src="calendar/lang/calendar-en.js"></script>
            <script type="text/javascript" src="calendar/lang/calendar-ru_win_.js"></script>
            <script type="text/javascript" src="calendar/calendar-setup.js"></script>

            <script type="text/javascript" src="ckeditor/ckeditor.js"></script>

            <script type="text/javascript" src="js/jquery.js"></script>
            <link rel="stylesheet" type="text/css" href="tabs/tabcontent.css"/>
            <script type="text/javascript" src="tabs/tabcontent.js"></script>


        </head>

        <body>
        <table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
        <tr><td valign="top"><? $this->MakeHeader($id) ?><table width="100%" cellpadding="0" cellspacing="0" height="100%" border="0">
        <tr><td style="padding: 19px 17px 0px 13px;" valign="top">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" >
        <tr>
        <td id="section_<?= $this->PATH_ARRAY[0] ?>"
            style="display: <?= $sectionState ? 'block' : 'none' ?>; background: url(img/d_gray.gif) repeat-y left top; width: 230px; padding-right: 17px;"
            valign="top">
            <? $this->MakeLeftMenu($id); ?>
            <br/>

            <div style="width: 230; height: 1px;">
                <spacer type="block" width="230" height="1">
            </div>
        </td>
        <td style="background: url(img/d_gray.gif) repeat-y left top; padding-right: 20px;" width="100%" valign="top">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td>
                <img src="img/menu_tri.gif" alt="" width="116" height="18" border="0">

                <div style="position:relative; left:100px; top:-23px;">
                    <div style="position:absolute; display: <?= $sectionState ? 'none' : 'block' ?>"
                         id="actionBlock_<?= $this->PATH_ARRAY[0] ?>">
                        <nobr>
                            <a href="#" onclick="return triggerSection(<?= $this->PATH_ARRAY[0] ?>);"><img
                                    src="img/bt_red_right.gif" width="16" height="15" border="0" align="absmiddle"
                                    style="margin-right:11px;"/></a>
                            <a href="#" onclick="return triggerSection(<?= $this->PATH_ARRAY[0] ?>);">отобразить меню
                                слева</a>
                        </nobr>
                    </div>
                    <div style="position:absolute; display: <?= $sectionState ? 'block' : 'none' ?>"
                         id="actionBlockReverse_<?= $this->PATH_ARRAY[0] ?>">
                        <nobr>
                            <a href="#" onclick="return triggerSection(<?= $this->PATH_ARRAY[0] ?>);"><img
                                    src="img/bt_red_left.gif" width="16" height="15" border="0" align="absmiddle"
                                    style="margin-right:11px;"/></a>
                            <a href="#" onclick="return triggerSection(<?= $this->PATH_ARRAY[0] ?>);">спрятать меню
                                слева</a>
                        </nobr>
                    </div>
                </div>
            </td>
        </tr>
        <tr><td style="padding: 0px 19px 0px 21px;">
    <?php
    }

    function MakeCommonFooter()
    {
        ?></td></tr></table>
        </td></tr></table>
        </td></tr></table>
        </td></tr>
        <tr>
            <td valign="bottom" style="padding-top: 20px;">
                <div
                    style="display: block; background: url(img/bottom_bg.gif) repeat-x top left #F0F0F0; height: 55px; padding: 6px 0px 0px 15px;">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td></td>
                            <td width="100%">&#160;</td>
                            <td class="bottom_copy">Система администрирования сайта<br>версия v3.1</td>
                            <td style="padding: 0px 12px 0px 0px;"><a href="http://adlabs.com.ua" target="_blank"><img
                                        src="i/logo_adlabs.gif"
                                        alt="Adlabs - Лаборатория интернет-маркетинга | создание и продвижение сайтов"
                                        width="88" height="31" border="0"/></a>
                            </td>
                        </tr>
                    </table>
                </div>

            </td>
        </tr></table>
        </td></tr></table>
        </body>
        </html>
    <?php
    }

    function GetScriptPath()
    {
        $id = $this->SCRIPT_ID;
        $PARR = array();
        $PATH = '';
        do {
            list($PARENTID, $NAME, $URL) = $this->selectrow_array('select PARENT_ID,NAME,URL from CMF_SCRIPT where CMF_SCRIPT_ID=?', $id);
            $PATH = '/ $NAME' . $PATH;
            array_push($PARR, $id);
            $id = $PARENTID;
        } while (isset($PARENTID) && $PARENTID && $PARENTID != 1);
        $PARR = array_reverse($PARR);
        $this->PATH = $PATH;
        $this->PATH_ARRAY = $PARR;
    }


    function MakeLeftMenu($PARENT_ID)
    {
        if (!$PARENT_ID) {
            $PARENT_ID = $this->PATH_ARRAY[0];
        }
        ?>
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td><img src="img/menu_tri.gif" alt="" width="116" height="18" border="0"></td>
            </tr>
            <tr>
                <td style="padding-left: 0px;">
                    <table width="100%" cellpadding="0" cellspacing="0"><?
                        $sth = $this->execute('select S.CMF_SCRIPT_ID,S.NAME,S.URL,S.DESCRIPTION,S.BACKGROUND,S.IMAGE from CMF_SCRIPT S inner join CMF_rights R on (S.CMF_SCRIPT_ID=R.CMF_SCRIPT_ID and S.PARENT_ID=? and S.STATUS=1) order by ORDERING', $PARENT_ID);
                        while (list($V_CMF_SCRIPT_ID, $V_NAME, $V_URL, $V_DESCRIPTION, $V_BACKGROUND, $V_IMAGE) = mysql_fetch_array($sth, MYSQL_NUM)) {
                            $IM_IMAGE = split('#', $V_IMAGE);
                            if ($V_BACKGROUND == '') {
                                $V_BACKGROUND = "#CCCC66";
                            }
                            $sectionState = isset($_SESSION['closedSections'][$V_CMF_SCRIPT_ID]) ? 0 : 1;
                            $iconUrl = isset($_SESSION['closedSections'][$V_CMF_SCRIPT_ID]) ? 'img/icon_maximize.gif' : 'img/icon_minimize.gif';
                            ?>
                            <tr>
                                <td class="menu_title"
                                    style="background: url(/images/adm/scr/<?= $IM_IMAGE[0] ?>) no-repeat left <?= $V_BACKGROUND ?>;"><?= $V_NAME ?></td>
                                <td align="right" style="background: <?= $V_BACKGROUND ?>;">
                                    <div style="display: <?= $sectionState ? 'none' : 'block' ?>"
                                         id="actionBlock_<?= $V_CMF_SCRIPT_ID ?>">
                                        <a href="#" onclick="return triggerSection(<?= $V_CMF_SCRIPT_ID ?>)">
                                            <img src="img/icon_maximize.gif" width="16" height="16" border="0"
                                                 hspace="10"/>
                                        </a>
                                    </div>
                                    <div style="display: <?= $sectionState ? 'block' : 'none' ?>"
                                         id="actionBlockReverse_<?= $V_CMF_SCRIPT_ID ?>">
                                        <a href="#" onclick="return triggerSection(<?= $V_CMF_SCRIPT_ID ?>)">
                                            <img src="img/icon_minimize.gif" width="16" height="16" border="0"
                                                 hspace="10"/>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                            <td class="menu" colspan="2">
                                <ul id="section_<?= $V_CMF_SCRIPT_ID ?>"
                                    style="display:<?= $sectionState ? 'block' : 'none' ?>;"
                                "><?
                                $sth1 = $this->execute('select S.CMF_SCRIPT_ID,S.NAME,S.URL,S.DESCRIPTION from CMF_SCRIPT S inner join CMF_rights R on (S.CMF_SCRIPT_ID=R.CMF_SCRIPT_ID and S.PARENT_ID=? and S.STATUS=1) order by ORDERING', $V_CMF_SCRIPT_ID);
                                while (list($V_CMF_SCRIPT_ID, $V_NAME, $V_URL, $V_DESCRIPTION) = mysql_fetch_array($sth1, MYSQL_NUM))
                                {
                                if (!defined('DEFAULT_URL')) define('DEFAULT_URL', $V_URL);
                                ?>
                                <li><a href="<?= $V_URL ?>" title="<?= $V_DESCRIPTION ?>"><?= $V_NAME ?></a></li>
                                <dt><?= $V_DESCRIPTION ?></dt>
                                <?
                                }
                                ?></ul></td></tr><?php
                        }
                        ?>
                        <tr>
                            <td><br></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    <?php
    }

    function MakeHeader($id)
    {
        if (!$id && $this->PATH_ARRAY[0]) $id = $this->PATH_ARRAY[0];
        $sth = $this->execute('select G.NAME from CMF_GROUP G inner join CMF_USER_GROUP UG on (G.CMF_GROUP_ID=UG.CMF_GROUP_ID and UG.CMF_USER_ID=?)', $this->USER_ID);
        $GROUPS_STR = '';
        while (list($V_NAME) = mysql_fetch_array($sth, MYSQL_NUM)) {
            $GROUPS_STR .= ' ' . $V_NAME . ' ';
        }
        ?>
        <div style="display: block; background: #339900; height: 5px;"><img src="img/0.gif" alt="" width="1" height="5"
                                                                            border="0"></div>
        <div style="display: block; background: url(img/top_bg.gif) repeat-x bottom left #1A5684; height: 61px;">
            <table width="100%" height="60" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td style="padding-left: 14px;" width="33%"><? #print $this->langSelection(); ?></td>
                    <td align="center" width="33%">

                        <table cellpadding="2" cellspacing="0" border="0">
                            <tr>
                                <td class="white"><b>Добро пожаловать: <?= $this->NAME ?></b></td>
                            </tr>
                            <tr>
                                <td class="gray">(<?= $GROUPS_STR ?>)</td>
                            </tr>
                        </table>

                    </td>
                    <td align="right" width="33%">

                        <table cellpadding="2" cellspacing="0">
                            <tr>
                                <td><a href="login.php?e=out"><img src="img/icon_exit.gif" alt="" width="18" height="15"
                                                                   border="0"></a></td>
                                <td class="top_icon"><a href="login.php?e=out">Logout</a></td>
                                <td><a href="#history"><a href="#error"><img src="img/icon_error.gif" alt="" width="18"
                                                                             height="15" border="0"></a></td>
                                <td class="top_icon"><a href="report_bug.php">Сообщить об ошибке</a></td>
                            </tr>

                            <tr>
                                <td><a href="#rules"><img src="img/icon_rules.gif" alt="" width="18" height="15"
                                                          border="0"></a></td>
                                <td class="top_icon"><a href="user_rights.php">Права пользователя</a></td>
                                <td>&nbsp;</td>
                                <td class="top_icon">&nbsp;</td>
                            </tr>
                        </table>

                    </td>
                </tr>
            </table>
        </div>

        <br>
        <div
            style="display: block; padding-left: 13px; background: url(img/top_menu_bg.gif) repeat-x left bottom; height: 25px;">
        <table cellpadding="0" cellspacing="0">
            <tr><?

                $this->execute('drop table if exists CMF_rights');
                $this->execute('create temporary table CMF_rights (CMF_SCRIPT_ID int not null,primary key(CMF_SCRIPT_ID))');
                $this->execute('insert into CMF_rights select CMF_SCRIPT_ID from CMF_SCRIPT_USER where CMF_USER_ID=? and R>0', $this->USER_ID);
                $this->execute('insert IGNORE into CMF_rights select CMF_SCRIPT_ID from CMF_SCRIPT_GROUP SG  inner join CMF_USER_GROUP UG on (SG.CMF_GROUP_ID=UG.CMF_GROUP_ID) where  UG.CMF_USER_ID=? and SG.R>0 group by SG.CMF_SCRIPT_ID', $this->USER_ID);

                $sth = $this->execute('select S.CMF_SCRIPT_ID,S.NAME,S.URL,S.DESCRIPTION from CMF_SCRIPT S inner join CMF_rights R on (S.CMF_SCRIPT_ID=R.CMF_SCRIPT_ID and S.PARENT_ID=1 and S.STATUS=1) order by S.ORDERING');
                $pic = 'top_menu_left.gif';
                while (list($V_CMF_SCRIPT_ID, $V_NAME, $V_URL, $V_DESCRIPTION) = mysql_fetch_array($sth, MYSQL_NUM)) {
                    if ($V_CMF_SCRIPT_ID == $id) {
                        ?>
                        <td><img src='img/<?= $pic ?>' alt='<?= $V_DESCRIPTION ?>' width='18' height='24' border='0'>
                        </td>
                        <td class='top_menu'><a href='index.php?e=NAV&id=<?= $V_CMF_SCRIPT_ID ?>'
                                                title='<?= $V_DESCRIPTION ?>'><b><?= $V_NAME ?></b></a></td><?
                    } else {
                        ?>
                        <td><img src='img/<?= $pic ?>' alt='<?= $V_DESCRIPTION ?>' width='18' height='24' border='0'>
                        </td>
                        <td class='top_menu'><a href='index.php?e=NAV&id=<?= $V_CMF_SCRIPT_ID ?>'
                                                title='<?= $V_DESCRIPTION ?>'><?= $V_NAME ?></a></td><?
                    }
                    $pic = 'top_menu_left2.gif';
                }
                ?>
                <td><img src='img/top_menu_right.gif' alt='' width='1' height='24' border='0'></td>
            </tr>
        </table></div><?
    }

    function langSelection()
    {
        ?>
        <!-- language -->
        <form id="langSelection" action="index.php" method="post">
            <select name="CMF_LANG_ID" onchange="document.getElementById('langSelection').submit();">
                <?
                $sth = $this->execute("select CMF_LANG_ID, NAME, if(CMF_LANG_ID=?,1,0) from CMF_LANG where STATUS=1 order by ORDERING asc", $this->CMF_LANG_ID);
                while (list($V_CMF_LANG_ID, $V_NAME, $V_SELECTED) = mysql_fetch_array($sth, MYSQL_NUM)) {
                    $selected = ($V_SELECTED ? " selected='selected'" : "");
                    @print <<<EOF
        <option value="$V_CMF_LANG_ID" $selected>$V_NAME</option>
EOF;
                }
                ?>
            </select>
            <input type="hidden" name="changeLang" value="1"/>
        </form>
        <!-- /language -->
    <?
    }


    function removeHTMLentity($xml)
    {
        $search = array(
            "'&nbsp;'i", "'&amp;'i", "'&quot;'i", "'&cent;'i", "'&euro;'i", "'&pound;'i", "'&yen;'i", "'&copy;'i", "'&reg;'i", "'&trade;'i",
            "'&permil;'i", "'&micro;'i", "'&middot;'i", "'&bull;'i", "'&hellip;'i", "'&prime;'i", "'&Prime;'i", "'&sect;'i", "'&para;'i", "'&szlig;'i",
            "'&lsaquo;'i", "'&rsaquo;'i", "'&laquo;'i", "'&raquo;'i", "'&lsquo;'i", "'&rsquo;'i", "'&ldquo;'i", "'&rdquo;'i", "'&sbquo;'i", "'&bdquo;'i",
            "'&lt;'i", "'&gt;'i", "'&le;'i", "'&ge;'i", "'&ndash;'i", "'&mdash;'i", "'&macr;'i", "'&oline;'i", "'&curren;'i", "'&brvbar;'i",
            "'&uml;'i", "'&iexcl;'i", "'&iquest;'i", "'&circ;'i", "'&tilde;'i", "'&deg;'i", "'&minus;'i", "'&plusmn;'i", "'&divide;'i", "'&frasl;'i",
            "'&times;'i", "'&sup1;'i", "'&sup2;'i", "'&sup3;'i", "'&frac14;'i", "'&frac12;'i", "'&frac34;'i", "'&fnof;'i", "'&int;'i", "'&sum;'i",
            "'&infin;'i", "'&radic;'i", "'&sim;'i", "'&cong;'i", "'&asymp;'i", "'&ne;'i", "'&equiv;'i", "'&isin;'i", "'&notin;'i", "'&ni;'i",
            "'&prod;'i", "'&and;'i", "'&or;'i", "'&not;'i", "'&cap;'i", "'&cup;'i", "'&part;'i", "'&forall;'i", "'&exist;'i", "'&empty;'i",
            "'&nabla;'i", "'&lowast;'i", "'&prop;'i", "'&ang;'i", "'&acute;'i", "'&cedil;'i", "'&ordf;'i", "'&ordm;'i", "'&dagger;'i", "'&Dagger;'i",
            "'&Agrave;'i", "'&Aacute;'i", "'&Acirc;'i", "'&Atilde;'i", "'&Auml;'i", "'&Aring;'i", "'&AElig;'i", "'&Ccedil;'i", "'&Egrave;'i", "'&Eacute;'i",
            "'&Ecirc;'i", "'&Euml;'i", "'&Igrave;'i", "'&Iacute;'i", "'&Icirc;'i", "'&Iuml;'i", "'&ETH;'i", "'&Ntilde;'i", "'&Ograve;'i", "'&Oacute;'i",
            "'&Ocirc;'i", "'&Otilde;'i", "'&Ouml;'i", "'&Oslash;'i", "'&OElig;'i", "'&Scaron;'i", "'&Ugrave;'i", "'&Uacute;'i", "'&Ucirc;'i", "'&Uuml;'i",
            "'&Yacute;'i", "'&Yuml;'i", "'&THORN;'i", "'&agrave;'i", "'&aacute;'i", "'&acirc;'i", "'&atilde;'i", "'&auml;'i", "'&aring;'i", "'&aelig;'i",
            "'&ccedil;'i", "'&egrave;'i", "'&eacute;'i", "'&ecirc;'i", "'&euml;'i", "'&igrave;'i", "'&iacute;'i", "'&icirc;'i", "'&iuml;'i", "'&eth;'i",
            "'&ntilde;'i", "'&ograve;'i", "'&oacute;'i", "'&ocirc;'i", "'&otilde;'i", "'&ouml;'i", "'&oslash;'i", "'&oelig;'i", "'&scaron;'i", "'&ugrave;'i",
            "'&uacute;'i", "'&ucirc;'i", "'&uuml;'i", "'&yacute;'i", "'&thorn;'i", "'&yuml;'i", "'&Alpha;'i", "'&Beta;'i", "'&Gamma;'i", "'&Delta;'i",
            "'&Epsilon;'i", "'&Zeta;'i", "'&Eta;'i", "'&Theta;'i", "'&Iota;'i", "'&Kappa;'i", "'&Lambda;'i", "'&Mu;'i", "'&Nu;'i", "'&Xi;'i",
            "'&Omicron;'i", "'&Pi;'i", "'&Rho;'i", "'&Sigma;'i", "'&Tau;'i", "'&Upsilon;'i", "'&Phi;'i", "'&Chi;'i", "'&Psi;'i", "'&Omega;'i",
            "'&alpha;'i", "'&beta;'i", "'&gamma;'i", "'&delta;'i", "'&epsilon;'i", "'&zeta;'i", "'&eta;'i", "'&theta;'i", "'&iota;'i", "'&kappa;'i",
            "'&lambda;'i", "'&mu;'i", "'&nu;'i", "'&xi;'i", "'&omicron;'i", "'&pi;'i", "'&rho;'i", "'&sigmaf;'i", "'&sigma;'i", "'&tau;'i",
            "'&upsilon;'i", "'&phi;'i", "'&chi;'i", "'&psi;'i", "'&omega;'i", "'&alefsym;'i", "'&piv;'i", "'&real;'i", "'&thetasym;'i", "'&upsih;'i",
            "'&weierp;'i", "'&image;'i", "'&larr;'i", "'&uarr;'i", "'&rarr;'i", "'&darr;'i", "'&harr;'i", "'&crarr;'i", "'&lArr;'i", "'&uArr;'i",
            "'&rArr;'i", "'&dArr;'i", "'&hArr;'i", "'&there4;'i", "'&sub;'i", "'&sup;'i", "'&nsub;'i", "'&sube;'i", "'&supe;'i", "'&oplus;'i",
            "'&otimes;'i", "'&perp;'i", "'&sdot;'i", "'&lceil;'i", "'&rceil;'i", "'&lfloor;'i", "'&rfloor;'i", "'&lang;'i", "'&rang;'i", "'&loz;'i",
            "'&spades;'i", "'&clubs;'i", "'&hearts;'i", "'&diams;'i", "'&ensp;'i", "'&emsp;'i", "'&thinsp;'i", "'&zwnj;'i", "'&zwj;'i", "'&lrm;'i",
            "'&rlm;'i", "'&shy;'i"
        );

        $replace = array(
            "&#160;", "&#38;", "&#34;", "&#162;", "&#8364;", "&#163;", "&#165;", "&#169;", "&#174;", "&#8482;",
            "&#8240;", "&#181;", "&#183;", "&#8226;", "&#8230;", "&#8242;", "&#8243;", "&#167;", "&#182;", "&#223;",
            "&#8249;", "&#8250;", "&#171;", "&#187;", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8218;", "&#8222;",
            "&#60;", "&#62;", "&#8804;", "&#8805;", "&#8211;", "&#8212;", "&#175;", "&#8254;", "&#164;", "&#166;",
            "&#168;", "&#161;", "&#191;", "&#710;", "&#732;", "&#176;", "&#8722;", "&#177;", "&#247;", "&#8260;",
            "&#215;", "&#185;", "&#178;", "&#179;", "&#188;", "&#189;", "&#190;", "&#402;", "&#8747;", "&#8721;",
            "&#8734;", "&#8730;", "&#8764;", "&#8773;", "&#8776;", "&#8800;", "&#8801;", "&#8712;", "&#8713;", "&#8715;",
            "&#8719;", "&#8743;", "&#8744;", "&#172;", "&#8745;", "&#8746;", "&#8706;", "&#8704;", "&#8707;", "&#8709;",
            "&#8711;", "&#8727;", "&#8733;", "&#8736;", "&#180;", "&#184;", "&#170;", "&#186;", "&#8224;", "&#8225;",
            "&#192;", "&#193;", "&#194;", "&#195;", "&#196;", "&#197;", "&#198;", "&#199;", "&#200;", "&#201;",
            "&#202;", "&#203;", "&#204;", "&#205;", "&#206;", "&#207;", "&#208;", "&#209;", "&#210;", "&#211;",
            "&#212;", "&#213;", "&#214;", "&#216;", "&#338;", "&#352;", "&#217;", "&#218;", "&#219;", "&#220;",
            "&#221;", "&#376;", "&#222;", "&#224;", "&#225;", "&#226;", "&#227;", "&#228;", "&#229;", "&#230;",
            "&#231;", "&#232;", "&#233;", "&#234;", "&#235;", "&#236;", "&#237;", "&#238;", "&#239;", "&#240;",
            "&#241;", "&#242;", "&#243;", "&#244;", "&#245;", "&#246;", "&#248;", "&#339;", "&#353;", "&#249;",
            "&#250;", "&#251;", "&#252;", "&#253;", "&#254;", "&#255;", "&#913;", "&#914;", "&#915;", "&#916;",
            "&#917;", "&#918;", "&#919;", "&#920;", "&#921;", "&#922;", "&#923;", "&#924;", "&#925;", "&#926;",
            "&#927;", "&#928;", "&#929;", "&#931;", "&#932;", "&#933;", "&#934;", "&#935;", "&#936;", "&#937;",
            "&#945;", "&#946;", "&#947;", "&#948;", "&#949;", "&#950;", "&#951;", "&#952;", "&#953;", "&#954;",
            "&#955;", "&#956;", "&#957;", "&#958;", "&#959;", "&#960;", "&#961;", "&#962;", "&#963;", "&#964;",
            "&#965;", "&#966;", "&#967;", "&#968;", "&#969;", "&#8501;", "&#982;", "&#8476;", "&#977;", "&#978;",
            "&#8472;", "&#8465;", "&#8592;", "&#8593;", "&#8594;", "&#8595;", "&#8596;", "&#8629;", "&#8656;", "&#8657;",
            "&#8658;", "&#8659;", "&#8660;", "&#8756;", "&#8834;", "&#8835;", "&#8836;", "&#8838;", "&#8839;", "&#8853;",
            "&#8855;", "&#8869;", "&#8901;", "&#8968;", "&#8969;", "&#8970;", "&#8971;", "&#9001;", "&#9002;", "&#9674;",
            "&#9824;", "&#9827;", "&#9829;", "&#9830;", "&#8194;", "&#8195;", "&#8201;", "&#8204;", "&#8205;", "&#8206;",
            "&#8207;", "&#173;"
        );
        $xml = preg_replace($search, $replace, $xml);

        return $xml;
    }


///////

    function Transform($xslfile, $xml)
    {
        #print '<!-- DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" -->'."\n";
        #print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">'."\n";
        if (is_file($this->docroot . '/templates/' . $xslfile)) {
            $xml = $this->removeHTMLentity($xml);

            if ($this->phpvers[0] == 5) {
                $xslt = new xsltProcessor;

                $xsl = new DOMDocument;
                $xsl->resolveExternals = TRUE;
                $xsl->substituteEntities = TRUE;

                $xsl->load($this->docroot . '/templates/' . $xslfile);
                $xslt->importStyleSheet($xsl);

                $pattern = "/<page>.*<\/page>/Uis";
                if (preg_match($pattern, $xml, $matches))
                    $xml = $matches[0];
                else
                    $xml = '<data>proba pera</data>';


                $xml_object = new DOMDocument;
                $xml_object->resolveExternals = TRUE;
                $xml_object->substituteEntities = TRUE;
                $xml_object->loadXML($xml);

                //print_r($xslt->transformToXML($xml_object));

                echo $xslt->transformToXML($xml_object);
                unset($xslt);
            } else {
                $xslt = domxml_xslt_stylesheet_file($this->docroot . '/templates/' . $xslfile);
                $dom = domxml_open_mem($xml);
                $final = $xslt->process($dom);
                print ($xslt->result_dump_mem($final));
                unset ($dom);
                unset($xslt);
            }
        } else print $this->docroot . '/templates/' . $xslfile . ' - not found';
    }

    function muteTransform($xslfile, $xml)
    {

        $file_path = $this->docroot . '/templates/' . $xslfile;
        if (is_file($file_path)) $file_path = $file_path;
        elseif (is_file($xslfile)) $file_path = $xslfile;


        if (is_file($file_path)) {
            if ($this->phpvers[0] == 5) {
                $xslt = new xsltProcessor;

                $xsl = new DOMDocument;
                $xsl->resolveExternals = TRUE;
                $xsl->substituteEntities = TRUE;

                $xsl->load($file_path);
                $xslt->importStyleSheet($xsl);

                $pattern = "/<page>.*<\/page>/Uis";
                if (preg_match($pattern, $xml, $matches))
                    $xml = $matches[0];
                else
                    $xml = '<data>proba pera</data>';


                $xml_object = new DOMDocument;
                $xml_object->resolveExternals = TRUE;
                $xml_object->substituteEntities = TRUE;
                $xml_object->loadXML($xml);

                $ret = $xslt->transformToXML($xml_object);
            } else {
                $xslt = domxml_xslt_stylesheet_file($file_path);
                $dom = domxml_open_mem($xml);
                $final = $xslt->process($dom);
                $ret = ($xslt->result_dump_mem($final));
                unset ($dom);
                unset($xslt);
            }
        } else print $file_path . ' - not found';

        return ($ret);
    }

//  User - functions
    function translit($cyr_str)
    {
        return strtr($cyr_str, $this->tr);
    }

    function GetMenu($pid, $id, $level = 0)
    {
        $ret = '';
        $sth = $this->execute("select ANOTHER_PAGES_ID,NAME,URL,IMAGE,IMAGE1,IS_NEW_WIN,REALCATNAME,DESCRIPTION from ANOTHER_PAGES where PARENT_ID=? and STATUS=1 order by ORDER_", $pid);
        while (list($V_ANOTHER_PAGES_ID, $V_NAME, $V_URL, $V_IMAGE1, $V_IMAGE2, $V_IS_NEW_WIN, $V_CATNAME, $V_DESCRIPTION) = mysql_fetch_array($sth, MYSQL_NUM)) {
            $IM_IMAGE = split('#', $V_IMAGE1);
            $IM_IMAGE1 = split('#', $V_IMAGE2);
            if (isset($id[$level]) && $V_ANOTHER_PAGES_ID == $id[$level]) $ret .= @"<menu id='$V_ANOTHER_PAGES_ID' sel='1' src='{$IM_IMAGE[0]}' w='{$IM_IMAGE[1]}' h='{$IM_IMAGE[2]}' src1='{$IM_IMAGE1[0]}' w1='{$IM_IMAGE1[1]}' h1='{$IM_IMAGE1[2]}' new='$V_IS_NEW_WIN'><name>$V_NAME</name><url>$V_URL</url><path>$V_CATNAME</path><description>$V_DESCRIPTION</description>" . $this->GetMenu($V_ANOTHER_PAGES_ID, $id, $level + 1) . "</menu>";
            else $ret .= @"<menu id='$V_ANOTHER_PAGES_ID' sel='0' src='{$IM_IMAGE[0]}' w='{$IM_IMAGE[1]}' h='{$IM_IMAGE[2]}' src1='{$IM_IMAGE1[0]}' w1='{$IM_IMAGE1[1]}' h1='{$IM_IMAGE1[2]}' new='$V_IS_NEW_WIN'><name>$V_NAME</name><url>$V_URL</url><path>$V_CATNAME</path><description>$V_DESCRIPTION</description></menu>";
        }

        return $ret;
    }


    function GetCatMenu($pid, $id, $level = 0)
    {
        $ret = '';
        $sth = $this->execute('select CATALOGUE_ID,NAME,IMAGE1,IF(CATNAME!="",REALCATNAME,"") as REALCATNAME,NO_DEDAULT from CATALOGUE where PARENT_ID=? and STATUS=1 and (COUNT_>0 or NO_DEDAULT=1) order by ORDERING', $pid);
        while (list($V_CATALOGUE_ID, $V_NAME, $V_IMAGE1, $V_REALCATNAME, $V_NO_DEDAULT) = mysql_fetch_array($sth, MYSQL_NUM)) {
            $IMG = explode('#', $V_IMAGE1);
            if (isset($id[$level]) && $V_CATALOGUE_ID == $id[$level]) $ret .= "<cat id='$V_CATALOGUE_ID' sel='1' nodef='$V_NO_DEDAULT' src='$IMG[0]' w='$IMG[1]' h='$IMG[2]'><name>$V_NAME</name><path>$V_REALCATNAME</path>" . $this->GetCatMenu($V_CATALOGUE_ID, $id, $level + 1) . '</cat>';
            else $ret .= "<cat id='$V_CATALOGUE_ID' nodef='$V_NO_DEDAULT' src='$IMG[0]' w='$IMG[1]' h='$IMG[2]'><name>$V_NAME</name><path>$V_REALCATNAME</path></cat>";
        }

        return $ret;
    }

    function GetCataloguePagesMenu($pid, $id, $level = 0)
    {
        $ret = '';
        $sth = $this->execute("select CATALOGUE_PAGES_ID,NAME,URL,REALCATNAME,DESCRIPTION from CATALOGUE_PAGES where PARENT_ID=? and STATUS=1 order by ORDER_", $pid);
        while (list($V_ANOTHER_PAGES_ID, $V_NAME, $V_URL, $V_CATNAME, $V_DESCRIPTION) = mysql_fetch_array($sth, MYSQL_NUM)) {
            $IM_IMAGE = split('#', $V_IMAGE1);
            $IM_IMAGE1 = split('#', $V_IMAGE2);
            if (isset($id[$level]) && $V_ANOTHER_PAGES_ID == $id[$level]) $ret .= @"<cat_page id='$V_ANOTHER_PAGES_ID' sel='1' src='{$IM_IMAGE[0]}' w='{$IM_IMAGE[1]}' h='{$IM_IMAGE[2]}' src1='{$IM_IMAGE1[0]}' w1='{$IM_IMAGE1[1]}' h1='{$IM_IMAGE1[2]}' new='$V_IS_NEW_WIN'><name>$V_NAME</name><url>$V_URL</url><path>$V_CATNAME</path><description>$V_DESCRIPTION</description>" . $this->GetCataloguePagesMenu($V_ANOTHER_PAGES_ID, $id, $level + 1) . "</cat_page>";
            else $ret .= @"<cat_page id='$V_ANOTHER_PAGES_ID' sel='0' src='{$IM_IMAGE[0]}' w='{$IM_IMAGE[1]}' h='{$IM_IMAGE[2]}' src1='{$IM_IMAGE1[0]}' w1='{$IM_IMAGE1[1]}' h1='{$IM_IMAGE1[2]}' new='$V_IS_NEW_WIN'><name>$V_NAME</name><url>$V_URL</url><path>$V_CATNAME</path><description>$V_DESCRIPTION</description></cat_page>";
        }

        return $ret;
    }


    /*
  function GetCatMenu($pid,$id, $bid=0)
  {
        $ret='';
        $sth=$this->execute('select CATALOGUE_ID,NAME,IMAGE1,REALCATNAME,NO_DEDAULT from CATALOGUE where PARENT_ID=? and STATUS=1 and (COUNT_>0 or NO_DEDAULT=1) order by ORDERING',$pid);
        while(list($V_CATALOGUE_ID,$V_NAME,$V_IMAGE1,$V_REALCATNAME,$V_NO_DEDAULT)=mysql_fetch_array($sth, MYSQL_NUM))
        {
          $IMG=explode('#',$V_IMAGE1);
          $ret.="<cat id='$V_CATALOGUE_ID' nodef='$V_NO_DEDAULT' src='$IMG[0]' w='$IMG[1]' h='$IMG[2]'><name>$V_NAME</name><path>$V_REALCATNAME</path></cat>";
        }
        return $ret;
  }
*/

    function GetTitles($id, $bcount = null)
    {
        list($TITLE, $DESCRIPTION, $KEYWORDS) = $this->selectrow_array('select TITLE,DESCRIPTION,KEYWORDS from ANOTHER_PAGES where ANOTHER_PAGES_ID=?', $id);

        return "<title>$TITLE</title><description>$DESCRIPTION</description><keywords>$KEYWORDS</keywords>" . $this->GetPageExtAttrs($bcount);
    }

    function GetPageExtAttrs($bcount)
    {
        if (!isset($bcount)) $bcount = $_COOKIE['noteCount'];
        $ret = '<srcid>' . $this->SRCID . '</srcid>';
        $ret .= "<basket_count cnt='$bcount'>" . intval($bcount) . ' ' . $this->get_word($bcount) . '</basket_count>';

        return $ret;
    }

    function UpdateRange($id)
    {
        $this->execute('delete from ITEMR where ITEM_ID=?', $id);
        $this->execute('insert into ITEMR (ITEM_ID,ATTRIBUT_ID,RANGE_LIST_ID) select I.ITEM_ID,A.ATTRIBUT_ID,R.RANGE_LIST_ID from ITEM0 I,ATTRIBUT A,RANGE_LIST R where I.ITEM_ID=? and I.ATTRIBUT_ID=A.ATTRIBUT_ID and R.ATTRIBUT_ID=A.ATTRIBUT_ID and A.IS_RANGEABLE=1 and R.MIN<=I.VALUE and if(R.MAX>0,if(I.VALUE<=R.MAX,1,0),1) and I.VALUE>0', $id);
        $this->execute('insert into ITEMR (ITEM_ID,ATTRIBUT_ID,RANGE_LIST_ID) select I.ITEM_ID,A.ATTRIBUT_ID,R.RANGE_LIST_ID from ITEM1 I,ATTRIBUT A,RANGE_LIST R where I.ITEM_ID=? and I.ATTRIBUT_ID=A.ATTRIBUT_ID and R.ATTRIBUT_ID=A.ATTRIBUT_ID and A.IS_RANGEABLE=1 and R.MIN<=I.VALUE and if(R.MAX>0,if(I.VALUE<=R.MAX,1,0),1) and I.VALUE>0', $id);

        return 0;
    }

    function UpdateAllRanges()
    {
        $this->execute('delete from ITEMR');
        $this->execute('insert into ITEMR (ITEM_ID,ATTRIBUT_ID,RANGE_LIST_ID) select I.ITEM_ID,A.ATTRIBUT_ID,R.RANGE_LIST_ID from ITEM0 I,ATTRIBUT A,RANGE_LIST R where I.ATTRIBUT_ID=A.ATTRIBUT_ID and R.ATTRIBUT_ID=A.ATTRIBUT_ID and A.IS_RANGEABLE=1 and R.MIN<=I.VALUE and if(R.MAX>0,if(I.VALUE<=R.MAX,1,0),1) and I.VALUE>0');
        $this->execute('insert into ITEMR (ITEM_ID,ATTRIBUT_ID,RANGE_LIST_ID) select I.ITEM_ID,A.ATTRIBUT_ID,R.RANGE_LIST_ID from ITEM1 I,ATTRIBUT A,RANGE_LIST R where I.ATTRIBUT_ID=A.ATTRIBUT_ID and R.ATTRIBUT_ID=A.ATTRIBUT_ID and A.IS_RANGEABLE=1 and R.MIN<=I.VALUE and if(R.MAX>0,if(I.VALUE<=R.MAX,1,0),1) and I.VALUE>0');

        return;
    }

    function CheckCount($id)
    {
        $summ = 0;
        $sth = $this->execute('select CATALOGUE_ID from CATALOGUE where PARENT_ID=?', $id);
        while (list($V_CATALOGUE_ID) = mysql_fetch_array($sth, MYSQL_NUM)) {
            $summ += $this->CheckCount($V_CATALOGUE_ID);
        }
        $summ += $this->selectrow_array('select count(*) from ITEM where CATALOGUE_ID=?', $id);
        $this->execute('update CATALOGUE set COUNT_=? where CATALOGUE_ID=?', $summ, $id);

        return $summ;
    }

    function Rebuild($id)
    {
        $sth = $this->execute('select CATALOGUE_ID from CATALOGUE where PARENT_ID=? and REALSTATUS=1', $id[0]);
        while (list($V_CATALOGUE_ID) = mysql_fetch_array($sth, MYSQL_NUM)) {
            $this->Rebuild(array_merge(array($V_CATALOGUE_ID), $id));
        }
        foreach ($id as $tid) {
            if ($tid) {
                $this->execute('insert into CAT_ITEM (CATALOGUE_ID,ITEM_ID) select ?,ITEM_ID from ITEM where CATALOGUE_ID=? and STATUS=1', $tid, $id[0]);
            }
        }

        return 0;
    }


    function CheckEmptyItems($table, $V_ATTRIBUT_ID)
    {

        list ($TYPE, $IS_RANGEABLE) = $this->selectrow_array('select TYPE,IS_RANGEABLE from ATTRIBUT where STATUS=1 and ATTRIBUT_ID=?', $V_ATTRIBUT_ID);
        $query = ''; //echo  $V_ATTRIBUT_ID."=>".$TYPE."=>". $IS_RANGEABLE ."<br>";
        if ($TYPE < 2 && $IS_RANGEABLE) {
            $query .= 'select S.ITEM_ID,G.RANGE_LIST_ID from ' . $table . ' S inner join ITEMR G on (S.ITEM_ID=G.ITEM_ID and G.ATTRIBUT_ID=' . $V_ATTRIBUT_ID . ') '; #group by G.RANGE_LIST_ID
        } elseif ($TYPE == 6) {
            $query .= 'select S.ITEM_ID,G.VALUE from ' . $table . ' S inner join ITEM0 G on (S.ITEM_ID=G.ITEM_ID and G.ATTRIBUT_ID=' . $V_ATTRIBUT_ID . '  and G.VALUE > 0) '; #group by G.VALUE
        } elseif ($TYPE == 2) {
            $query .= 'select S.ITEM_ID,G.VALUE from ' . $table . ' S inner join ITEM2 G on (S.ITEM_ID=G.ITEM_ID and G.ATTRIBUT_ID=' . $V_ATTRIBUT_ID . '  and G.VALUE <> "" inner join ATTRIBUT_LIST AL on AL.ATTRIBUT_LIST_ID=G.VALUE) '; #group by  G.VALUE
        } elseif ($TYPE == 0 || $TYPE == 3) {
            $query .= 'select S.ITEM_ID,G.VALUE from ' . $table . ' S inner join ITEM0 G on (S.ITEM_ID=G.ITEM_ID and G.ATTRIBUT_ID=' . $V_ATTRIBUT_ID . '  and G.VALUE <> "") inner join ATTRIBUT_LIST AL on AL.ATTRIBUT_LIST_ID=G.VALUE'; #group by G.VALUE
        }

        $count = 0; //echo $query."<br>";
        $sth = $this->execute($query);
        while (list($V_ITEM_ID, $V_VALUE) = @mysql_fetch_array($sth, MYSQL_NUM)) {
            echo $V_ITEM_ID . "=>" . $V_VALUE . "<br>";
            if (($TYPE == 6 && ($V_VALUE == 1 || $V_VALUE == 0)) || ($TYPE != 6 && $V_VALUE != '')) $count++;
        }
        //echo $V_ATTRIBUT_ID."=>".$count."<br>";
        if ($count > 0) {
            return 0;
        } else {
            return 1;
        }
    }

    function GetTempTableName($PARAM)
    {
        $i = 0;
        if (count($PARAM) == 0) return 't0';

        $i = 1;
        foreach ($PARAM as $attr => $val) {
            list($TYPE, $IS_RANGEABLE) = $this->selectrow_array('select TYPE,IS_RANGEABLE from ATTRIBUT where STATUS=1 and ATTRIBUT_ID=?', $attr);
            $this->execute('DROP TABLE IF EXISTS t' . $i);
            $this->execute('create temporary table t' . $i . ' (ITEM_ID int(12) unsigned auto_increment NOT NULL,PRIMARY KEY (`ITEM_ID`)) ENGINE = MEMORY');
            $query = 'insert into t' . $i . ' select S.ITEM_ID from t' . (1 - $i) . ' S inner join ';

            $value = $PARAM[$attr];

            if ($TYPE < 2 && $IS_RANGEABLE) $query .= 'ITEMR G on (S.ITEM_ID=G.ITEM_ID and G.ATTRIBUT_ID=?) where G.RANGE_LIST_ID=?';
            //elseif ($TYPE <2 && !$IS_RANGEABLE) $query.='{ITEM$TYPE G on (S.ITEM_ID=G.ITEM_ID and G.ATTRIBUT_ID=?) where ABS(G.VALUE-?)<=0.1*G.VALUE';
            //elseif($TYPE >1 && $TYPE<5) $query.='ITEM0 G on (S.ITEM_ID=G.ITEM_ID and G.ATTRIBUT_ID=?) where G.VALUE=?';
            //elseif($TYPE==6) $query.='ITEM0 G on (S.ITEM_ID=G.ITEM_ID and G.ATTRIBUT_ID=?) where G.VALUE=?';
            //elseif($TYPE==2) $query.='ITEM2 G on (S.ITEM_ID=G.ITEM_ID and G.ATTRIBUT_ID=?) where G.VALUE=?';
            elseif ($TYPE == 2) {
                $value = $this->selectrow_array("select NAME from ATTRIBUT_LIST where ATTRIBUT_ID=? and ATTRIBUT_LIST_ID=?", $attr, $PARAM[$attr]);
                if ($value == '') $value = $PARAM[$attr];
                $query .= 'ITEM2 G on (S.ITEM_ID=G.ITEM_ID) where G.ATTRIBUT_ID=? and G.VALUE=?';
            } elseif ($TYPE == 0 || $TYPE == 3) {
                //$value = $this->selectrow_array("select NAME from ATTRIBUT_LIST where ATTRIBUT_ID=? and ATTRIBUT_LIST_ID=?",$attr,$PARAM[$attr]);
                //if($value == '')
                //$value=$PARAM[$attr];
                $query .= 'ITEM0 G on (S.ITEM_ID=G.ITEM_ID) where G.ATTRIBUT_ID=? and G.VALUE=?';
            } elseif ($TYPE > 4) {
                if ($value == 0) $query .= 'ITEM0 G on (S.ITEM_ID=G.ITEM_ID) where G.ATTRIBUT_ID=? and (G.VALUE=? or G.VALUE=2)';
                else $query .= 'ITEM0 G on (S.ITEM_ID=G.ITEM_ID) where G.ATTRIBUT_ID=? and G.VALUE=?';
            }
            //echo $query;
            $this->execute($query, $attr, $value);
            $i = ($i + 1) % 2;
        }

        return "t" . (1 - $i);
    }

    function GetDocPath($id)
    {
        $PARR = array($id);
        $PATH = '';
        while (list($PARENTID, $NAME, $CATNAME, $URL, $IS_GROUP_NODE) = $this->selectrow_array('select PARENT_ID,NAME,IF(CATNAME!="",REALCATNAME,"") as REALCATNAME,URL,IS_GROUP_NODE from ANOTHER_PAGES where ANOTHER_PAGES_ID=?', $id)) {
            if (!$IS_GROUP_NODE) {
                $PATH = "<path id='$id'><name>$NAME</name><path>$CATNAME</path><url>$URL</url></path>" . $PATH;
            }
            if ($PARENTID == 0) break;
            $id = $PARENTID;
            $PARR[] = $id;
        };
        $PARR = array_reverse($PARR);

        return array($PARR, $PATH);
    }

    function GetCatPath($id)
    {
        $PARR = array($id);
        $PATH = '';
        while (list($PARENTID, $NAME, $CATNAME) = $this->selectrow_array('select PARENT_ID,NAME,IF(CATNAME!="",REALCATNAME,"") as REALCATNAME from CATALOGUE where CATALOGUE_ID=?', $id)) {
            $PATH = "<path id='$id'><name>$NAME</name><path>$CATNAME</path></path>" . $PATH;
            if ($PARENTID == 0) break;
            $id = $PARENTID;
            $PARR[] = $id;
        };
        $PARR = array_reverse($PARR);

        return array($PARR, $PATH);
    }

    function GetCataloguePagesPath($id)
    {
        $PARR = array($id);
        $PATH = '';
        while (list($PARENTID, $NAME, $CATNAME) = $this->selectrow_array('select PARENT_ID,NAME,IF(CATNAME!="",REALCATNAME,"") as REALCATNAME from CATALOGUE_PAGES where CATALOGUE_PAGES_ID=?', $id)) {
            $PATH = "<path id='$id'><name>$NAME</name><path>$CATNAME</path></path>" . $PATH;
            if ($PARENTID == 0) break;
            $id = $PARENTID;
            $PARR[] = $id;
        };
        $PARR = array_reverse($PARR);

        return array($PARR, $PATH);
    }

//      Mysql - functions
    function Param($name)
    {
        return isset($_REQUEST[$name]) ? $_REQUEST[$name] : '';
    }

    function GetSequence($name)
    {
        $this->execute('UPDATE SEQUENCES SET ID=LAST_INSERT_ID(ID+1) where NAME=?', $name);

        return mysql_insert_id($this->dbh);
    }


    function last_insert_id()
    {
        return mysql_insert_id($this->dbh);
    }

    function sql_err()
    {
        return mysql_error($this->dbh);
    }


    function explain()
    {
        $args = func_get_args();
        $query = call_user_func_array("___mysql_make_qw", $args);
        $sth = mysql_query('explain ' . $query, $this->dbh);
        $ret = '<table border=1 align=center><tr>';
        $fcount = mysql_num_fields($sth);
        for ($i = 0; $i < $fcount; $i++) $ret .= '<td>' . mysql_field_name($sth, $i) . '</td>';
        while ($r = mysql_fetch_array($sth, MYSQL_NUM)) {
            $ret .= '<tr>';
            for ($i = 0; $i < $fcount; $i++) {
                $ret .= '<td valign=top>' . $r[$i] . '</td>';
            }
            $ret .= "</tr>";
        }
        mysql_free_result($sth);

        return $ret;
    }

    function execute()
    {
        $args = func_get_args();
        $query = call_user_func_array("___mysql_make_qw", $args);
        if ($this->DEBUG > 0) {
            print "<pre>";
            print_r($query);
            print "</pre>";
        }

        return mysql_query($query, $this->dbh);
    }

    function selectrow_array()
    {
        $args = func_get_args();
        $query = call_user_func_array("___mysql_make_qw", $args);
        $sth = mysql_query($query, $this->dbh);
        if ($sth) {
            $arr = mysql_fetch_array($sth, MYSQL_NUM);
            mysql_free_result($sth);
        } else $arr[0] = '';
        if (count($arr) == 1) return $arr[0];
        else return $arr;
    }

    function selectrow_arrayQ()
    {
        $args = func_get_args();
        $query = call_user_func_array("___mysql_make_qw", $args);
        $sth = mysql_query($query, $this->dbh);
        if ($sth)
            if ($arr = mysql_fetch_array($sth, MYSQL_NUM)) {
                mysql_free_result($sth);
                for ($i = 0; $i < count($arr); $i++) $arr[$i] = htmlspecialchars($arr[$i]);
// php5 only            foreach ($arr as &$val) $val=htmlspecialchars($val);
                if (count($arr) == 1) return $arr[0];
                else return $arr;
            }

        return '';
    }

//  utils
    function UnlinkFile($name, $VIRTUAL_IMAGE_PATH)
    {
        list($name) = split('#', $name);
        @unlink($this->docroot . '/images' . $VIRTUAL_IMAGE_PATH . $name);
    }

    function FileCopy($from, $to)
    {
        @copy($this->docroot . $from, $this->docroot . $to);
    }

    function GetImagePath($path)
    {
        return ($this->docroot . '/images/' . $path);
    }

    function PictureOptimal($id, $body, $slash, $prefix, $path, &$pictures, &$pictypes)
    {
        $params = array('name', 'zoom', 'align', 'format', 'src', 'width', 'height', 'href', 'target', 'border');
        $params1 = array('format1', 'src1', 'width1', 'height1');
        $h['format'] = 'jpg';
        $ret = '<picture';


        $count = preg_match_all('/(\w+?)\s*?\=\s*?"(.*?)"/', $body, $p);
        for ($i = 0; $i < $count; $i++) $h[$p[1][$i]] = $p[2][$i];

        if (!$h['format1']) {
            $h['format1'] = $h['format'];
        }
        if (isset($pictypes[$h['name']])) $h['format'] = $pictypes[$h['name']]; else $pictypes[$h['name']] = $h['format'];
        if (isset($pictypes[$h['name'] . '_b'])) $h['format1'] = $pictypes[$h['name'] . '_b'];

        $name = $prefix . '_' . $id . '_' . $h['name'] . '.' . $h['format'];
        $name1 = $prefix . '_' . $id . '_' . $h['name'] . '_b' . '.' . $h['format1'];
        $h['src'] = $path . $name;
        $h['src1'] = $path . $name1;

        list($h['width'], $h['height']) = @getimagesize($this->docroot . '/images' . $h['src']);
        list($h['width1'], $h['height1']) = @getimagesize($this->docroot . '/images' . $h['src1']);

        if ($h['zoom'] == 'yes') $params = array_merge($params, $params1);

        foreach ($params as $tmp) {
            if (array_key_exists($tmp, $h)) {
                $ret .= " $tmp=\"{$h[$tmp]}\"";
            }
        }
        $pictures[$h['name']] = $h['zoom'];

        return $ret . $slash . '>';
    }

    function FileOptimal($id, $body, $slash, $prefix, $path, &$files, &$filetypes)
    {
        $params = array('name', 'src', 'size', 'format');

        $h['format'] = 'doc';
        $ret = '<file';

        $count = preg_match_all('/(\w+?)\s*?\=\s*?"(.*?)"/', $body, $p);
        for ($i = 0; $i < $count; $i++) $h[$p[1][$i]] = $p[2][$i];

        if (isset($filetypes[$h['name']])) {
            $h['format'] = $filetypes[$h['name']];
        }
        $name = $prefix . '_' . $id . '_' . $h['name'] . '.' . $h['format'];
        $h['src'] = $path . $name;

        if (file_exists($this->docroot . '/images' . $h['src'])) $h['size'] = filesize($this->docroot . '/images' . $h['src']);


        foreach ($params as $tmp) {
            if (array_key_exists($tmp, $h)) {
                $ret .= " $tmp=\"{$h[$tmp]}\"";
            }
        }
        $files[$h['name']] = $h['size'];

        return $ret . $slash . '>';
    }

    function FlashOptimal($id, $body, $slash, $prefix, $path, &$flashes, &$flashtypes)
    {
        $params = array('name', 'width', 'height', 'src', 'format');
        $h['format'] = 'swf';
        $ret = '<flash';

        $count = preg_match_all('/(\w+?)\s*?\=\s*?"(.*?)"/', $body, $p);
        for ($i = 0; $i < $count; $i++) $h[$p[1][$i]] = $p[2][$i];

        if (isset($flashtypes[$h['name']])) $h['format'] = $flashtypes[$h['name']]; else $flashtypes[$h['name']] = $h['format'];

        $name = $prefix . '_' . $id . '_' . $h['name'] . '.' . $h['format'];
        $h['src'] = $path . $name;

#       list($h['width'], $h['height']) = @getimagesize($this->docroot.'/images'.$h['src']);
        if (file_exists($this->docroot . '/images' . $h['src'])) $h['size'] = filesize($this->docroot . '/images' . $h['src']);

        foreach ($params as $tmp) {
            if (array_key_exists($tmp, $h)) {
                $ret .= " $tmp=\"{$h[$tmp]}\"";
            }
        }
        $flashes[$h['name']] = $h['size'];

        return $ret . $slash . '>';
    }


    function PicturePost($fildname, $oldname, $name, $VIRTUAL_IMAGE_PATH)
    {
        if ($_FILES[$fildname]['error'] == UPLOAD_ERR_OK) {
            $tmpname = $_FILES[$fildname]['tmp_name'];
            $remotename = $_FILES[$fildname]['name'];
            if (preg_match('/\.([^\.]+?)$/', $remotename, $p)) {
                if ($oldname) {
                    list($oldname) = split('#', $oldname);
                    @unlink($this->docroot . '/images' . $VIRTUAL_IMAGE_PATH . $oldname);
                }
                list($width, $height, $type, $attr) = getimagesize($tmpname);
                $name = $name . '.' . strtolower($p[1]);
                move_uploaded_file($tmpname, $this->docroot . '/images' . $VIRTUAL_IMAGE_PATH . $name);

                return "$name#$width#$height";
            }
        } else return $oldname;
    }

    function PicturePostResize($fildname, $oldname, $name, $VIRTUAL_IMAGE_PATH, $width = '', $height = '', $rgb = 0xFFFFFF, $quality = 100)
    {
        if ($width == '' && $height == '') {
            if (!strchr($name, 's_')) $width = 100;
            else $width = 200;
        }

        if ($_FILES[$fildname]['error'] == UPLOAD_ERR_OK) {
            $tmpname = $_FILES[$fildname]['tmp_name'];
            $remotename = $_FILES[$fildname]['name'];
            $src = $tmpname;
            if (preg_match('/\.([^\.]+?)$/', $remotename, $p)) {
                if ($oldname) {
                    list($oldname) = split('#', $oldname);
                    @unlink($this->docroot . '/images' . $VIRTUAL_IMAGE_PATH . $oldname);
                }
                $size = getimagesize($tmpname);

                $format = strtolower(substr($size['mime'], strpos($size['mime'], '/') + 1));
                $icfunc = "imagecreatefrom" . $format;
                if (!function_exists($icfunc)) return false;

                $ratio = 1;

                if ($size[0] <= $width) $x_ratio = 1;
                else $x_ratio = $width / $size[0];

                if ($size[1] <= $height) $y_ratio = 1;
                else $y_ratio = $height / $size[1];

                //echo $x_ratio."=>".$y_ratio."<hr>";

                if (!$x_ratio) $ratio = $y_ratio;
                elseif (!$y_ratio) $ratio = $x_ratio;
                //elseif (!$x_ratio && $y_ratio) return 0;

                //echo $ratio;
                if ($width && $height) {
                    $new_width = $width;
                    $new_height = $height;
                } else {
                    $new_width = floor($size[0] * $ratio);
                    $new_height = floor($size[1] * $ratio);
                }
                $new_left = 0;
                $new_top = 0;

                $name = $name . '.' . strtolower($p[1]);
                $isrc = $icfunc($src);
                $idest = imagecreatetruecolor($new_width, $new_height);
                $dest = $this->docroot . '/images' . $VIRTUAL_IMAGE_PATH . $name; //echo $name."<br>";

                imagefill($idest, 0, 0, $rgb);
                imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, $new_width, $new_height, $size[0], $size[1]);

                imagejpeg($idest, $dest, $quality);
                imagedestroy($isrc);
                imagedestroy($idest);

                //@chmod($dest, 0777);
                return "$name#$new_width#$new_height";
            }
        } else return $oldname;
    }


    function FilePost($fildname, $oldname, $name, $VIRTUAL_IMAGE_PATH)
    {
        if ($_FILES[$fildname]['error'] == UPLOAD_ERR_OK) {
            $tmpname = $_FILES[$fildname]['tmp_name'];
            $remotename = $_FILES[$fildname]['name'];
            if (preg_match('/\.([^\.]+?)$/', $remotename, $p)) {
                $suffix = $p[1];
                if ($oldname) {
                    list($oldname) = split('#', $oldname);
                    @unlink($this->docroot . '/images' . $VIRTUAL_IMAGE_PATH . $oldname);
                }
                $size = filesize($tmpname);
                $name = $name . '.' . strtolower($p[1]);
                move_uploaded_file($tmpname, $this->docroot . '/images' . $VIRTUAL_IMAGE_PATH . $name);

                return "$name#$size#$suffix";
            }
        } else return $oldname;
    }

    function Spravotchnik()
    {
        $ret = '';
        $args = func_get_args();
        $cur = array_shift($args);
        $sth = call_user_func_array(array(&$this, 'execute'), $args);
        while (list($V_ID, $V_VAL) = mysql_fetch_array($sth, MYSQL_NUM)) {
            if (is_array($cur)) {
                if (in_array($V_ID, $cur)) {
                    $ret .= "<option value='$V_ID' selected=''>$V_VAL</option>";
                } else {
                    $ret .= "<option value='$V_ID'>$V_VAL</option>";
                }
            } else {
                if ($cur == $V_ID) {
                    $ret .= "<option value='$V_ID' selected=''>$V_VAL</option>";
                } else {
                    $ret .= "<option value='$V_ID'>$V_VAL</option>";
                }
            }
        }

        return $ret;
    }

    function TreeSpravotchnik()
    {
        $ret = '';
        $args = func_get_args();
        $sel = array_shift($args);
        $query = array_shift($args);
        $parent_id = array_shift($args);
        $path = array_shift($args);
        $sth = call_user_func_array(array(&$this, 'execute'), array_merge(array($query), $args, array($parent_id)));
        while (list($V_ID, $V_VAL) = mysql_fetch_array($sth, MYSQL_NUM)) {
            if (is_array($sel)) {
                if (in_array($V_ID, $sel)) {
                    $ret .= "<option value='$V_ID' selected=''>$path/$V_VAL</option>";
                } else {
                    $ret .= "<option value='$V_ID'>$path/$V_VAL</option>";
                }
            } else {
                if ($sel == $V_ID) {
                    $ret .= "<option value='$V_ID' selected=''>$path/$V_VAL</option>";
                } else {
                    $ret .= "<option value='$V_ID'>$path/$V_VAL</option>";
                }
            }
            $ret .= call_user_func_array(array(&$this, 'TreeSpravotchnik'), array_merge(array($sel, $query, $V_ID, "$path/$V_VAL"), $args));
        }

        return $ret;
    }

    function GetTreePath()
    {
        $ret = '';
        $args = func_get_args();
        $query = $args[0];
        $parentId = $args[1];
        while (list($parentId, $name) = $this->selectrow_array($query, $parentId)) {
            $ret = ' /' . $name . $ret;
        }

        return $ret;
    }

    function Enumerator(&$arr, $cur)
    {
        $ret = '';
        $i = 0;

        foreach ($arr as $it) {
            if ((string) $i === (string) $cur) {
                $ret .= "<option value=\"$i\" selected=\"\">$it</option>";
            } else {
                $ret .= "<option value=\"$i\">$it</option>";
            }
            $i++;
        }

        return $ret;
    }

    function Enumerator2(&$arr, $cur)
    {
        $ret = '';
        $i = 0;

        foreach ($arr as $it) {
            if ((string) $it === (string) $cur) {
                $ret .= "<option value=\"$it\" selected=\"\" style=\"background: #$it\">$it</option>";
            } else {
                $ret .= "<option value=\"$it\" style=\"background: #$it\">$it</option>";
            }
            $i++;
        }

        return $ret;
    }

    /**
     * @return void
     * @desc Сохраняет захлопнутые секции в сессию.
     */
    function makeCookieActions()
    {
        global $closedSections;
        $closedSections = (isset($_SESSION['closedSections']) && $_SESSION['closedSections']) ? $_SESSION['closedSections'] : array();

        $sectionActionsString = isset($_COOKIE['sectionActions']);
        $sectionActionsArray = explode('|', $sectionActionsString);

        for ($i = 0; $i < count($sectionActionsArray); $i++) {
            if (preg_match('~(-|\+)?(\d+)~', $sectionActionsArray[$i], $matches)) {
                switch ($matches[1]) {
                    case "-":
                        $closedSections[$matches[2]] = 1;
                        break;
                    default:
                        unset($closedSections[$matches[2]]);
                        break;
                }
            }
        }
        setcookie('sectionActions', '', null, '/admin/');

        return;
    }

    /**
     * @return array
     * @desc Выполняет запрос и возвращает двумерный массив
     */
    function select()
    {
        $args = func_get_args();
        $query = call_user_func_array("___mysql_make_qw", $args);
        if ($this->DEBUG > 0) show($query);
        $result = @mysql_query($query, $this->dbh) or die (mysql_error() . "<!--\r\n" . $query . "\r\n-->");
        $i = 0;
        $OutputArray = array();
        while ($data = mysql_fetch_assoc($result)) {
            $OutputArray[$i] = $data;
            $i++;
        }

        return ($OutputArray);
    }
}


function ___mysql_make_qw()
{
    $args = func_get_args();
    $tmpl =& $args[0];
    $tmpl = str_replace("%", "%%", $tmpl);
    $tmpl = str_replace("?", "%s", $tmpl);
    foreach ($args as $i => $v) {
        if (!$i) continue;
        if (is_int($v) && $v[0] != '0') continue; // changed 18.10.2006
        $args[$i] = "'" . mysql_real_escape_string($v) . "'";
    }
    for ($i = $c = count($args) - 1; $i < $c + 20; $i++)
        $args[$i + 1] = "UNKNOWN_PLACEHOLDER_$i";

    return call_user_func_array("sprintf", $args);
}


/**
 * @return string
 *
 * @param mixed $var
 *
 * @desc Возвращает строку-представление переданного аргумента в удобочитаемом (в браузере) виде
 */
function show($var)
{
    print "<hr><pre>";
    print_r($var);
    print "</pre>";
}

/**
 * @return void
 *
 * @param resource $sth
 *
 * @desc Фетчит и выводит в удобочитаемом виде результат sql-запроса
 */
function showMysqlResult($sth)
{
    $i = 0;
    while ($data = mysql_fetch_assoc($sth)) {
        $OutputArray[$i] = $data;
        $i++;
    }
    show($OutputArray);
}

?>
