<?php
require('core.php');

//ini_set("display_errors", 1);
//ini_set("display_startup_errors", 1);

set_time_limit(0);

define('SITE_PATH', $_SERVER['DOCUMENT_ROOT']);

define('ROOT_PATH', realpath(dirname(__FILE__) . "/../../"));

define('ZEND_PATH', ROOT_PATH . '/library');

//define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . '/../');
//define('ZEND_PATH', ROOT_PATH.'Zend');


define('ROOT_WWW', $_SERVER['DOCUMENT_ROOT']);
define('APPLICATION_MODELS', ROOT_PATH . '/application');

define('UPLOAD_XML', ROOT_PATH . '/upload_xml');
define('UPLOAD_IMAGES', ROOT_PATH . '/upload_images');

define('IS_LIDER', 'hits');
define('IS_RECOMEND', 'newest');

set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            realpath(ROOT_PATH),
            realpath(ZEND_PATH),
            realpath(APPLICATION_MODELS),
            get_include_path(),
        )
    )
);

$cmf = new SCMF('ITEM_IMPORT');
if (!$cmf->GetRights()) {
    header('Location: login.php');
    exit;
}

if (isset($_REQUEST['del_file']) && $_REQUEST['del_file']) {
    $fn = UPLOAD_XML . '/' . $_REQUEST['fl'];
    if (file_exists($fn)) {
        unlink($fn);
    }
    header('Location: ITEM_IMPORT.php');
}

$cmf->HeaderNoCache();
$cmf->MakeCommonHeader();
?>
    <script type="text/javascript" src="js/index.js"></script>
    <h2 class='h2'>Обновление каталога товаров</h2>
    <form action="ITEM_IMPORT.php" method="POST" enctype="multipart/form-data">
        <b>Выберите XML-файл:</b> <input type="file" name="file"> <br/><br/>
        <input type="submit" name="update_now" value="Обновить сейчас">
        <input type="submit" name="update_later" value="Обновить по расписанию">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="button" name="load_images" value="Обновить фото товаров" style="background: #FFCCCC;">
    </form>
    <br>
    <div id="err_mess"></div>

<?
//Вывод списка файлов в каталоге
$dir = UPLOAD_XML;
$fd = opendir($dir);
echo '<p><table width="50%">';
$num = 1;
while ($files = readdir($fd)) {
    if ($files != '..' && $files != '.') {
        $_file_args = explode('.', $files);
        $_file_ext = array_pop($_file_args);

        if ($_file_ext == 'xml' || $_file_ext == 'zip') {
            echo '<tr><td width="10">' . $num . '. </td><td><b>' . $files . '</b></td><td><a href="ITEM_IMPORT.php?del_file=1&fl=' . $files . '" onClick="return confirm(\'Вы действительно хотите удалить файл?\')">Удалить</a></td></tr>';
            $num++;
        }
    }
}
echo '</table>';
closedir($fd);

require_once 'Zend/Loader.php';
require_once 'Zend/Exception.php';
//require_once ZEND_PATH.'Zend/Loader.php';
//require_once ZEND_PATH.'Zend/Exception.php';

require_once SITE_PATH . '/lib/CreateSEFU.class.php';

require_once ROOT_PATH . '/include/GrabberException.php';
require_once ROOT_PATH . '/include/class.item_import.php';
require_once ROOT_PATH . '/include/class.item_image_convert.php';

require_once ROOT_PATH . '/include/class.sitemap.php';

require_once ROOT_PATH . '/include/imageResize/config_mage.ini.php';
require_once ROOT_PATH . '/include/imageResize/imageResizer.php';

require_once ROOT_PATH . '/application/models/ZendDBEntity.php';

Zend_Loader::loadClass('Zend_Config_Ini');
Zend_Loader::loadClass('Zend_Registry');
Zend_Loader::loadClass('Zend_Db');
Zend_Loader::loadClass('Zend_Db_Table');
Zend_Loader::loadClass('Zend_Db_Expr');

$config = new Zend_Config_Ini(APPLICATION_MODELS . '/configs/application.ini', 'production');


Zend_Registry::set('config', $config);


if (isset($_POST['update_now']) && $_POST['update_now']) {
    $upl_file = isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'] : '';

    if ($upl_file) {
        $upl_file_name = $_FILES['file']['name'];

        $_file_args = explode('.', $upl_file_name);
        $_file_ext = array_pop($_file_args);
        $_file_ext = strtolower($_file_ext);

        if ($_file_ext != 'xml' && $_file_ext != 'zip') {
            echo '<font color="red">Файл должен быть в формате XML млм ZIP архив</font><br></br>';
        } else {
            $path = UPLOAD_XML . '/' . $upl_file_name;

            if (is_uploaded_file($upl_file)) {
                move_uploaded_file($upl_file, $path);
            }
        }

        $item_import = new item_import();
        $d = dir(UPLOAD_XML);

        while (false !== ($entry = $d->read())) {
            if ($entry != '.' && $entry != '..') {
                $_file_args = explode('.', $entry);
                $_file_ext = array_pop($_file_args);
                $_file_ext = strtolower($_file_ext);

                $_file_path = UPLOAD_XML . '/' . $entry;
                if ($_file_ext == 'zip') {
                    $zip = new ZipArchive;
                    $res = $zip->open($_file_path);
                    if ($res === true) {
                        $zip->extractTo(UPLOAD_XML . '/');
                        $zip->close();
                    }

                    unlink($_file_path);
                }
            }
        }
        $d->rewind();
        while (false !== ($entry = $d->read())) {
            if ($entry != '.' && $entry != '..') {
                $_file_args = explode('.', $entry);
                $_file_ext = array_pop($_file_args);

                $_file_path = UPLOAD_XML . '/' . $entry;

                if ($_file_ext == 'xml') {
                    $item_import->loadXMLFile($_file_path);
                    $item_import->run();

                    unlink($_file_path);
                }
            }
        }
        $d->close();

//    $item_image_convert = new item_image_convert();
//    $item_image_convert->run();

//    $map = new SiteMap();
//    $map->run();

    } else {
        echo '<font color="red">Нужно выбрать XML млм ZIP архив</font><br></br>';
    }
} elseif (isset($_POST['update_later']) && $_POST['update_later']) {
    $upl_file = isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'] : '';
    if ($upl_file) {
        $upl_file_name = $_FILES['file']['name'];

        $_file_args = explode('.', $upl_file_name);
        $_file_ext = array_pop($_file_args);
        $_file_ext = strtolower($_file_ext);

        if ($_file_ext != 'xml' && $_file_ext != 'zip') {
            echo '<font color="red">Файл должен быть в формате XML млм ZIP архив</font><br></br>';
        } else {
            $path = UPLOAD_XML . '/' . $upl_file_name;
            if (is_uploaded_file($upl_file)) {
                move_uploaded_file($upl_file, $path);
            }
            echo '<meta http-equiv="Refresh" content="1;url=ITEM_IMPORT.php">';
        }
    } else {
        echo '<font color="red">Нужно выбрать XML млм ZIP архив</font><br></br>';
    }
}

$cmf->MakeCommonFooter();
$cmf->Close();
unset($cmf);
?>