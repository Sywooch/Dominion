<?php

/**
 * Рассылка сообщений подписавшивмся клиентам
 * о товарах которых не было но поступившитх в продажу
 *
 *
 */
require_once realpath(dirname(__FILE__) . '/../application/configs/') . '/config.php';

require_once 'Zend/Loader.php';

Zend_Loader::loadClass('Zend_Config_Ini');
Zend_Loader::loadClass('Zend_Registry');
Zend_Loader::loadClass('Zend_Db');
Zend_Loader::loadClass('Zend_Db_Table');
Zend_Loader::loadClass('Zend_Db_Expr');
require_once ROOT_PATH . '/include/class.item_subscribe.php';
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

$item_import = new item_subscribe();
$item_import->run();