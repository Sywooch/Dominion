<?php
ini_set("display_errors",1);
ini_set("display_startup_errors",1);
require ('core.php');
include 'lib/tovar_check/config.php';
include 'lib/shopuser/class.model.php';
include 'lib/shopuser/class.export_shopuser.php';

$cmf = new SCMF('SHOPUSER_EXPORT');

session_set_cookie_params($cmf->sessionCookieLifeTime,'/admin/');
session_start();

if (!$cmf->GetRights()) {header('Location: login.php'); exit;}
$cmf->HeaderNoCache();
$cmf->makeCookieActions();
if(isset($_POST['shopusersexport']) && $_POST['shopusersexport'])
{
    $model = new shopuserModel($cmf);
    $tovar_check = new export_shopuser($model);
    $tovar_check->run();
    $excel_data = $tovar_check->getExcelData();

    $generate = new ExcelReportGenerate();
    $generate->addPage('Покупатели');
    $generate->setSpreadsheetData($excel_data);
    $generate->getFile('Shupusers.xls');
}
$cmf->MakeCommonHeader();

print <<<EOF

<h2 class='h2'>Экспорт покупателей</h2>
<form action="" method="post">
<input type="submit" name="shopusersexport" value="Экспорт">
</form>
EOF;


$cmf->MakeCommonFooter();
$cmf->Close();
unset($cmf);
?>
