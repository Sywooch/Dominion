<?php

/**
 * Запуск индексатора поискового индекса для поитска по атрибутам
 * вешается на cron.
 * Запускается ASIS без параметров
 *
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

echo "Get DB connections... \n";

$db = Zend_Db::factory($adapter, $db_config);
$db->getConnection();

echo "Connected \n";
$registry->set('db_connect', $db);

require_once ROOT_PATH . '/include/search_cash/class.search_cash.php';
require_once ROOT_PATH . '/include/search_cash/class.search_cash_item.php';

$sCash = new search_cash_item();

echo "Start cache building...";
$sCash->buildCash();

echo "Done!";