<?php

/**
 * Диспетчер приложения
 * сюда переводятся все запросы с помощью .htaccess
 */
require_once realpath(dirname(__FILE__) . '/../application/configs/') . '/config.php';

/** Zend_Application */
require_once 'Zend/Application.php';

require_once 'Zend/Session.php';
Zend_Session::start();

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
$application->bootstrap()->run();