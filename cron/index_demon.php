<?php

/**
 * Генерация поискового индекса
 */
require_once realpath(dirname(__FILE__) . '/../application/configs/') . '/config.php';

require_once ROOT_PATH . '/include/class.item_subscribe.php';
//require_once APPLICATION_PATH.'/models/ZendDBEntity.php';
require_once 'Zend/Loader.php';

//recursive_remove_directory(INDEX_PATH, TRUE);

Zend_Loader::loadClass('Zend_Config_Ini');
Zend_Loader::loadClass('Zend_Registry');
Zend_Loader::loadClass('Zend_Db');
Zend_Loader::loadClass('Zend_Db_Table');
Zend_Loader::loadClass('Zend_Db_Expr');
Zend_Loader::loadClass('Zend_Search_Lucene_Analysis_Analyzer');
Zend_Loader::loadClass('Zend_Search_Lucene');

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

$index = Zend_Search_Lucene::create(INDEX_PATH);

Zend_Loader::loadClass('models_Item');
Zend_Loader::loadClass('models_Catalogue');

$Item = new models_Item();
//$Catalogue = new models_Catalogue();

Zend_Search_Lucene_Analysis_Analyzer::setDefault(
        new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive());


$items = $Item->getItemsSearch();

foreach ($items as $item) {
    $doc = new Zend_Search_Lucene_Document();
    $doc->addField(Zend_Search_Lucene_Field::UnIndexed('item_id',
                                                       $item['ITEM_ID']));
    $doc->addField(Zend_Search_Lucene_Field::Text('name', $item['NAME'], 'UTF-8'));
    $doc->addField(Zend_Search_Lucene_Field::Text('brand_name',
                                                  $item['BRAND_NAME'], 'UTF-8'));
    $doc->addField(Zend_Search_Lucene_Field::Text('catalogue', $item['CNAME'],
                                                  'UTF-8'));
    $doc->addField(Zend_Search_Lucene_Field::Text('description',
                                                  $item['DESCRIPTION'], 'UTF-8'));
    $doc->addField(Zend_Search_Lucene_Field::Keyword('article', $item['ARTICLE']));

    $attrValues = $Item->getItemSearchAttrs($item['ITEM_ID']);
    if (!empty($attrValues)) {
        $attrSearchField = '';
        foreach ($attrValues as $val) {
            $attrSearchField .= $val['NAME'] . " ";
        }
        $doc->addField(Zend_Search_Lucene_Field::Text('attr_val',
                                                      $attrSearchField, 'UTF-8'));
    }

    $url = $item['CATALOGUE_REALCATNAME'] . $item['ITEM_ID'] . '-' . $item['CATNAME'] . '/';
    $doc->addField(Zend_Search_Lucene_Field::UnIndexed('url', $url));
    $index->addDocument($doc);
}

$index->optimize();

function recursive_remove_directory($directory, $empty = FALSE)
{
    if (substr($directory, -1) == '/') {
        $directory = substr($directory, 0, -1);
    }
    if (!file_exists($directory) || !is_dir($directory)) {
        return FALSE;
    } elseif (is_readable($directory)) {
        $handle = opendir($directory);
        while (FALSE !== ($item = readdir($handle))) {
            if ($item != '.' && $item != '..') {
                $path = $directory . '/' . $item;
                if (is_dir($path)) {
                    recursive_remove_directory($path);
                } else {
                    unlink($path);
                }
            }
        }
        closedir($handle);
        if ($empty == FALSE) {
            if (!rmdir($directory)) {
                return FALSE;
            }
        }
    }
    return TRUE;
}