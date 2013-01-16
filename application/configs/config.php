<?php

/**
 * Подключаем пути и константы для всего приложения
 * Можно использывать как для приложения (сайта) так и для скриптов крона
 */
defined('DEBUG_MODE') or define('DEBUG_MODE', false);


defined('SITE_PATH')
    || define('SITE_PATH', realpath(dirname(__FILE__) . '/../../www'));

defined('ROOT_PATH')
    || define('ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));

defined('INDEX_PATH')
    || define('INDEX_PATH', ROOT_PATH . '/search');

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', ROOT_PATH . '/application');

// Define path to library directory
defined('LIBRARY_PATH')
    || define('LIBRARY_PATH', ROOT_PATH . '/library');


defined('HTTP_HOST')
    || define('HTTP_HOST', 'http://7560000.com.ua');

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV',
              (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));


// Ensure library/ is on include_path
set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
          realpath(LIBRARY_PATH),
          realpath(APPLICATION_PATH),
          get_include_path(),
        )
    )
);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
} else {
    error_reporting(0);
}

require_once APPLICATION_PATH . '/models/ZendDBEntity.php';
