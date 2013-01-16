<?php

/**
 * Генерация прайсов для прайсообменников
 *
 * Запускается на кроне с параметрами
 * Праметр запуска- имя прайсообменника
 * Задается как имя Action
 *
 * Например php /home/kkb/public_html/price_export.php pn  - генерим прайс для pn.com.ua
 */
require_once realpath(dirname(__FILE__) . '/../application/configs/') . '/config.php';

require_once 'Zend/Loader.php';

Zend_Loader::loadClass('Zend_Config_Ini');
Zend_Loader::loadClass('Zend_Registry');
Zend_Loader::loadClass('Zend_Db');
Zend_Loader::loadClass('Zend_Db_Table');
Zend_Loader::loadClass('Zend_Db_Expr');

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

require_once ROOT_PATH . '/include/class.price_export.php';
require_once ROOT_PATH . '/library/App/View/DOMxml.class.php';

$indent = !empty($argv[1]) ? $argv[1] : '';
if (!empty($indent)) {
    $Suscribe = new price_export($indent);

    $method = $indent . 'Action';
    $Suscribe->$method();
}