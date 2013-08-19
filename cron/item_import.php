<?php

/**
 * Импортер данных по крону
 * работает с ранее загруженными XML файлами с товарам и ценами
 */
require_once realpath(dirname(__FILE__) . '/../application/configs/') . '/config.php';
define('IS_LIDER', 'hits');
define('IS_RECOMEND', 'newest');

require_once 'Zend/Loader.php';

Zend_Loader::loadClass('Zend_Config_Ini');
Zend_Loader::loadClass('Zend_Registry');
Zend_Loader::loadClass('Zend_Db');
Zend_Loader::loadClass('Zend_Db_Table');
Zend_Loader::loadClass('Zend_Db_Expr');
Zend_Loader::loadClass('Zend_Exception');


define('UPLOAD_XML', ROOT_PATH . '/upload_xml');
define('UPLOAD_IMAGES', ROOT_PATH . '/upload_images');

require_once ROOT_PATH . '/include/GrabberException.php';
require_once ROOT_PATH . '/include/class.item_import.php';
require_once ROOT_PATH . '/include/class.item_image_convert.php';
require_once ROOT_PATH . '/include/class.sitemap.php';
require_once ROOT_PATH . '/include/imageResize/config_mage.ini.php';
require_once ROOT_PATH . '/include/imageResize/ImageResize.php';
require_once ROOT_PATH . "/include/generateDescription.php";

require_once SITE_PATH . "/lib/CreateSEFU.class.php";

require SITE_PATH."/lib/MetaGenerate.php";
require SITE_PATH."/lib/MetaGenerateModelStrategy.php";
            
$registry = Zend_Registry::getInstance();

$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');

$db_config = array(
    'host' => $config->resources->db->params->host,
    'username' => $config->resources->db->params->username,
    'password' => $config->resources->db->params->password,
    'dbname' => $config->resources->db->params->dbname);

$adapter = $config->resources->db->adapter;

$db = Zend_Db::factory($adapter, $db_config);
$db->getConnection();

$registry->set('db_connect', $db);

$item_import = new item_import();
$d = dir(UPLOAD_XML);

// Если есть в каталоге zip  архивы - распаковываем
while (false !== ($entry = $d->read())) {
    if ($entry != '.' && $entry != '..') {
        $_file_args = explode('.', $entry);
        $_file_ext = array_pop($_file_args);
        $_file_ext = strtolower($_file_ext);

        $_file_path = UPLOAD_XML . '/' . $entry;
        if ($_file_ext == 'zip') {
            $zip = new ZipArchive;
            $res = $zip->open($_file_path);
            if ($res === TRUE) {
                $zip->extractTo(UPLOAD_XML . '/');
                $zip->close();
            }

            unlink($_file_path);
        }
    }
}
// Перематываем указатель на начало каталога
$d->rewind();

$doRebuild = false;

while (false !== ($entry = $d->read())) {
    if ($entry != '.' && $entry != '..') {
        $_file_args = explode('.', $entry);
        $_file_ext = array_pop($_file_args);

        $_file_path = UPLOAD_XML . '/' . $entry;

        if ($_file_ext == 'xml') {
            $item_import->loadXMLFile($_file_path);
            $item_import->run();

            unlink($_file_path);
            $doRebuild = true;
        }
    }
}
$d->close();

// Если была загрузка данных из файла - деалем перестоение sitemap.xml и генерацию кратких описаний

if ($doRebuild) {
    $map = new SiteMap();
    $map->run();


    $sefu = new generateDescription();
    $sefu->run();
    
    $model = MetaGenerateModelStrategy::getModel($db);    
    $meta = new MetaGenerate($model);

    $meta->metaCatalogue();
    $meta->metaItems();
}