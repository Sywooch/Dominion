<?php
ini_set("display_errors",1);
ini_set("display_startup_errors",1);

require ('core.php');
include 'lib/tovar_check/config.php';
include 'lib/tovar_check/class.check_rashod.php';
include 'lib/tovar_check/class.tovar_check.php';
include 'lib/tovar_check/class.check_model.php';

include 'lib/tovar_check/iPlaceholders.php';
include 'lib/tovar_check/class.placeholder_data.php';

  $cmf= new SCMF();
  
  $objConfig = new Config();  
  $config = $objConfig->getConfig(PHP_EXCEL_CONFIG_PATH.'/tov_check_config.xml');

  $zakaz_id = !empty($_GET['zakaz_id']) ? $_GET['zakaz_id']:0;

  if(!empty($zakaz_id)){
    $model = new checkModel($cmf);
    $tovar_check = new tovar_check($config, $model, $zakaz_id);
    $tovar_check->run();
    $excel_data = $tovar_check->getExcelData();
            
    $generate = new ExcelReportGenerate();
    $generate->addPage('Товарный чек');
    $generate->setSpreadsheetData($excel_data);
    $generate->getFile('cash-memo.xls');
  }
?>