<?php
require ('core.php');
include 'lib/tovar_check/config.php';
include 'lib/tovar_check/class.check_rashod.php';
include 'lib/tovar_check/class.rashodnaya.php';
include 'lib/tovar_check/class.check_model.php';

include 'lib/tovar_check/iPlaceholders.php';
include 'lib/tovar_check/class.placeholder_data.php';

  $cmf= new SCMF();

  $objConfig = new Config();
  $config = $objConfig->getConfig(PHP_EXCEL_CONFIG_PATH.'/rashodnaya.xml');

  $zakaz_id = !empty($_GET['zakaz_id']) ? $_GET['zakaz_id']:0;

  if(!empty($zakaz_id)){
    $model = new checkModel($cmf);
    
    $tovar_check = new rashodnaya($config, $model, $zakaz_id);
    $tovar_check->run();
    $excel_data = $tovar_check->getExcelData();
    
    $generate = new ExcelReportGenerate();
    $generate->addPage('Расходная');
    $generate->setSpreadsheetData($excel_data);
    $generate->getFile('rashodnaya.xls');
  }
?>