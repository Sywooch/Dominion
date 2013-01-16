<?php
/**
 * Конвертор и загрузка фотографий для каталога товаров
 *
 * Работает на кроне
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

  $registry = Zend_Registry::getInstance();

  $config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini', 'production');

  $db_config = array(
      'host' => $config->resources->db->params->host,
      'username' => $config->resources->db->params->username,
      'password' => $config->resources->db->params->password,
      'dbname' => $config->resources->db->params->dbname);

  $adapter = $config->resources->db->adapter;

  $db = Zend_Db::factory($adapter, $db_config);
  $db->getConnection();

  $registry->set('db_connect', $db);


define('UPLOAD_XML', ROOT_PATH.'/upload_xml');
define('UPLOAD_IMAGES', ROOT_PATH.'/upload_images');

require_once ROOT_PATH.'/include/GrabberException.php';
require_once ROOT_PATH.'/include/class.item_image_convert.php';

require_once ROOT_PATH.'/include/imageResize/config_mage.ini.php';
require_once ROOT_PATH.'/include/imageResize/imageResizer.php';


$item_image_convert = new item_image_convert();
$item_image_convert->run();