<?php

require_once __DIR__ . '/../../../../application/configs/config.php';

//define('PHP_EXCEL_PATH', realpath(dirname(__FILE__) . '/../../../../include'));
define('PHP_EXCEL_GENERATE_PATH', realpath(dirname(__FILE__) . '/ExcelGenerate'));
define('PHP_EXCEL_CONFIG_PATH', realpath(dirname(__FILE__) . '/Config'));
//set_include_path(PHP_EXCEL_PATH.PATH_SEPARATOR.PHP_EXCEL_GENERATE_PATH.PATH_SEPARATOR.PHP_EXCEL_CONFIG_PATH);

//require_once "PHPExcel/PHPExcel.php";

require_once 'Config/AbstractConfig.php';
require_once 'Config/XmlConfig.php';

require_once PHP_EXCEL_CONFIG_PATH.'/Config.php';

require_once 'ExcelGenerate/ExcelReportGenerate.php';