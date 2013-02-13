<?php
set_time_limit(0);
defined('SITE_PATH')
    || define('SITE_PATH', realpath(dirname(__FILE__) . '/../'));
// Set the application root path
defined('ROOT_PATH')
    || define('ROOT_PATH', realpath($_SERVER['DOCUMENT_ROOT'].'/../'));

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', ROOT_PATH . '/application');
    
// Define path to application directory
defined('APPLICATION_MODELS')
    || define('APPLICATION_MODELS', ROOT_PATH . '/application/models');

// Define path to library directory
defined('LIBRARY_PATH')
    || define('LIBRARY_PATH', ROOT_PATH . '/library');
    
// Define path to library directory
defined('ZEND_PATH')
    || define('ZEND_PATH', ROOT_PATH . '/library/Zend');    
    

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(LIBRARY_PATH),
    realpath(APPLICATION_PATH),
    realpath(ZEND_PATH),
    get_include_path(),
)));

require_once APPLICATION_PATH.'/models/ZendDBEntity.php';
require_once ZEND_PATH.'/Loader.php';

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

require_once ROOT_PATH.'/include/GrabberException.php';
require_once ROOT_PATH.'/include/class.item_image_convert.php';

require_once ROOT_PATH.'/include/imageResize/config_mage.ini.php';
require_once ROOT_PATH.'/include/imageResize/imageResizer.php';


$item_image_convert = new item_image_convert();
$item_image_convert->run();
?>
